<?php

return [
    'provider' => env('AI_PROVIDER', 'mock'),
    'base_url' => env('AI_BASE_URL', env('OPENAI_BASE_URL', 'https://api.openai.com/v1')),
    'api_key' => env('AI_API_KEY', env('OPENAI_API_KEY')),
    'model' => env('AI_MODEL', env('OPENAI_MODEL', 'gpt-4.1-mini')),
    'queue' => (bool) env('AI_QUEUE', false),
    'force_mock' => (bool) env('AI_FORCE_MOCK', true),
];
