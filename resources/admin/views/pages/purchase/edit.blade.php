@extends('admin::shared.layout')
@section('layout')
    <div class="form-admin" x-data="purchaseForm">
        <div class="form-bg"></div>
        <form id="form" class="form-wrapper" enctype="multipart/form-data" style="max-width: 100%;padding: 0 50px;">
            <div class="form-header">
                <h3>
                    <i data-feather="arrow-left" s-click-link="{!! route('admin-purchase-list', 1) !!}"></i>
                    Update P A C
                </h3>
            </div>

            {{ csrf_field() }}
            <div class="form-body">
                <div class="row-2">
                    <div class="form-row">
                        <label>PAC Number<span>*</span></label>
                        <input type="text" name="pac_number" placeholder="Enter pac number..."
                            x-model="formSubmitData.pac_number" :disabled="formSubmitData.disable">
                        <template x-for="item in dataError?.pac_number">
                            <div class="errorCenter">
                                <span class="error" x-text="item">Error</span>
                            </div>
                        </template>
                    </div>
                    <div class="form-row">
                        <label>PO/LO Number<span>*</span></label>
                        <input type="text" name="po_number" placeholder="Enter pl/lo number..."
                            x-model="formSubmitData.po_number" :disabled="formSubmitData.disable">
                        <template x-for="item in dataError?.po_number">
                            <div class="errorCenter">
                                <span class="error" x-text="item">Error</span>
                            </div>
                        </template>
                    </div>
                </div>
                <div class="row-2">
                    <div class="form-row">
                        <label>Po Date </label>
                        <input type="text" name="po_date" id="po_date" autocomplete="off"
                            placeholder="Select issue  date" x-model="formSubmitData.po_date" x-ref="po_date"
                            :disabled="formSubmitData.disable">
                        <template x-for="item in dataError?.po_date">
                            <div class="errorCenter">
                                <span class="error" x-text="item">Error</span>
                            </div>
                        </template>
                    </div>
                    <div class="form-row">
                        <label>Customer<span>*</span></label>
                        <select id="customer_id" name="customer_id" x-model="formSubmitData.customer_id"
                            x-init="fetchSelectCustomer()" :disabled="formSubmitData.disable">
                            <option value="">Select customer...</option>
                        </select>
                        <template x-for="item in dataError?.customer_id">
                            <div class="errorCenter">
                                <span class="error" x-text="item">Error</span>
                            </div>
                        </template>
                    </div>
                </div>
                <div class="row-2">
                    <div class="form-row">
                        <label>Project<span>*</span></label>
                        <select id="project_id" name="project_id" x-init="fetchSelectProject()"
                            x-model="formSubmitData.project_id" :disabled="formSubmitData.disable">
                            <option value="">Select project...</option>
                        </select>
                        <template x-for="item in dataError?.project_id">
                            <div class="errorCenter">
                                <span class="error" x-text="item">Error</span>
                            </div>
                        </template>
                    </div>
                    <div class="form-row">
                        <label>Issue date</label>
                        <input type="text" name="issue_date" id="issue_date" autocomplete="off"
                            placeholder="Select issue  date" x-model="formSubmitData.issue_date" x-ref="issue_date"
                            :disabled="formSubmitData.disable">
                        <template x-for="item in dataError?.issue_date">
                            <div class="errorCenter">
                                <span class="error" x-text="item">Error</span>
                            </div>
                        </template>
                    </div>
                </div>
                <div class="row-2">
                    <div class="form-row">
                        <label>End date</label>
                        <input type="text" name="end_date" id="end_date" autocomplete="off"
                            placeholder="Select issue  date" x-model="formSubmitData.end_date" x-ref="end_date"
                            :disabled="formSubmitData.disable">
                        <template x-for="item in dataError?.end_date">
                            <div class="errorCenter">
                                <span class="error" x-text="item">Error</span>
                            </div>
                        </template>
                    </div>
                    <div class="form-row">
                        <label>Type (Cores or Mbps...)</label>
                        <input type="text" name="pac_type" placeholder="Enter type..." x-model="formSubmitData.pac_type"
                            :disabled="formSubmitData.disable">
                        <template x-for="item in dataError?.pac_type">
                            <div class="errorCenter">
                                <span class="error" x-text="item">Error</span>
                            </div>
                        </template>
                    </div>
                </div>
                <div class="row-2">
                    <div class="form-row">
                        <label>Length</label>
                        <input type="number" name="length" min="0" step="any"
                            oninput="validity.valid||(value='');" placeholder="Enter length..."
                            x-model="formSubmitData.length" :disabled="formSubmitData.disable">
                        <template x-for="item in dataError?.length">
                            <div class="errorCenter">
                                <span class="error" x-text="item">Error</span>
                            </div>
                        </template>
                    </div>
                    <div class="form-row">
                        <label>Cores</label>
                        <input type="number" oninput="validity.valid||(value='');" min="0" step="any"
                            name="cores" placeholder="Enter Core..." x-model="formSubmitData.cores"
                            :disabled="formSubmitData.disable">
                        <template x-for="item in dataError?.cores">
                            <div class="errorCenter">
                                <span class="error" x-text="item">Error</span>
                            </div>
                        </template>
                    </div>
                </div>
                <div class="row-2">
                    <div class="form-row">
                        <label>Service Type <span>*</span></label>
                        <select @change="getServiceByType($event.target.value)" id="type_id" name="type_id"
                            x-model="formSubmitData.type_id" :disabled="formSubmitData.disable">
                            <option value="">Select service type...</option>
                            @foreach ($types as $type)
                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                            @endforeach
                        </select>
                        <template x-for="item in dataError?.type_id">
                            <div class="errorCenter">
                                <span class="error" x-text="item">Error</span>
                            </div>
                        </template>
                    </div>
                    <div class="form-row">
                        <label>Contract Number</label>
                        <input type="text" name="contract_number" placeholder="Enter contract number..." x-model="formSubmitData.contract_number"
                            :disabled="formSubmitData.disable">
                        <template x-for="item in dataError?.contract_number">
                            <div class="errorCenter">
                                <span class="error" x-text="item">Error</span>
                            </div>
                        </template>
                    </div>
                </div>
                <div class="row-2">
                    <div class="form-row">
                        <label>Location <span>*</span></label>
                        <input type="text" name="location" placeholder="Enter location..." x-model="formSubmitData.location"
                            :disabled="formSubmitData.disable">
                        <template x-for="item in dataError?.location">
                            <div class="errorCenter">
                                <span class="error" x-text="item">Error</span>
                            </div>
                        </template>
                    </div>
                    <div class="form-row">
                        <label>Type Data</label>
                        <select id="type_data" name="type_data" x-model="formSubmitData.type_data"
                            :disabled="formSubmitData.disable">
                            <option value="new">New</option>
                            <option value="old">Old</option>
                        </select>
                    </div>
                </div>
                <div class="row-2">
                    <div class="form-row">
                        <label>@lang('user.form.status.label')<span>*</span></label>
                        <select name="status" x-model="formSubmitData.status">
                            <option value="1">Active</option>
                            <option value="2">Inactive</option>
                            <option value="3">Terminate</option>
                        </select>
                        <template x-for="item in dataError?.status">
                            <div class="errorCenter">
                                <span class="error" x-text="item">Error</span>
                            </div>
                        </template>
                    </div>
                </div>
                <div class="row">
                    <div class="table customTable">
                        <div class="table-wrapper">
                            <div class="table-header">
                                <div class="row table-row-5">
                                    <span>Nº</span>
                                </div>
                                <div class="row table-row-15 text-start">
                                    <span>Item</span>
                                </div>
                                <div class="row table-row-20 text-start">
                                    <span>Description</span>
                                </div>
                                <div class="row table-row-15 text-start">
                                    <span>Quantity</span>
                                </div>
                                <div class="row table-row-10 text-start">
                                    <span>UOM</span>
                                </div>
                                <div class="row table-row-10 text-end ">
                                    <span>Unit Price($)</span>
                                </div>
                                <div class="row table-row-15 text-end">
                                    <span>Amount($)</span>
                                </div>
                                <div class="row table-row-10 text-end">
                                    <span>Action</span>
                                </div>
                            </div>
                            <div class="table-body">
                                <template x-for="(item,index) in dataForm">
                                    <div class="column">
                                        <div class="row table-row-5">
                                            <span x-text="index+1"></span>
                                        </div>
                                        <div class="row table-row-15 text-start">
                                            <span>
                                                <select name="service_id[]" x-model="item.service_id.value"
                                                    :class="item.service_id.error ? 'borderRed' : ''"
                                                    :disabled="formSubmitData.disable">
                                                    <option value="">Select service...</option>
                                                    <template x-for="value in List_service_in_type">
                                                        <option :value="value.id" x-text="value.name"
                                                            :selected="value.id == item.service_id.value ? true : false">
                                                        </option>
                                                    </template>
                                                </select>
                                            </span>
                                        </div>
                                        <div class="row table-row-20 text-start">
                                            <span>
                                                <textarea class="txta" role="textbox" contenteditable :disabled="formSubmitData.disable" name="des[]"
                                                    :class="item.des.error ? 'borderRed' : ''" x-model="item.des.value" placeholder="des ..."></textarea>
                                            </span>
                                        </div>
                                        <div class="row table-row-15 text-start">
                                            <span><input :disabled="formSubmitData.disable" type="number"
                                                    x-model="item.qty.value" :class="item.qty.error ? 'borderRed' : ''"
                                                    placeholder="qty ..." class="input-table"
                                                    @input="inputChange(item,'qty')"></span>
                                        </div>
                                        <div class="row table-row-10 text-start">
                                            <span><input type="text" :disabled="formSubmitData.disable"
                                                    x-model="item.uom.value" :class="item.uom.error ? 'borderRed' : ''"
                                                    placeholder="uom ..." min="1" step="1"
                                                    class="input-table"></span>
                                        </div>
                                        <div class="row table-row-10 text-start">
                                            <span><input :disabled="formSubmitData.disable" type="number"
                                                    x-model="item.price.value"
                                                    :class="item.price.error ? 'borderRed' : ''" min="0.01"
                                                    step="0.01" placeholder="price ..." class="input-table"
                                                    @input="inputChange(item,'price')"></span>
                                        </div>
                                        <div class="row table-row-15 text-start">
                                            <span><input x-model="formatDollar(numberRound(item.amount.value,2))"
                                                    :class="item.amount.error ? 'borderRed' : ''" placeholder="amount ..."
                                                    class="input-table" disabled></span>
                                        </div>
                                        <div class="row table-row-10 text-start">
                                            <span>
                                                <button type="button" class="delete" @click="removeInput(item,index)"
                                                    :disabled="formSubmitData.disable">
                                                    <i class="material-symbols-outlined">delete</i>
                                                </button>
                                                <template x-if="!item.remove || dataForm.length <= 1">
                                                    <button type="button" class="add" @click="addItem(index)"
                                                        :disabled="formSubmitData.disable">
                                                        <i class="material-symbols-outlined">add</i>
                                                    </button>
                                                </template>
                                            </span>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                        <div class="customTableFooterOutTableBlock">
                            <div class="table-wrapper">
                                <div class="table-body">
                                    <div class="column">
                                        <div class="row table-row-45"></div>
                                        <div class="row table-row-15 borderCus">
                                            <span>Sub Total&nbsp;:</span>
                                        </div>
                                        <div class="row table-row-15 borderCus">
                                            <span x-text="'$ '+ formatDollar(sub_total.dollar)"
                                                x-model="formatDollar(sub_total.dollar)"></span>
                                        </div>
                                        <div class="row table-row-15 borderCus">
                                            <span x-text="'R '+ formatDollar(sub_total.khmer)"
                                                x-model="formatDollar(sub_total.khmer)"></span>
                                        </div>
                                        <div class="row table-row-10"></div>
                                    </div>
                                    <div class="column">
                                        <div class="row table-row-45"></div>
                                        <div class="row table-row-15 borderCus">
                                            <span>VAT 10%&nbsp;:</span>
                                        </div>
                                        <div class="row table-row-15 borderCus">
                                            <span x-text="'$ '+ formatDollar(vat.dollar)"
                                                x-model="formatDollar(vat.dollar)"></span>
                                        </div>
                                        <div class="row table-row-15 borderCus">
                                            <span x-text="'R '+ formatDollar(vat.khmer)"
                                                x-model="formatDollar(vat.khmer)"></span>
                                        </div>
                                        <div class="row table-row-10"></div>
                                    </div>
                                    <div class="column">
                                        <div class="row table-row-45"></div>
                                        <div class="row table-row-15 borderCus bLast">
                                            <span>Grand Totola&nbsp;:</span>
                                        </div>
                                        <div class="row table-row-15 borderCus bLast">
                                            <span x-text="'$ '+ formatDollar(grand_total.dollar)"
                                                x-model="formatDollar(grand_total.dollar)"></span>
                                        </div>
                                        <div class="row table-row-15 borderCus bLast">
                                            <span x-text="'R '+ formatDollar(grand_total.khmer)"
                                                x-model="formatDollar(grand_total.khmer)"></span>
                                        </div>
                                        <div class="row table-row-10"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <template x-for="item in dataError?.dataTable">
                        <div class="errorCenter">
                            <span class="error" x-text="item">Error</span>
                        </div>
                    </template>
                </div>
                <div class="form-button" style="">
                    <button type="button" color="primary" @click="submitFrom()"
                        :disabled="formSubmitData.disable || submitLoading">
                        <div class="loading loadingSubmit" x-show="submitLoading">
                            <span id="spinner"></span>
                        </div>
                        <i data-feather="save" x-show="!submitLoading"></i>&nbsp;&nbsp;
                        <span>Submit</span>
                    </button>
                    <button color="danger" :disabled="formSubmitData.disable || submitLoading" type="button"
                        s-click-link="{!! route('admin-purchase-list', 1) !!}">
                        <i data-feather="x"></i>
                        <span>Cancel</span>
                    </button>
                </div>
            </div>
            <div class="form-footer"></div>
        </form>
    </div>
    @include('admin::file-manager.popup')
