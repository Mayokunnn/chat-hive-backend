<?php

return [
    'credentials_file' => base_path(env('FIREBASE_CREDENTIALS')),
    'api_key' => env('FIREBASE_API_KEY'),
    'auth_domain' => env('FIREBASE_AUTH_DOMAIN'),
    'default_storage_bucket' => env('FIREBASE_STORAGE_BUCKET'),
];
