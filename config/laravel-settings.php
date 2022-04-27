<?php


return [

    /*
     * The name of the table we save the settings in.
     */
    'table' => 'settings',

    /*
     * Config related to the caching of settings
     */
    'cache' => [
        // How long, in seconds, to cache a setting value for.
        'ttl' => 3600
    ],

    /*
     * Config related to the encryption of settings
     */
    'encryption' => [
        // Should all settings be encrypted by default? This can be overridden on each individual setting.
        'default' => false
    ],

    /*
     * You can register any settings here instead of your service provider if you'd like.
     *
     * For class-based settings, just put a list of class names in this array.
     * For an anonymous setting, view the documentation to see how to register one here.
     */
    'settings' => [
        //        \App\Setting\SiteTheme::class,
        //        [ // An anonymous setting
        //            'type' => 'user', // 'user', 'global', or a custom type
        //            'key' => 'timezone', // The setting key
        //            'defaultValue' => 'Europe/London', // The default value
        //            // The field. You must serialize this so your config can still be cached.
        //            'fieldOptions' => serialize(\FormSchema\Generator\Field::textInput('timezone')->setValue('Europe/London')),
        //            'groups' => ['language', 'content'], // Groups to put the setting in
        //            'rules' => ['string|timezone'] // Laravel validation rules to check the setting value
        //        ]
    ],

    /*
     * You can register setting groups here.
     */
    'groups' => [
        //        'branding' => [
        //            'title' => 'Branding',
        //            'subtitle' => 'Settings related to the site brand'
        //        ],
    ],

    /*
     * Register aliases for class-based settings here. Registering an alias makes a class-based setting available through a simple string key.
     */
    'aliases' => [

    ],

    /*
     * Config related to routes this package registers
     */
    'routes' => [
        'api' => [
            // Should we register routes?
            'enabled' => true,
            // What should we prefix the URL with?
            'prefix' => 'api/elbowspace/settings',
            // Any middleware to apply to the routes
            'middleware' => []
        ]
    ],

    /*
     * Config related to the JavaScript package
     */
    'js' => [
        // A list of setting keys/aliases that we should make available to the frontend on every page
        'autoload' => [
            'unit_system',
            'dark_mode',
            'stats_order_preference',
            'bruit_api_key'
        ]
    ]

];