@stop
@section('script')
    <script>
        Alpine.data('purchaseForm', () => ({
            sub_total: {
                dollar: 0,
                khmer: 0
            },
            vat: {
                dollar: 0,
                khmer: 0
            },
            grand_total: {
                dollar: 0,
                khmer: 0
            },
            formSubmitData: {
                pac_number: null,
                po_number: null,
                customer_id: null,
                project_id: null,
                issue_date: null,
                pac_type: null,
                length: null,
                cores: null,
                type_data: null,
                type_id: null,
                dataTable: [],
                total_qty: 0,
                total_price: 0,
                total_vat: 0,
                disable: false,
                contract_number:null,
                location:null,
                end_date:null,
                po_date:null,
                state:null,
            },
            dataForm: [{
                id: Number(moment().format('YYYYMMDDHHmmss')),
                service_id: {
                    value: "",
                    error: false
                },
                name: {
                    value: ""
                },
                des: {
                    value: "",
                    error: false
                },
                uom: {
                    value: "",
                    error: false
                },
                qty: {
                    value: "",
                    error: false
                },
                price: {
                    value: "",
                    error: false
                },
                amount: {
                    value: "",
                    error: false
                },
                remove: false
            }],
            List_service_in_type: [],
            exhange_rate: @json($rate) ? @json($rate).rate : "",
            dataError: [],
            submitLoading: false,
            deleteItemID: [],
            async init() {
                let data = @json($data);

                //getDataTypeSerive
                await this.getServiceByType(data?.type_id);

                //checkCurrentSelect2
                this.appendSelect2HtmlCurrentSelect('customer_id', data?.customer?.id, data?.customer
                    ?.name_en);
                this.appendSelect2HtmlCurrentSelect('project_id', data?.project?.id, data?.project?.name);

                this.formSubmitData = data;
                this.formSubmitData.disable = false;

                if (data?.purchase_detail?.length > 0) {
                    this.dataForm = [];
                    data.purchase_detail.forEach(val => {
                        let item = {
                            id: Number(moment().format('YYYYMMDDHHmmss')),
                            purchase_detail_id: val.id,
                            service_id: {
                                value: val.service_id,
                                error: false
                            },
                            des: {
                                value: val.des,
                                error: false
                            },
                            uom: {
                                value: val.uom,
                                error: false
                            },
                            qty: {
                                value: val.qty,
                                error: false
                            },
                            price: {
                                value: val.price,
                                error: false
                            },
                            amount: {
                                value: val.amount,
                                error: false
                            },
                            remove: true
                        };
                        this.dataForm.push(item);
                    });
                    this.dataForm[this.dataForm.length - 1].remove = false;
                    this.calcuatorAmount();
                }
                $("#po_date").datepicker({
                    changeYear: true,
                    gotoCurrent: true,
                    yearRange: "-100:+100",
                    dateFormat: "yy-mm-dd",
                });

                $("#issue_date").datepicker({
                    changeYear: true,
                    gotoCurrent: true,
                    yearRange: "-100:+100",
                    dateFormat: "yy-mm-dd",
                    onSelect: (select) => {
                        $('#end_date').datepicker('option', 'minDate', select)
                    }
                });
                $("#end_date").datepicker({
                    changeYear: true,
                    gotoCurrent: true,
                    yearRange: "-100:+100",
                    dateFormat: "yy-mm-dd",
                    onSelect: (select) => {
                        $('#issue_date').datepicker('option', 'maxDate', select)
                    }
                });

            },
            fetchSelectCustomer() {
                $('#customer_id').select2({
                    placeholder: `Select customer...`,
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
            fetchSelectProject() {
                $('#project_id').select2({
                    placeholder: `Select project...`,
                    ajax: {
                        url: '{{ route('admin-select-project') }}',
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
                                        text: item?.name ?? item?.phone,
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
                    this.formSubmitData.project_id = _id;
                });
            },
            selectOption($event, index) {
                let id = $event.target.value;
                this.dataForm[index].service_id.value = id;
            },

            removeInput(item, index) {
                if (this.dataForm.length == 1) {
                    this.dataForm = [];
                    this.increateItemOneEmptyData();
                } else {
                    this.dataForm.splice(index, 1);
                }

                if (item?.purchase_detail_id) {
                    this.deleteItemID.push(item.purchase_detail_id);
                }
                this.dataForm[this.dataForm.length - 1].remove = false;
                this.calcuatorAmount();
            },
            addItem(index) {
                this.checkValidation((res) => {
                    if (res.length > 0) {
                        return false;
                    } else {
                        this.dataForm[index].remove = true;
                        this.increateItemOneEmptyData();
                    }
                });
            },

            checkValidation(callback) {
                let error = [];
                if (this.dataForm.length > 0) {
                    this.dataForm.forEach(val => {
                        this.checkValue(val.service_id) ? error.push(true) : false;
                        this.checkValue(val.des) ? error.push(true) : false;
                        this.checkValue(val.uom) ? error.push(true) : false;
                        this.checkValue(val.qty) ? error.push(true) : false;
                        this.checkValue(val.price) ? error.push(true) : false;
                        this.checkValue(val.amount) ? error.push(true) : false;
                    });
                }
                callback(error);
            },
            increateItemOneEmptyData() {
                let dataObject = {
                    id: Number(moment().format('YYYYMMDDHHmmss')),
                    service_id: {
                        value: "",
                        error: false
                    },
                    des: {
                        value: "",
                        error: false
                    },
                    uom: {
                        value: "",
                        error: false
                    },
                    qty: {
                        value: "",
                        error: false
                    },
                    price: {
                        value: "",
                        error: false
                    },
                    amount: {
                        value: "",
                        error: false
                    },
                    remove: false
                };

                this.dataForm.push(dataObject);
            },
            checkValue(data) {
                if (!data.value) {
                    data.error = true;
                    return true;
                } else {
                    data.error = false;
                    return false;
                }
            },

            async getServiceByType(id) {
                await Axios.get(`/admin/purchase/type-service/${id?id:null}`).then(resp => {
                    this.List_service_in_type = resp.data;
                });
                //resetTableSelectService
                this.dataForm.map(item => {
                    item.service_id.value = "";
                    item.service_id.error = false;
                });
            },
            async fetchData(url, callback) {
                await fetch(url, {
                        method: "GET",
                        headers: {
                            "Content-Type": "application/json",
                            "Accept": "application/json",
                        }
                    })
                    .then(response => response.json())
                    .then(response => {
                        callback(response);
                    })
                    .catch((e) => {})
                    .finally(async (res) => {});
            },
            numberRound(num, decimalPlaces) {
                if (!decimalPlaces) {
                    return Math.round(num);
                }
                var p = Math.pow(10, decimalPlaces);
                return Math.round(num * p) / p;
            },

            formatDollar(num) {
                var p = num.toFixed(2).split(".");
                return p[0].split("").reverse().reduce(function(acc, num, i, orig) {
                    return num + (num != "-" && i && !(i % 3) ? "," : "") + acc;
                }, "") + (p[1] ? "." + p[1] : '');
            },
            inputChange(item, type) {
                let qty = parseFloat(item.qty.value);
                let price = parseFloat(item.price.value);
                if ((!qty || qty <= 0) && type == "qty") {
                    item.qty.value = 1;
                }
                if ((!price || price <= 0) && type == "price") {
                    item.price.value = 1;
                }
                this.calcuatorAmount();
            },

            // calcuatorAmount() {
            //     this.sub_total.dollar = 0;
            //     this.sub_total.khmer = 0;
            //     this.vat.dollar = 0;
            //     this.vat.khmer = 0;
            //     this.formSubmitData.total_qty = 0;
            //     this.formSubmitData.total_price = 0;
            //     this.formSubmitData.total_vat = 0;

            //     this.dataForm.forEach(item => {
            //         this.sub_total.dollar += this.numberRound(Number(item.price.value) * Number(item.qty
            //             .value), 2);
            //         item.amount.value = this.numberRound(Number(item.price.value) * Number(item.qty
            //             .value), 2);
            //         this.vat.dollar += this.numberRound(Number(item.amount.value * 0.1), 2);
            //         this.formSubmitData.total_qty += this.numberRound((item.qty.value ? Number(item.qty
            //             .value) : 0), 2);
            //         this.formSubmitData.total_price += Number(item.amount.value);
            //     });

            //     this.formSubmitData.total_vat = this.numberRound(this.vat.dollar, 2);
            //     this.vat.khmer = this.numberRound(this.vat.dollar * this.exhange_rate, 2);
            //     this.grand_total.dollar = this.numberRound((this.vat.dollar + Number(this.sub_total.dollar)),
            //         2);
            //     this.grand_total.khmer = this.numberRound(Number(this.grand_total.dollar * this.exhange_rate),
            //         2);
            //     this.sub_total.khmer = this.numberRound((Number(this.grand_total.khmer) - Number(this.vat
            //         .khmer)), 2);
            // },
            calcuatorAmount() {
                this.sub_total.dollar = 0;
                this.sub_total.khmer = 0;
                this.vat.dollar = 0;
                this.vat.khmer = 0;
                this.formSubmitData.total_qty = 0;
                this.formSubmitData.total_price = 0;
                this.formSubmitData.total_vat = 0;

                this.dataForm.forEach(item => {
                    this.sub_total.dollar += Number(item.price.value) * Number(item.qty.value);
                    item.amount.value = this.numberRound(Number(item.price.value) * Number(item.qty
                        .value), 2);
                    this.vat.dollar += Number(item.amount.value * 0.1);
                    this.formSubmitData.total_qty += item.qty.value ? Number(item.qty.value) : 0;
                    this.formSubmitData.total_price += Number(item.amount.value);
                });
                //dollar
                this.formSubmitData.total_qty = this.numberRound(this.formSubmitData.total_qty, 2);
                this.formSubmitData.total_price = this.numberRound(this.formSubmitData.total_price, 2);

                this.sub_total.dollar = this.numberRound(this.sub_total.dollar, 2);
                this.formSubmitData.total_vat = this.numberRound(this.vat.dollar, 2);
                this.grand_total.dollar = this.numberRound(this.vat.dollar + Number(this.sub_total.dollar), 2);

                //khmer
                this.vat.khmer = this.numberRound(this.vat.dollar * this.exhange_rate);
                this.grand_total.khmer = this.numberRound(Number(this.grand_total.dollar) * Number(this
                    .exhange_rate));
                this.sub_total.khmer = this.numberRound((Number(this.grand_total.khmer) - Number(this.vat
                    .khmer)));
            },

            submitFrom() {
                this.dataError = [];
                this.checkValidation((valid) => {
                    if (valid.length > 0) {
                        return true;
                    } else {
                        this.$store.confirmDialog.open({
                            data: {
                                title: "Message",
                                message: "Are you sure to save?",
                                btnClose: "Close",
                                btnSave: "Yes",
                            },
                            afterClosed: (result) => {
                                if (result) {
                                    this.submitLoading = true;
                                    this.formSubmitData.disable = true;
                                    let issue_date = this.$refs.issue_date.value;
                                    let end_date = this.$refs.end_date.value;
                                    let po_date = this.$refs.po_date.value;
                                    const data = this.formSubmitData;
                                    data.issue_date = issue_date;
                                    data.end_date = end_date;
                                    data.po_date = po_date;
                                    data.dataTable = this.dataForm.length ? JSON.stringify(
                                        this.dataForm) : [];
                                    setTimeout(() => {
                                        Axios({
                                            url: `{{ route('admin-purchase-save') }}`,
                                            method: 'POST',
                                            data: {
                                                ...data,
                                                deleteItemID: this
                                                    .deleteItemID,
                                            }
                                        }).then((res) => {
                                            if (res.data
                                                .message ==
                                                "success") {
                                                this.formSubmitData
                                                    .disable = false;
                                                this.submitLoading = false;
                                                setTimeout(
                                                    () => {
                                                        window.location
                                                            .href =
                                                            '{!! route('admin-purchase-list', 1) !!}';
                                                    }, 100);
                                            }
                                        }).catch((e) => {
                                            this.dataError = e
                                                .response?.data
                                                .errors;
                                            this.formSubmitData.disable =
                                                false;
                                            this.submitLoading =
                                                false;
                                        }).finally(() => {
                                            this.formSubmitData.disable =
                                                false;
                                            this.submitLoading = false;
                                        });
                                    }, 500);
                                }
                            }
                        });
                    }
                });
            },
            appendSelect2HtmlCurrentSelect(select2ID, id, name) {
                var option = "<option selected></option>";
                var optionHTML = $(option).val(id ? id : null).text(name ? name : name);
                $(`#${select2ID}`).append(optionHTML).trigger('change');
            }

        }));
    </script>

@stop
