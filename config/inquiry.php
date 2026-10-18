<?php

return [

    // The year in an Inquiry ID is calculated in this timezone (the business is in Lagos).
    'timezone' => 'Africa/Lagos',

    // Last option in the "Service of interest" list. The other options come
    // from the visible service categories in the database.
    'other_service_label' => 'Not sure yet / Other',

    // PLACEHOLDER budget bands. Richard must confirm or edit these before launch.
    // The array key is what gets stored; the value is what visitors and admins see.
    'budget_options' => [
        'under_500k' => 'Under ₦500,000',
        '500k_1m' => '₦500,000 - ₦1,000,000',
        '1m_3m' => '₦1,000,000 - ₦3,000,000',
        'above_3m' => 'Above ₦3,000,000',
        'not_sure' => 'Not sure yet',
    ],

    // Per-IP limits on form submissions (every attempt counts, valid or not).
    'rate_limits' => [
        'per_minute' => 5,
        'per_hour' => 15,
    ],

];