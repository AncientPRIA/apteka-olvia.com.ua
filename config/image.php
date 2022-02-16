<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Image Driver
    |--------------------------------------------------------------------------
    |
    | Intervention Image supports "GD Library" and "Imagick" to process images
    | internally. You may choose one of them according to your PHP
    | configuration. By default PHP's "GD Library" implementation is used.
    |
    | Supported: "gd", "imagick"
    |
    */

    'driver' => 'gd',

    'watermark' => [
        'product' => [
            'relative_path' => 'uploads/watermarks/product3.png',
            'width' => 60,
            'height' => 60,
            'position' => 'bottom-right',
            'x' => 10,
            'y' => 10,
        ]
    ]

];
