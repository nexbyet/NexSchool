<?php

namespace App\Http\Controllers;

use App\Models\SchoolSetting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;

class InstallController extends Controller
{
    public function welcome()
    {
        return view('install.welcome');
    }

    public function setLanguage(Request $request)
    {
        $lang = $request->input('lang', 'gu');
        session(['install.lang' => $lang]);
        return redirect()->route('install.requirements');
    }

    public function requirements()
    {
        $checks = $this->runRequirements();
        $allPass = collect($checks)->every(fn($c) => $c['pass']);
        return view('install.requirements', compact('checks', 'allPass'));
    }

    public function requirementsNext()
    {
        return redirect()->route('install.database');
    }

    public function database()
    {
        // If .env exists with DB creds, auto-detect (reinstall)
        $db = [];
        if (file_exists(base_path('.env'))) {
            $env = file_get_contents(base_path('.env'));
            preg_match('/^DB_HOST=(.*)$/m', $env, $m) && $db['host'] = $m[1];
            preg_match('/^DB_PORT=(.*)$/m', $env, $m) && $db['port'] = $m[1];
            preg_match('/^DB_DATABASE=(.*)$/m', $env, $m) && $db['name'] = $m[1];
            preg_match('/^DB_USERNAME=(.*)$/m', $env, $m) && $db['user'] = $m[1];
            preg_match('/^DB_PASSWORD=(.*)$/m', $env, $m) && $db['pass'] = $m[1];
        }
        return view('install.database', compact('db'));
    }

    public function testDatabase(Request $request)
    {
        $host = $request->input('host');
        $port = $request->input('port', 3306);
        $name = $request->input('name');
        $user = $request->input('user');
        $pass = $request->input('pass');

        try {
            new \PDO("mysql:host={$host};port={$port};dbname={$name}", $user, $pass);
            session(['install.db' => compact('host', 'port', 'name', 'user', 'pass')]);
            return response()->json(['success' => true, 'message' => 'Connection successful!']);
        } catch (\PDOException $e) {
            return response()->json(['success' => false, 'message' => 'Connection failed: ' . $e->getMessage()]);
        }
    }

    public function license()
    {
        return view('install.license');
    }

    public function activateLicense(Request $request)
    {
        $request->validate(['license_key' => 'required|string']);
        $licenseKey = trim($request->license_key);

        $decoded = $this->verifyKey($licenseKey);
        if (!$decoded) {
            return response()->json(['success' => false, 'message' => 'Invalid license key (HMAC mismatch).']);
        }

        $currentDomain = $request->getHost();
        if ($decoded['domain'] !== $currentDomain && $decoded['domain'] !== '*') {
            return response()->json(['success' => false, 'message' => "License not valid for '$currentDomain'."]);
        }

        $expiry = $decoded['expiry'];
        if ($expiry !== '2099-12-31' && $expiry < now()->format('Y-m-d')) {
            return response()->json(['success' => false, 'message' => 'License has expired.']);
        }

        session(['install.license_key' => $licenseKey]);
        return response()->json(['success' => true, 'message' => 'License valid!', 'expiry' => $expiry, 'domain' => $decoded['domain']]);
    }

    public function admin()
    {
        return view('install.admin');
    }

