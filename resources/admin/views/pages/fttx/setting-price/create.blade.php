<style>
    .form-admin .form-wrapper .form-body .form-row button {
        width: 25px;
        height: 25px;
        min-width: 25px;
        min-height: 25px;
        margin: 0;
        padding: 0;
        display: flex;
        justify-content: center;
        align-items: center;
        border-radius: 50%;
        cursor: pointer;

    }

    .form-admin .form-wrapper .form-body .form-row button i {
        font-size: 18px;
    }

    .form-admin .form-wrapper .form-body .form-row button.add {
        background: #1266f1;
    }

    .form-admin .form-wrapper .form-body .form-row div button.remove {
        background: red;
    }

    .form-admin .form-wrapper .form-body .form-row div input {
        background-color: #fff;
        border-radius: 4px;
        border: 1px solid #d8dce5;
        box-sizing: border-box;
        color: #5a5e66;
        display: inline-block;
        font-size: 14px;
        height: 40px;
        line-height: 1;
        outline: 0;
        padding: 0 15px;
        transition: border-color 0.2s cubic-bezier(0.645, 0.045, 0.355, 1);
        width: 100%;
    }
</style>
<template x-data="{}" x-if="$store.storeSettingPriceDialog.active">
    <div class="dialog" x-data="xInsertSettingPrice" x-bind:style="{ zIndex: $store.libs.getLastIndex() + 1 }">
        <div class="dialog-container dialogContainerForm">
            <div class="diglogForm" :style="{ width: $store.storeSettingPriceDialog.options.config.width }">
                <div class="dialog-form-header" x-bind:style="{ zIndex: $store.libs.getLastIndex() + 1 }">
                    <h3 x-show="!dialogData">Create Setting Price</h3>
                    <h3 x-show="dialogData">Edit Setting Price</h3>
                    <span class="material-symbols-outlined" @click="dialogClose()">close</span>
                </div>
                <div class="form-admin" id="my-section">
                    <form id="form" class="form-wrapper" action="#">
                        <div class="form-body">
                            <div class="row-3">
                                <div class="form-row">
                                    <label>Type<span>*</span></label>
                                    <select x-model="formSubmitData.type">
                                        <option value="">Select type...</option>
                                        @foreach (config('dummy.setting_price_type') as $type)
                                            <option value="{{ $type['key'] }}">{{ $type['text'] }}</option>
                                        @endforeach
                                    </select>
                                    
                                    <template x-for="item in dataError?.type">
                                        <div class="errorCenter">
                                            <span style="position: static;" class="error" x-text="item">Error</span>
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
                                <div class="form-row" style="display: inline">
                                    <span style="display: flex;justify-content: space-between;">
                                        <label>Price<span>*</span></label>
                                        <button type="button" class="add" @click="addInput('price')">
                                            <i class="material-symbols-outlined">add</i>
                                        </button>
                                    </span>
                                    <template x-if="prices.length > 0">
                                        <template x-for="(item, index) in prices" :key="index">
                                            <div :style="index > 0 ? { marginTop: '12px' } : {}">
                                                <template x-if="index > 0">
                                                    <button style="float: right;" type="button" class="remove"
                                                        @click="removeInput(index,'price')">
                                                        <i class="material-symbols-outlined">delete</i>
                                                    </button>
                                                </template>
                                                <input type="number" x-model="prices[index]"
                                                    placeholder="Enter price">
                                            </div>
                                        </template>
                                    </template>
                                    <template x-for="item in dataError?.price">
                                        <div class="errorCenter">
                                            <span style="position: static;" class="error" x-text="item">Error</span>
                                        </div>
                                    </template>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-row">
                                    <label>Description</label>
                                    <textarea x-model="formSubmitData.description" rows="5" placeholder="Enter description"></textarea>
                                </div>
                            </div>
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
    Alpine.data('xInsertSettingPrice', () => ({
        submitLoading: false,
        dataError: [],
        formSubmitData: {
            price: null,
            type: null,
            description: null,
            status: 1,
            disable: false
        },
        dialogData: null,
        prices: [''],
        async init() {
            let data = this.$store.storeSettingPriceDialog.options.data;
            this.dialogData = data;
            if (this.dialogData) {
                this.submitLoading = true;
                this.prices = this.dialogData.price;
                this.formSubmitData.type = this.dialogData.type;
                this.formSubmitData.description = this.dialogData.description;
                this.formSubmitData.status = this.dialogData.status;
                setTimeout(() => {
                    this.submitLoading = false;
                }, "500");
            }
        },

        submitFrom() {
            this.formSubmitData.price = this.checkArray(this.prices) == false ? this.prices : '';
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
                                url: `{{ route('admin-setting-price-save') }}`,
                                method: 'POST',
                                data: {
                                    ...data,
                                    id: id,
                                }
                            }).then((res) => {
                                console.log(res);

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
                                console.log(this.dataError);

                                this.submitLoading = false;


                            }).finally(() => {
                                this.submitLoading = false;

                            });
                        }, 500);
                    }
                }
            });
        },
        addInput(type) {
            if (type == 'price') {
                this.prices.push('');
            }
        },
        removeInput(index, type) {
            if (type == 'price') {
                this.prices.splice(index, 1);
            } 
        },
        checkArray(array) {
            return array.every(price => price === '' || price === null);
        },
        dialogClose() {
            this.$store.storeSettingPriceDialog.active = false;
        },
    }));
</script>
<script>
    Alpine.store('storeSettingPriceDialog', {
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
    window.storeSettingPriceDialog = (options) => {
        Alpine.store('storeSettingPriceDialog', {
            active: true,
            options: {
                ...Alpine.store('storeSettingPriceDialog').options,
                ...options
            }
        });
    };
</script>
