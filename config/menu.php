<?php

return [
    //dashboard
    [
        'path' => 'admin/dashboard',
        'active' => 'admin/dashboard',
        'permission' => 'dashboard-view',
        'name' => [
            'en' => 'Dashboard',
            'km' => 'ផ្ទាំងគ្រប់គ្រង',
        ],
        'icon' => 'home',
    ],
    // Type
    [
        'path' => 'admin/type/list/1',
        'active' => 'admin/type*',
        'permission' => 'service-type-view',
        'name' => [
            'en' => 'Service Type',
        ],
        'icon' => 'server',
    ],
    // Service
    [
        'path' => 'admin/service/list/1',
        'active' => 'admin/service*',
        'permission' => 'service-view',
        'name' => [
            'en' => 'Service',
        ],
        'icon' => 'airplay',
    ],

    // Project
    [
        'path' => 'admin/project/list/1',
        'active' => 'admin/project*',
        'permission' => 'project-view',
        'name' => [
            'en' => 'Projects',
        ],
        'icon' => 'codepen',
    ],
    // Customer
    [
        'path' => 'admin/customer/list/1',
        'active' => 'admin/customer*',
        'permission' => 'customer-view',
        'name' => [
            'en' => 'Customers',
        ],
        'icon' => 'users',
    ],

    // Po
    [
        'path' => 'admin/po/po-service/list/1',
        'active' => 'admin/po/po-service*',
        'permission' => 'purchase-order-view',
        'name' => [
            'en' => 'Po',
        ],
        'icon' => 'shopping-cart',
    ],


    // Purchase
    [
        'path' => 'admin/purchase/list/1',
        'active' => 'admin/purchase*',
        'permission' => 'purchase-view',
        'name' => [
            'en' => 'P A C',
        ],
        'icon' => 'book',
    ],

    // Invoice
    [
        'path' => 'admin/invoice/list/all',
        'active' => 'admin/invoice/*',
        'permission' => 'invoice-view',
        'name' => [
            'en' => 'Invoices',
        ],
        'icon' => 'file',
    ],

    // Summary invoice 
    [
        'active' => 'admin/report/summary-invoice*',
        'permission' => array('infra-view', 'submarine-view'),
        'name' => [
            'en' => 'Summary Invoice',
        ],
        'icon' => 'file',
        'children'  => [
            [
                'path' => 'admin/report/summary-invoice/infra/list',
                'active' => 'admin/report/summary-invoice/infra/*',
                'permission' => 'infra-view',
                'name' => [
                    'en' => 'Infra Invoice',
                ],
            ],
            [
                'path' => 'admin/report/summary-invoice/submarine/list',
                'active' => 'admin/report/summary-invoice/submarine/*',
                'permission' => 'submarine-view',
                'name' => [
                    'en' => 'Submarine Invoice',
                ],
            ],
        ]
    ],


    // Credit Note
    [
        'path' => 'admin/credit-note/list/1',
        'active' => 'admin/credit-note*',
        'permission' => 'credit-note-view',
        'name' => [
            'en' => 'Credit Note',
        ],
        'icon' => 'slash',
    ],

    // receipt
    [
        'path' => 'admin/receipt/list/1',
        'active' => 'admin/receipt*',
        'permission' => 'receipt-view',
        'name' => [
            'en' => 'Receipts',
        ],
        'icon' => 'file',
    ],

    // FTTH Service
    [
        'path' => 'admin/ftth-service/list/1',
        'active' => 'admin/ftth-service/*',
        'permission' => 'ftth-service-view',
        'name' => [
            'en' => 'FTTH Service',
        ],
        'icon' => 'airplay',
    ],

    // order
    [
        'active' => 'admin/work-order*',
        'permission' => array('work-order-order', 'work-order-invoice', 'work-order-credit-note', 'work-order-receipt'),
        'name' => [
            'en' => 'Work Order',
        ],
        'icon' => 'shopping-cart',
        'children'  => [
            [
                'path' => 'admin/work-order/order/list/1',
                'active' => 'admin/work-order/order/*',
                'permission' => 'work-order-order',
                'name' => [
                    'en' => 'Order',
                ],
            ],
            [
                'path' => 'admin/work-order/invoice/list/all',
                'active' => 'admin/work-order/invoice/*',
                'permission' => 'work-order-invoice',
                'name' => [
                    'en' => 'Invoice',
                ],
            ],
            [
                'path' => 'admin/work-order/credit-note/list/1',
                'active' => 'admin/work-order/credit-note/*',
                'permission' => 'work-order-credit-note',
                'name' => [
                    'en' => 'Credit Note',
                ],
            ],
            [
                'path' => 'admin/work-order/receipt/list/1',
                'active' => 'admin/work-order/receipt/*',
                'permission' => 'work-order-receipt',
                'name' => [
                    'en' => 'Receipt',
                ],
            ]
        ]
    ],

    // Fttx
    [
        'active' => 'admin/fttx*',
        'permission' => array('fttx-view', 'fttx-customer-type-view', 'fttx-pos-speed-view', 'fttx-setting-price-view', 'fttx-annual-report-view'),
        'name' => [
            'en' => 'FTTx',
        ],
        'icon' => 'wifi',
        'children'  => [
            [
                'path' => 'admin/fttx/fttx/list/all',
                'active' => 'admin/fttx/fttx/*',
                'permission' => 'fttx-view',
                'name' => [
                    'en' => 'FTTx',
                ],
            ],
            [
                'path' => 'admin/fttx/customer-type/list/1',
                'active' => 'admin/fttx/customer-type/*',
                'permission' => 'fttx-customer-type-view',
                'name' => [
                    'en' => 'Customer Type',
                ],
            ],
            [
                'path' => 'admin/fttx/customer-price/list/1',
                'active' => 'admin/fttx/customer-price/*',
                'permission' => 'fttx-customer-price-view',
                'name' => [
                    'en' => 'Customer Price',
                ],
            ],
            [
                'path' => 'admin/fttx/pos-speed/list/1',
                'active' => 'admin/fttx/pos-speed/*',
                'permission' => 'fttx-pos-speed-view',
                'name' => [
                    'en' => 'Pos Speed',
                ],
            ],
            [
                'path' => 'admin/fttx/setting-price/list/1',
                'active' => 'admin/fttx/setting-price/*',
                'permission' => 'fttx-setting-price-view',
                'name' => [
                    'en' => 'Setting Price',
                ],
            ],
            [
                'path' => 'admin/fttx/expiration-report/list',
                'active' => 'admin/fttx/expiration-report/*',
                'permission' => 'fttx-expiration-report-view',
                'name' => [
                    'en' => 'Expiration Income Report',
                ],
            ],
            [
                'path' => 'admin/fttx/report/list?fttx_status=2',
                'active' => 'admin/fttx/report/*',
                'permission' => 'fttx-annual-report-view',
                'name' => [
                    'en' => 'Annual Report',
                ],
            ],

        ]
    ],

    // DmcFileManager
    [
        'path' => 'admin/dmc-file-manager/list',
        'active' => 'admin/dmc-file-manager/*',
        'permission' => 'dmc-file-manager-view',
        'name' => [
            'en' => 'DMC FileManager',
        ],
        'icon' => 'folder',
    ],


    //CFOCN Report
    [
        'active' => 'admin/report/receive-payment/list,admin/report/ar-acging/list,admin/report/customer-info/list,admin/report/revenue/list,admin/report/income/list',
        'permission' => ['report-invoice-view', 'report-receive-payment-view', 'report-ar-acging-view', 'report-ar-project-view', 'report-cfocn-customer-view', 'report-revenue-view', 'report-income-view'],
        'name' => [
            'en' => 'CFOCN Report',
            'km' => 'CFOCN របាយការណ៍',
        ],
        'icon' => 'file-text',
        'children' => [
            [
                'path' => 'admin/report/receive-payment/list',
                'active' => 'admin/report/receive-payment/*',
                'permission' => 'report-receive-payment-view',
                'name' => [
                    'en' => 'Receive Payment',
                ],
                'icon' => 'columns',
            ],
            [
                'path' => 'admin/report/ar-acging/list',
                'active' => 'admin/report/ar-acging*',
                'permission' => 'report-ar-acging-view',
                'name' => [
                    'en' => 'A/R By Aging',
                    'km' => 'A/R By Aging',
                ],
            ],
            [
                'path' => 'admin/report/customer-info/list',
                'active' => 'admin/report/customer-info/list',
                'permission' => 'report-cfocn-customer-view',
                'name' => [
                    'en' => 'Customer Info',
                    'km' => 'Customer Info',
                ],
            ],
            [
                'path' => 'admin/report/revenue/list',
                'active' => 'admin/report/revenue/list',
                'permission' => 'report-revenue-view',
                'name' => [
                    'en' => 'Revenue',
                    'km' => 'Revenue',
                ],
            ],
            [
                'path' => 'admin/report/income/list',
                'active' => 'admin/report/income/list',
                'permission' => 'report-income-view',
                'name' => [
                    'en' => 'Income',
                    'km' => 'Income',
                ],
            ],
        ],
    ],

    //Tax Report
    [
        'active' => 'admin/report/sale-journal/list',
        'permission' => ['report-sale-journal-view'],
        'name' => [
            'en' => 'Tax Report',
            'km' => 'របាយការណ៍ពន្ធ',
        ],
        'icon' => 'file-text',
        'children' => [
            [
                'path' => 'admin/report/sale-journal/list',
                'active' => 'admin/report/sale-journal/list',
                'permission' => 'report-sale-journal-view',
                'name' => [
                    'en' => 'Sale Journal',
                    'km' => 'ទិនានុប្បវត្តិលក់',
                ],
            ],
        ],
    ],

    //MPTC Report
    [
        'active' => 'admin/report/customer/list,admin/report/old-customer/list,admin/report/customer-dmc/list',
        'permission' => ['report-customer-view'],
        'name' => [
            'en' => 'MPTC Report',
            'km' => 'MPTC របាយការណ៍',
        ],
        'icon' => 'file-text',
        'children' => [
            [
                'path' => 'admin/report/customer-dmc/list',
                'active' => 'admin/report/customer-dmc/*',
                'permission' => 'report-customer-view',
                'name' => [
                    'en' => 'Customer DMC',
                    'km' => 'Customer DMC',
                ],
            ]
        ],
    ],

    //Summary Annual Report

    [
        'active' => 'admin/report/annual-report/list/invoice-receipt,admin/report/annual-report/list/invoice-detail',
        'permission' => ['invoice-receipt-report', 'invoice-detail-report'],
        'name' => [
            'en' => 'Summary Annual Report',
            'km' => '',
        ],
        'icon' => 'file-text',
        'children' => [
            [
                'path' => 'admin/report/annual-report/list/invoice-receipt',
                'active' => 'admin/report/annual-report/list/invoice-receipt',
                'permission' => 'invoice-receipt-report',
                'name' => [
                    'en' => 'Invoice & Receipt',
                    'km' => '',
                ],
            ],
            [
                'path' => 'admin/report/annual-report/list/invoice-detail',
                'active' => 'admin/report/annual-report/list/invoice-detail',
                'permission' => 'invoice-detail-report',
                'name' => [
                    'en' => 'Invoice Detail',
                    'km' => '',
                ],
            ],
        ],
    ],

    //document managerment

    [
        'active' => 'admin/document/*',
        'permission' => ['document-pac-view', 'document-invoice-view', 'document-receipt-view', 'document-contract-view'],
        'name' => [
            'en' => 'document',
            'km' => 'ឯកសារ',
        ],
        'icon' => 'folder',
        'children' => [
            [
                'path' => 'admin/document/document-pac',
                'active' => 'admin/document/document-pac',
                'permission' => 'document-pac-view',
                'name' => [
                    'en' => 'P A C',
                    'km' => '',
                ],
            ],
            [
                'path' => 'admin/document/document-invoice',
                'active' => 'admin/document/document-invoice',
                'permission' => 'document-invoice-view',
                'name' => [
                    'en' => 'Invoice',
                    'km' => '',
                ],
            ],
            [
                'path' => 'admin/document/document-receipt',
                'active' => 'admin/document/document-receipt',
                'permission' => 'document-receipt-view',
                'name' => [
                    'en' => 'Receipt',
                    'km' => '',
                ],
            ],
            [
                'path' => 'admin/document/document-contract',
                'active' => 'admin/document/document-contract',
                'permission' => 'document-contract-view',
                'name' => [
                    'en' => 'Contract',
                    'km' => '',
                ],
            ],
        ],
    ],

    // User
    [
        'path' => 'admin/user/list/1',
        'active' => 'admin/user*',
        'permission' => 'user-view',
        'name' => [
            'en' => 'User',
        ],
        'icon' => 'user',
    ],

    //setting
    [
        'active' => 'admin/close-date/*,admin/rate*,admin/license-fee/list*,admin/bank-account/*,admin/logo-control*',
        'permission' => ['close-date', 'exchange-rate', 'license-fee', 'bank-account', 'logo-control'],
        'name' => [
            'en' => 'Setting',
            'km' => 'កំណត់',
        ],
        'icon' => 'settings',
        'children' => [
            [
                'path' => 'admin/close-date/list/1',
                'active' => 'admin/close-date/*',
                'permission' => 'close-date',
                'name' => [
                    'en' => 'Close Date',
                    'km' => '',
                ],
            ],
            [
                'path' => 'admin/rate',
                'active' => 'admin/rate',
                'permission' => 'exchange-rate',
                'name' => [
                    'en' => 'Exchang Rate',
                    'km' => 'អត្រាប្រចាំឆ្នាំ',
                ],
            ],
            [
                'path' => 'admin/license-fee/list/1',
                'active' => 'admin/license-fee/list*',
                'permission' => 'license-fee',
                'name' => [
                    'en' => 'License Fee',
                    'km' => '',
                ],
            ],
            [
                'path' => 'admin/bank-account/list/1',
                'active' => 'admin/bank-account/list*',
                'permission' => 'bank-account',
                'name' => [
                    'en' => 'Bank Account',
                    'km' => '',
                ],
            ],
            [
                'path' => 'admin/logo-control',
                'active' => 'admin/logo-control',
                'permission' => 'logo-control',
                'name' => [
                    'en' => 'Lock Logo',
                    'km' => '',
                ],
            ],

        ],
    ],
];
