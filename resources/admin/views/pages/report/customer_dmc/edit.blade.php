<template x-data="{}" x-if="$store.editDialog.active">
    <div class="dialog" x-data="editDialogPage" x-bind:style="{ zIndex: $store.libs.getLastIndex() + 1 }">
        <div class="dialog-container dialogContainerForm">
            <div class="diglogForm" x-bind:style="{ width: $store.editDialog?.config?.width }">
                <div class="dialog-form-header" x-bind:style="{ zIndex: $store.libs.getLastIndex() + 1 }">
                    <h3 x-text="$store.editDialog?.options?.title"></h3>
                    <span class="material-symbols-outlined" @click="dialogClose()">close</span>
                </div>
                <div class="form-admin" id="my-section">
                    <form id="form" class="form-wrapper" enctype="multipart/form-data">
                        <div class="form-body">
                            <div class="row-2">
                                <div class="form-row">
                                    <label>Register Date</label>
                                    <input type="text" x-model="dataForm.register_date" x-ref="register_date" id="register_date" autocomplete="off"
                                        placeholder="Register Date" :disabled="dataForm.disable">
                                </div>
                                <div class="form-row">
                                    <label>Customer ID<span>*</span></label>
                                    <input type="text" x-model="dataForm.customer_code" autocomplete="off" :disabled="dataForm.disable" placeholder="Customer ID">
                                    <template x-for="item in dataError?.customer_code">
                                        <div class="errorCenter">
                                            <span class="error" x-text="item"></span>
                                        </div>
                                    </template>
                                </div>
                                <div class="form-row">
                                    <label>Customer Name<span>*</span></label>
                                    <input type="text" x-model="dataForm.customer_name" autocomplete="off" :disabled="dataForm.disable" placeholder="Customer Name">
                                    <template x-for="item in dataError?.customer_name">
                                        <div class="errorCenter">
                                            <span class="error" x-text="item"></span>
                                        </div>
                                    </template>
                                </div>
                                <div class="form-row">
                                    <label>Po Date</label>
                                    <input type="text" x-model="dataForm.po_date" x-ref="po_date" id="po_date" autocomplete="off"
                                        placeholder="Po Date" :disabled="dataForm.disable">
                                </div>
                                <div class="form-row">
                                    <label>PO Number<span>*</span></label>
                                    <input type="text" x-model="dataForm.po_number" autocomplete="off" :disabled="dataForm.disable" placeholder="PO Number">
                                    <template x-for="item in dataError?.po_number">
                                        <div class="errorCenter">
                                            <span class="error" x-text="item"></span>
                                        </div>
                                    </template>
                                </div>
                                <div class="form-row">
                                    <label>PAC Date / Billing Date</label>
                                    <input type="text" x-model="dataForm.pac_date" x-ref="pac_date" id="pac_date" autocomplete="off"
                                        placeholder="Po Date" :disabled="dataForm.disable">
                                </div>
                                <div class="form-row">
                                    <label>PAC Number<span>*</span></label>
                                    <input type="text" x-model="dataForm.pac_number" autocomplete="off" :disabled="dataForm.disable" placeholder="PAC Number">
                                    <template x-for="item in dataError?.pac_number">
                                        <div class="errorCenter">
                                            <span class="error" x-text="item"></span>
                                        </div>
                                    </template>
                                </div>
                                <div class="form-row">
                                    <label>Products (Leasing Capacity)<span>*</span></label>
                                    <input type="text" x-model="dataForm.service_type" autocomplete="off" :disabled="dataForm.disable" placeholder="Products (Leasing Capacity)">
                                    <template x-for="item in dataError?.service_type">
                                        <div class="errorCenter">
                                            <span class="error" x-text="item"></span>
                                        </div>
                                    </template>
                                </div>
                                <div class="form-row">
                                    <label>Type (Cores or Mbps)<span>*</span></label>
                                    <input type="text" x-model="dataForm.type" autocomplete="off" :disabled="dataForm.disable" placeholder="Type (Cores or Mbps)">
                                    <template x-for="item in dataError?.type">
                                        <div class="errorCenter">
                                            <span class="error" x-text="item"></span>
                                        </div>
                                    </template>
                                </div>
                                <div class="form-row">
                                    <label>QTY (Cores or Mbps)<span>*</span></label>
                                    <input type="text" x-model="dataForm.qty_cores" autocomplete="off" :disabled="dataForm.disable" placeholder="QTY (Cores or Mbps)">
                                    <template x-for="item in dataError?.qty_cores">
                                        <div class="errorCenter">
                                            <span class="error" x-text="item"></span>
                                        </div>
                                    </template>
                                </div>
                                <div class="form-row">
                                    <label>Length (km)<span>*</span></label>
                                    <input type="number" step=".001" x-model="dataForm.length" autocomplete="off" :disabled="dataForm.disable" placeholder="Length (km)">
                                    <template x-for="item in dataError?.length">
                                        <div class="errorCenter">
                                            <span class="error" x-text="item"></span>
                                        </div>
                                    </template>
                                </div>
                                <div class="form-row">
                                    <label>Location<span>*</span></label>
                                    <input type="text"  x-model="dataForm.location" autocomplete="off" :disabled="dataForm.disable" placeholder="location">
                                    <template x-for="item in dataError?.location">
                                        <div class="errorCenter">
                                            <span class="error" x-text="item"></span>
                                        </div>
                                    </template>
                                </div>
                                <div class="form-row">
                                    <label>Status <span>*</span></label>
                                    <select x-model="dataForm.status" :disabled="dataForm.disable">
                                        <option value="1">Active</option>
                                        <option value="2">Deactive</option>
                                    </select>
                                    <template x-for="item in dataError?.status">
                                        <div class="errorCenter">
                                            <span class="error" x-text="item"></span>
                                        </div>
                                    </template>
                                </div>
                                <div class="form-row" x-show="dataForm.status == 2 || dataForm.inactive_date">
                                    <label>Deactive Date</label>
                                    <input type="text" x-model="dataForm.inactive_date" x-ref="inactive_date" id="inactive_date" autocomplete="off"
                                        placeholder="Deactive Date" :disabled="dataForm.disable">
                                </div>
                                <div class="form-row">
                                    <label>Customer Address<span>*</span></label>
                                    <textarea rows="5" x-model="dataForm.customer_address" autocomplete="off" :disabled="dataForm.disable" placeholder="Customer Address"></textarea>
                                    <template x-for="item in dataError?.customer_address">
                                        <div class="errorCenter">
                                            <span class="error" x-text="item"></span>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                        <div class="form-footer">
                            <div style="width: 100%; display: flex; justify-content: space-between;">
                                <div>
                                    <button type="button" class="close" @click="onDelete(dialogData)">
                                        <i class='bx bx-trash bx-tada-hover'></i>
                                        <span>Delete</span>
                                    </button>
                                </div>
                                <div style="display: flex;">
                                    <button type="button" class="close" @click="dialogClose()">
                                        <i class='bx bx-x bx-tada-hover'></i>
                                        <span>Close</span>
                                    </button>
                                    <button class="bg-primary" type="button" @click="submitForm()">
                                        <i class='bx bx-save bx-tada-hover'></i>
                                        <span>Update</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>

                    <template x-if="loading">
                        @include('admin::components.spinner')
                    </template>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    Alpine.data('editDialogPage', () => ({
        loading: false,
        dataForm: {
            disable: false,
            id: null,
            dmc_customer_id: null,
            register_date: null,
            pac_date: null,
            po_date: null,
            customer_code: null,
            customer_name: null,
            po_number: null,
            pac_number: null,
            customer_address: null,
            service_type: null,
            type: null,
            qty_cores: null,
            length: null,
            location: null,
            status: 1,
            inactive_date: null,
        },
        dialogData: null,
        dataError: [],
        async init() {
            let data = this.$store.editDialog.options.data;
            this.dialogData = data;
            this.initDate();
            
            //Patch Value
            this.dataForm.id = data?.id;
            this.dataForm.dmc_customer_id = data?.dmc_customer_id;
            this.dataForm.register_date = data?.register_date ? moment(data?.register_date).format('YYYY/MM/DD') : null;
            this.dataForm.pac_date = data?.pac_date ? moment(data?.pac_date).format('YYYY/MM/DD') : null;
            this.dataForm.po_date = data?.po_date ? moment(data?.po_date).format('YYYY/MM/DD') : null;
            this.dataForm.customer_code = data?.customer_code;
            this.dataForm.customer_name = data?.customer_name;
            this.dataForm.po_number = data?.po_number;
            this.dataForm.pac_number = data?.pac_number;
            this.dataForm.service_type = data?.service_type;
            this.dataForm.type = data?.type;
            this.dataForm.qty_cores = data?.qty_cores;
            this.dataForm.length = data?.length;
            this.dataForm.status = data?.status;
            this.dataForm.inactive_date = data?.inactive_date ? moment(data?.inactive_date).format('YYYY/MM/DD') : null;
            this.dataForm.customer_address = data?.customer_address;
            this.dataForm.location = data?.location;
        },
        dialogClose() {
            this.$store.editDialog.active = false;
        },
        initDate() {
            $("#register_date,#inactive_date,#pac_date,#po_date").datepicker({
                changeYear: true,
                gotoCurrent: true,
                yearRange: "-100:+100",
                dateFormat: "yy/mm/dd",
            });
        },  
        onDelete(data) {
            this.$store.confirmDialog.open({
                data: {
                    title: "Message",
                    message: `Are you sure to delete this record ?`,
                    btnClose: "Close",
                    btnSave: "Delete",
                },
                afterClosed: (result) => {
                    if (result) {
                        Axios({
                            url: `{{ route('admin-report-customer-dmc-delete') }}`,
                            method: 'DELETE',
                            data: {
                                id: data.id
                            }
                        }).then((res) => {
                            Toast({
                                message: res.data.message,
                                status: res.data.status,
                                size: 'small',
                                duration: 5000,
                            });
                            if (res.data?.error == false) {
                                setTimeout(() => {
                                    this.dialogClose();
                                    this.$store.editDialog.options.afterClose(res);
                                }, 1000);
                            }
                        }).catch((e) => {
                            console.log(e);
                        });
                    }
                }
            });
        },
        submitForm() {
            this.dataError = [];
            this.$store.confirmDialog.open({
                data: {
                    title: "Message",
                    message: "Are you sure to update?",
                    btnClose: "Close",
                    btnSave: "Yes",
                },
                afterClosed: (result) => {
                    if (result) {
                        this.dataForm.disable = true;
                        this.loading = true;
                        this.dataForm.inactive_date = this.$refs.inactive_date.value;
                        this.dataForm.register_date = this.$refs.register_date.value;
                        this.dataForm.pac_date = this.$refs.pac_date.value;
                        this.dataForm.po_date = this.$refs.po_date.value;
                        let formData = this.dataForm;
                        setTimeout(() => {
                            Axios.post(`{{ route('admin-report-customer-dmc-edit') }}`, formData)
                            .then((res) => {
                                Toast({
                                    message: res.data.message,
                                    status: res.data.status,
                                    size: 'small',
                                    duration: 5000,
                                });
                                if (res.data?.error == false) {
                                    setTimeout(() => {
                                        this.dialogClose();
                                        this.$store.editDialog.options.afterClose(res);
                                    }, 1000);
                                }
                                this.loading = false;
                                this.dataForm.disable = false;
                            }).catch((e) => {
                                this.dataError = e.response?.data.errors;
                                this.loading = false;
                                this.dataForm.disable = false;
                            }).finally(() => {
                                this.loading = false;
                                this.dataForm.disable = false;
                            });
                        }, 500);
                    }
                }
            });
        },
    }));
</script>

{{-- store --}}
<script>
    Alpine.store('editDialog', {
        active: false,
        dmcBtn: false,
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
    window.editDialog = (options) => {
        Alpine.store('editDialog', {
            active: true,
            config: options?.config ?? null,
            options: {
                ...Alpine.store('editDialog').options,
                ...options
            }
        });
    };
</script>
