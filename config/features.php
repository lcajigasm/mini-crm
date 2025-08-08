<?php

return [
    'TELEPHONY' => (bool) env('FEATURE_TELEPHONY', false),
    'WHATSAPP' => (bool) env('FEATURE_WHATSAPP', false),
    'HUBSPOT' => (bool) env('FEATURE_HUBSPOT', false),
];
