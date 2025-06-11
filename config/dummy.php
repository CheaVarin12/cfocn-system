<?php

return [
    'fttx_new_install_price' => [
        'first_level' => [
            'key'           => 1,
            'text'          => '0-350m',
            'start_unit'    => 0,
            'end_unit'      => 350,
            'price'         => 85,
        ],
        'second_level' => [
            'key'           => 2,
            'text'          => '351-500m',
            'start_unit'    => 351,
            'end_unit'      => 500,
            'price'         => 108,
        ],
        'third_level' => [
            'key'           => 3,
            'text'          => '500-800m',
            'start_unit'    => 500,
            'end_unit'      => 800,
            'price'         => 130,
        ],
        'fourth_level' => [
            'key'                   => 4,
            'text'                  => 'over 800m',
            'start_unit'            => 801,
            'end_unit_third_level'  => 800,
            'end_unit'              => '',
            'price_over_calculate'  => 0.5,
            'price'                 => 130,
        ],

    ],

    'status' => [
        'active' => 1,
        'inactive' => 2,
    ],
    'po_service' => [
        'purchases_order' => [
            'key'           => 'purchases_order',
            'text'          => 'Purchases Order',
        ],
        'lease_order' => [
            'key'           => 'lease_order',
            'text'          => 'Lease Order',
        ],
        'service_order' => [
            'key'           => 'service_order',
            'text'          => 'Service Order',
        ],
    ],
    'tax_status' => [
        'include_tax' => [
            'key'           => 1,
            'text'          => 'Include tax',
        ],
        'not_include_tax' => [
            'key'           => 2,
            'text'          => 'Not include tax',
        ],
    ],
    'user' => [
        'role' => [
            'super_admin' => 'super_admin',
            'admin' => 'admin',
        ]
    ],
    'fttx_status' => [
        'new_install' => [
            'key'           => 1,
            'text'          => 'New Install',
        ],
        'active' => [
            'key'           => 2,
            'text'          => 'Active',
        ],
        'dismantle' => [
            'key'           => 3,
            'text'          => 'Dismantle',
        ],
        'reactive' => [
            'key'           => 4,
            'text'          => 'Reactive',
        ],
        'relocation' => [
            'key'           => 5,
            'text'          => 'Relocation',
        ],
        'replace' => [
            'key'           => 6,
            'text'          => 'Replace installation and maintenance team',
        ],
        'charge_only_new' => [
            'key'           => 7,
            'text'          => 'Charge only new installation',
        ],

    ],
    'applicant_team_install' => [
        'cfocn' => [
            'key'           => 'cfocn',
            'text'          => 'CFOCN',
        ],
        'isp' => [
            'key'           => 'isp',
            'text'          => 'ISP',
        ],
    ],
    'team_install' => [
        'cfocn' => [
            'key'           => 'cfocn',
            'text'          => 'CFOCN',
        ],
        'isp' => [
            'key'           => 'isp',
            'text'          => 'ISP',
        ],
    ],
    'setting_price_type' => [
        'fiber_jumper_fee' => [
            'key'           => 1,
            'text'          => 'Fiber Jumper Fee',
        ],
        'digging_fee' => [
            'key'           => 2,
            'text'          => 'Digging Fee',
        ],
        'rental_pole' => [
            'key'           => 3,
            'text'          => 'Rental pole Fee',
        ],
    ],

    'payment_status' => [
        'paid_over_one_year_old_order' => [
            'key'           => 1,
            'text'          => 'Paid over one year old order (已付满一年 旧工单)',
        ],
        'paid_first_three_months_new_order' => [
            'key'           => 2,
            'text'          => 'Paid first 3 months  new order (已付第一次3个月 新工单)',
        ],
        'paid_second_three_months_new_order' => [
            'key'           => 3,
            'text'          => 'Paid Second 3 months  new order (已付第二次3个月 新工单)',
        ],
        'paid_first_six_months_new_order' => [
            'key'           => 4,
            'text'          => 'Paid first 6 months  new order (已付第一次6个月 新工单)',
        ],
        'paid_second_six_months_new_order' => [
            'key'           => 5,
            'text'          => 'Paid Second 6 months  new order (已付第二次6个月 新工单)',
        ],
        'paid_over_one_year_new_order' => [
            'key'           => 6,
            'text'          => 'Paid over one year new order (已付满一年 新工单)',
        ],
    ],
    'fttx_status_total' => [
        'new_install' => [
            'key'           => 1,
            'text'          => 'Total New Installed Users 新装机用户总数',
        ],
        'active' => [
            'key'           => 2,
            'text'          => 'Total Active Users 活跃用户总数',
        ],
        'dismantle' => [
            'key'           => 3,
            'text'          => 'Total Dismantle Users 拆机用户总数',
        ],
        'reactive' => [
            'key'           => 4,
            'text'          => 'Total Reactive Users 复活重启用户总数',
        ],
        'relocation' => [
            'key'           => 5,
            'text'          => 'Total Relocation Users 搬迁用户总数',
        ],
        'replace' => [
            'key'           => 6,
            'text'          => 'Total users of replacement installation and maintenance team 更换安装维护团队用户总数',
        ],
    ],

    'order_description' => [
        'purchase_order' => '
                            Notes: The length of specific routes above is indicative only; the chargeable FOC length
                            shall be subject to the length in the PAC. All stretch up along government cut roads, we
                            do not include maintenance service.
                            <ol> 
                                <li style="font-weight: bold;"> 
                                Conditions of the Unit Price
                                </li>
                                <p style="margin-left:40px;">(a)  Upon Purchase Order signed by both parties, BEEUNION shall pay 100% of the 
                                purchase price within 15 days after PAC signed by both parties.
                                </p>

                                <p style="margin-left:40px;">
                                (b) CFOCN  offer BEEUNION twelve (12) months starting from the PAC sign date as
                                 the Warranty Period.During the  Warranty Period,no maintenance fee shall be paid
                                  by BEEUNION. Upon the expiration of  Warranty Period,BEEUNION shall pay CFOCN the annual
                                 maintenance fee equivalent to 3% of the purchase price based on PAC length.
                                </p>

                                  <p style="margin-left:40px;">
                                  (c) All payment shall be made by BEEUNION to CFOCN within fifteen (15) calendar
                                   days after receive invoice from CFOCN.
                                 </p>

                                  <p style="margin-left:40px;">
                                  (d) The Purchase Order is accepted and signed in two copies by both parties, one copy for each.
                                 </p>

                                <p style="margin-left:40px;">
                                (e)  The Purchase Order shall be signed & stamped within (30) days from the issue date.
                                 </p>

                                    <p style="margin-left:40px;">
                                    (f) All the pricing in this order excludes the 10% VAT.
                                 </p>
                            </ol>
                            ',

        'service_order' => '
                            <ol> 
                                <li> 
                                 The total payment is USD $80 with VAT excluded, for this service shall be settled in one time within two weeks after the project completed.
                                </li>
                                <li> 
                                 The service order is accepted and signed in two copies by both parties, one copy for each.
                                </li>
                            </ol>
                            ',

        'lease_order' => '
                            Notes: The length of specific routes above is indicative only; the chargeable FOC length
                            shall be subject to the length in the PAC. All stretch up along government cut roads, we
                            do not include maintenance service.
                            <ol> 
                                <li> 
                                 Conditions of the Unit Price
                                    <ol style="list-style-type: lower-alpha;">
                                        <li>Service Price = Unit Price * Length in the PAC. (Minimum 1000m or 1km per duct fiber HOP or Node).</li>
                                        <li>In the event that the Customer does not reject the PAC in writing with sufficient proof within seven (7) working days after receiving the PAC, the PAC shall be deemed accepted and signed by Customer, and this Order will start charging automatically.</li>
                                        <li>Customer shall pay monthly service charge of the total service price hereof within fourteen (14) working days upon the receipt of the invoice issued by Provider.</li>
                                        <li>All the pricing in this order excludes the 10% VAT.</li>
                                    </ol>
                                </li>
                            
                                <li>The Lease Order is accepted and signed in two copies by both parties, one copy for each.</li>
                                <li>The Lease Order shall be signed & stamped within (30) days from the issue date.</li>
                                <li>Upon PAC signed by Customer, Customer shall pay advance 12 months\' service charge of total service price (including onetime charge if any) hereof within fourteen (14) working days upon the receipt of the invoice issued by Provider. </li>
                                <li>Starting from the 13<sup>th</sup>  month, customer shall pay monthly of total service price hereof within fourteen (14) working days upon the receipt of the invoice issued by provider.</li>
                                <li>This Order shall be two (2) year starting from the date of the PAC accepted and shall be automatically renewed for another one (1) year after expiration of such initial service term or renewed term (if any) unless at least three (3) months before expiration of previous term a Party gives a prior written notice to the other Party for non-renewal.</li>
                                <li>If the Lease Order, by reason of any situation whatsoever other than CFOCN\'s fault, is unilaterally terminated by Customer or CFOCN prior to the expiration thereof in the initial two (2) year, Customer shall pay CFOCN for the Service Price of remaining months if the service usage by Customer is less than 24 months\' period and indemnify any incidental and consequential damages incurred to CFOCN as of the actual termination date.</li>
                                <li> 
                                Customer guarantees as followed:
                                   <ol style="list-style-type: lower-alpha;">
                                       <li>Its raw materials and components are not from countries, businesses or individuals that are prohibited from trading or sanctioned by the United States or China.</li>
                                       <li>Customer does not and will not directly or indirectly pay or authorize any payments to government officials as defined in the International Anti-Corruption and Anti-Bribery Principles or intend to provide any benefit to improperly influence or buy government officials. If the Customer is a government official, it does not accept and will not accept such payment in the future.</li>
                                       <li>during the performance of the agreement, both parties guarantees that its shareholders, directors, employees, and individuals who have primary responsibility for the performance of the contract ("relevant personnel") or its Affiliates and their related personnel are not.
                                            involved in the US or China sanctions list.
                                       </li>
                                       <li>Customer may use the Delivered OFC within its normal business operations to supply telecommunications services in accordance with all applicable laws and regulations, PROVIDED ALWAYS THAT such normal business operations do not include the sale, resale, exchange, lease, share, or other types of transfer of Customer\'s rights to the Relevant OFC to any third party and shall in no circumstances constitute de facto competition in business with Provider; and</li>
                                       <li>Customer shall not create, encumber, mortgage, or file any security interests, including but not limited to a pledge, mortgage, or hypothec over its rights to the Delivered OFC in favor of any third party, without the prior written consent of Provider.</li>
                                   </ol>
                               </li>
                            </ol>
                            
                            If above mentioned statements and warranties are breached by the Customer, CFOCN has the right to suspend or terminate the service or this lease order. In addition, if such suspension or termination occurs, whether it is the ongoing activities of the Customer, or the contract with other third parties before the suspension or termination of the lease order, the losses and compensations incurred are borne by the Customer.
                            ',
    ],

    'order_agreement' => [
        'purchase_order' => 'According to discussion,XXX as the Customer, would like to purchase CFOCN fiber in SHV. After this order agreed by both parties, CFOCN, as the Provider,agrees to provide relevant services with the terms and condition as below:',

        'lease_order' => 'According to discussion, XXXXX .as the Customer, would like to lease CFOCN fiber. After this order agreed by both parties, CFOCN, as the Provider, agrees to provide relevant services with the terms and condition as below:',

        // 'service_order'
        'service_order' => 'Base on the operation plan demand,XXX requires to splicing 4c at Splicing 4core to Naga II at St.National Amssembly MH#16+1 (LO509). After this order has been confirmed by both parties, CFOCN agrees to provide the service accordingly with the terms and conditions below:',
    ],

    'subject' => [
        'purchase_order' => 'POXX-CFOCN-XXX-BACKBONE',
        'lease_order' => 'LOXX-CFOCN-XX-METRO',
    ],
    'reference' => 'LOXX-CFOCN-XX-METRO',
];
