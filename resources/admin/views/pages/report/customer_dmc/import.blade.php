<template x-data="{}" x-if="$store.importCustomer.active">
    <div class="dialog" x-data="xImportCustomer" x-bind:style="{ zIndex: $store.libs.getLastIndex() + 1 }">
        <div class="dialog-container dialogContainerForm">
            <div class="diglogForm" x-bind:style="{ width: $store.importCustomer?.config?.width }">
                <div class="dialog-form-header" x-bind:style="{ zIndex: $store.libs.getLastIndex() + 1 }">
                    <h3 x-text="$store.importCustomer?.options?.title"></h3>
                    <span class="material-symbols-outlined" @click="dialogClose()">close</span>
                </div>
                <div class="form-admin" id="my-section">
                    <form id="form" class="form-wrapper" enctype="multipart/form-data">
                        <div class="form-body">
                            <div class="row-2">
                                <div class="form-row">
                                    <label>Project <span>*</span></label>
                                    <select name="project" id="project">
                                        <option value="">Select Project</option>
                                        <template x-for="(project, index) in projects">
                                            <option :value="project.id" x-text="project.name"></option>
                                        </template>
                                    </select>
                                    <template x-for="item in dataError?.project_id">
                                        <div class="errorCenter">
                                            <span class="error" x-text="item"></span>
                                        </div>
                                    </template>
                                </div>
                                <div class="form-row">
                                    <label>Customer File (xls, xlsx)<span>*</span></label>
                                    <input type="file" id="customer_file" name="customer_file" autocomplete="off"
                                        :disabled="dataForm.disable" style="padding: 10px;" accept=".xls,.xlsx">
                                    <template x-for="item in dataError?.customer_file">
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
    Alpine.data('xImportCustomer', () => ({
        loading: false,
        dataForm: {
            disable: false,
        },
        dataError: [],
        projects: [],
        async init() {
            let dataStore = this.$store.importCustomer.options.data;
            this.fetchProject((res) => {
                this.projects = res.data;
            });
        },
        dialogClose() {
            this.$store.importCustomer.active = false;
        },
        async fetchProject(callback) {
            await fetch(`{{ route('admin-select-project') }}`, {
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
        submitFrom() {
            let dataStore = this.$store.importCustomer.options.data;
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
                        let projectId = document.getElementById('project').value;
                        let file = document.querySelector('#customer_file');
                        let customer_file = file.files[0] == undefined ? '' : file.files[0];
                        const formData = new FormData();
                        formData.append('project_id', projectId);
                        formData.append('customer_file', customer_file);
                        setTimeout(() => {
                            Axios.post(`{{ route('admin-report-customer-dmc-import-excel') }}`, formData, {
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
                                        this.$store.importCustomer.options.afterClose(res);
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
    Alpine.store('importCustomer', {
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
    window.importCustomer = (options) => {
        Alpine.store('importCustomer', {
            active: true,
            config: options?.config ?? null,
            options: {
                ...Alpine.store('importCustomer').options,
                ...options
            }
        });
    };
</script>
