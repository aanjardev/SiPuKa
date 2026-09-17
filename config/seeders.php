<?php

return [
    'allow_destructive_in_production' => (bool) env('ALLOW_PRODUCTION_SEEDING', false),
    'admin_password' => env('SEED_ADMIN_PASSWORD'),
    'legacy_admin_password' => env('SEED_LEGACY_ADMIN_PASSWORD'),
];