    public function saveAdmin(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);
        session(['install.admin' => $request->only('name', 'email', 'password')]);
        return redirect()->route('install.school');
    }

    public function school()
    {
        return view('install.school');
    }

    public function saveSchool(Request $request)
    {
        $request->validate([
            'school_name_gu' => 'required|string|max:255',
            'school_name_en' => 'required|string|max:255',
            'address' => 'nullable|string',
            'mobile' => 'nullable|string',
        ]);
        session(['install.school' => $request->only('school_name_gu', 'school_name_en', 'address', 'mobile')]);
        return redirect()->route('install.run');
    }

    public function run()
    {
        return view('install.run');
    }

    public function process(Request $request)
    {
        try {
            $db = session('install.db');
            $admin = session('install.admin');
            $school = session('install.school');
            $licenseKey = session('install.license_key');
            $isReinstall = file_exists(base_path('.env'));

            if (!$db || !$admin || !$school) {
                return response()->json(['success' => false, 'message' => 'Complete all steps first.']);
            }

            // ── Set up DB config in current process ──
            putenv('DB_CONNECTION=mysql');
            putenv('DB_HOST=' . $db['host']);
            putenv('DB_PORT=' . $db['port']);
            putenv('DB_DATABASE=' . $db['name']);
            putenv('DB_USERNAME=' . $db['user']);
            putenv('DB_PASSWORD=' . $db['pass']);
            config(['database.default' => 'mysql']);
            foreach (['host','port','database','username','password'] as $k) {
                $map = ['database'=>'name','username'=>'user','password'=>'pass'];
                $src = $map[$k] ?? $k;
                config(["database.connections.mysql.$k" => $db[$src]]);
            }
            \Illuminate\Support\Facades\DB::purge('sqlite');
            \Illuminate\Support\Facades\DB::purge('mysql');

            $kernel = app()->make(\Illuminate\Contracts\Console\Kernel::class);

            // ── Fresh install: write .env ──
            if (!$isReinstall) {
                $envContent = file_exists(base_path('.env.example')) ? file_get_contents(base_path('.env.example')) : '';
                if (empty($envContent)) {
                    $envContent = "APP_NAME=NexSchool\nAPP_ENV=production\nAPP_DEBUG=false\nAPP_TIMEZONE=Asia/Kolkata\n";
                }
                $this->envSet($envContent, 'DB_CONNECTION', 'mysql');
                $this->envSet($envContent, 'DB_HOST', $db['host']);
                $this->envSet($envContent, 'DB_PORT', $db['port']);
                $this->envSet($envContent, 'DB_DATABASE', $db['name']);
                $this->envSet($envContent, 'DB_USERNAME', $db['user']);
                $this->envSet($envContent, 'DB_PASSWORD', $db['pass']);
                $this->envSet($envContent, 'APP_NAME', 'NexSchool');
                $this->envSet($envContent, 'APP_ENV', 'production');
                $this->envSet($envContent, 'APP_DEBUG', 'false');
                $this->envSet($envContent, 'APP_URL', $request->getSchemeAndHttpHost());
                $this->envSet($envContent, 'SESSION_DRIVER', 'file');
                if (!str_contains($envContent, 'LICENSE_SERVER_URL')) {
                    $envContent .= "\nLICENSE_SERVER_URL=" . config('license.server_url');
                    $envContent .= "\nLICENSE_HMAC_SECRET=" . config('license.hmac_secret');
                }
                file_put_contents(base_path('.env'), $envContent);

                // Remove leftover SQLite
                if (file_exists(database_path('database.sqlite'))) {
                    @unlink(database_path('database.sqlite'));
                }

                // Generate APP_KEY
                $kernel->call('key:generate', ['--force' => true]);
            }

            // ── Create storage symlink ──
            if (is_dir(base_path('storage/app/public')) && !file_exists(public_path('storage'))) {
                $kernel->call('storage:link');
            }

            // ── Run migrations (safe for both fresh/reinstall) ──
            $kernel->call('migrate', ['--force' => true]);

            // ── Create or update admin (safe for both fresh/reinstall) ──
            $existingAdmin = User::where('role', 'admin')->first();
            if ($existingAdmin) {
                $existingAdmin->update([
                    'name' => $admin['name'],
                    'email' => $admin['email'],
                    'password' => Hash::make($admin['password']),
                ]);
            } else {
                User::create([
                    'name' => $admin['name'],
                    'email' => $admin['email'],
                    'password' => Hash::make($admin['password']),
                    'role' => 'admin',
                ]);
            }

            // ── Save school settings ──
            $setting = SchoolSetting::firstOrNew(['id' => 1]);
            $setting->fill([
                'school_name_gu' => $school['school_name_gu'] ?? '',
                'school_name_en' => $school['school_name_en'] ?? '',
                'address' => $school['address'] ?? '',
                'mobile' => $school['mobile'] ?? '',
                'app_version' => config('app.version', '1.0.0'),
            ]);
            if ($licenseKey) {
                $setting->license_key = $licenseKey;
                $setting->license_status = 'active';
            }
            $setting->save();

            // ── Mark installed ──
            file_put_contents(storage_path('installed.lock'), now()->toIso8601String());

            // ── Store summary & clear session ──
            session(['install_complete' => [
                'email' => $admin['email'],
                'school_name' => $school['school_name_gu'] ?? $school['school_name_en'] ?? 'NexSchool',
            ]]);
            session()->forget(['install.db', 'install.admin', 'install.school', 'install.license_key', 'install.lang']);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function complete()
    {
        // Load details from DB (session may be lost after key change)
        $setting = SchoolSetting::find(1);
        $admin = User::where('role', 'admin')->first();
        return view('install.complete', [
            'email' => $admin?->email ?? session('install_complete.email', 'admin@nexschool.com'),
            'school_name' => $setting?->school_name_gu ?? $setting?->school_name_en ?? session('install_complete.school_name', 'NexSchool'),
        ]);
    }

    // ─── Reinstall (admin only, password-protected) ──────────────

    public function reinstallForm()
    {
        return view('install.reinstall');
    }

    public function reinstallConfirm(Request $request)
    {
        $request->validate(['password' => 'required']);
        if (!Hash::check($request->password, auth()->user()->password)) {
            return back()->withErrors(['password' => 'Incorrect password.']);
        }
        @unlink(storage_path('installed.lock'));
        auth()->logout();
        return redirect()->route('install.welcome');
    }

    // ─── Helpers ────────────────────────────────────────────────

    private function runRequirements(): array
    {
        return [
            'php_version' => ['label' => 'PHP ' . PHP_VERSION, 'pass' => version_compare(PHP_VERSION, '8.1.0', '>=')],
            'pdo' => ['label' => 'PDO Extension', 'pass' => extension_loaded('PDO')],
            'pdo_mysql' => ['label' => 'PDO MySQL', 'pass' => extension_loaded('pdo_mysql')],
            'mbstring' => ['label' => 'MBString Extension', 'pass' => extension_loaded('mbstring')],
            'gd' => ['label' => 'GD Extension', 'pass' => extension_loaded('gd')],
            'zip' => ['label' => 'ZIP Extension', 'pass' => extension_loaded('zip')],
            'json' => ['label' => 'JSON Extension', 'pass' => extension_loaded('json')],
            'openssl' => ['label' => 'OpenSSL Extension', 'pass' => extension_loaded('openssl')],
            'xml' => ['label' => 'XML Extension', 'pass' => extension_loaded('xml')],
            'curl' => ['label' => 'cURL Extension', 'pass' => extension_loaded('curl')],
            'storage_writable' => ['label' => 'Storage Writable', 'pass' => is_writable(storage_path())],
            'cache_writable' => ['label' => 'Bootstrap/Cache Writable', 'pass' => is_writable(base_path('bootstrap/cache'))],
        ];
    }

    private function verifyKey(string $licenseKey): array|false
    {
        $secret = config('license.hmac_secret');
        $decoded = base64_decode($licenseKey, true);
        if (!$decoded) return false;
        $parts = explode('||', $decoded);
        if (count($parts) !== 2) return false;
        $data = $parts[0];
        $signature = $parts[1];
        $dataParts = explode('|', $data);
        if (count($dataParts) < 3) return false;
        if (!hash_equals(hash_hmac('sha256', $data, $secret), $signature)) return false;
        return ['domain' => $dataParts[0], 'expiry' => $dataParts[1], 'features' => $dataParts[2]];
    }

    private function envSet(string &$env, string $key, ?string $value = ''): void
    {
        $value = $value ?? '';
        if (preg_match('/^' . preg_quote($key, '/') . '=/m', $env)) {
            $env = preg_replace('/^' . preg_quote($key, '/') . '=.*$/m', $key . '=' . $value, $env);
        } elseif (preg_match('/^#\s*' . preg_quote($key, '/') . '=/m', $env)) {
            $env = preg_replace('/^#\s*' . preg_quote($key, '/') . '=.*$/m', $key . '=' . $value, $env);
        } else {
            $env .= "\n" . $key . '=' . $value;
        }
    }
}
