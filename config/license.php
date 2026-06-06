<?php
return [
    /*
    | License Server — where the Laravel app phones home to
    | Set this in your .env: LICENSE_SERVER_URL=https://license.nexbyet.com
    */
    'server_url' => env('LICENSE_SERVER_URL', 'https://license.nexbyet.com'),

    /*
    | HMAC Secret — must match the secret in license-tool/config.php
    | Set this in your .env: LICENSE_HMAC_SECRET=your-256-bit-hex-key
    */
    'hmac_secret' => env('LICENSE_HMAC_SECRET', 'nxsch-9a4f2c8e1b7d3f6a0c5e8b2d4f7a1c3'),

    /*
    | Ping interval in hours (how often to phone home)
    */
    'ping_interval' => env('LICENSE_PING_INTERVAL', 24),
];
