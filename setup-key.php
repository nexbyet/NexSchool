<?php
$envPath = __DIR__ . '/.env';
if (!file_exists($envPath)) {
    die('.env file not found!');
}
$env = file_get_contents($envPath);
if (preg_match('/^APP_KEY=.+/m', $env)) {
    die('APP_KEY already set.');
}
$key = 'base64:' . base64_encode(random_bytes(32));
if (preg_match('/^APP_KEY=/m', $env)) {
    $env = preg_replace('/^APP_KEY=.*$/m', 'APP_KEY=' . $key, $env);
} else {
    $env = preg_replace('/^APP_NAME=.+/m', "$0\nAPP_KEY=" . $key, $env);
}
file_put_contents($envPath, $env);
echo "APP_KEY generated successfully!<br><a href='/install'>Continue to /install</a>";