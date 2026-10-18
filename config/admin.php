<?php

return [

    // The initial administrator created by the AdminUserSeeder on a fresh install.
    // The password is read from .env (ADMIN_INITIAL_PASSWORD) and is never stored in
    // source code. Once the account exists it is never changed by the seeder.
    'initial_name' => env('ADMIN_NAME', 'Richard Ese'),
    'initial_email' => env('ADMIN_EMAIL', 'richard@eclipzmedia.com'),
    'initial_password' => env('ADMIN_INITIAL_PASSWORD'),

];