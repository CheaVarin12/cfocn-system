@if (count($data) > 0)
    @if ($totalWorkOrderExpire > 0 && request('expire'))
        <div class="content-form-filter" style="margin-top: 17px;">
            <div class="form-filter">
                @can('fttx-renewal')
                    <div style="display: flex; gap: 5px; flex-wrap: wrap;float: right;">
                        <button mat-flat-button="" type="submit" class="btn-create bg-primary minWithAuto"
                            style="height: 35px;font-size:14px;" @click="renewalAll()">
                            Renewal 1 Month (Total {{ $totalWorkOrderExpire }})
                        </button>
                    </div>
                @endcan
            </div>
        </div>
    @endif
    @if (!request('expire'))
        <div class="content-form-filter" style="margin-top: 17px;">
            <div class="form-filter">
                @can('fttx-update')
                    <div style="display: flex; gap: 5px; flex-wrap: wrap;float: right;">
                        <button mat-flat-button type="submit" class="btn-create bg-primary minWithAuto"
                            style="height: 35px; font-size:14px;" @click="updateStatus(1,'New Install')">
                            Update New Install to Active ({{ $totalWorkOrderNewInstall }})
                        </button>
                        <button mat-flat-button type="submit" class="btn-create bg-primary minWithAuto"
                            style="height: 35px; font-size:14px;" @click="updateStatus(4,'Reactive')">
                            Update Reactive to Active ({{ $totalWorkOrderReactive }})
                        </button>
                        <button mat-flat-button type="submit" class="btn-create bg-primary minWithAuto"
                            style="height: 35px; font-size:14px;" @click="updateStatus(5,'Relocation')">
                            Update Relocation to Active ({{ $totalWorkOrderRelocation }})
                        </button>
                    </div>
                @endcan
            </div>
        </div>
    @endif
