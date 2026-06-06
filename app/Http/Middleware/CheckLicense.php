<?php

namespace App\Http\Middleware;

use App\Models\SchoolSetting;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckLicense
{
    public function handle(Request $request, Closure $next): Response
    {
        $setting = SchoolSetting::find(1);

        // No license key at all → redirect to license page
        if (!$setting || empty($setting->license_key)) {
            if (!$request->routeIs('settings.license*')) {
                return redirect()->route('settings.license');
            }
            return $next($request);
        }

        // Decode and verify license key
        $decoded = $this->verifyKey($setting->license_key);
        if (!$decoded) {
            $setting->license_status = 'invalid';
            $setting->save();
            if (!$request->routeIs('settings.license*')) {
                return redirect()->route('settings.license')->with('error', 'લાઇસન્સ કી અમાન્ય છે.');
            }
            return $next($request);
        }

        // Check domain
        $currentDomain = $request->getHost();
        $licenseDomain = $decoded['domain'];
        if ($licenseDomain !== $currentDomain && $licenseDomain !== '*') {
            $setting->license_status = 'domain_mismatch';
            $setting->save();
            if (!$request->routeIs('settings.license*')) {
                return redirect()->route('settings.license')->with('error', 'લાઇસન્સ આ ડોમેન માટે માન્ય નથી.');
            }
            return $next($request);
        }

        // Check expiry
        $expiry = $decoded['expiry'];
        if ($expiry !== '2099-12-31' && $expiry < now()->format('Y-m-d')) {
            $setting->license_status = 'expired';
            $setting->save();
            if (!$request->routeIs('settings.license*')) {
                return redirect()->route('settings.license')->with('error', 'લાઇસન્સની મુદત સમાપ્ત થઈ ગઈ છે.');
            }
            return $next($request);
        }

        // Check status from DB (could be revoked by ping)
        if ($setting->license_status === 'revoked') {
            if (!$request->routeIs('settings.license*')) {
                return redirect()->route('settings.license')->with('error', 'આ લાઇસન્સ રિવોક કરવામાં આવ્યું છે.');
            }
            return $next($request);
        }

        // All good — ensure status is active
        if ($setting->license_status !== 'active') {
            $setting->license_status = 'active';
            $setting->licensee_name = $decoded['licensee'] ?? $setting->licensee_name;
            $setting->licensed_until = $expiry;
            $setting->save();
        }

        return $next($request);
    }

    /**
     * Decode and verify HMAC-signed license key.
     * Returns array with domain, expiry, features or false.
     * Also checks fallback: if a plain "licensee|domain|expiry" is in DB, skip HMAC
     * (for old records), but the key must still exist.
     */
    private function verifyKey(string $licenseKey): array|false
    {
        $secret = config('license.hmac_secret');

        $decoded = base64_decode($licenseKey, true);
        if (!$decoded) return false;

        $parts = explode('||', $decoded);
        if (count($parts) !== 2) {
            // Fallback: maybe it's stored as raw JSON for legacy
            $json = json_decode($licenseKey, true);
            if ($json && isset($json['domain'])) {
                return $json;
            }
            return false;
        }

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
