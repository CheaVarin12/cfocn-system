<template x-data="{}" x-if="$store.addReason.active">
    <div class="dialog" x-data="xAddReason" x-bind:style="{ zIndex: $store.libs.getLastIndex() + 1 }">
        <div class="dialog-container dialogContainerForm">
            <div class="diglogForm" x-bind:style="{ width: $store.addReason?.config?.width }">
                <div class="dialog-form-header" x-bind:style="{ zIndex: $store.libs.getLastIndex() + 1 }">
                    <h3 x-text="$store.addReason?.options?.title"></h3>
                    <span class="material-symbols-outlined" @click="dialogClose()">close</span>
                </div>
                <div class="form-admin" id="my-section">
                    <form id="form" class="form-wrapper" action="#" method="POST">
                        <div class="form-body">
                            <div class="row">
                                <div class="form-row">
                                    <label>Descirption<span>*</span></label>
                                    <input type="text" name="des_reason" x-model="des_reason" placeholder="Enter des reason ...">
                                </div>
                            </div>
                        </div>
                        <div class="form-footer">
                            <button class="primary" type="button" @click="add()">
                                <i class='bx bx-plus bx-tada-hover'></i>
                                <span>Add</span>
                            </button>
                            <button type="button" class="close" @click="dialogClose()">
                                <i class='bx bx-x bx-tada-hover'></i>
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
    Alpine.data('xAddReason', () => ({
        submitLoading: false,
        des_reason:null,
        data: {
            purchase_type: false
        },
        async init() {
            // this.submitLoading = true;
            let dataStore = this.$store.addReason.options.data;
            this.des_reason = dataStore.des_reason;
            
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
        dialogClose() {
            this.$store.addReason.active = false;
        },
        add(){
            let data = {
                des_reason : this.des_reason
            }
            this.$store.addReason.options.afterClose(data);
            this.dialogClose();
        }
    }));
</script>

{{-- store --}}
<script>
    Alpine.store('addReason', {
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
    window.addReason = (options) => {
        Alpine.store('addReason', {
            active: true,
            config: options?.config ?? null,
            options: {
                ...Alpine.store('addReason').options,
                ...options
            }
        });
    };
</script>
