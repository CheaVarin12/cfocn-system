<template x-data="{}" x-if="$store.uploadFileList.active">
    <div class="dialog" x-data="xUploadFileList" x-bind:style="{ zIndex: $store.libs.getLastIndex() + 1 }">
        <div class="dialog-container dialogContainerForm">
            <div class="diglogForm" :style="{ width: $store.uploadFileList.options.config.width }">
                <div class="dialog-form-header" x-bind:style="{ zIndex: $store.libs.getLastIndex() + 1 }">
                    <h3>Document</h3>
                    <div style="display: flex;">
                        <button style="margin-right: 12px;background:rgba(0, 0, 255, 0.6784313725)" type="button"
                            class="btn-create" @click="dialog.open('uploadFileDialog',dataDialog.id)">
                            <i style="font-size: 18px;" class="material-symbols-outlined">upload</i>
                            <span style="color: white;font-size: 14px;">Upload</span>
                        </button>
                        <button style="background: rgba(255, 0, 0, 0.7607843137)" type="button" class="btn-create mr-3"
                            @click="dialogClose()">
                            <i style="font-size: 18px;" class="material-symbols-outlined">close</i>
                            <span style="color: white;font-size: 14px;">Close</span>
                        </button>
                    </div>
                </div>
                <div class="content-body footerTableScroll">
                    <div class="tableLayoutCon tableLayoutWithFooter " style="height: 90vh;">
                        <div class="tableLy">
                            <div class="tableCustomScroll">
                                <div class="table excel">
                                    <template x-if="fileData.length > 0">
                                        <div class="excel-body">
                                            <table class="tableWidth">
                                                <thead class="column">
                                                    <tr>
                                                        <th class="row" style="width: 52px;">No</th>
                                                        <th class="row">File Name</th>
                                                        <th class="row" style="width: 52px;">Action</th>

                                                    </tr>
                                                </thead>
                                                <tbody class="column" style="margin-bottom: 12px;">
                                                    <template x-for="(item,index) in fileData">
                                                        <tr>
                                                            <td class="row" x-text="index+1"></td>
                                                            <td style="padding: 6px;overflow: hidden;text-overflow: ellipsis;white-space: nowrap;"
                                                                class="row" x-text="item.name"></td>
                                                            <td class="row">
                                                                <i @click="viewFile(item)"
                                                                    onMouseOver="this.style.border='1px solid #1266f1'"
                                                                    onMouseOut="this.style.border='none'"
                                                                    style="color: #1266f1; font-size: 18px;"
                                                                    class="material-symbols-outlined">visibility</i>
                                                                <i @click="removeFile(item,index)"
                                                                    onMouseOver="this.style.border='1px solid #f93154'"
                                                                    onMouseOut="this.style.border='none'"
                                                                    style="margin-left:3px; color: #f93154 ;font-size: 18px;"
                                                                    class="material-symbols-outlined">delete</i>
                                                            </td>
                                                        </tr>
                                                    </template>
                                                </tbody>
                                            </table>
                                        </div>
                                    </template>
                                    <template x-if="fileData.length < 1">
                                        @component('admin::components.emptyReport', [
                                            'name' => 'File is empty',
                                            'msg' => 'There is no data.',
                                            'style' => 'padding: 10px 0 80px 0;',
                                        ])
                                        @endcomponent
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <template x-if="submitLoading">
                    @include('admin::components.spinner')
                </template>
            </div>
            <template x-if="dialog.component.uploadFileDialog">
                @include('admin::pages.po.upload-file.upload-file')
            </template>
        </div>
    </div>
</template>
<script>
    Alpine.data('xUploadFileList', () => ({
        submitLoading: false,
        fileData: [],
        baseUrl: '{{ url('') }}',
        dataDialog: null,
        async init() {
            // this.submitLoading = true;
            let data = this.$store.uploadFileList.options.data;
            this.dataDialog = data;
            await this.getDetailData(data.id);
        },
        async getDetailData(poId) {
            let id = poId;
            await Axios.get(`/admin/po/get-file/${id?id:null}`).then(resp => {
                this.fileData = resp.data.data
            });
        },
        upload(data) {
            uploadFile({
                active: true,
                data: data
            });
        },
        dialogClose() {
            this.$store.uploadFileList.active = false;
        },
        removeFile(data, index) {
            this.$store.confirmDialog.open({
                data: {
                    title: "Message",
                    message: "Are you sure to delete?",
                    btnClose: "Close",
                    btnSave: "Yes",
                },
                afterClosed: (result) => {
                    if (result) {
                        setTimeout(() => {
                            this.submitLoading = true;
                            Axios({
                                url: `{{ route('admin-delete-file') }}`,
                                method: 'POST',
                                data: {
                                    id: data.id,
                                }
                            }).then((res) => {
                                let message = res.data.message;
                                if (message == "file_deleted") {
                                    Toast({
                                        title: 'upload file',
                                        message: 'File deleted successfully',
                                        status: 'success',
                                        size: 'small',
                                    });
                                }
                                setTimeout(
                                    () => {
                                        this.fileData.splice(index, 1);
                                        this.submitLoading = false;
                                    }, 100);

                            }).catch((e) => {

                            }).finally(() => {
                                this.getDetailData(this.dataDialog.id)
                            });
                        }, 500);
                    }
                }
            });
        },
        viewFile(data) {
            if (data?.path) {
                window.open(`${this.baseUrl}/documents/${data.path}`, '_blank');
            } else {
                alert("File path is missing!");
            }
        },
        upload() {
            this.$store.dialog.open('uploadFileDialog', this.dataDialog.id);
        }
    }));
</script>
<script>
    Alpine.store('uploadFileList', {
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
        },
        afterClose: () => {},
    });
    window.uploadFileList = (options) => {
        Alpine.store('uploadFileList', {
            active: true,
            options: {
                ...Alpine.store('uploadFileList').options,
                ...options
            },
            afterClose: (res) => {
                console.log('close');
            },
        });
    };
</script>
