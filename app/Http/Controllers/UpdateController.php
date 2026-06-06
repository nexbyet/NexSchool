<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;

class UpdateController extends Controller
{
    public function index()
    {
        $currentVersion = $this->getCurrentVersion();
        return view('settings.updates', compact('currentVersion'));
    }

    public function check()
    {
        try {
            $owner = config('github.owner');
            $repo  = config('github.repo');
            $token = config('github.token');

            $url = "https://api.github.com/repos/{$owner}/{$repo}/releases/latest";

            $headers = ['Accept' => 'application/vnd.github.v3+json'];
            if ($token) {
                $headers['Authorization'] = 'Bearer ' . $token;
            }

            $response = Http::timeout(10)->withHeaders($headers)->get($url);

            if (!$response->successful()) {
                return response()->json([
                    'success' => false,
                    'message' => 'GitHub API ઍક્સેસ કરી શકાયું નહીં. સ્ટેટસ: ' . $response->status(),
                ]);
            }

            $release = $response->json();
            $latestVersion = ltrim($release['tag_name'] ?? 'v0.0.0', 'v');
            $currentVersion = $this->getCurrentVersion();

            return response()->json([
                'success' => true,
                'update_available' => version_compare($latestVersion, $currentVersion, '>'),
                'current_version' => $currentVersion,
                'latest_version' => $latestVersion,
                'changelog' => $release['body'] ?? '',
                'download_url' => $release['assets'][0]['browser_download_url'] ?? null,
                'published_at' => $release['published_at'] ?? null,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'અપડેટ ચેક નિષ્ફળ: ' . $e->getMessage(),
            ]);
        }
    }

    public function run()
    {
        @ini_set('max_execution_time', 300);
        @ini_set('memory_limit', '512M');

        try {
            $owner = config('github.owner');
            $repo  = config('github.repo');
            $token = config('github.token');

            $url = "https://api.github.com/repos/{$owner}/{$repo}/releases/latest";
            $headers = ['Accept' => 'application/vnd.github.v3+json', 'User-Agent' => 'NexSchool-Updater/1.0'];
            if ($token) {
                $headers['Authorization'] = 'Bearer ' . $token;
            }

            $releaseResponse = Http::timeout(15)->withHeaders($headers)->get($url);
            if (!$releaseResponse->successful()) {
                return $this->jsonError('GitHub API ઍક્સેસ કરી શકાયું નહીં.', $releaseResponse->status());
            }

            $release = $releaseResponse->json();
            $tagName = $release['tag_name'] ?? '';
            $latestVersion = ltrim($tagName, 'v');

            if (empty($release['zipball_url'])) {
                return $this->jsonError('રિલીઝમાં ZIP ડાઉનલોડ URL મળ્યો નથી.');
            }

            $currentVersion = $this->getCurrentVersion();
            if (!version_compare($latestVersion, $currentVersion, '>')) {
                return $this->jsonError('કોઈ નવું અપડેટ ઉપલબ્ધ નથી.');
            }

            $tmpBase = storage_path('app/update_tmp');
            if (!is_dir($tmpBase)) {
                mkdir($tmpBase, 0755, true);
            }

            $ts = time();
            $zipPath = "{$tmpBase}/{$ts}.zip";
            $extractDir = "{$tmpBase}/{$ts}";

            $zipData = Http::timeout(120)->withHeaders($headers)->get($release['zipball_url']);
            if (!$zipData->successful()) {
                return $this->jsonError('ZIP ડાઉનલોડ નિષ્ફળ.', $zipData->status());
            }
            file_put_contents($zipPath, $zipData->body());

            if (!class_exists('ZipArchive')) {
                @unlink($zipPath);
                return $this->jsonError('PHP ZipArchive એક્સટેન્શન જરૂરી છે.');
            }

            $zip = new \ZipArchive;
            if ($zip->open($zipPath) !== true) {
                @unlink($zipPath);
                return $this->jsonError('ZIP ફાઇલ ખોલી શકાઈ નહીં.');
            }
            $zip->extractTo($extractDir);
            $zip->close();
            @unlink($zipPath);

            $sourceDir = $this->findSourceDir($extractDir);
            if (!$sourceDir) {
                $this->rmDirRecursive($extractDir);
                return $this->jsonError('એક્સટ્રેક્ટેડ ફાઇલોમાં એપ્લિકેશન રૂટ મળ્યો નથી.');
            }

            $updateJsonPath = $sourceDir . '/update.json';
            $updateConfig = [];
            if (file_exists($updateJsonPath)) {
                $updateConfig = json_decode(file_get_contents($updateJsonPath), true) ?? [];
                $requires = $updateConfig['requires'] ?? null;
                if ($requires && version_compare($currentVersion, $requires, '<')) {
                    $this->rmDirRecursive($extractDir);
                    return $this->jsonError("આ અપડેટ માટે v{$requires} જરૂરી છે, પરંતુ તમારી પાસે v{$currentVersion} છે.");
                }
                if (!empty($updateConfig['version'])) {
                    $latestVersion = $updateConfig['version'];
                }
            }

            $this->copyToBase($sourceDir);

            Artisan::call('migrate', ['--force' => true]);

            foreach ($updateConfig['delete_files'] ?? [] as $file) {
                $path = base_path($file);
                if (file_exists($path) && !is_dir($path)) {
                    @unlink($path);
                }
            }

            if (!is_dir(base_path('vendor'))) {
                Artisan::call('optimize:clear');
                $this->rmDirRecursive($extractDir);
                return $this->jsonError('vendor ડિરેક્ટરી મળી નથી. કૃપા કરીને "composer install --no-dev" ચલાવો અને ફરી પ્રયાસ કરો.');
            }

            Artisan::call('optimize:clear');

            \App\Models\SchoolSetting::find(1)?->update(['app_version' => $latestVersion]);

            cache()->forget('school_settings');

            $this->rmDirRecursive($extractDir);

            return response()->json([
                'success' => true,
                'message' => "અપડેટ v{$latestVersion} સફળતાપૂર્વક ઇન્સ્ટોલ થયું!",
            ]);
        } catch (\Exception $e) {
            if (isset($extractDir) && is_dir($extractDir)) {
                $this->rmDirRecursive($extractDir);
            }
            if (isset($zipPath) && file_exists($zipPath)) {
                @unlink($zipPath);
            }
            return $this->jsonError('અપડેટ નિષ્ફળ: ' . $e->getMessage());
        }
    }

