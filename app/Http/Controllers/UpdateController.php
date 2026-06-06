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
        // Stub — actual update logic needs careful implementation
        // 1. Download ZIP from GitHub release asset
        // 2. Verify hash
        // 3. Extract via ZipArchive
        // 4. Run new migrations
        // 5. Delete obsolete files
        // 6. Clear cache
        // 7. Update version in DB

        return response()->json([
            'success' => false,
            'message' => 'અપડેટ ફંક્શન હજી અમલમાં નથી. કૃપા કરીને GitHub પરથી મેન્યુઅલી અપડેટ કરો.',
        ]);
    }

    private function getCurrentVersion(): string
    {
        return \App\Models\SchoolSetting::find(1)?->app_version ?? config('app.version', '1.0.0');
    }
}