@endif
<div style="margin-top: 71px;"
    class="tableLayoutCon tableLayoutWithFooter {{ count($data) <= 0 ? 'heightAuthReport' : '' }}">
    <div class="tableLy">
        <div class="out_table_css">
            <div class="excel-content">
                <div class="excel-wrapper">
                    @if (count($data) > 0)
                        <div class="reportTitle">
                        </div>
                        <div class="excel-header">
                            <div class="excel-header-left">
                                <div class="excel-header-left-wrapper">
                                    <p class="fontWeight"></p>
                                </div>
                            </div>
                            <div class="excel-header-right">

                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="tableCustomScroll">
            <div class="table excel">
                @if (count($data) > 0)
                    <div class="excel-body">
                        <table class="tableWidth">
                            <thead class="column">
                                <tr>
                                    <th class="first-part">No</th>
                                    @if ($columnFttx[0]->status == 1)
                                        <th class="first-part">Isp Name</th>
                                    @endif
                                    @if ($columnFttx[1]->status == 1)
                                        <th class="first-part">Work Order ISP</th>
                                    @endif
                                    @if ($columnFttx[2]->status == 1)
                                        <th class="first-part">Work Order CFOCN</th>
                                    @endif
                                    @if ($columnFttx[3]->status == 1)
                                        <th class="first-part">Subscriber No</th>
                                    @endif
                                    @if ($columnFttx[4]->status == 1)
                                        <th class="first-part">ISP EX Work Order ISP</th>
                                    @endif
                                    @if ($columnFttx[5]->status == 1)
                                        <th class="first-part">Status</th>
                                    @endif
                                    @if ($columnFttx[6]->status == 1)
                                        <th class="first-part">Name</th>
                                    @endif
                                    @if ($columnFttx[7]->status == 1)
                                        <th class="first-part">Phone</th>
                                    @endif
                                    @if ($columnFttx[8]->status == 1)
                                        <th class="first-part">Address</th>
                                    @endif
                                    @if ($columnFttx[9]->status == 1)
                                        <th class="first-part">Zone</th>
                                    @endif
                                    @if ($columnFttx[10]->status == 1)
                                        <th class="first-part">City</th>
                                    @endif
                                    @if ($columnFttx[11]->status == 1)
                                        <th class="first-part">PORT</th>
                                    @endif
                                    @if ($columnFttx[12]->status == 1)
                                        <th class="first-part">Pos Speed</th>
                                    @endif
                                    @if ($columnFttx[13]->status == 1)
                                        <th class="first-part">Applicant Team Install</th>
                                    @endif
                                    @if ($columnFttx[14]->status == 1)
                                        <th class="first-part">Team Install</th>
                                    @endif
                                    @if ($columnFttx[15]->status == 1)
                                        <th class="first-part">Create Time</th>
                                    @endif
                                    @if ($columnFttx[16]->status == 1)
                                        <th class="first-part">Completed Time</th>
                                    @endif
                                    @if ($columnFttx[17]->status == 1)
                                        <th class="first-part">Date EX Complete Old Order</th>
                                    @endif
                                    @if ($columnFttx[18]->status == 1)
                                        <th class="first-part">Dismantle Date</th>
                                    @endif
                                    @if ($columnFttx[19]->status == 1)
                                        <th class="first-part">Dismantle Order CFOCN</th>
                                    @endif
                                    @if ($columnFttx[20]->status == 1)
                                        <th class="first-part">LayFiber</th>
                                    @endif
                                    @if ($columnFttx[21]->status == 1)
                                        <th class="first-part">Remark</th>
                                    @endif
                                    @if ($columnFttx[22]->status == 1)
                                        <th class="first-part">Reactive date </th>
                                    @endif
                                    <th class="first-part">Reactive payment period</th>
                                    @if ($columnFttx[23]->status == 1)
                                        <th class="first-part">Change splitter date </th>
                                    @endif
                                    @if ($columnFttx[24]->status == 1)
                                        <th class="first-part">Relocation Date </th>
                                    @endif
                                    <th></th>
                                    @if ($columnFttx[25]->status == 1)
                                        <th class="second-part">Start Payment Date</th>
                                    @endif
                                    @if ($columnFttx[26]->status == 1)
                                        <th class="second-part">Last Payment Date</th>
                                    @endif
                                    @if ($columnFttx[27]->status == 1)
                                        <th class="second-part">Initial Installation Order Complete Time</th>
                                    @endif
                                    @if ($columnFttx[28]->status == 1)
                                        <th class="second-part">First Relocation Order Complete Date</th>
                                    @endif
                                    @if ($columnFttx[29]->status == 1)
                                        <th class="second-part">Payment Date</th>
                                    @endif
                                    @if ($columnFttx[30]->status == 1)
                                        <th class="second-part">Payment status</th>
                                    @endif
                                    @if ($columnFttx[31]->status == 1)
                                        <th class="second-part" colspan="2">Online Days <br> 用户在线时间</th>
                                    @endif
                                    @if ($columnFttx[32]->status == 1)
                                        <th class="second-part">Deadline</th>
                                    @endif
                                    @if ($columnFttx[33]->status == 1)
                                        <th class="second-part">Day Remaining</th>
                                    @endif
                                    @if ($columnFttx[34]->status == 1)
                                        <th class="second-part">Customer Type</th>
                                    @endif
                                    @if ($columnFttx[35]->status == 1)
                                        <th class="second-part">New Installation Fee</th>
                                    @endif
                                    @if ($columnFttx[36]->status == 1)
                                        <th class="second-part">Fiber Jumper Fee</th>
                                    @endif
                                    @if ($columnFttx[37]->status == 1)
                                        <th class="second-part">Digging Fee</th>
                                    @endif
                                    @if ($columnFttx[38]->status == 1)
                                        <th class="second-part">First Payment Period</th>
                                    @endif
                                    @if ($columnFttx[39]->status == 1)
                                        <th class="second-part">Initial Payment Period</th>
                                    @endif
                                    @if ($columnFttx[40]->status == 1)
                                        <th class="second-part">Rental Price</th>
                                    @endif
                                    @if ($columnFttx[41]->status == 1)
                                        <th class="second-part">PPCC</th>
                                    @endif
                                    @if ($columnFttx[42]->status == 1)
                                        <th class="second-part">Number of Pole</th>
                                    @endif
                                    @if ($columnFttx[43]->status == 1)
                                        <th class="second-part">Rental pole</th>
                                    @endif
                                    @if ($columnFttx[44]->status == 1)
                                        <th class="second-part">Other fee</th>
                                    @endif
                                    @if ($columnFttx[45]->status == 1)
                                        <th class="second-part">Discount</th>
                                    @endif
                                    @if ($columnFttx[46]->status == 1)
                                        <th class="second-part">Total</th>
                                    @endif
                                    @if ($columnFttx[47]->status == 1)
                                        <th class="second-part">Remark</th>
                                    @endif
                                    <th class="second-part"></th>
                                </tr>
                                <tr>
                                    <th class="first-part">序号</th>
                                    @if ($columnFttx[0]->status == 1)
                                        <th class="first-part">运营商名称</th>
                                    @endif
                                    @if ($columnFttx[1]->status == 1)
                                        <th class="first-part">ISP 工单号</th>
                                    @endif
                                    @if ($columnFttx[2]->status == 1)
                                        <th class="first-part">CFOCN工单号</th>
                                    @endif
                                    @if ($columnFttx[3]->status == 1)
                                        <th class="first-part">用户编号</th>
                                    @endif
                                    @if ($columnFttx[4]->status == 1)
                                        <th class="first-part">旧工单号</th>
                                    @endif
                                    @if ($columnFttx[5]->status == 1)
                                        <th class="first-part">状态</th>
                                    @endif
                                    @if ($columnFttx[6]->status == 1)
                                        <th class="first-part">姓名</th>
                                    @endif
                                    @if ($columnFttx[7]->status == 1)
                                        <th class="first-part">电话</th>
                                    @endif
                                    @if ($columnFttx[8]->status == 1)
                                        <th class="first-part">地址</th>
                                    @endif
                                    @if ($columnFttx[9]->status == 1)
                                        <th class="first-part">区域</th>
                                    @endif
                                    @if ($columnFttx[10]->status == 1)
                                        <th class="first-part">城市</th>
                                    @endif
                                    @if ($columnFttx[11]->status == 1)
                                        <th class="first-part">端口</th>
                                    @endif
                                    @if ($columnFttx[12]->status == 1)
                                        <th class="first-part">分光比</th>
                                    @endif
                                    @if ($columnFttx[13]->status == 1)
                                        <th class="first-part">申请安装团队</th>
                                    @endif
                                    @if ($columnFttx[14]->status == 1)
                                        <th class="first-part">安装团队</th>
                                    @endif
                                    @if ($columnFttx[15]->status == 1)
                                        <th class="first-part">初始安装日期</th>
                                    @endif
                                    @if ($columnFttx[16]->status == 1)
                                        <th class="first-part">安装完工日期</th>
                                    @endif
                                    @if ($columnFttx[17]->status == 1)
                                        <th class="first-part">历史工单完工日</th>
                                    @endif
                                    @if ($columnFttx[18]->status == 1)
                                        <th class="first-part">拆机日期</th>
                                    @endif
                                    @if ($columnFttx[19]->status == 1)
                                        <th class="first-part">拆机工单</th>
                                    @endif
                                    @if ($columnFttx[20]->status == 1)
                                        <th class="first-part">放缆</th>
                                    @endif
                                    @if ($columnFttx[21]->status == 1)
                                        <th class="first-part">备注</th>
                                    @endif
                                    @if ($columnFttx[22]->status == 1)
                                        <th class="first-part">反应日期</th>
                                    @endif
                                    <th class="first-part">Reactive Payment Period</th>
                                    @if ($columnFttx[23]->status == 1)
                                        <th class="first-part">变更日期分割器</th>
                                    @endif
                                    @if ($columnFttx[24]->status == 1)
                                        <th class="first-part">搬迁日期</th>
                                    @endif
                                    <th></th>
                                    @if ($columnFttx[25]->status == 1)
                                        <th class="second-part">开始计费日期</th>
                                    @endif
                                    @if ($columnFttx[26]->status == 1)
                                        <th class="second-part">上一次付款日期</th>
                                    @endif
                                    @if ($columnFttx[27]->status == 1)
                                        <th class="second-part">初始安装订单完成时间</th>
                                    @endif
                                    @if ($columnFttx[28]->status == 1)
                                        <th class="second-part">第一移机工单完成日期两次后</th>
                                    @endif
                                    @if ($columnFttx[29]->status == 1)
                                        <th class="second-part">付款日期</th>
                                    @endif
                                    @if ($columnFttx[30]->status == 1)
                                        <th class="second-part">付款状态</th>
                                    @endif
                                    @if ($columnFttx[31]->status == 1)
                                        <th class="second-part">Month</th>
                                        <th class="second-part">Day</th>
                                    @endif
                                    @if ($columnFttx[32]->status == 1)
                                        <th class="second-part">截止日期</th>
                                    @endif
                                    @if ($columnFttx[33]->status == 1)
                                        <th class="second-part">剩余天数</th>
                                    @endif
                                    @if ($columnFttx[34]->status == 1)
                                        <th class="second-part">客户类型</th>
                                    @endif
                                    @if ($columnFttx[35]->status == 1)
                                        <th class="second-part">安装费</th>
                                    @endif
                                    @if ($columnFttx[36]->status == 1)
                                        <th class="second-part">跳纤</th>
                                    @endif
                                    @if ($columnFttx[37]->status == 1)
                                        <th class="second-part">开挖费</th>
                                    @endif
                                    @if ($columnFttx[38]->status == 1)
                                        <th class="second-part">第一个付款期</th>
                                    @endif
                                    @if ($columnFttx[39]->status == 1)
                                        <th class="second-part">首次付款期限</th>
                                    @endif
                                    @if ($columnFttx[40]->status == 1)
                                        <th class="second-part">租金单价</th>
                                    @endif
                                    @if ($columnFttx[41]->status == 1)
                                        <th class="second-part">万古湖</th>
                                    @endif
                                    @if ($columnFttx[42]->status == 1)
                                        <th class="second-part">极数</th>
                                    @endif
                                    @if ($columnFttx[43]->status == 1)
                                        <th class="second-part">电杆</th>
                                    @endif
                                    @if ($columnFttx[44]->status == 1)
                                        <th class="second-part">其它收费</th>
                                    @endif
                                    @if ($columnFttx[45]->status == 1)
                                        <th class="second-part">优惠折扣</th>
                                    @endif
                                    @if ($columnFttx[46]->status == 1)
                                        <th class="second-part">合计</th>
                                    @endif
                                    @if ($columnFttx[47]->status == 1)
                                        <th class="second-part">备注</th>
                                    @endif
                                    <th class="second-part"></th>
                                </tr>
                            </thead>
                            <tbody class="column" style="margin-bottom: 12px;">
                                @foreach ($data as $key => $item)
                                    <tr class="list"
                                        style="{{ calculateDaysBetween($item->deadline) < 0 && $item->status != 3 && $item->check_status != 'inactive' ? 'background: #e6375730 !important;' : '' }} {{ $item->check_status != 'inactive' && $item->status != 3 ? 'border: solid 3px #00b74a;color:green;' : '' }}"
                                        @can('fttx-update')@click="storeDialog({{ $item->id }})"@endcan
                                        @can('fttx-report-detail') @contextmenu.prevent="reportDetailDialog({{ $item->id }})" @endcan
                                        title="Click to edit ,Right click to view detail">
                                        <td class="row">
                                            {{ $key + 1 }}
                                        </td>
                                        @if ($columnFttx[0]->status == 1)
                                            <td class="row">
                                                {{ $item?->customer?->name_en ?? ($item->customer->name_kh ?? '') }}
                                            </td>
                                        @endif
                                        @if ($columnFttx[1]->status == 1)
                                            <td class="row">
                                                {{ $item->work_order_isp ?? '-' }}
                                            </td>
                                        @endif
                                        @if ($columnFttx[2]->status == 1)
                                            <td class="row">
                                                {{ $item->work_order_cfocn ?? '-' }}
                                            </td>
                                        @endif
                                        @if ($columnFttx[3]->status == 1)
                                            <td class="row">
                                                {{ $item->subscriber_no ?? '-' }}
                                            </td>
                                        @endif
                                        @if ($columnFttx[4]->status == 1)
                                            <td class="row">
                                                {{ $item->isp_ex_work_order_isp ?? '-' }}
                                            </td>
                                        @endif
                                        @if ($columnFttx[5]->status == 1)
                                            <td class="row">
                                                @foreach (config('dummy.fttx_status') as $status)
                                                    @if ($status['key'] == $item->status)
                                                        <span>{!! $status['text'] ? $status['text'] : '--' !!}</span>
                                                    @endif
                                                @endforeach
                                            </td>
                                        @endif
                                        @if ($columnFttx[6]->status == 1)
                                            <td class="row">
                                                {{ $item->name ?? '-' }}
                                            </td>
                                        @endif
                                        @if ($columnFttx[7]->status == 1)
                                            <td class="row">
                                                {{ $item->phone ?? '-' }}
                                            </td>
                                        @endif
                                        @if ($columnFttx[8]->status == 1)
                                            <td class="row white-space-nowrap textLeft">
                                                <div style="width: 150px;">
                                                    {{ $item->address ?? '-' }}
                                                </div>
                                            </td>
                                        @endif
                                        @if ($columnFttx[9]->status == 1)
                                            <td class="row">
                                                {{ $item->zone ?? '-' }}
                                            </td>
                                        @endif
                                        @if ($columnFttx[10]->status == 1)
                                            <td class="row">
                                                {{ $item->city ?? '-' }}
                                            </td>
                                        @endif
                                        @if ($columnFttx[11]->status == 1)
                                            <td class="row">
                                                {{ $item->port ?? '-' }}
                                            </td>
                                        @endif
                                        @if ($columnFttx[12]->status == 1)
                                            <td class="row">
                                                {{ $item?->posSpeed?->split_pos ?? '-' }}
                                            </td>
                                        @endif
                                        @if ($columnFttx[13]->status == 1)
                                            <td class="row">
                                                {{ strtoupper($item->applicant_team_install) ?? '-' }}
                                            </td>
                                        @endif
                                        @if ($columnFttx[14]->status == 1)
                                            <td class="row">
                                                {{ strtoupper($item->team_install) ?? '-' }}
                                            </td>
                                        @endif
                                        @if ($columnFttx[15]->status == 1)
                                            <td class="row">
                                                {{ $item->create_time ? date('d-M-y', strtotime($item->create_time)) : '-' }}
                                            </td>
                                        @endif
                                        @if ($columnFttx[16]->status == 1)
                                            <td class="row">
                                                {{ $item->completed_time ? date('d-M-y', strtotime($item->completed_time)) : '-' }}
                                            </td>
                                        @endif
                                        @if ($columnFttx[17]->status == 1)
                                            <td class="row">
                                                {{ $item->date_ex_complete_old_order ? date('d-M-y', strtotime($item->date_ex_complete_old_order)) : '-' }}
                                            </td>
                                        @endif
                                        @if ($columnFttx[18]->status == 1)
                                            <td class="row">
                                                {{ $item->dismantle_date ? date('d-M-y', strtotime($item->dismantle_date)) : '-' }}
                                            </td>
                                        @endif
                                        @if ($columnFttx[19]->status == 1)
                                            <td class="row">
                                                {{ $item->dismantle_order_cfocn ?? '-' }}
                                            </td>
                                        @endif
                                        @if ($columnFttx[20]->status == 1)
                                            <td class="row">
                                                {{ $item->lay_fiber ?? '-' }}
                                            </td>
                                        @endif
                                        @if ($columnFttx[21]->status == 1)
                                            <td class="row">
                                                {{ $item->remark_first ?? '-' }}
                                            </td>
                                        @endif
                                        @if ($columnFttx[22]->status == 1)
                                            <td class="row">
                                                {{ $item->reactive_date ? date('d-M-y', strtotime($item->reactive_date)) : '-' }}
                                            </td>
                                        @endif
                                        <td class="row">
                                            {{ $item->reactive_payment_period ? $item->reactive_payment_period : '-' }}
                                        </td>
                                        @if ($columnFttx[23]->status == 1)
                                            <td class="row">
                                                {{ $item->change_splitter_date ? date('d-M-y', strtotime($item->change_splitter_date)) : '-' }}
                                            </td>
                                        @endif
                                        @if ($columnFttx[24]->status == 1)
                                            <td class="row">
                                                {{ $item->relocation_date ? date('d-M-y', strtotime($item->relocation_date)) : '-' }}
                                            </td>
                                        @endif

                                        <td>
                                            <div style="width: 20px;"></div>
                                        </td>
                                        @if ($columnFttx[25]->status == 1)
                                            <td class="row">
                                                {{ $item->start_payment_date ? date('d-M-y', strtotime($item->start_payment_date)) : '-' }}
                                            </td>
                                        @endif
                                        @if ($columnFttx[26]->status == 1)
                                            <td class="row">
                                                {{ $item->last_payment_date ? date('d-M-y', strtotime($item->last_payment_date)) : '-' }}
                                            </td>
                                        @endif
                                        @if ($columnFttx[27]->status == 1)
                                            <td class="row">
                                                {{ $item->initial_installation_order_complete_time ? date('d-M-y', strtotime($item->initial_installation_order_complete_time)) : '-' }}
                                            </td>
                                        @endif
                                        @if ($columnFttx[28]->status == 1)
                                            <td class="row">
                                                {{ $item->first_relocation_order_complete_date ? date('d-M-y', strtotime($item->first_relocation_order_complete_date)) : '-' }}
                                            </td>
                                        @endif
                                        @if ($columnFttx[29]->status == 1)
                                            <td class="row">
                                                {{ $item->payment_date ? date('d-M-y', strtotime($item->payment_date)) : '-' }}
                                            </td>
                                        @endif
                                        @if ($columnFttx[30]->status == 1)
                                            <td class="row">
                                                {{ $item->payment_status ?? '-' }}
                                            </td>
                                        @endif
                                        @if ($columnFttx[31]->status == 1)
                                            <td class="row">
                                                {{ getNumberOfMonth($item->completed_time, $item->deadline) ?? '-' }}
                                                Month
                                            </td>
                                            <td class="row">
                                                {{ getDaysBetweenDates($item->completed_time, $item->deadline) ?? '-' }}
                                                Day
                                            </td>
                                        @endif

                                        @if ($columnFttx[32]->status == 1)
                                            <td class="row">
                                                {{ $item->deadline ? date('d-M-y', strtotime($item->deadline)) : '-' }}
                                            </td>
                                        @endif
                                        @if ($columnFttx[33]->status == 1)
                                            <td class="row">
                                                {{ calculateDaysBetween($item->deadline) ?? '-' }} Day
                                            </td>
                                        @endif
                                        @if ($columnFttx[34]->status == 1)
                                            <td class="row">
                                                {{ $item?->customer_type ?? '-' }}
                                            </td>
                                        @endif
                                        @if ($columnFttx[35]->status == 1)
                                            <td class="row">
                                                {{ $item->new_installation_fee ?? '-' }}
                                            </td>
                                        @endif
                                        @if ($columnFttx[36]->status == 1)
                                            <td class="row">
                                                {{ $item->fiber_jumper_fee ?? '-' }}
                                            </td>
                                        @endif
                                        @if ($columnFttx[37]->status == 1)
                                            <td class="row">
                                                {{ $item->digging_fee ?? '-' }}
                                            </td>
                                        @endif
                                        @if ($columnFttx[38]->status == 1)
                                            <td class="row">
                                                {{ $item->first_payment_period ?? '-' }}
                                            </td>
                                        @endif
                                        @if ($columnFttx[39]->status == 1)
                                            <td class="row">
                                                {{ $item->initial_payment_period ?? '-' }}
                                            </td>
                                        @endif
                                        @if ($columnFttx[40]->status == 1)
                                            <td class="row">
                                                {{ $item->rental_price ?? '-' }}
                                            </td>
                                        @endif
                                        @if ($columnFttx[41]->status == 1)
                                            <td class="row">
                                                {{ $item->ppcc ?? '-' }}
                                            </td>
                                        @endif
                                        @if ($columnFttx[42]->status == 1)
                                            <td class="row">
                                                {{ $item->number_of_pole ?? '-' }}
                                            </td>
                                        @endif
                                        @if ($columnFttx[43]->status == 1)
                                            <td class="row">
                                                {{ $item->rental_pole ?? '-' }}
                                            </td>
                                        @endif
                                        @if ($columnFttx[44]->status == 1)
                                            <td class="row">
                                                {{ $item->other_fee ?? '-' }}
                                            </td>
                                        @endif
                                        @if ($columnFttx[45]->status == 1)
                                            <td class="row">
                                                {{ $item->discount ?? '-' }}
                                            </td>
                                        @endif
                                        @if ($columnFttx[46]->status == 1)
                                            <td class="row">
                                                {{ $item->total_calculate ?? '-' }}
                                            </td>
                                        @endif
                                        @if ($columnFttx[47]->status == 1)
                                            <td class="row">
                                                {{ $item->remark_second ?? '-' }}
                                            </td>
                                        @endif
                                        <td class="row" onclick="event.stopPropagation();">
                                            @canany(['fttx-update', 'fttx-delete'])
                                                <div class="dropdown">
                                                    <i data-feather="more-vertical" class="action-btn"
                                                        id="dropdownMenuButton" data-mdb-toggle="dropdown"
                                                        aria-expanded="false">
                                                    </i>
                                                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton"
                                                        style="height: auto !important;">
                                                        @can('fttx-update')
                                                            <li>
                                                                <a class="dropdown-item"
                                                                    @click="storeDialog({{ $item->id }})">
                                                                    <i style="width: 50px;" data-feather="edit"
                                                                        class="text-primary"></i>
                                                                    <span>Edit</span>
                                                                </a>
                                                            </li>
                                                        @endcan
                                                        @can('fttx-renewal')
                                                            @if ($item->status != 3)
                                                                @if ($item->check_status != 'inactive')
                                                                    <li>
                                                                        <a class="dropdown-item"
                                                                            @click="renewalDialog({{ $item->id }})">
                                                                            <i style="width: 50px;" data-feather="plus-circle"
                                                                                class="text-primary"></i>
                                                                            <span>Renewal</span>
                                                                        </a>
                                                                    </li>
                                                                @endif
                                                            @endif
                                                        @endcan
                                                        @can('fttx-report-detail')
                                                            <li>
                                                                <a class="dropdown-item"
                                                                    @click="reportDetailDialog({{ $item->id }})">
                                                                    <i style="width: 50px;" data-feather="eye"
                                                                        class="text-primary"></i>
                                                                    <span>View Report</span>
                                                                </a>
                                                            </li>
                                                        @endcan
                                                        @can('fttx-delete')
                                                            <li>
                                                                <a class="dropdown-item"
                                                                    onclick="$onConfirmMessage(
                                                                '{!! route('admin-fttx-delete', $item->id) !!}',
                                                                '@lang('dialog.msg.delete', ['name' => ''])',
                                                                {
                                                                    confirm: '@lang('dialog.button.delete')',
                                                                    cancel: '@lang('dialog.button.cancel')'
                                                                }
                                                            );">
                                                                    <i style="width: 50px;" data-feather="trash"
                                                                        class="text-danger"></i>
                                                                    <span>Delete</span>
                                                                </a>
                                                            </li>
                                                        @endcan
                                                    </ul>
                                                </div>
                                            @endcan
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="column" style=" position: sticky;inset-inline-start: 0;">
                            </tfoot>
                        </table>
                    </div>
                @else
                    @component('admin::components.emptyReport', [
                        'name' => 'Fttx empty',
                        'msg' => 'There is no data.',
                        'style' => 'padding: 10px 0 80px 0;',
                    ])
                    @endcomponent
                @endif
            </div>
        </div>
    </div>
</div>
