<template x-data="{}" x-if="$store.storeFttxDialog.active">
    <div class="dialog" x-data="xInsertFttx" x-bind:style="{ zIndex: $store.libs.getLastIndex() + 1 }">
        <div class="dialog-container dialogContainerForm">
            <div class="diglogForm">
                <div class="dialog-form-header" x-bind:style="{ zIndex: $store.libs.getLastIndex() + 1 }">
                    <h3 x-show="!dialogData">Create Fttx</h3>
                    <h3 x-show="dialogData">Edit Fttx</h3>
                    <span class="material-symbols-outlined" @click="dialogClose()">close</span>
                </div>
                <div class="form-admin" id="my-section">
                    <form id="form" class="form-wrapper" action="#">
                        <div class="form-body">
                            <div class="row-3">
                                <div class="form-row">
                                    <label>Isp Name (工单号)<span>*</span></label>
                                    <select id="customer_id" name="customer_id" x-model="formSubmitData.customer_id"
                                        x-init="fetchSelectCustomer()" :disabled="formSubmitData.disable">
                                    </select>
                                    <template x-for="item in dataError?.customer_id">
                                        <div class="errorCenter">
                                            <span class="error" x-text="item">Error</span>
                                        </div>
                                    </template>
                                </div>
                                <div class="form-row">
                                    <label>Work Order ISP (ISP 工单号)<span>*</span></label>
                                    <input type="text" x-model="formSubmitData.work_order_isp"
                                        placeholder="Enter work order ISP">
                                    <template x-for="item in dataError?.work_order_isp">
                                        <div class="errorCenter">
                                            <span class="error" x-text="item">Error</span>
                                        </div>
                                    </template>
                                </div>
                                <div class="form-row">
                                    <label>Work Order CFOCN (CFOCN工单号)<span>*</span></label>
                                    <input type="text" x-model="formSubmitData.work_order_cfocn"
                                        placeholder="Enter work order CFOCN">
                                    <template x-for="item in dataError?.work_order_cfocn">
                                        <div class="errorCenter">
                                            <span class="error" x-text="item">Error</span>
                                        </div>
                                    </template>
                                </div>
                            </div>
                            <div class="row-3">
                                <div class="form-row">
                                    <label>Subscriber No (用户编号)</label>
                                    <input type="text" x-model="formSubmitData.subscriber_no"
                                        placeholder="Enter subscriber no">
                                    <template x-for="item in dataError?.subscriber_no">
                                        <div class="errorCenter">
                                            <span class="error" x-text="item">Error</span>
                                        </div>
                                    </template>
                                </div>
                                <div class="form-row">
                                    <label>ISP EX Work Order ISP (旧工单号)</label>
                                    <input type="text" x-model="formSubmitData.isp_ex_work_order_isp"
                                        placeholder="Enter ISP EX work order ISP">
                                    <template x-for="item in dataError?.isp_ex_work_order_isp">
                                        <div class="errorCenter">
                                            <span class="error" x-text="item">Error</span>
                                        </div>
                                    </template>
                                </div>
                                <div class="form-row">
                                    <label>Status (状态)<span>*</span></label>
                                    <select x-model="formSubmitData.status">
                                        <option value="">Select status...</option>
                                        @foreach (config('dummy.fttx_status') as $type)
                                            <option value="{{ $type['key'] }}">{{ $type['text'] }}</option>
                                        @endforeach
                                    </select>
                                    <template x-for="item in dataError?.status">
                                        <div class="errorCenter">
                                            <span class="error" x-text="item">Error</span>
                                        </div>
                                    </template>
                                </div>
                            </div>
                            <div class="row-2">
                                <div class="form-row">
                                    <label>Name (姓名)</label>
                                    <input type="text" x-model="formSubmitData.name" placeholder="Enter name">
                                    <template x-for="item in dataError?.name">
                                        <div class="errorCenter">
                                            <span class="error" x-text="item">Error</span>
                                        </div>
                                    </template>
                                </div>
                                <div class="form-row">
                                    <label>Phone (电话)</label>
                                    <input type="text" x-model="formSubmitData.phone" placeholder="Enter phone">
                                    <template x-for="item in dataError?.phone">
                                        <div class="errorCenter">
                                            <span class="error" x-text="item">Error</span>
                                        </div>
                                    </template>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-row">
                                    <label>Address (地址)</label>
                                    <input type="text" x-model="formSubmitData.address" placeholder="Enter address">
                                    <template x-for="item in dataError?.address">
                                        <div class="errorCenter">
                                            <span class="error" x-text="item">Error</span>
                                        </div>
                                    </template>
                                </div>
                            </div>
                            <div class="row-3">
                                <div class="form-row">
                                    <label>Zone (区域)</label>
                                    <input type="text" x-model="formSubmitData.zone" placeholder="Enter zone">
                                    <template x-for="item in dataError?.zone">
                                        <div class="errorCenter">
                                            <span class="error" x-text="item">Error</span>
                                        </div>
                                    </template>
                                </div>
                                <div class="form-row">
                                    <label>CITY (城市)</label>
                                    <input type="text" x-model="formSubmitData.city" placeholder="Enter city">
                                    <template x-for="item in dataError?.city">
                                        <div class="errorCenter">
                                            <span class="error" x-text="item">Error</span>
                                        </div>
                                    </template>
                                </div>
                                <div class="form-row">
                                    <label>PORT (端口)</label>
                                    <input type="text" x-model="formSubmitData.port" placeholder="Enter prot">
                                    <template x-for="item in dataError?.port">
                                        <div class="errorCenter">
                                            <span class="error" x-text="item">Error</span>
                                        </div>
                                    </template>
                                </div>
                            </div>
                            <div class="row-3">
                                <div class="form-row">
                                    <label>POS SPEED (分光比)<span>*</span></label>
                                    <select x-model="formSubmitData.pos_speed_id" @change="getPricePosSpeed()">
                                        <option>Select pos speed...</option>
                                        @foreach ($posSpeed as $value)
                                            <option value="{{ $value->id }}">{{ $value->split_pos }}</option>
                                        @endforeach
                                    </select>
                                    <template x-for="item in dataError?.pos_speed_id">
                                        <div class="errorCenter">
                                            <span class="error" x-text="item">Error</span>
                                        </div>
                                    </template>
                                </div>
                                <div class="form-row">
                                    <label>Applicant Team Install (申请安装团队)</label>
                                    <input list="applicantTeamInstall" type="text"
                                        x-model="formSubmitData.applicant_team_install"
                                        placeholder="Enter applicant team install">
                                    <datalist id="applicantTeamInstall">
                                        @foreach (config('dummy.applicant_team_install') as $type)
                                            <option value="{{ $type['text'] }}"></option>
                                        @endforeach
                                    </datalist>
                                    <template x-for="item in dataError?.applicant_team_install">
                                        <div class="errorCenter">
                                            <span class="error" x-text="item">Error</span>
                                        </div>
                                    </template>
                                </div>
                                <div class="form-row">
                                    <label>Team Install (安装团队)<span>*</span></label>
                                    <input list="teamInstall" type="text" x-model="formSubmitData.team_install"
                                        placeholder="Enter applicant team install">
                                    <datalist id="teamInstall">
                                        @foreach (config('dummy.team_install') as $type)
                                            <option value="{{ $type['text'] }}"></option>
                                        @endforeach
                                    </datalist>
                                    <template x-for="item in dataError?.team_install">
                                        <div class="errorCenter">
                                            <span class="error" x-text="item">Error</span>
                                        </div>
                                    </template>
                                </div>
                            </div>
                            <div class="row-3">
                                <div class="form-row">
                                    <label>Create Time (初始安装日期)</label>
                                    <input type="date" id="create_time" name="create_time" class="form-input"
                                        x-model="formSubmitData.create_time">
                                    <template x-for="item in dataError?.create_time">
                                        <div class="errorCenter">
                                            <span class="error" x-text="item">Error</span>
                                        </div>
                                    </template>
                                </div>
                                <div class="form-row">
                                    <label>Completed Time (安装完工日期)<span>*</span></label>
                                    <input type="date" id="completed_time" name="completed_time"
                                        x-model="formSubmitData.completed_time" class="form-input">
                                    <template x-for="item in dataError?.completed_time">
                                        <div class="errorCenter">
                                            <span class="error" x-text="item">Error</span>
                                        </div>
                                    </template>
                                </div>
                                <div class="form-row">
                                    <label>Date EX Complete Old Order (历史工单完工日)</label>
                                    <input type="date" id="date_ex_complete_old_order"
                                        name="date_ex_complete_old_order"
                                        x-model="formSubmitData.date_ex_complete_old_order" class="form-input">
                                    <template x-for="item in dataError?.date_ex_complete_old_order">
                                        <div class="errorCenter">
                                            <span class="error" x-text="item">Error</span>
                                        </div>
                                    </template>
                                </div>
                            </div>
                            <div class="row-3">
                                <div class="form-row">
                                    <label>Dismantle Date (拆机日期)</label>
                                    <input type="date" id="dismantle_date" name="dismantle_date"
                                        x-model="formSubmitData.dismantle_date" class="form-input">
                                    <template x-for="item in dataError?.dismantle_date">
                                        <div class="errorCenter">
                                            <span class="error" x-text="item">Error</span>
                                        </div>
                                    </template>
                                </div>
                                <div class="form-row">
                                    <label>Dismantle Order CFOCN (拆机工单)</label>
                                    <input type="text" x-model="formSubmitData.dismantle_order_cfocn"
                                        placeholder="Enter dismantle order cfocn">
                                    <template x-for="item in dataError?.dismantle_order_cfocn">
                                        <div class="errorCenter">
                                            <span class="error" x-text="item">Error</span>
                                        </div>
                                    </template>
                                </div>
                                <div class="form-row">
                                    <label>Lay Fiber (放缆)</label>
                                    <input type="number" x-model="formSubmitData.lay_fiber"
                                        placeholder="Enter work order ISP">
                                    <template x-for="item in dataError?.lay_fiber">
                                        <div class="errorCenter">
                                            <span class="error" x-text="item">Error</span>
                                        </div>
                                    </template>
                                </div>
                            </div>
                            <div class="row-3">
                                <div class="form-row" style="flex: 2;">
                                    <label>Remark (备注)</label>
                                    <input type="text" x-model="formSubmitData.remark_first"
                                        placeholder="Enter remark">
                                    <template x-for="item in dataError?.remark_first">
                                        <div class="errorCenter">
                                            <span class="error" x-text="item">Error</span>
                                        </div>
                                    </template>
                                </div>
                                <div class="form-row">
                                    <label>Reactive date (Complete Date) (反应日期)</label>
                                    <input type="date" id="reactive_date" x-model="formSubmitData.reactive_date"
                                        class="form-input">
                                    <template x-for="item in dataError?.reactive_date">
                                        <div class="errorCenter">
                                            <span class="error" x-text="item">Error</span>
                                        </div>
                                    </template>
                                </div>
                            </div>
                            <div class="row-3">
                                   <div class="form-row">
                                    <label>Reactive Payment Period</label>
                                    <input type="number"  placeholder="Enter reactive payment period"
                                        x-model="formSubmitData.reactive_payment_period" class="form-input">
                                    <template x-for="item in dataError?.reactive_payment_period">
                                        <div class="errorCenter">
                                            <span class="error" x-text="item">Error</span>
                                        </div>
                                    </template>
                                </div>
                                <div class="form-row">
                                    <label> Change Splitter Date (Complete Date) (变更日期分割器)</label>
                                    <input type="date" id="change_splitter_date"
                                        x-model="formSubmitData.change_splitter_date" class="form-input">
                                    <template x-for="item in dataError?.change_splitter_date">
                                        <div class="errorCenter">
                                            <span class="error" x-text="item">Error</span>
                                        </div>
                                    </template>
                                </div>
                                <div class="form-row">
                                    <label> Relocation Date (Complete Date) (搬迁日期)</label>
                                    <input type="date" id="relocation_date"
                                        x-model="formSubmitData.relocation_date" class="form-input">
                                    <template x-for="item in dataError?.relocation_date">
                                        <div class="errorCenter">
                                            <span class="error" x-text="item">Error</span>
                                        </div>
                                    </template>
                                </div>
                            </div>
                            <div class="row-3">
                                <div class="form-row">
                                </div>
                            </div>
                            <div class="row-3">
                                <div class="form-row">
                                    <label>Start Payment date (开始计费日期)<span>*</span></label>
                                    <input type="date" id="start_payment_date"
                                        x-model="formSubmitData.start_payment_date" class="form-input">
                                    <template x-for="item in dataError?.start_payment_date">
                                        <div class="errorCenter">
                                            <span class="error" x-text="item">Error</span>
                                        </div>
                                    </template>
                                </div>
                                <div class="form-row">
                                    <label>Last payment date (上一次付款日期)</label>
                                    <input type="date" id="last_payment_date"
                                        x-model="formSubmitData.last_payment_date" class="form-input">
                                    <template x-for="item in dataError?.last_payment_date">
                                        <div class="errorCenter">
                                            <span class="error" x-text="item">Error</span>
                                        </div>
                                    </template>
                                </div>
                                <div class="form-row">
                                    <label>Initial Installation order complete time (初始安装订单完成时间)</label>
                                    <input type="date" id="initial_installation_order_complete_time"
                                        x-model="formSubmitData.initial_installation_order_complete_time"
                                        class="form-input">
                                    <template x-for="item in dataError?.initial_installation_order_complete_time">
                                        <div class="errorCenter">
                                            <span class="error" x-text="item">Error</span>
                                        </div>
                                    </template>
                                </div>
                            </div>
                            <div class="row-3">
                                <div class="form-row">
                                    <label>First relocation order complete date (第一移机工单完成日期两次后)</label>
                                    <input type="date" id="first_relocation_order_complete_date"
                                        name="first_relocation_order_complete_date"
                                        x-model="formSubmitData.first_relocation_order_complete_date"
                                        class="form-input">
                                    <template x-for="item in dataError?.first_relocation_order_complete_date">
                                        <div class="errorCenter">
                                            <span class="error" x-text="item">Error</span>
                                        </div>
                                    </template>
                                </div>
                                <div class="form-row">
                                    <label>Payment Date (付款日期)</label>
                                    <input type="date" id="payment_date" x-model="formSubmitData.payment_date"
                                        class="form-input">
                                    <template x-for="item in dataError?.payment_date">
                                        <div class="errorCenter">
                                            <span class="error" x-text="item">Error</span>
                                        </div>
                                    </template>
                                </div>
                                <div class="form-row">
                                    <label>Payment Status (付款状态)</label>
                                    <input list="paymentStatus" type="text"
                                        x-model="formSubmitData.payment_status" placeholder="Enter payment status">
                                    <datalist id="paymentStatus">
                                        @foreach (config('dummy.payment_status') as $type)
                                            <option value="{{ $type['text'] }}"></option>
                                        @endforeach
                                    </datalist>
                                    <template x-for="item in dataError?.payment_status">
                                        <div class="errorCenter">
                                            <span class="error" x-text="item">Error</span>
                                        </div>
                                    </template>
                                </div>
                            </div>
                            <div class="row-3">
                                <div class="form-row">
                                    <label>Deadline (截止日期)<span>*</span></label>
                                    <input @change="getNumberOfMonth()" type="date" id="deadline"
                                        x-model="formSubmitData.deadline" class="form-input">
                                    <template x-for="item in dataError?.deadline">
                                        <div class="errorCenter">
                                            <span class="error" x-text="item">Error</span>
                                        </div>
                                    </template>
                                </div>
                                <div class="form-row">
                                    <label>Month (月)</label>
                                    <input type="number" x-model="formSubmitData.month" placeholder="Enter month"
                                        readonly>
                                    <template x-for="item in dataError?.month">
                                        <div class="errorCenter">
                                            <span class="error" x-text="item">Error</span>
                                        </div>
                                    </template>
                                </div>
                                <div class="form-row">
                                    <label>Day (天)</label>
                                    <input type="number" x-model="formSubmitData.day" placeholder="Enter day"
                                        readonly>
                                    <template x-for="item in dataError?.day">
                                        <div class="errorCenter">
                                            <span class="error" x-text="item">Error</span>
                                        </div>
                                    </template>
                                </div>
                            </div>
                            <div class="row-3">
                                <div class="form-row">
                                    <label>Day Remaining (剩余天数)</label>
                                    <input type="text" x-model="formSubmitData.day_remaining"
                                        placeholder="Enter day remaining">
                                    <template x-for="item in dataError?.day_remaining">
                                        <div class="errorCenter">
                                            <span class="error" x-text="item">Error</span>
                                        </div>
                                    </template>
                                </div>
                                <div class="form-row">
                                    <label>Customer Type (客户类型)</label>
                                    <input list="customerType" type="text" x-model="formSubmitData.customer_type"
                                        placeholder="Enter customer type">
                                    <datalist id="customerType">
                                        @foreach ($customerType as $value)
                                            <option value="{{ $value->name }}">{{ $value->name }}</option>
                                        @endforeach
                                    </datalist>
                                    <template x-for="item in dataError?.customer_type">
                                        <div class="errorCenter">
                                            <span class="error" x-text="item">Error</span>
                                        </div>
                                    </template>
                                </div>
                                <div class="form-row">
                                    <label>New installation fee (安装费)</label>
                                    <input list="newInstallationFee" type="number"
                                        x-model="formSubmitData.new_installation_fee"
                                        placeholder="Enter new installation fee" @input="getTotalAmount()">
                                    <datalist id="newInstallationFee">
                                        <template x-for="item in new_install_price_array">
                                            <option :value="item"></option>
                                        </template>
                                    </datalist>
                                    <template x-for="item in dataError?.new_installation_fee">
                                        <div class="errorCenter">
                                            <span class="error" x-text="item">Error</span>
                                        </div>
                                    </template>
                                </div>
                            </div>
                            <div class="row-3">
                                <div class="form-row">
                                    <label>Fiber jumper fee (跳纤)</label>
                                    <input list="fiberJumperFee" type="number" @input="getTotalAmount()"
                                        x-model="formSubmitData.fiber_jumper_fee"
                                        placeholder="Enter fiber jumper fee">
                                    <datalist id="fiberJumperFee">
                                        @foreach ($fiberJumperFee as $value)
                                            <option value="{{ $value }}"></option>
                                        @endforeach
                                    </datalist>
                                    <template x-for="item in dataError?.fiber_jumper_fee">
                                        <div class="errorCenter">
                                            <span class="error" x-text="item">Error</span>
                                        </div>
                                    </template>
                                </div>
                                <div class="form-row">
                                    <label>Digging fee (开挖费)</label>
                                    <input list="diggingFee" type="number" x-model="formSubmitData.digging_fee"
                                        placeholder="Enter digging fee" @input="getTotalAmount()">
                                    <datalist id="diggingFee">
                                        @foreach ($diggingFee as $value)
                                            <option value="{{ $value }}"></option>
                                        @endforeach
                                    </datalist>
                                    <template x-for="item in dataError?.digging_fee">
                                        <div class="errorCenter">
                                            <span class="error" x-text="item">Error</span>
                                        </div>
                                    </template>
                                </div>
                                <div class="form-row">
                                    <label>First payment period (第一个付款期) <span>*</span></label>
                                    <select x-model="formSubmitData.first_payment_period" @change="getTotalAmount()">
                                        <option value="">Select first payment period...</option>
                                        <option value="3">3 Month</option>
                                        <option value="6">6 Month</option>
                                        <option value="12">12 Month</option>
                                    </select>
                                    <template x-for="item in dataError?.first_payment_period">
                                        <div class="errorCenter">
                                            <span class="error" x-text="item">Error</span>
                                        </div>
                                    </template>
                                </div>
                            </div>
                            <div class="row-3">
                                <div class="form-row">
                                    <label>Initial payment Period (首次付款期限)</label>
                                    <input type="number" x-model="formSubmitData.initial_payment_period"
                                        placeholder="Enter initial payment period" @input="getTotalAmount()">
                                    <template x-for="item in dataError?.initial_payment_period">
                                        <div class="errorCenter">
                                            <span class="error" x-text="item">Error</span>
                                        </div>
                                    </template>
                                </div>
                                <div class="form-row">
                                    <label>Rental Price (租金单价)</label>
                                    <input list="rentalPrice" type="number" x-model="formSubmitData.rental_price"
                                        placeholder="Enter rental price" @input="getTotalAmount()">
                                    <datalist id="rentalPrice">
                                        <template x-for="item in rental_price_array">
                                            <option :value="item"></option>
                                        </template>
                                    </datalist>
                                    <template x-for="item in dataError?.rental_price">
                                        <div class="errorCenter">
                                            <span class="error" x-text="item">Error</span>
                                        </div>
                                    </template>
                                </div>
                                <div class="form-row">
                                    <label>PPCC (万古湖)</label>
                                    <input list="ppccPrice" type="number" x-model="formSubmitData.ppcc"
                                        placeholder="Enter ppcc" @input="getTotalAmount()">
                                    <datalist id="ppccPrice">
                                        <template x-for="item in ppcc_price_array">
                                            <option :value="item"></option>
                                        </template>
                                    </datalist>
                                    <template x-for="item in dataError?.ppcc">
                                        <div class="errorCenter">
                                            <span class="error" x-text="item">Error</span>
                                        </div>
                                    </template>
                                </div>
                            </div>
                            <div class="row-3">
                                <div class="form-row">
                                    <label>Number Of Pole (极数)</label>
                                    <input type="number" x-model="formSubmitData.number_of_pole"
                                        placeholder="Enter number of pole">
                                    <template x-for="item in dataError?.number_of_pole">
                                        <div class="errorCenter">
                                            <span class="error" x-text="item">Error</span>
                                        </div>
                                    </template>
                                </div>
                                <div class="form-row">
                                    <label>Rental pole (电杆)</label>
                                    <input list="rentalPole" type="number" x-model="formSubmitData.rental_pole"
                                        placeholder="Enter rental pole" @input="getTotalAmount()">
                                    <datalist id="rentalPole">
                                        @foreach ($rentalPole as $value)
                                            <option value="{{ $value }}"></option>
                                        @endforeach
                                    </datalist>
                                    <template x-for="item in dataError?.rental_pole">
                                        <div class="errorCenter">
                                            <span class="error" x-text="item">Error</span>
                                        </div>
                                    </template>
                                </div>
                                <div class="form-row">
                                    <label>Other fee (其它收费)</label>
                                    <input type="number" x-model="formSubmitData.other_fee"
                                        placeholder="Enter other fee" @input="getTotalAmount()">
                                    <template x-for="item in dataError?.other_fee">
                                        <div class="errorCenter">
                                            <span class="error" x-text="item">Error</span>
                                        </div>
                                    </template>
                                </div>
                            </div>
                            <div class="row-2">
                                <div class="form-row">
                                    <label>Discount (优惠折扣)</label>
                                    <input type="number" x-model="formSubmitData.discount"
                                        placeholder="Enter discount" @input="getTotalAmount()">
                                    <template x-for="item in dataError?.discount">
                                        <div class="errorCenter">
                                            <span class="error" x-text="item">Error</span>
                                        </div>
                                    </template>
                                </div>
                                <div class="form-row">
                                    <label>Total (合计)</label>
                                    <input type="number" x-model="formSubmitData.total" placeholder="Enter total"
                                        readonly>
                                    <template x-for="item in dataError?.total">
                                        <div class="errorCenter">
                                            <span class="error" x-text="item">Error</span>
                                        </div>
                                    </template>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-row" style="flex: 2;">
                                    <label>Remark (备注)</label>
                                    <input type="text" x-model="formSubmitData.remark_second"
                                        placeholder="Enter remark second">
                                    <template x-for="item in dataError?.remark_second">
                                        <div class="errorCenter">
                                            <span class="error" x-text="item">Error</span>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                        <div class="form-footer">
                            <div style="width: 100%; display: flex; justify-content: space-between;">
                                <div style="display: flex;">
                                    @can('fttx-renewal')
                                        <template x-if="dialogData">
                                            <button type="button" class="primary" @click="renewalDialog(dialogData.id)">
                                                <span>Renewal</span>
                                            </button>
                                        </template>
                                    @endcan
                                    @can('fttx-report-detail')
                                        <template x-if="dialogData">
                                            <button type="button" class="primary"
                                                @click="reportDetailDialog(dialogData.id)">
                                                <span>View Report</span>
                                            </button>
                                        </template>
                                    @endcan
                                </div>
                                <div style="display: flex;">
                                    <button type="button" class="primary" color="primary" @click="submitFrom()">
                                        <i class='bx bx-save'></i>
                                        <span>Save</span>
                                    </button>
                                    <button type="button" class="close" @click="dialogClose()">
                                        <i class='bx bx-x'></i>
                                        <span>Close</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                    <template x-if="submitLoading">
                        @include('admin::components.spinner')
                    </template>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    Alpine.data('xInsertFttx', () => ({
        submitLoading: false,
        dataError: [],
        rental_price_array: [],
        ppcc_price_array: [],
        new_install_price_array: [],
        formSubmitData: {
            work_order_isp: null,
            customer_id: null,
            work_order_cfocn: null,
            subscriber_no: null,
            isp_ex_work_order_isp: null,
            status: null,
            name: null,
            phone: null,
            address: null,
            zone: null,
            city: null,
            port: null,
            pos_speed_id: null,
            applicant_team_install: null,
            team_install: null,
            create_time: null,
            completed_time: null,
            date_ex_complete_old_order: null,
            dismantle_date: null,
            dismantle_order_cfocn: null,
            lay_fiber: null,
            remark_first: null,
            reactive_date: null,
            reactive_payment_period: null,
            change_splitter_date: null,
            relocation_date: null,
            start_payment_date: null,
            last_payment_date: null,
            initial_installation_order_complete_time: null,
            first_relocation_order_complete_date: null,
            payment_date: null,
            payment_status: null,
            online_days: null,
            month: null,
            day: null,
            deadline: null,
            day_remaining: null,
            customer_type: null,
            new_installation_fee: null,
            fiber_jumper_fee: null,
            digging_fee: null,
            first_payment_period: null,
            initial_payment_period: null,
            rental_price: null,
            ppcc: null,
            number_of_pole: null,
            rental_pole: null,
            other_fee: null,
            discount: null,
            total: null,
            remark_second: null,
            disable: false
        },
        dialogData: null,
        async init() {
            let id = this.$store.storeFttxDialog.options.data;
            if (id) {
                await this.getFttx(id);
            }
            if (this.dialogData) {
                this.submitLoading = true;
                this.formSubmitData.work_order_isp = this.dialogData.work_order_isp;
                this.formSubmitData.work_order_cfocn = this.dialogData.work_order_cfocn;
                this.formSubmitData.subscriber_no = this.dialogData.subscriber_no;
                this.formSubmitData.isp_ex_work_order_isp = this.dialogData.isp_ex_work_order_isp;
                this.formSubmitData.status = this.dialogData.status;
                this.formSubmitData.name = this.dialogData.name;
                this.formSubmitData.phone = this.dialogData.phone;
                this.formSubmitData.address = this.dialogData.address;
                this.formSubmitData.zone = this.dialogData.zone;
                this.formSubmitData.city = this.dialogData.city;
                this.formSubmitData.port = this.dialogData.port;
                this.formSubmitData.pos_speed_id = this.dialogData.pos_speed_id;
                this.formSubmitData.applicant_team_install = this.dialogData.applicant_team_install;
                this.formSubmitData.team_install = this.dialogData.team_install;
                this.formSubmitData.create_time = this.dialogData.create_time;
                this.formSubmitData.completed_time = this.dialogData.completed_time;
                this.formSubmitData.date_ex_complete_old_order = this.dialogData
                    .date_ex_complete_old_order;
                this.formSubmitData.dismantle_date = this.dialogData.dismantle_date;
                this.formSubmitData.dismantle_order_cfocn = this.dialogData.dismantle_order_cfocn;
                this.formSubmitData.lay_fiber = this.dialogData.lay_fiber;
                this.formSubmitData.remark_first = this.dialogData.remark_first;
                this.formSubmitData.reactive_date = this.dialogData.reactive_date;
                this.formSubmitData.reactive_payment_period = this.dialogData.reactive_payment_period;
                this.formSubmitData.change_splitter_date = this.dialogData.change_splitter_date;
                this.formSubmitData.relocation_date = this.dialogData.relocation_date;
                this.formSubmitData.start_payment_date = this.dialogData.start_payment_date;
                this.formSubmitData.last_payment_date = this.dialogData.last_payment_date;
                this.formSubmitData.initial_installation_order_complete_time = this.dialogData
                    .initial_installation_order_complete_time;
                this.formSubmitData.first_relocation_order_complete_date = this.dialogData
                    .first_relocation_order_complete_date;
                this.formSubmitData.payment_date = this.dialogData.payment_date;
                this.formSubmitData.payment_status = this.dialogData.payment_status;
                this.formSubmitData.deadline = this.dialogData.deadline;
                this.formSubmitData.customer_id = this.dialogData.customer_id;
                this.formSubmitData.day_remaining = this.dialogData.day_remaining;
                this.formSubmitData.customer_type = this.dialogData.customer_type;
                this.formSubmitData.new_installation_fee = this.dialogData.new_installation_fee;
                this.formSubmitData.fiber_jumper_fee = this.dialogData.fiber_jumper_fee;
                this.formSubmitData.digging_fee = this.dialogData.digging_fee;
                this.formSubmitData.first_payment_period = this.dialogData.first_payment_period;
                this.formSubmitData.initial_payment_period = this.dialogData.initial_payment_period;
                this.formSubmitData.rental_price = this.dialogData.rental_price;
                this.formSubmitData.ppcc = this.dialogData.ppcc;
                this.formSubmitData.rental_pole = this.dialogData.rental_pole;
                this.formSubmitData.other_fee = this.dialogData.other_fee;
                this.formSubmitData.discount = this.dialogData.discount;
                this.formSubmitData.total = this.dialogData.total;
                this.formSubmitData.remark_second = this.dialogData.remark_second;
                this.getNumberOfMonth();
                this.getPricePosSpeed();
                //checkCurrentSelect2
                this.appendSelect2HtmlCurrentSelect('customer_id', this.dialogData?.customer?.id, this
                    .dialogData?.customer
                    ?.name_en);
                setTimeout(() => {
                    this.submitLoading = false;
                }, "500");
            }
        },
        async getFttx(id) {
            await Axios.get(`/admin/select/get-fttx/${id}`).then(resp => {
                this.dialogData = resp.data;
            });
        },

        submitFrom() {
            let dataStore = this.$store.storeFttxDialog.options.data;
            this.dataError = [];
            this.$store.confirmDialog.open({
                data: {
                    title: "Message",
                    message: "Are you sure to save?",
                    btnClose: "Close",
                    btnSave: "Yes",
                },
                afterClosed: (result) => {
                    if (result) {
                        let id = this.dialogData ? this.dialogData.id : null;
                        this.submitLoading = true;
                        let data = this.formSubmitData ?? {};

                        setTimeout(() => {
                            Axios({
                                url: `{{ route('admin-fttx-save') }}`,
                                method: 'POST',
                                data: {
                                    ...data,
                                    id: id,
                                }
                            }).then((res) => {
                                this.submitLoading = false;
                                let status = res.data.status;
                                if (status == "success") {
                                    this.dialogClose();
                                    reloadUrl(
                                        "{!! url()->current() !!}"
                                    );
                                    Toast({
                                        title: 'Fttx',
                                        message: res.data.message,
                                        status: 'success',
                                        size: 'small',
                                    });
                                }
                            }).catch((e) => {
                                this.dataError = e.response
                                    ?.data.errors;
                                this.submitLoading = false;


                            }).finally(() => {
                                this.submitLoading = false;

                            });
                        }, 500);
                    }
                }
            });
        },
        getNumberOfMonth() {
            let startDate = this.formSubmitData.completed_time;
            let endDate = this.formSubmitData.deadline;
            if (startDate && endDate && endDate >= startDate) {
                const start = new Date(startDate);
                const end = new Date(endDate);

                const diffInMonths = (end.getFullYear() - start.getFullYear()) * 12 + (end.getMonth() -
                    start
                    .getMonth());

                const diffInDays = (end - new Date(start.getFullYear(), start.getMonth() + diffInMonths,
                    start
                    .getDate())) / (1000 * 60 * 60 * 24);

                const total = diffInMonths + (diffInDays / 30);
                this.formSubmitData.month = ((Math.round(total * 100) / 100) - 0.01).toFixed(2);
            } else {
                this.formSubmitData.month = 0;
            }
            this.getDaysBetweenDates(this.formSubmitData.completed_time, this.formSubmitData.deadline);
            this.calculateDaysBetween(this.formSubmitData.deadline)
        },
        getDaysBetweenDates(date1, date2) {
            if (date1 && date2 && date2 >= date1) {
                const startDate = new Date(date1);
                const endDate = new Date(date2);

                const timeDifference = Math.abs(endDate - startDate);

                const daysDifference = timeDifference / (1000 * 60 * 60 * 24);

                this.formSubmitData.day = daysDifference;
            } else {
                this.formSubmitData.month = 0;
            }
        },
        calculateDaysBetween(date) {
            if (date) {
                const today = new Date();
                const inputDate = new Date(date);
                const differenceInTime = inputDate - today;
                const differenceInDays = Math.ceil(differenceInTime / (1000 * 60 * 60 * 24));
                this.formSubmitData.day_remaining = differenceInDays;
            }
        },
        dialogClose() {
            this.$store.storeFttxDialog.active = false;
        },
        fetchSelectCustomer() {
            $('#customer_id').select2({
                placeholder: `Select isp...`,
                ajax: {
                    url: '{{ route('admin-select-customer') }}',
                    dataType: 'json',
                    type: "GET",
                    quietMillis: 50,
                    data: (param) => {
                        return {
                            search: param.term
                        };
                    },
                    processResults: (data) => {
                        return {
                            results: $.map(data.data, (item) => {
                                return {
                                    text: item?.name_en ? item?.name_en : item?.name_kh,
                                    id: item.id
                                }
                            })
                        };
                    }
                }
            }).on('select2:open', (e) => {
                document.querySelector('.select2-search__field').focus();
            }).on('select2:close', async (eventClose) => {
                const _id = eventClose.target.value;
                this.formSubmitData.customer_id = _id;
            });
        },
        appendSelect2HtmlCurrentSelect(select2ID, id, name) {
            var option = "<option selected></option>";
            var optionHTML = $(option).val(id ? id : null).text(name ? name : name);
            $(`#${select2ID}`).append(optionHTML).trigger('change');
        },
        async getPricePosSpeed() {
            let id = this.formSubmitData.pos_speed_id ? this.formSubmitData.pos_speed_id : null;
            await Axios.get(`/admin/fttx/fttx/get-pos-speed/${id?id:null}`).then(resp => {
                this.rental_price_array = resp.data.data.rental_price;
                this.ppcc_price_array = resp.data.data.ppcc_price;
                this.new_install_price_array = resp.data.data.new_install_price;
            });
        },
        getTotalAmount() {
            let newInstallationFee = this.formSubmitData.new_installation_fee ? this.formSubmitData
                .new_installation_fee : 0;
            let fiberJumperFee = this.formSubmitData.fiber_jumper_fee ? this.formSubmitData
                .fiber_jumper_fee : 0;
            let diggingFee = this.formSubmitData.digging_fee ? this.formSubmitData.digging_fee : 0;
            let firstPaymentPeriod = this.formSubmitData.first_payment_period ? this.formSubmitData
                .first_payment_period : 0;
            let rentalPrice = this.formSubmitData.rental_price ? this.formSubmitData.rental_price : 0;
            let ppcc = this.formSubmitData.ppcc ? this.formSubmitData.ppcc : 0;
            let rentalPole = this.formSubmitData.rental_pole ? this.formSubmitData.rental_pole : 0;
            let otherFee = this.formSubmitData.other_fee ? this.formSubmitData.other_fee : 0;
            let discount = this.formSubmitData.discount ? this.formSubmitData.discount : 0;

            this.formSubmitData.total = (Number(newInstallationFee) + Number(fiberJumperFee) + Number(
                diggingFee) + Number(otherFee)) + ((Number(rentalPrice) + Number(ppcc) + Number(
                rentalPole)) * Number(firstPaymentPeriod)) - Number(discount);
            this.formSubmitData.total = this.numberRound(this.formSubmitData.total);
        },
        numberRound(num, decimalPlaces = null) {
            if (!decimalPlaces) {
                return Math.round(num);
            }
            var p = Math.pow(10, decimalPlaces);
            return Math.round(num * p) / p;
        },

    }));
</script>
<script>
    Alpine.store('storeFttxDialog', {
        active: false,
        options: {
            data: null,
            selected: null,
            multiple: false,
            title: 'Choose an option',
            placeholder: 'Type to search...',
            allow_close: true,
            onReady: () => {},
            onSearch: () => {},
            beforeClose: () => {},
            afterClose: () => {}
        }
    });
    window.storeFttxDialog = (options) => {
        Alpine.store('storeFttxDialog', {
            active: true,
            options: {
                ...Alpine.store('storeFttxDialog').options,
                ...options
            }
        });
    };
</script>
