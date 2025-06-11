<?php

// return [
// 	'mode'                  => 'utf-8',
// 	'format'                => 'A4',
// 	'author'                => '',
// 	'subject'               => '',
// 	'keywords'              => '',
// 	'creator'               => 'Laravel Pdf',
// 	'display_mode'          => 'fullpage',
// 	'tempDir'               => base_path('../temp/'),
// 	'pdf_a'                 => false,
// 	'pdf_a_auto'            => false,
// 	'icc_profile_path'      => ''
// ];
return [
    'font_path' => base_path('resources/admin/fonts/'),
    'font_data' => [
        'battambang' => [
            'R'  => 'khmer_os_ battambang.ttf',    // regular font
            'useOTL' => 0xFF,    // required for complicated langs like Persian, Arabic and Chinese
            'useKashida' => 75,  // required for complicated langs like Persian, Arabic and Chinese
        ],
        'khmeros_muol' => [
            'R'  => 'khmeros_muol.ttf',    // regular font
            'useOTL' => 0xFF,    // required for complicated langs like Persian, Arabic and Chinese
            'useKashida' => 75,  // required for complicated langs like Persian, Arabic and Chinese
        ],
        'bayon' => [
            'R'  => 'Bayon-Regular.ttf',    // regular font
            'useOTL' => 0xFF,    // required for complicated langs like Persian, Arabic and Chinese
            'useKashida' => 75,  // required for complicated langs like Persian, Arabic and Chinese
        ],
        'kmSBBICsf' => [
            'R'  => 'kmSBBICsf.ttf',    // regular font
            'useOTL' => 0xFF,    // required for complicated langs like Persian, Arabic and Chinese
            'useKashida' => 75,  // required for complicated langs like Persian, Arabic and Chinese
        ],
        'Siemreap' => [
            'R'  => 'khmeros_muol.ttf',    // regular font
            'useOTL' => 0xFF,    // required for complicated langs like Persian, Arabic and Chinese
            'useKashida' => 75,  // required for complicated langs like Persian, Arabic and Chinese
        ],
    ],
    'mode'                  => 'utf-8',
    // 'format'                => [80,160],
    'format'                => 'A4',
    'author'                => '',
    'subject'               => '',
    'keywords'              => '',
    'creator'               => 'Laravel Pdf',
    'display_mode'          => 'fullpage',
    'tempDir'               => base_path('../temp/')
];
