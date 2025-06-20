<?php
return [
    'app_name' => 'Gideons Technology',
    'fcm_server_key' => env('FCM_SERVER_KEY'),
    'oauth' => [
        'google' => [
            'client_id' => env('GOOGLE_CLIENT_ID'),
            'client_secret' => env('GOOGLE_CLIENT_SECRET'),
            'redirect_uri' => env('APP_URL') . '/auth/google/callback',
            'auth_url' => 'https://accounts.google.com/o/oauth2/v2/auth',
            'token_url' => 'https://oauth2.googleapis.com/token',
            'user_info_url' => 'https://www.googleapis.com/oauth2/v3/userinfo',
            'scope' => 'email profile'
        ],
        'facebook' => [
            'client_id' => env('FACEBOOK_CLIENT_ID'),
            'client_secret' => env('FACEBOOK_CLIENT_SECRET'),
            'redirect_uri' => env('APP_URL') . '/auth/facebook/callback',
            'auth_url' => 'https://www.facebook.com/v12.0/dialog/oauth',
            'token_url' => 'https://graph.facebook.com/v12.0/oauth/access_token',
            'user_info_url' => 'https://graph.facebook.com/v12.0/me?fields=id,name,email',
            'scope' => 'email'
        ]
    ]
];