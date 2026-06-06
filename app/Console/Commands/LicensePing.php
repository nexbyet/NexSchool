<?php

namespace App\Console\Commands;

use App\Models\SchoolSetting;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class LicensePing extends Command
{
    protected $signature = 'license:ping';
    protected $description = 'Phone home to license server to verify license validity';

    public function handle(): int
    {
        $setting = SchoolSetting::find(1);
        if (!$setting || empty($setting->license_key)) {
            $this->warn('No license key found. Skipping ping.');
            return Command::SUCCESS;
        }

        $secret = config('license.hmac_secret');
        $domain = $this->extractDomainFromKey($setting->license_key, $secret) ?? gethostname();
        $serverUrl = config('license.server_url');
        $pingInterval = config('license.ping_interval');

        // Check if ping is needed (only ping once every ping_interval hours)
        if ($setting->last_license_ping) {
            $nextPing = $setting->last_license_ping->addHours($pingInterval);
            if ($nextPing->isFuture()) {
                $this->info('Ping not needed yet. Next ping: ' . $nextPing->format('Y-m-d H:i'));
                return Command::SUCCESS;
            }
        }

        $this->info('Pinging license server...');

        try {
            $response = Http::timeout(10)->post($serverUrl . '/index.php?action=api-ping', [
                'domain' => $domain,
                'license_key' => $setting->license_key,
                'version' => config('app.version', '1.0.0'),
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $status = $data['status'] ?? 'unknown';
                $valid = $data['valid'] ?? false;

                $this->info("Server response: status=$status, valid=" . ($valid ? 'true' : 'false'));

                $setting->last_license_ping = now();
                $setting->licensee_name = $data['licensee'] ?? $setting->licensee_name;

                if (!$valid) {
                    $setting->license_status = match ($status) {
                        'revoked' => 'revoked',
                        'expired' => 'expired',
                        'domain_mismatch' => 'domain_mismatch',
                        default => 'invalid',
                    };
                    $this->warn("License marked as: {$setting->license_status}");
                } else {
                    $setting->license_status = 'active';
                }

                $setting->save();
                $this->info('License ping completed successfully.');
            } else {
                $this->warn('License server returned status: ' . $response->status());
            }
        } catch (\Exception $e) {
            $this->error('Failed to ping license server: ' . $e->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    private function extractDomainFromKey(string $licenseKey, string $secret): ?string
    {
        $decoded = base64_decode($licenseKey, true);
        if (!$decoded) return null;
        $parts = explode('||', $decoded);
        if (count($parts) !== 2) return null;
        $dataParts = explode('|', $parts[0]);
        return $dataParts[0] ?? null;
    }
}
