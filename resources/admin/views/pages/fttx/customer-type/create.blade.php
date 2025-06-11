<template x-data="{}" x-if="$store.storeCustomerTypeDialog.active">
    <div class="dialog" x-data="xInsertCustomerType" x-bind:style="{ zIndex: $store.libs.getLastIndex() + 1 }">
        <div class="dialog-container dialogContainerForm">
            <div class="diglogForm" :style="{ width: $store.storeCustomerTypeDialog.options.config.width }">
                <div class="dialog-form-header" x-bind:style="{ zIndex: $store.libs.getLastIndex() + 1 }">
                    <h3 x-show="!dialogData">Create Customer Type</h3>
                    <h3 x-show="dialogData">Edit Customer Type</h3>
                    <span class="material-symbols-outlined" @click="dialogClose()">close</span>
                </div>
                <div class="form-admin" id="my-section">
                    <form id="form" class="form-wrapper" action="#">
                        <div class="form-body">
                            <div class="row-2">
                                <div class="form-row">
                                    <label>Name<span>*</span></label>
                                    <input type="text" x-model="formSubmitData.name" placeholder="Enter name">
                                    <template x-for="item in dataError?.name">
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
    Alpine.data('xInsertCustomerType', () => ({
        submitLoading: false,
        dataError: [],
        formSubmitData: {
            name: null,
            status: 1,
            description: null,
            disable: false
        },
        dialogData: null,
        async init() {
            let data = this.$store.storeCustomerTypeDialog.options.data;
            this.dialogData = data;
            if (this.dialogData) {
                this.submitLoading = true;
                this.formSubmitData.name = this.dialogData.name;
                this.formSubmitData.description = this.dialogData.description;
                this.formSubmitData.status = this.dialogData.status;
                setTimeout(() => {
                    this.submitLoading = false;
                }, "500");
            }
        },

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
                        let data = this.formSubmitData ?? {};

                        setTimeout(() => {
                            Axios({
                                url: `{{ route('admin-customer-type-save') }}`,
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
        dialogClose() {
            this.$store.storeCustomerTypeDialog.active = false;
        },
    }));
</script>
<script>
    Alpine.store('storeCustomerTypeDialog', {
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
    window.storeCustomerTypeDialog = (options) => {
        Alpine.store('storeCustomerTypeDialog', {
            active: true,
            options: {
                ...Alpine.store('storeCustomerTypeDialog').options,
                ...options
            }
        });
    };
</script>