    private function getCurrentVersion(): string
    {
        return \App\Models\SchoolSetting::find(1)?->app_version ?? config('app.version', '1.0.0');
    }

    private function jsonError(string $message, ?int $status = null)
    {
        if ($status) {
            $message .= ' (HTTP ' . $status . ')';
        }
        return response()->json(['success' => false, 'message' => $message]);
    }

    private function findSourceDir(string $dir): ?string
    {
        $items = array_diff(scandir($dir), ['.', '..']);
        foreach ($items as $item) {
            $path = $dir . DIRECTORY_SEPARATOR . $item;
            if (is_dir($path) && is_dir($path . DIRECTORY_SEPARATOR . 'app') && is_dir($path . DIRECTORY_SEPARATOR . 'config')) {
                return $path;
            }
        }
        if (is_dir($dir . DIRECTORY_SEPARATOR . 'app')) {
            return $dir;
        }
        return null;
    }

    private function copyToBase(string $source): void
    {
        $base = base_path();
        $excludes = ['.env', 'storage' . DIRECTORY_SEPARATOR . 'installed.lock'];

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($source, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $item) {
            $relative = str_replace($source . DIRECTORY_SEPARATOR, '', $item->getPathname());

            $skip = false;
            foreach ($excludes as $ex) {
                if (str_starts_with($relative, $ex)) {
                    $skip = true;
                    break;
                }
            }
            if ($skip) continue;

            $dest = $base . DIRECTORY_SEPARATOR . $relative;

            if ($item->isDir()) {
                if (!is_dir($dest)) {
                    mkdir($dest, 0755, true);
                }
            } else {
                @copy($item->getPathname(), $dest);
                @chmod($dest, 0644);
            }
        }
    }

    private function rmDirRecursive(string $dir): void
    {
        if (!is_dir($dir)) return;
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach ($iterator as $item) {
            if ($item->isDir()) {
                @rmdir($item->getPathname());
            } else {
                @unlink($item->getPathname());
            }
        }
        @rmdir($dir);
    }
}
