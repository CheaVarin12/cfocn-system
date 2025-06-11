@php
    use Carbon\Carbon;
@endphp
<table>
    <thead>
        <tr></tr>
        <tr></tr>
        <tr>
            <th style="vertical-align:middle; text-align:center; background:#ACB9CA;" rowspan="2">No</th>
            <th style="vertical-align:middle; text-align:center; background:#ACB9CA;" rowspan="2">Isp Name</th>
            <th style="vertical-align:middle; text-align:center; background:#ACB9CA;" rowspan="2">Work Order ISP</th>
            <th style="vertical-align:middle; text-align:center; background:#ACB9CA;" rowspan="2">Work Order CFOCN</th>
            <th style="vertical-align:middle; text-align:center; background:#ACB9CA;" rowspan="2">Subscriber No</th>
            <th style="vertical-align:middle; text-align:center; background:#ACB9CA;" rowspan="2">ISP EX Work Order
                ISP</th>
            <th style="vertical-align:middle; text-align:center; background:#ACB9CA;" rowspan="2">Status</th>
            <th style="vertical-align:middle; text-align:center; background:#ACB9CA;" rowspan="2">Name</th>
            <th style="vertical-align:middle; text-align:center; background:#ACB9CA;" rowspan="2">Phone</th>
            <th style="vertical-align:middle; text-align:center; background:#ACB9CA;" rowspan="2">Address</th>
            <th style="vertical-align:middle; text-align:center; background:#ACB9CA;" rowspan="2">Zone</th>
            <th style="vertical-align:middle; text-align:center; background:#ACB9CA;" rowspan="2">City</th>
            <th style="vertical-align:middle; text-align:center; background:#ACB9CA;" rowspan="2">PORT</th>
            <th style="vertical-align:middle; text-align:center; background:#ACB9CA;" rowspan="2">Pos Speed</th>
            <th style="vertical-align:middle; text-align:center; background:#ACB9CA;" rowspan="2">Applicant Team
                Install</th>
            <th style="vertical-align:middle; text-align:center; background:#ACB9CA;" rowspan="2">Team Install</th>
            <th style="vertical-align:middle; text-align:center; background:#ACB9CA;" rowspan="2">Create Time</th>
            <th style="vertical-align:middle; text-align:center; background:#ACB9CA;" rowspan="2">Completed Time</th>
            <th style="vertical-align:middle; text-align:center; background:#ACB9CA;" rowspan="2">Date EX Complete
                Old Order</th>
            <th style="vertical-align:middle; text-align:center; background:#ACB9CA;" rowspan="2"> Dismantle Date
            </th>
            <th style="vertical-align:middle; text-align:center; background:#ACB9CA;" rowspan="2">Dismantle Order
                CFOCN</th>
            <th style="vertical-align:middle; text-align:center; background:#ACB9CA;" rowspan="2">LayFiber</th>
            <th style="vertical-align:middle; text-align:center; background:#ACB9CA;" rowspan="2">Remark</th>
            <th style="vertical-align:middle; text-align:center; background:#C6E0B4;" rowspan="2">Start Payment Date
            </th>
            <th style="vertical-align:middle; text-align:center; background:#C6E0B4;" rowspan="2">Last Payment Date
            </th>
            <th style="vertical-align:middle; text-align:center; background:#C6E0B4;" rowspan="2">InitialInstallation
                Order Complete Time</th>
            <th style="vertical-align:middle; text-align:center; background:#C6E0B4;" rowspan="2">First
                RelocationOrder Complete Date</th>
            <th style="vertical-align:middle; text-align:center; background:#C6E0B4;" rowspan="2">Payment Date</th>
            <th style="vertical-align:middle; text-align:center; background:#C6E0B4;" rowspan="2">Payment status</th>
            <th style="vertical-align:middle; text-align:center; background:#C6E0B4;" colspan="2" rowspan="2">
                Online Days <br> 用户在线时间</th>
            <th style="vertical-align:middle; text-align:center; background:#C6E0B4;" rowspan="2">Deadline</th>
            <th style="vertical-align:middle; text-align:center; background:#C6E0B4;" rowspan="2">Day Remaining</th>
            <th style="vertical-align:middle; text-align:center; background:#C6E0B4;" rowspan="2">Customer Type</th>
            <th style="vertical-align:middle; text-align:center; background:#C6E0B4;" rowspan="2">New Installation
                Fee</th>
            <th style="vertical-align:middle; text-align:center; background:#C6E0B4;" rowspan="2">Fiber Jumper Fee
            </th>
            <th style="vertical-align:middle; text-align:center; background:#C6E0B4;" rowspan="2">Digging Fee</th>
            <th style="vertical-align:middle; text-align:center; background:#C6E0B4;" rowspan="2">Initial
                PaymentPeriod</th>
            <th style="vertical-align:middle; text-align:center; background:#C6E0B4;" rowspan="2">Rental Price</th>
            <th style="vertical-align:middle; text-align:center; background:#C6E0B4;" rowspan="2">PPCC</th>
            <th style="vertical-align:middle; text-align:center; background:#C6E0B4;" rowspan="2">Number Of Pole</th>
            <th style="vertical-align:middle; text-align:center; background:#C6E0B4;" rowspan="2">Rental pole</th>
            <th style="vertical-align:middle; text-align:center; background:#C6E0B4;" rowspan="2">Other fee</th>
            <th style="vertical-align:middle; text-align:center; background:#C6E0B4;" rowspan="2">Discount</th>
            <th style="vertical-align:middle; text-align:center; background:#C6E0B4;" rowspan="2">Total</th>
            <th style="vertical-align:middle; text-align:center; background:#C6E0B4;" rowspan="2">Remark</th>
            {{-- detail --}}
            <th></th>
            @for ($i = 1; $i <= $totalMonth; $i++)
                <th colspan="12" style="vertical-align:middle; text-align:center;">
                    {{ Carbon::parse(addMonth($firstDate, $i))->format('M-Y') }}</th>
                <th rowspan="2" style="vertical-align:middle; text-align:center; background:#C6E0B4;">
                    {{ Carbon::parse(addMonth($firstDate, $i))->format('M-Y') }}</th>
            @endfor

            {{-- total --}}
            <th></th>
            <th colspan="9" style="vertical-align:middle; text-align:center; background:#FFC000;">Total</th>
        </tr>
        <tr>
            <th></th>
            @for ($i = 1; $i <= $totalMonth; $i++)
                <th style="vertical-align:middle; text-align:center;">Expiry Date</th>
                <th style="vertical-align:middle; text-align:center;">New installationi fee</th>
                <th style="vertical-align:middle; text-align:center;">Fiber jumper fee</th>
                <th style="vertical-align:middle; text-align:center;">Digging fee</th>
                <th style="vertical-align:middle; text-align:center;">Rental unit price</th>
                <th style="vertical-align:middle; text-align:center;">PPCC</th>
                <th style="vertical-align:middle; text-align:center;">Rental fee</th>
                <th style="vertical-align:middle; text-align:center;">Other fee</th>
                <th style="vertical-align:middle; text-align:center;">Discount</th>
                <th style="vertical-align:middle; text-align:center;">Remark</th>
                <th style="vertical-align:middle; text-align:center;">Invoice Number</th>
                <th style="vertical-align:middle; text-align:center;">Receipt Number</th>
            @endfor

            <th></th>
            <th style="vertical-align:middle; text-align:center; background:#FFC000;">New installationi fee</th>
            <th style="vertical-align:middle; text-align:center; background:#FFC000;">Fiber jumper fee</th>
            <th style="vertical-align:middle; text-align:center; background:#FFC000;">Digging fee</th>
            <th style="vertical-align:middle; text-align:center; background:#FFC000;">Rental unit price</th>
            <th style="vertical-align:middle; text-align:center; background:#FFC000;">PPCC</th>
            <th style="vertical-align:middle; text-align:center; background:#FFC000;">Rental fee</th>
            <th style="vertical-align:middle; text-align:center; background:#FFC000;">Other fee</th>
            <th style="vertical-align:middle; text-align:center; background:#FFC000;">Discount</th>
            <th style="vertical-align:middle; text-align:center; background:#FFC000;">Total</th>
        </tr>
        <tr>
            <th style="vertical-align:middle; text-align:center; background:#ACB9CA;">序号</th>
            <th style="vertical-align:middle; text-align:center; background:#ACB9CA;">运营商名称</th>
            <th style="vertical-align:middle; text-align:center; background:#ACB9CA;">ISP 工单号</th>
            <th style="vertical-align:middle; text-align:center; background:#ACB9CA;">CFOCN工单号</th>
            <th style="vertical-align:middle; text-align:center; background:#ACB9CA;">用户编号</th>
            <th style="vertical-align:middle; text-align:center; background:#ACB9CA;">旧工单号</th>
            <th style="vertical-align:middle; text-align:center; background:#ACB9CA;">状态</th>
            <th style="vertical-align:middle; text-align:center; background:#ACB9CA;">姓名</th>
            <th style="vertical-align:middle; text-align:center; background:#ACB9CA;">电话</th>
            <th style="vertical-align:middle; text-align:center; background:#ACB9CA;">地址</th>
            <th style="vertical-align:middle; text-align:center; background:#ACB9CA;">区域</th>
            <th style="vertical-align:middle; text-align:center; background:#ACB9CA;">城市</th>
            <th style="vertical-align:middle; text-align:center; background:#ACB9CA;">端口</th>
            <th style="vertical-align:middle; text-align:center; background:#ACB9CA;">分光比</th>
            <th style="vertical-align:middle; text-align:center; background:#ACB9CA;">申请安装团队</th>
            <th style="vertical-align:middle; text-align:center; background:#ACB9CA;">安装团队</th>
            <th style="vertical-align:middle; text-align:center; background:#ACB9CA;">初始安装日期</th>
            <th style="vertical-align:middle; text-align:center; background:#ACB9CA;">安装完工日期</th>
            <th style="vertical-align:middle; text-align:center; background:#ACB9CA;">历史工单完工日</th>
            <th style="vertical-align:middle; text-align:center; background:#ACB9CA;">拆机日期</th>
            <th style="vertical-align:middle; text-align:center; background:#ACB9CA;">拆机工单</th>
            <th style="vertical-align:middle; text-align:center; background:#ACB9CA;">放缆</th>
            <th style="vertical-align:middle; text-align:center; background:#ACB9CA;">备注</th>

            <th style="vertical-align:middle; text-align:center; background:#C6E0B4;">开始计费日期</th>
            <th style="vertical-align:middle; text-align:center; background:#C6E0B4;">上一次付款日期</th>
            <th style="vertical-align:middle; text-align:center; background:#C6E0B4;">初始安装订单完成时间</th>
            <th style="vertical-align:middle; text-align:center; background:#C6E0B4;">第一移机工单完成日期两次后</th>
            <th style="vertical-align:middle; text-align:center; background:#C6E0B4;">付款日期</th>
            <th style="vertical-align:middle; text-align:center; background:#C6E0B4;">付款状态</th>
            <th style="vertical-align:middle; text-align:center; background:#C6E0B4;">Month</th>
            <th style="vertical-align:middle; text-align:center; background:#C6E0B4;">Day</th>
            <th style="vertical-align:middle; text-align:center; background:#C6E0B4;">截止日期</th>
            <th style="vertical-align:middle; text-align:center; background:#C6E0B4;">剩余天数</th>
            <th style="vertical-align:middle; text-align:center; background:#C6E0B4;">客户类型</th>
            <th style="vertical-align:middle; text-align:center; background:#C6E0B4;">安装费</th>
            <th style="vertical-align:middle; text-align:center; background:#C6E0B4;">跳纤</th>
            <th style="vertical-align:middle; text-align:center; background:#C6E0B4;">开挖费</th>
            <th style="vertical-align:middle; text-align:center; background:#C6E0B4;">首次付款期限</th>
            <th style="vertical-align:middle; text-align:center; background:#C6E0B4;">租金单价</th>
            <th style="vertical-align:middle; text-align:center; background:#C6E0B4;">万古湖</th>
            <th style="vertical-align:middle; text-align:center; background:#C6E0B4;">极数</th>
            <th style="vertical-align:middle; text-align:center; background:#C6E0B4;">电杆</th>
            <th style="vertical-align:middle; text-align:center; background:#C6E0B4;">其它收费</th>
            <th style="vertical-align:middle; text-align:center; background:#C6E0B4;">优惠折扣</th>
            <th style="vertical-align:middle; text-align:center; background:#C6E0B4;">合计</th>
            <th style="vertical-align:middle; text-align:center; background:#C6E0B4;">备注</th>

            <th></th>
            @for ($i = 1; $i <= $totalMonth; $i++)
                <th style="vertical-align:middle; text-align:center;">到期日期</th>
                <th style="vertical-align:middle; text-align:center;">安装费</th>
                <th style="vertical-align:middle; text-align:center;">跳纤</th>
                <th style="vertical-align:middle; text-align:center;">开挖费</th>
                <th style="vertical-align:middle; text-align:center;">租金单价</th>
                <th style="vertical-align:middle; text-align:center;">万古湖</th>
                <th style="vertical-align:middle; text-align:center;">电杆</th>
                <th style="vertical-align:middle; text-align:center;">其它收费</th>
                <th style="vertical-align:middle; text-align:center;">优惠折扣</th>
                <th style="vertical-align:middle; text-align:center;">备注</th>
                <th style="vertical-align:middle; text-align:center;">发票号码</th>
                <th style="vertical-align:middle; text-align:center;">收据号码</th>
                <th style="vertical-align:middle; text-align:center; background:#C6E0B4;">Total for this month</th>
            @endfor

            <th></th>
            <th style="vertical-align:middle; text-align:center; background:#FFC000;">安装费</th>
            <th style="vertical-align:middle; text-align:center; background:#FFC000;">跳纤费</th>
            <th style="vertical-align:middle; text-align:center; background:#FFC000;">开挖费</th>
            <th style="vertical-align:middle; text-align:center; background:#FFC000;">租金单价</th>
            <th style="vertical-align:middle; text-align:center; background:#FFC000;">万古湖</th>
            <th style="vertical-align:middle; text-align:center; background:#FFC000;">电杆</th>
            <th style="vertical-align:middle; text-align:center; background:#FFC000;">其它收费</th>
            <th style="vertical-align:middle; text-align:center; background:#FFC000;">优惠折扣</th>
            <th style="vertical-align:middle; text-align:center; background:#FFC000;">合计</th>
        </tr>

    </thead>
    <tbody style="margin-bottom: 12px;">
        @foreach ($data as $key => $item)
            <tr>
                <td >{{ $key + 1 }}</td>
                <td>{{ $item?->customer?->name_en ?? $item?->customer?->name_en }}</td>
                <td>{{ $item->work_order_isp ?? '' }}</td>
                <td>{{ $item->work_order_cfocn ?? '' }}</td>
                <td>{{ $item->subscriber_no ?? '' }}</td>
                <td>{{ $item->isp_ex_work_order_isp ?? '' }}</td>
                <td>
                    @foreach (config('dummy.fttx_status') as $status)
                        @if ($status['key'] == $item->status)
                            {!! $status['text'] ? $status['text'] : '--' !!}
                        @endif
                        @endforeach
                </td>
                <td>{{ $item->name ?? '' }}</td>
                <td>{{ $item->phone ?? '' }}</td>
                <td>{{ $item->address ?? '' }}</td>
                <td>{{ $item->zone ?? '' }}</td>
                <td>{{ $item->city ?? '' }}</td>
                <td>{{ $item->port ?? '' }}</td>
                <td>{{ $item?->posSpeed?->split_pos ?? '' }}</td>
                <td>{{ strtoupper($item->applicant_team_install) ?? '' }}</td>
                <td>{{ strtoupper($item->team_install) ?? '' }}</td>
                <td>{{ $item->create_time ? date('d-M-y', strtotime($item->create_time)) : '' }}</td>
                <td>{{ $item->completed_time ? date('d-M-y', strtotime($item->completed_time)) : '' }}</td>
                <td>{{ $item->date_ex_complete_old_order ? date('d-M-y', strtotime($item->date_ex_complete_old_order)) : '' }}
                </td>
                <td>{{ $item->dismantle_date ? date('d-M-y', strtotime($item->dismantle_date)) : '' }}</td>
                <td>{{ $item->dismantle_order_cfocn ?? '' }}</td>
                <td>{{ $item->lay_fiber ?? '' }}</td>
                <td>{{ $item->remark_first ?? '' }}</td>

                <td> {{ $item->start_payment_date ? date('d-M-y', strtotime($item->start_payment_date)) : '' }}</td>
                <td>{{ $item->last_payment_date ? date('d-M-y', strtotime($item->last_payment_date)) : '' }}</td>
                <td>{{ $item->initial_installation_order_complete_time ? date('d-M-y', strtotime($item->initial_installation_order_complete_time)) : '' }}
                </td>
                <td>{{ $item->first_relocation_order_complete_date ? date('d-M-y', strtotime($item->first_relocation_order_complete_date)) : '' }}
                </td>
                <td>{{ $item->payment_date ? date('d-M-y', strtotime($item->payment_date)) : '' }}</td>
                <td>{{ $item->payment_status ?? '' }}</td>
                <td>{{ getNumberOfMonth($item->start_payment_date, $item->deadline) ?? '' }}
                    Month</td>
                <td> {{ getDaysBetweenDates($item->start_payment_date, $item->deadline) ?? '' }}
                    Day</td>
                <td> {{ $item->deadline ? date('d-M-y', strtotime($item->deadline)) : '' }}</td>
                <td> {{ calculateDaysBetween($item->deadline) ?? '' }} Day</td>
                <td> {{ $item?->customer_type ?? '' }}</td>
                <td> {{ $item->new_installation_fee ?? '' }}</td>
                <td> {{ $item->fiber_jumper_fee ?? '' }}</td>
                <td> {{ $item->digging_fee ?? '' }}</td>
                <td> {{ $item->initial_payment_period ?? '' }}</td>
                <td> {{ $item->rental_price ?? '' }}</td>
                <td> {{ $item->ppcc ?? '' }}</td>
                <td> {{ $item->number_of_pole ?? '' }}</td>
                <td> {{ $item->rental_pole ?? '' }}</td>
                <td> {{ $item->other_fee ?? '' }}</td>
                <td> {{ $item->discount ?? '' }}</td>
                <td> {{ $item->total_calculate ?? '' }}</td>
                <td> {{ $item->remark_second ?? '' }}</td>

                <td></td>

                @foreach ($item->fttxDetail as $value)
                    <td>{{ $value->expiry_date ? date('d-M-y', strtotime($value->expiry_date)) : '' }}</td>
                    <td>{{ $value->new_installation_fee ?? '' }}</td>
                    <td>{{ $value->fiber_jumper_fee ?? '' }}</td>
                    <td>{{ $value->digging_fee ?? '' }}</td>
                    <td>{{ $value->rental_unit_price ?? '' }}</td>
                    <td>{{ $value->ppcc ?? '' }}</td>
                    <td>{{ $value->pole_rental_fee ?? '' }}</td>
                    <td>{{ $value->other_fee ?? '' }}</td>
                    <td>{{ $value->discount ?? '' }}</td>
                    <td>{{ $value->remark ?? '' }}</td>
                    <td>{{ $value->invoice_number ?? '' }}</td>
                    <td>{{ $value->receipt_number ?? '' }}</td>
                    <td style="vertical-align:middle; text-align:center; background:#C6E0B4;">
                        {{ $value->total_amount ?? '' }}</td>
                @endforeach

                @for ($i = 1; $i <= $totalMonth - count($item->fttxDetail); $i++)
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td style="vertical-align:middle; text-align:center; background:#C6E0B4;"></td>
                @endfor

                <td></td>

                <td style="vertical-align:middle; text-align:center; background:#FFC000;">
                    {{ $item->total_new_installation_fee ?? '' }}</td>
                <td style="vertical-align:middle; text-align:center; background:#FFC000;">
                    {{ $item->total_fiber_jumper_fee ?? '' }}</td>
                <td style="vertical-align:middle; text-align:center; background:#FFC000;">
                    {{ $item->total_digging_fee ?? '' }}</td>
                <td style="vertical-align:middle; text-align:center; background:#FFC000;">
                    {{ $item->total_rental_unit_price ?? '' }}</td>
                <td style="vertical-align:middle; text-align:center; background:#FFC000;">
                    {{ $item->total_ppcc ?? '' }}</td>
                <td style="vertical-align:middle; text-align:center; background:#FFC000;">
                    {{ $item->total_pole_rental_fee ?? '' }}</td>
                <td style="vertical-align:middle; text-align:center; background:#FFC000;">
                    {{ $item->total_other_fee ?? '' }}</td>
                <td style="vertical-align:middle; text-align:center; background:#FFC000;">
                    {{ $item->total_discount ?? '' }}</td>
                <td style="vertical-align:middle; text-align:center; background:#FFC000;">
                    {{ $item->total_total_amount ?? '' }}</td>
            </tr>
        @endforeach
    </tbody>

</table>
