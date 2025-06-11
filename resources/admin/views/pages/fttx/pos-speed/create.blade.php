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
<template x-data="{}" x-if="$store.storePosSpeedDialog.active">
    <div class="dialog" x-data="xInsertPosSeed" x-bind:style="{ zIndex: $store.libs.getLastIndex() + 1 }">
        <div class="dialog-container dialogContainerForm">
            <div class="diglogForm" :style="{ width: $store.storePosSpeedDialog.options.config.width }">
                <div class="dialog-form-header" x-bind:style="{ zIndex: $store.libs.getLastIndex() + 1 }">
                    <h3 x-show="!dialogData">Create Pos Speed</h3>
                    <h3 x-show="dialogData">Edit Pos Speed</h3>
                    <span class="material-symbols-outlined" @click="dialogClose()">close</span>
                </div>
                <div class="form-admin" id="my-section">
                    <form id="form" class="form-wrapper" action="#">
                        <div class="form-body">
                            <div class="row-3">
                                <div class="form-row" style="flex: 2;">
                                    <label>Split (POS)<span>*</span></label>
                                    <input type="text" x-model="formSubmitData.split_pos"
                                        placeholder="Enter split (POS)">
                                    <template x-for="item in dataError?.split_pos">
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
                            </div>
                            <div class="row">
                                <div class="form-row">
                                    <label>Description</label>
                                    <textarea x-model="formSubmitData.description" rows="5" placeholder="Enter description"></textarea>
                                </div>
                            </div>
                            <div class="row-3">
                                <div class="form-row" style="display: inline">
                                    <span style="display: flex;justify-content: space-between;">
                                        <label>Rental Price<span>*</span></label>
                                        <button type="button" class="add" @click="addInput('rental_price')">
                                            <i class="material-symbols-outlined">add</i>
                                        </button>
                                    </span>
                                    <template x-if="rental_prices.length > 0">
                                        <template x-for="(item, index) in rental_prices" :key="index">
                                            <div :style="index > 0 ? { marginTop: '12px' } : {}">
                                                <template x-if="index > 0">
                                                    <button style="float: right;" type="button" class="remove"
                                                        @click="removeInput(index,'rental_price')">
                                                        <i class="material-symbols-outlined">delete</i>
                                                    </button>
                                                </template>
                                                <input type="number" x-model="rental_prices[index]"
                                                    placeholder="Enter rental price">
                                            </div>
                                        </template>
                                    </template>
                                    <template x-for="item in dataError?.rental_price">
                                        <div class="errorCenter">
                                            <span style="position: static;" class="error" x-text="item">Error</span>
                                        </div>
                                    </template>
                                </div>
                                <div class="form-row" style="display: inline">
                                    <span style="display: flex;justify-content: space-between;">
                                        <label>PPCC Price<span>*</span></label>
                                        <button type="button" class="add" @click="addInput('ppcc_price')">
                                            <i class="material-symbols-outlined">add</i>
                                        </button>
                                    </span>
                                    <template x-if="ppcc_prices.length > 0">
                                        <template x-for="(item, index) in ppcc_prices" :key="index">
                                            <div :style="index > 0 ? { marginTop: '12px' } : {}">
                                                <template x-if="index > 0">
                                                    <button style="float: right;" type="button" class="remove"
                                                        @click="removeInput(index,'ppcc_price')">
                                                        <i class="material-symbols-outlined">delete</i>
                                                    </button>
                                                </template>
                                                <input type="number" x-model="ppcc_prices[index]"
                                                    placeholder="Enter ppcc price">
                                            </div>
                                        </template>
                                    </template>
                                    <template x-for="item in dataError?.ppcc_price">
                                        <div class="errorCenter">
                                            <span style="position: static;" class="error" x-text="item">Error</span>
                                        </div>
                                    </template>
                                </div>
                                <div class="form-row" style="display: inline">
                                    <span style="display: flex;justify-content: space-between;">
                                        <label>New Install Price<span>*</span></label>
                                        <button type="button" class="add" @click="addInput('new_install_price')">
                                            <i class="material-symbols-outlined">add</i>
                                        </button>
                                    </span>
                                    <template x-if="new_install_prices.length > 0">
                                        <template x-for="(item, index) in new_install_prices" :key="index">
                                            <div :style="index > 0 ? { marginTop: '12px' } : {}">
                                                <template x-if="index > 0">
                                                    <button style="float: right;" type="button" class="remove"
                                                        @click="removeInput(index,'new_install_price')">
                                                        <i class="material-symbols-outlined">delete</i>
                                                    </button>
                                                </template>
                                                <input type="number" x-model="new_install_prices[index]"
                                                    placeholder="Enter new install price">
                                            </div>
                                        </template>
                                    </template>
                                    <template x-for="item in dataError?.new_install_price">
                                        <div class="errorCenter">
                                            <span style="position: static;" class="error" x-text="item">Error</span>
                                        </div>
                                    </template>
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
    Alpine.data('xInsertPosSeed', () => ({
        submitLoading: false,
        dataError: [],
        formSubmitData: {
            split_pos: null,
            rental_price: null,
            ppcc_price: null,
            new_install_price: null,
            description: null,
            status: 1,
            disable: false
        },
        dialogData: null,
        rental_prices: [''],
        ppcc_prices: [''],
        new_install_prices: [''],
        async init() {
            let data = this.$store.storePosSpeedDialog.options.data;
            this.dialogData = data;
            if (this.dialogData) {
                this.submitLoading = true;
                this.formSubmitData.split_pos = this.dialogData.split_pos;
                this.rental_prices = this.dialogData.rental_price;
                this.ppcc_prices = this.dialogData.ppcc_price;
                this.new_install_prices = this.dialogData.new_install_price;
                this.formSubmitData.description = this.dialogData.description;
                this.formSubmitData.status = this.dialogData.status;
                setTimeout(() => {
                    this.submitLoading = false;
                }, "500");
            }
        },

        submitFrom() {
            this.formSubmitData.rental_price = this.checkArray(this.rental_prices) == false ? this.rental_prices : '';
            this.formSubmitData.ppcc_price = this.checkArray(this.ppcc_prices) == false ? this.ppcc_prices :'';
            this.formSubmitData.new_install_price = this.checkArray(this.new_install_prices) == false ? this.new_install_prices : '';
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
                                url: `{{ route('admin-pos-speed-save') }}`,
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
            if (type == 'rental_price') {
                this.rental_prices.push('');
            } else if (type == 'ppcc_price') {
                this.ppcc_prices.push('');
            } else if (type == 'new_install_price') {
                this.new_install_prices.push('');
            }
        },
        removeInput(index, type) {
            if (type == 'rental_price') {
                this.rental_prices.splice(index, 1);
            } else if (type == 'ppcc_price') {
                this.ppcc_prices.splice(index, 1);
            } else if (type == 'new_install_price') {
                this.new_install_prices.splice(index, 1);
            }
        },
        checkArray(array) {
            return array.every(price => price === '' || price === null);
        },
        dialogClose() {
            this.$store.storePosSpeedDialog.active = false;
        },
    }));
</script>
<script>
    Alpine.store('storePosSpeedDialog', {
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
    window.storePosSpeedDialog = (options) => {
        Alpine.store('storePosSpeedDialog', {
            active: true,
            options: {
                ...Alpine.store('storePosSpeedDialog').options,
                ...options
            }
        });
    };
</script>
