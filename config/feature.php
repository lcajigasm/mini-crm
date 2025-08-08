<?php

return [
    // Feature flags toggled via environment variables
    'TELEPHONY' => (bool) env('FEATURE_TELEPHONY', false),
    'WHATSAPP' => (bool) env('FEATURE_WHATSAPP', false),
    'HUBSPOT' => (bool) env('FEATURE_HUBSPOT', false),
];


