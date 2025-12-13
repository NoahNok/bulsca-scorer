<?php

return [
    'enabled' => env('SSO_ENABLED', true),
    'auth_server' => env('SSO_AUTH_SERVER'),
    'client_id' => env('SSO_CLIENT_ID'),
    'client_secret' => env('SSO_CLIENT_SECRET'),
    'redirect_uri' => env('APP_URL') . '/auth/sso/callback',
];