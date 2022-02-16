<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Stripe, Mailgun, SparkPost and others. This file provides a sane
    | default location for this type of information, allowing packages
    | to have a conventional place to find your various credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'sparkpost' => [
        'secret' => env('SPARKPOST_SECRET'),
    ],

    'stripe' => [
        'model' => App\User::class,
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
        'webhook' => [
            'secret' => env('STRIPE_WEBHOOK_SECRET'),
            'tolerance' => env('STRIPE_WEBHOOK_TOLERANCE', 300),
        ],
    ],


    /* SOCIALITE */
    // facebook, twitter, linkedin, google, github, gitlab or bitbucket
    /* OLD google
     * 'client_id' => '1048303107043-j5furna8okjmuvjg02925oi5v82dsmi3.apps.googleusercontent.com',
     * 'client_secret' => 'xxj5q5B59pjf3aQwTyVpT2df',
     * */
    'google' => [
        'client_id' => '268701285777-7oelc8belfo7pv7o524dm6uejnaum738.apps.googleusercontent.com',
        'client_secret' => 'f4C4tWuX8GCbbob8sbHp3pNT',
        'redirect' => '/login/google/callback'
    ],
    'vkontakte' => [
        'client_id' => '7693142',
        'client_secret' => 'VMHMBunWcU3vXuckFHte',
        'redirect' => '/login/vkontakte/callback'
    ],
    'instagram' => [
        'client_id' => 'e206c9f9b6044d0387f2f1787e8e4b42',
        'client_secret' => '754e3d81875f41eb88cf782b98177e59',
        'redirect' => '/login/instagram/callback'
    ],
    'facebook' => [
        'client_id' => '144690123757473',
        'client_secret' => '99b2992707b1fb64fc0b8cd8d5ae7b09',
        'redirect' => '/login/facebook/callback'
    ],


    /* SOCIALITE END */

    /* CAPTCHA */
    'captcha' => [
        'secret' => '6LdXFLgUAAAAAM4Tq_cEUPHBvvb6dD9Jpnm5DiaI',
    ]
    /* CAPTCHA END */

];
