<?php

namespace Database\Seeders;

use App\Models\FttxShowHideColumn;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FttxShowHideColumnSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        FttxShowHideColumn::truncate();

        $dataColumn = [
            'customer_id'                                       => 'Isp Name (运营商名称)',
            'work_order_isp'                                    => 'Work Order ISP (ISP 工单号)',
            'work_order_cfocn'                                  => 'Work Order CFOCN (CFOCN工单号)',
            'subscriber_no'                                     => 'Subscriber No (用户编号)',
            'isp_ex_work_order_isp'                             => 'ISP EX Work Order ISP (旧工单号)',
            'status'                                            => 'Status (状态)',
            'name'                                              => 'Name (姓名)',
            'phone'                                             => 'phone (电话)',
            'address'                                           => 'Address (地址)',
            'zone'                                              => 'Zone (区域)',
            'city'                                              => 'City (城市)',
            'port'                                              => 'PORT (端口)',
            'pos_speed_id'                                      => 'Pos Speed (分光比)',
            'applicant_team_install'                            => 'Applicant Team Install (申请安装团队)',
            'team_install'                                      => 'Team Install (安装团队)',
            'create_time'                                       => 'Create Time (初始安装日期)',
            'completed_time'                                    => 'Completed Time (安装完工日期)',
            'date_ex_complete_old_order'                        => 'Date EX Complete Old Order (历史工单完工日)',
            'dismantle_date'                                    => 'Dismantle Date (拆机日期)',
            'dismantle_order_cfocn'                             => 'Dismantle Order CFOCN (拆机工单)',
            'lay_fiber'                                         => 'LayFiber (放缆)',
            'remark_first'                                      => 'Remark (备注)',
            'reactive_date'                                     => 'Reactive date (反应日期)',
            'change_splitter_date'                              => 'Change splitter date (变更日期分割器)',
            'relocation_date'                                   => 'Relocation Date (搬迁日期)',
            'start_payment_date'                                => 'Start Payment Date (开始计费日期)',
            'last_payment_date'                                 => 'Last Payment Date (上一次付款日期)',
            'initial_installation_order_complete_time'          => 'Initial Installation Order Complete Time (初始安装订单完成时间)',
            'first_relocation_order_complete_date'              => 'First Relocation Order Complete Date (第一移机工单完成日期两次后)',
            'payment_date'                                      => 'Payment Date (付款日期)',
            'payment_status'                                    => 'Payment status (付款状态)',
            'online_days'                                       => 'Online Days (用户在线时间)',
            'deadline'                                          => 'Deadline (截止日期)',
            'day_remaining'                                     => 'Day Remaining (剩余天数)',
            'customer_type'                                     => 'Customer Type (客户类型)',
            'new_installation_fee'                              => 'New Installation Fee (安装费)',
            'fiber_jumper_fee'                                  => 'Fiber Jumper Fee (跳纤)',
            'digging_fee'                                       => 'Digging Fee (开挖费)',
            'first_payment_period'                              => 'First Payment Period (第一个付款期)',
            'initial_payment_period'                            => 'Initial Payment Period (首次付款期限)',
            'rental_price'                                      => 'Rental Price (地址)',
            'ppcc'                                              => 'PPCC (万古湖)',
            'number_of_pole'                                    => 'Number of Pole (极数)',
            'rental_pole'                                       => 'Rental pole (电杆)',
            'other_fee'                                         => 'Other fee (其它收费)',
            'discount'                                          => 'Discount (优惠折扣)',
            'total'                                             => 'Total (合计)',
            'remark_second'                                     => 'Remark (备注)',
        ];

        foreach ($dataColumn as $key => $value) {
            FttxShowHideColumn::create([
                'name' => $value,
                'column' => $key,
                'status' => 1
            ]);
        }
    }
}
