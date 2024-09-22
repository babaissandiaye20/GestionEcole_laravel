<?php

return [
    'credentials_file' => env('FIREBASE_CREDENTIALS', base_path('config/firebase_credentials.json')),
    'project_id' => env('FIREBASE_PROJECT_ID'), // Add project ID configuration
];
