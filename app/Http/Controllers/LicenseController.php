<?php

namespace App\Http\Controllers;

use App\Models\SchoolSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class LicenseController extends Controller
{
    public function index()
    {
        $setting = SchoolSetting::find(1);
        return view('settings.license', compact('setting'));
    }

    public function activate(Request $request)
    {
        $request->validate([
            'license_key' => 'required|string',
        ]);

        $licenseKey = trim($request->license_key);
        $setting = SchoolSetting::firstOrNew(['id' => 1]);

        // Verify the key locally first
        $decoded = $this->verifyKey($licenseKey);
        if (!$decoded) {
            return response()->json([
                'success' => false,
                'message' => 'આ લાઇસન્સ કી અમાન્ય છે અથવા તેની HMAC સિગ્નેચર મેળ ખાતી નથી.',
            ]);
        }

        // Check domain
        $currentDomain = $request->getHost();
        if ($decoded['domain'] !== $currentDomain && $decoded['domain'] !== '*') {
            return response()->json([
                'success' => false,
                'message' => "આ લાઇસન્સી કી '$currentDomain' માટે નથી. માન્ય ડોમેન: {$decoded['domain']}",
            ]);
        }

        // Check expiry
        $expiry = $decoded['expiry'];
        if ($expiry !== '2099-12-31' && $expiry < now()->format('Y-m-d')) {
            return response()->json([
                'success' => false,
                'message' => 'લાઇસન્સની મુદત સમાપ્ત થઈ ગઈ છે: ' . $expiry,
            ]);
        }

        // Also verify with remote license server
        try {
            $response = Http::timeout(10)->post(config('license.server_url') . '/index.php?action=api-ping', [
                'domain' => $currentDomain,
                'license_key' => $licenseKey,
                'version' => config('app.version', '1.0.0'),
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if (!$data['valid']) {
                    return response()->json([
                        'success' => false,
                        'message' => 'રિમોટ સર્વરે લાઇસન્સ અમાન્ય ગણાવ્યું: ' . ($data['message'] ?? ''),
                    ]);
                }
            }
            // If server unreachable, still allow (offline activation)
        } catch (\Exception $e) {
            // Server offline — activate locally (graceful)
        }

        // Save license
        $setting->license_key = $licenseKey;
        $setting->license_status = 'active';
        $setting->licensed_until = $expiry;
        $setting->last_license_ping = now();
        $setting->save();

        return response()->json([
            'success' => true,
            'message' => 'લાઇસન્સ સફળતાપૂર્વક સક્રિય થયું! 🎉',
            'expiry' => $expiry,
        ]);
    }

    public function deactivate(Request $request)
    {
        $setting = SchoolSetting::find(1);
        if ($setting) {
            $setting->license_key = null;
            $setting->license_status = 'unlicensed';
            $setting->licensee_name = null;
            $setting->licensed_until = null;
            $setting->last_license_ping = null;
            $setting->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'લાઇસન્સ દૂર કરવામાં આવ્યું.',
        ]);
    }

    public function status()
    {
        $setting = SchoolSetting::find(1);
        if (!$setting || empty($setting->license_key)) {
            return response()->json([
                'licensed' => false,
                'status' => 'unlicensed',
            ]);
        }

        $decoded = $this->verifyKey($setting->license_key);
        return response()->json([
            'licensed' => $setting->license_status === 'active' && $decoded !== false,
            'status' => $setting->license_status,
            'licensee' => $setting->licensee_name,
            'expiry' => $setting->licensed_until,
            'domain' => $decoded['domain'] ?? null,
        ]);
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

        $expectedSig = hash_hmac('sha256', $data, $secret);
        if (!hash_equals($expectedSig, $signature)) return false;

        return [
            'domain' => $dataParts[0],
            'expiry' => $dataParts[1],
            'features' => $dataParts[2],
        ];
    }
}
