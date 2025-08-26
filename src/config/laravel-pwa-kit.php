<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Enable PWA
    |--------------------------------------------------------------------------
    | Globally enable or disable Progressive Web App functionality.
    */
    'enable_pwa' => true,

    /*
    |--------------------------------------------------------------------------
    | Show Install Toast on First Load
    |--------------------------------------------------------------------------
    |
    | Determines whether the PWA install toast should be displayed when a user
    | first visits the site. Once the toast is shown or dismissed, it will not
    | reappear for that user on the same day, preventing repeated interruptions
    | and improving user experience.
    |
    | Type: `bool`
    | Default: true
    |
    */
    'install-toast-show' => true,


    /*
    |--------------------------------------------------------------------------
    | PWA Manifest Configuration
    |--------------------------------------------------------------------------
    | Defines metadata for your Progressive Web App.
    | This configuration is used to generate the manifest.json file.
    | Reference: https://developer.mozilla.org/en-US/docs/Web/Manifest
    */
    'manifest' => [
        'appName' => env('APP_NAME', 'Laravel'),
        'name' => env('APP_NAME', 'Laravel'),
        'shortName' => env('APP_NAME', 'Laravel'),
        'short_name' => env('APP_NAME', 'Laravel'),
        'startUrl' => '/',
        'start_url' => '/',
        'scope' => '/',
        'author' => 'Rabiul Islam',
        'version' => '1.0',
        'description' => 'A description of your web app.',
        'orientation' => 'portrait',
        'dir' => 'auto',
        'lang' => 'en',
        'display' => 'standalone',
        'themeColor' => '#FF5733',
        'theme_color' => '#FF5733',
        'backgroundColor' => '#ffffff',
        'background_color' => '#FF5733',
        'icons' => [
            [
                'src' => 'logo.png',
                'sizes' => '512x512',
                'type' => 'image/png',
            ]
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Debug Mode
    |--------------------------------------------------------------------------
    | Enables verbose logging for service worker events and cache information.
    */
    'debug' => env('LARAVEL_PWA_DEBUG', false),

    /*
    |--------------------------------------------------------------------------
    | Toast Content
    |--------------------------------------------------------------------------
    | Title and description text for the install prompt toast.
    */
    'title' => 'Welcome to ' . env('APP_NAME', 'Laravel') . '!',
    'description' => 'Click the <strong>Install Now</strong> button & enjoy it just like an app.',

    /*
    |--------------------------------------------------------------------------
    | Mobile View Position
    |--------------------------------------------------------------------------
    | Position of the PWA install toast on small devices.
    | Supported values: "top", "bottom".
    | RTL mode is supported and respects <html dir="rtl">.
    */
    'small_device_position' => 'bottom',

    /*
    |--------------------------------------------------------------------------
    | Install Now Button Text
    |--------------------------------------------------------------------------
    | Defines the text shown on the "Install Now" button inside the PWA
    | installation toast. This can be customized for localization.
    |
    | Example: 'install_now_button_text' => 'অ্যাপ ইন্সটল করুন'
    */
    'install_now_button_text' => 'Install Now',

    /*
    |--------------------------------------------------------------------------
    | Livewire Integration
    |--------------------------------------------------------------------------
    | Optimize PWA functionality for applications using Laravel Livewire.
    */
    'livewire-app' => false,
];
