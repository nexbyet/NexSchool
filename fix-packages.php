<?php
$envPath = __DIR__ . '/.env';
if (!file_exists($envPath)) {
    die('.env not found');
}
$env = file_get_contents($envPath);
if (!preg_match('/^APP_KEY=.+/m', $env)) {
    $key = 'base64:' . base64_encode(random_bytes(32));
    $env = preg_replace('/^APP_KEY=.*$/m', 'APP_KEY=' . $key, $env);
    file_put_contents($envPath, $env);
}
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->call('package:discover');
echo "Package discover done!<br><a href='/public'>Go to app</a>";