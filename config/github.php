<?php

// GitHub update configuration
// All values are read from .env (never committed to git)
return [
    'owner' => env('GITHUB_REPO_OWNER', 'your-username'),
    'repo'  => env('GITHUB_REPO_NAME', 'NexSchool'),
    'token' => env('GITHUB_TOKEN', null),
];
