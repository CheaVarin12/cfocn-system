<template x-data="{}" x-if="$store.storeCustomerPriceDialog.active">
    <div class="dialog" x-data="xInsertCustomerPrice" x-bind:style="{ zIndex: $store.libs.getLastIndex() + 1 }">
        <div class="dialog-container dialogContainerForm">
            <div class="diglogForm" style="width: 80%;">
                <div class="dialog-form-header" x-bind:style="{ zIndex: $store.libs.getLastIndex() + 1 }">
                    <h3 x-show="!dialogData">Create Customer Price</h3>
                    <h3 x-show="dialogData">Edit Customer Price</h3>
                    <span class="material-symbols-outlined" @click="dialogClose()">close</span>
                </div>
                <div class="form-admin" id="my-section">
                    <form id="form" class="form-wrapper" action="#">
                        <div class="form-body">
                            <div class="row-3">
                                <div class="form-row">
                                    <label>Isp Name <span>*</span></label>
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
                                    <label>@lang('user.form.status.label')<span>*</span></label>
                                    <select x-model="formSubmitData.status">
                                        <option value="1">Active</option>
                                        <option value="2">Inactive</option>
                                    </select>
                                </div>
                                <div class="form-row">
                                    <label>New Install Price (0-350m)</label>
                                    <input type="text" x-model="formSubmitData.new_install_price_first_level" placeholder="Enter new install price">
                                    <template x-for="item in dataError?.new_install_price_first_level">
                                        <div class="errorCenter">
                                            <span class="error" x-text="item">Error</span>
                                        </div>
                                    </template>
                                </div>
                            </div>
                            <div class="row-3">
                                <div class="form-row">
                                    <label>New Install Price (351-500m)</label>
                                    <input type="text" x-model="formSubmitData.new_install_price_second_level" placeholder="Enter new install price">
                                    <template x-for="item in dataError?.new_install_price_second_level">
                                        <div class="errorCenter">
                                            <span class="error" x-text="item">Error</span>
                                        </div>
                                    </template>
                                </div>
                                <div class="form-row">
                                    <label>New Install Price (501-800m)</label>
                                    <input type="text" x-model="formSubmitData.new_install_price_third_level" placeholder="Enter new install price">
                                    <template x-for="item in dataError?.new_install_price_third_level">
                                        <div class="errorCenter">
                                            <span class="error" x-text="item">Error</span>
                                        </div>
                                    </template>
                                </div>
                                <div class="form-row">
                                    <label>New Install Price (over 800m (prince/meter))</label>
                                    <input type="text" x-model="formSubmitData.new_install_price_fourth_level" placeholder="Enter new install price">
                                    <template x-for="item in dataError?.new_install_price_fourth_level">
                                        <div class="errorCenter">
                                            <span class="error" x-text="item">Error</span>
                                        </div>
                                    </template>
                                </div>
                            </div>
                            <template x-for="(posSpeed, index) in posSpeedDataLoop">
                                <div>
                                    <div class="form-row row-pos-speed">
                                        <label x-text="'Pos Speed ('+ posSpeed.split_pos + ')'"></label>
                                    </div>
                                    <div class="div-pos-speed">
                                        <div class="row-2 pos-speed-input">
                                            <input type="hidden" x-model="getPosSpeed(posSpeed.id).pos_speed_id">
                                            <div class="form-row" style="margin-bottom: 0px;">
                                                <label>Rental Price</label>
                                                <input type="number"
                                                    x-model="getPosSpeed(posSpeed.id).rental_price"
                                                    placeholder="Enter rental price">
                                            </div>
                                            <div class="form-row">
                                                <label>Start Date</label>
                                                <input id="dateInput" type="date" 
                                                    x-model="getPosSpeed(posSpeed.id).start_date_current_price">
                                            </div>
                                        </div>
 
                                        <div class="form-row" style="margin-bottom:0;margin-left: 14px;">
                                            <label>History Price</label>
                                        </div>
                                        <div class="row" style="padding: 0px 0px 7px 14px ">
                                            <div class="table customTable" style="padding-right: 15px;margin: 0px 0;">
                                                <div class="table-wrapper">
                                                    <div class="table-header" style="height: auto;">
                                                        <div class="row table-row-5"><span>Nº</span></div>
                                                        <div class="row table-row-15 text-start"><span>Rental price</span></div>
                                                        <div class="row table-row-15 text-start"><span>Start Date</span></div>
                                                        <div class="row table-row-15 text-start"><span>End Date</span></div>
                                                        <div class="row table-row-35 text-start"><span>Description</span></div>
                                                        <div class="row table-row-15 text-end"><span>Action</span></div>
                                                    </div>
                                                    <div class="table-body">
                                                        <template x-for="(item, index) in dataForm[posSpeed.id]">
                                                            <div class="column">
                                                                <div class="row table-row-5 no"><span
                                                                        x-text="index + 1"></span></div>
                                                                <div class="row table-row-15 text-start input-text">
                                                                    <input type="number"
                                                                        :disabled="formSubmitData.disable"
                                                                        x-model="item.rental_price.value"
                                                                        placeholder="Rental price..."
                                                                        class="input-table">
                                                                </div>
                                                                <div class="row table-row-15 text-start input-date">
                                                                    <input type="date"
                                                                        :disabled="formSubmitData.disable"
                                                                        x-model="item.start_date.value"
                                                                        class="input-table">
                                                                </div>
                                                                <div class="row table-row-15 text-start input-date">
                                                                    <input type="date"
                                                                        :disabled="formSubmitData.disable"
                                                                        x-model="item.end_date.value"
                                                                        class="input-table">
                                                                </div>
                                                                <div class="row table-row-35 text-start input-text">
                                                                    <span>
                                                                        <textarea x-model="item.description.value"
                                                                            :disabled="formSubmitData.disable" placeholder="Description..."></textarea>
                                                                    </span>
                                                                </div>
                                                                <div class="row table-row-15 text-start action">
                                                                    <span>
                                                                        <button type="button" class="delete"
                                                                            @click="removeInput(index, posSpeed.id)"
                                                                            :disabled="formSubmitData.disable">
                                                                            <i
                                                                                class="material-symbols-outlined">delete</i>
                                                                        </button>
                                                                        <template
                                                                            x-if="index === dataForm[posSpeed.id].length - 1">
                                                                            <button type="button" class="add"
                                                                                @click="addItem(index, posSpeed.id)"
                                                                                :disabled="formSubmitData.disable">
                                                                                <i
                                                                                    class="material-symbols-outlined">add</i>
                                                                            </button>
                                                                        </template>
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        </template>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                        <div class="form-footer">
                            <button type="button" class="primary" color="primary" @click="submitFrom()">
                                <i class='bx bx-save'></i>
                                <span>Save</span>
                            </button>
                            <button type="button" class="close" @click="dialogClose()">
                                <i class='bx bx-x'></i>
                                <span>Close</span>
                            </button>
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
    Alpine.data('xInsertCustomerPrice', () => ({
        submitLoading: false,
        dataError: [],
        posSpeedDataLoop: @json($posSpeeds) ?? [],
        formSubmitData: {
            customer_id: null,
            new_install_price_first_level: null,
            new_install_price_second_level: null,
            new_install_price_third_level: null,
            new_install_price_fourth_level: null,
            status: 1,
            disable: false,
            pos_speeds: [],
        },
        customer_id_err: {
            empty: false,
            incorrect: false
        },
        dataForm: {},
        dialogData: null,
        async init() {
            let data = this.$store.storeCustomerPriceDialog.options.data;
            this.dialogData = data;
            this.submitLoading = true;

            if (this.dialogData) {
                let newInstallPrice = JSON.parse(this.dialogData.new_install_price);
                // Editing existing data
                this.formSubmitData.customer_id = this.dialogData.customer_id;
                this.formSubmitData.new_install_price_first_level =newInstallPrice.first;
                this.formSubmitData.new_install_price_second_level =newInstallPrice.second;
                this.formSubmitData.new_install_price_third_level =newInstallPrice.third;
                this.formSubmitData.new_install_price_fourth_level =newInstallPrice.fourth;
                this.formSubmitData.status = this.dialogData.status;
                this.appendSelect2HtmlCurrentSelect('customer_id', data?.customer?.id, data?.customer
                    ?.name_en);

                this.initializePosSpeed(JSON.parse(data.pos_speeds) || []);
            } else {
                this.posSpeedDataLoop.forEach(posSpeed => {
                    this.dataForm[posSpeed.id] = [{
                        rental_price: {
                            value: '',
                        },
                        new_install_price_first_level: {
                            value: '',
                        },
                        new_install_price_second_level: {
                            value: '',
                        },
                        new_install_price_third_level: {
                            value: '',
                        },
                        new_install_price_fourth_level: {
                            value: '',
                        },
                        start_date: {
                            value: '',
                        },
                        end_date: {
                            value: '',
                        },
                        description: {
                            value: '',
                        },
                    }];
                });
            }

            setTimeout(() => {
                this.submitLoading = false;
            }, 500);
        },
        initializePosSpeed(posSpeeds) {
            let posSpeedValue = posSpeeds[0];
            this.formSubmitData.pos_speeds = posSpeedValue.map(posSpeed => ({
                pos_speed_id: posSpeed.pos_speed_id,
                rental_price: posSpeed.rental_price || '',
                start_date_current_price: posSpeed.start_date_current_price || '',
            }));

            // Populate historical pricing data
            posSpeedValue.forEach(posSpeed => {
                if (!posSpeed.dataTable || posSpeed.dataTable.length === 0) {
                    this.dataForm[posSpeed.pos_speed_id] = [{
                        rental_price: {
                            value: '',
                        },
                        new_install_price_first_level: {
                            value: '',
                        },
                        new_install_price_second_level: {
                            value: '',
                        },
                        new_install_price_third_level: {
                            value: '',
                        },
                        new_install_price_fourth_level: {
                            value: '',
                        },
                        start_date: {
                            value: '',
                        },
                        end_date: {
                            value: '',
                        },
                        description: {
                            value: '',
                        }
                    }];
                } else {
                    this.dataForm[posSpeed.pos_speed_id] = posSpeed.dataTable.map(history => ({
                        rental_price: {
                            value: history.rental_price.value || '',
                        },
                        new_install_price_first_level: {
                            value: history.new_install_price_first_level.value || '',
                        },
                        new_install_price_second_level: {
                            value: history.new_install_price_second_level.value || '',
                        },
                        new_install_price_third_level: {
                            value: history.new_install_price_third_level.value || '',
                        },
                        new_install_price_fourth_level: {
                            value: history.new_install_price_fourth_level.value || '',
                        },
                        start_date: {
                            value: history.start_date.value || '',
                        },
                        end_date: {
                            value: history.end_date.value || '',
                        },
                        description: {
                            value: history.description.value || '',
                        }
                    }));
                }
            });
        },

        getPosSpeed(posSpeedId) {
            let posSpeed = this.formSubmitData.pos_speeds.find(p => p.pos_speed_id == posSpeedId);
            if (!posSpeed) {
                posSpeed = {
                    pos_speed_id: posSpeedId,
                    rental_price: '',
                    start_date_current_price: ''
                };
                this.formSubmitData.pos_speeds.push(posSpeed);
            }
            return posSpeed;
        },
        addItem(index, posSpeedId) {
            this.dataForm[posSpeedId].push({
                rental_price: {
                    value: '',
                },
                new_install_price_first_level: {
                    value: '',
                },
                new_install_price_second_level: {
                    value: '',
                },
                new_install_price_third_level: {
                    value: '',
                },
                new_install_price_fourth_level: {
                    value: '',
                },
                start_date: {
                    value: '',
                },
                end_date: {
                    value: '',
                },
                description: {
                    value: '',
                },
            });
        },
        removeInput(index, id) {
            if (this.dataForm[id].length > 1) {
                this.dataForm[id].splice(index, 1);
            }
        },
        // Submit function
        submitFrom() {
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

                        let formData = {
                            id: id,
                            customer_id: this.formSubmitData.customer_id,
                            new_install_price_first_level: this.formSubmitData.new_install_price_first_level,
                            new_install_price_second_level: this.formSubmitData.new_install_price_second_level,
                            new_install_price_third_level: this.formSubmitData.new_install_price_third_level,
                            new_install_price_fourth_level: this.formSubmitData.new_install_price_fourth_level,
                            status: this.formSubmitData.status,
                            pos_speeds: this.formSubmitData.pos_speeds.map(posSpeed => ({
                                ...posSpeed,
                                dataTable: this.dataForm[posSpeed
                                    .pos_speed_id] || []
                            }))
                        };

                        Axios.post(`{{ route('admin-customer-price-save') }}`, formData)
                            .then((res) => {
                                this.submitLoading = false;

                                if (res.data.status === "success") {
                                    this.dialogClose();
                                    reloadUrl("{!! url()->current() !!}");
                                    Toast({
                                        title: 'Fttx',
                                        message: res.data.message,
                                        status: 'success',
                                        size: 'small',
                                    });
                                }
                            })
                            .catch((e) => {
                                this.dataError = e.response?.data.errors;
                                console.error("Error:", this.dataError);
                                this.submitLoading = false;
                            })
                            .finally(() => {
                                this.submitLoading = false;
                            });
                    }
                }
            });
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
        dialogClose() {
            this.$store.storeCustomerPriceDialog.active = false;
        },
    }));
</script>
<script>
    Alpine.store('storeCustomerPriceDialog', {
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
    window.storeCustomerPriceDialog = (options) => {
        Alpine.store('storeCustomerPriceDialog', {
            active: true,
            options: {
                ...Alpine.store('storeCustomerPriceDialog').options,
                ...options
            }
        });
    };
</script>
