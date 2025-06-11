<template x-data="{}" x-if="$store.importPAC.active">
    <div class="dialog" x-data="ximportPAC" x-bind:style="{ zIndex: $store.libs.getLastIndex() + 1 }">
        <div class="dialog-container dialogContainerForm">
            <div class="diglogForm" x-bind:style="{ width: $store.importPAC?.config?.width }">
                <div class="dialog-form-header" x-bind:style="{ zIndex: $store.libs.getLastIndex() + 1 }">
                    <h3 x-text="$store.importPAC?.options?.title"></h3>
                    <span class="material-symbols-outlined" @click="dialogClose()">close</span>
                </div>
                <div class="form-admin" id="my-section">
                    <form id="form" class="form-wrapper" enctype="multipart/form-data">
                        <div class="form-body">
                            <div class="row">
                                <div class="form-row">
                                    <label>PAC File (xls, xlsx)<span>*</span></label>
                                    <input type="file" id="pac_file" name="pac_file" autocomplete="off"
                                        :disabled="dataForm.disable" style="padding: 10px;" accept=".xls,.xlsx">
                                    <template x-for="item in dataError?.pac_file">
                                        <div class="errorCenter">
                                            <span class="error" x-text="item"></span>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                        <div class="form-footer">
                            <button class="bg-success" type="button" @click="submitFrom()">
                                <i class='bx bx-cloud-upload bx-tada-hover'></i>
                                <span>Import</span>
                            </button>
                            <button type="button" class="close" @click="dialogClose()">
                                <i class='bx bx-x bx-tada-hover'></i>
                                <span>Close</span>
                            </button>
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
    Alpine.data('ximportPAC', () => ({
        loading: false,
        des_reason: null,
        dataForm: {
            disable: false,
        },
        dataError: [],
        async init() {
            let dataStore = this.$store.importPAC.options.data;
        },
        dialogClose() {
            this.$store.importPAC.active = false;
        },
        submitFrom() {
            let dataStore = this.$store.importPAC.options.data;
            this.dataError = [];
            this.$store.confirmDialog.open({
                data: {
                    title: "Message",
                    message: "Are you sure to import?",
                    btnClose: "Close",
                    btnSave: "Yes",
                },
                afterClosed: (result) => {
                    if (result) {
                        this.dataForm.disable = true;
                        this.loading = true;
                        let file = document.querySelector('#pac_file');
                        let pac_file = file.files[0] == undefined ? '' : file.files[0];
                        const formData = new FormData();
                        formData.append('pac_file', pac_file);
                        setTimeout(() => {
                            Axios.post(`{{ route('admin-purchase-import-excel') }}`, formData, {
                                headers: {
                                    'Content-Type': 'multipart/form-data',
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
                                        this.$store.importPAC.options.afterClose(res);
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
    Alpine.store('importPAC', {
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
    window.importPAC = (options) => {
        Alpine.store('importPAC', {
            active: true,
            config: options?.config ?? null,
            options: {
                ...Alpine.store('importPAC').options,
                ...options
            }
        });
    };
</script>
