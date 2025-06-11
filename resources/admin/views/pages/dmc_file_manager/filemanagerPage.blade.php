<div class="filemanagerLayout">
    <div class="dmc-file-manager-tab">
        <template x-for="(item,index) in dmcTypeData">
            <div class="d-tab" :class="(docType.id == item.id) ? 'active' : ''" @click="clickDocTypeFile(item)">
                <i class='bx bx-folder'></i>
                <label x-text="item['name']"></label>
            </div>
        </template>
    </div>
    <div class="bodyDMCFileManage">
        <div class="fileLeftgp">
            <div class="btnLeftAddnew">
                <div class="btnSearchgp">
                    <input type="text" x-model="dataYear.search" class="btnSearch" placeholder="Search year ..."
                        x-on:input="onInputYear()" />
                </div>
            </div>
            <div class="listItemLeft">
                <label>My Drive</label>
                <div class="listGp" :class="(year.id == null || !year.id) ? 'active' : ''" @click="clickYear(null)">
                    <i class='bx bx-file'></i>
                    <div>All file</div>
                </div>
                <label x-show="dataYear?.data?.data?.length > 0">LABELS</label>
                <template x-if="!loadingYear">
                    <template x-for="item in dataYear.array">
                        <div class="listGp" :class="year.id == item.id ? 'active' : ''" @click="clickYear(item)">
                            <i class='bx bx-folder'></i>
                            <div x-html="item.year"></div>
                        </div>
                    </template>
                </template>
                <template x-if="loadingYear">
                    <div x-data="{ dataEmpty: 5 }" class="gpLoadingShimmer">
                        <template x-for="data in dataEmpty">
                            <div class="loadingShimmerLayout loadingFileShimmer">
                                <div class="itemLoad">
                                    <div class="imgBox"></div>
                                    <div class="textItemGp">
                                        <div class="lineBox-2"></div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </template>
            </div>
            <div class="btnFooterLeft">
                <button class="btnLoadMore"
                    :disabled="(dataYear.loading ? true : false) || (dataYear?.data?.current_page == dataYear?.data?.last_page ?
                        true : false)"
                    @click="loadingMoreYear()">
                    <i class='bx bx-loader-alt bx-spin' x-show="dataYear.loading"></i>
                    <span x-text="!dataYear.loading ? 'View more':'Loading ...'"></span>
                </button>
            </div>
        </div>
        <div class="fileRightgp">
            <div class="listItemRight">
                <template x-if="year.id">
                    <div class="dmc-file-listing-month">
                        <div class="dmc-header">
                            <div class="dmcTextLabel">
                                <i class='bx bx-folder'></i>
                                <label class="dm-text" x-text="year.name"></label>
                            </div>
                        </div>
                        <div class="dmc-month-body">
                            <div class="DMitem" :class="(month.id == 'all') ? 'active' : ''"
                                @click="clickMonth({id:'all',name:'All file'})">
                                <i class='bx bx-folder'></i>
                                <label class="dm-text" x-text="'All File'"></label>
                            </div>
                            <template x-for="(item,index) in dataMonth.data">
                                <div class="DMitem" :class="(month.id == monthData[item.month].id) ? 'active' : ''"
                                    @click="clickMonth(monthData[item.month])">
                                    <i class='bx bx-folder'></i>
                                    <label class="dm-text" x-text="monthData[item.month]?.name"></label>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>
                <div class="gp-itemRight-item">
                    <div class="itemRight-header">
                        <input type="text" x-model="invoiceSearch" placeholder="Search invoice number ..."
                            x-on:input="invoiceFetchSearch()" />
                        <div class="refreshDev" @click="reloadData()">
                            <i class='bx bx-refresh' :class="refreshLoading ? 'bx-spin' : ''"></i>
                        </div>
                    </div>
                    <div class="gp-itemRight">
                        <template x-if="!dataLoading && !empty">
                            <template x-for="item in data?.data">
                                <div class="itemRight">
                                    <div class="container-cus">
                                        <div class="item">
                                            <div class="gp-img" x-data="{ logoFile: item?.extension_type == 'xlsx' ? 'excel.webp' : 'pdf.png' }">
                                                <img :src="urlLogoFile + '/' + logoFile" class="img">

                                            </div>
                                            <div class="gp-txt">
                                                <h4 x-text="(item.file_type == 'customer_info'?'Customer':'') || (item.file_type == 'credit_note'?'Credit Note':'') || (item.file_type == 'invoice'?'invoice':'') || (item.file_type == 'invoice_void' ? 'Invoice Void' : '') 
                                                || (item.file_type == 'submarine_invoice_summary' ? 'Invoice Summary' : '') || (item.file_type == 'infra_invoice_summary' ? 'Invoice Summary' : '')">
                                                </h4>
                                                <label x-text="item?.credit_note ? item?.credit_note?.credit_note_number : item.invoice_number"></label>
                                                <label x-text="item.year+'/'+item.month"></label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="deleteImage">
                                        <template x-if="item?.extension_type =='pdf'">
                                            <span class="material-symbols-outlined view" @click="viewPdf(item)">
                                                visibility
                                            </span>
                                        </template>
                                        <span class="material-symbols-outlined download" @click="downloadFile(item)">
                                            download
                                        </span>
                                    </div>
                                </div>
                            </template>
                        </template>
                        <template x-if="dataLoading && !empty">
                            <div x-data="{ dataEmpty: 8 }" class="gpLoadingShimmer loadingFileInvoiceShimmer">
                                <template x-for="data in dataEmpty">
                                    <div class="loadingShimmerLayout">
                                        <div class="itemLoad">
                                            <div class="imgBox"></div>
                                            <div class="textItemGp">
                                                <div class="lineBox-2"></div>
                                                <div class="lineBox-2"></div>
                                                <div class="lineBox-2"></div>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </template>
                    </div>
                    <div class="emptyDMCFile" x-show="empty && !loading && !docLoading">
                        @component('admin::components.empty', [
                            'name' => 'File not found',
                            'image' => asset('images/logo/em.svg'),
                            'style' => 'padding: 50px 0;',
                        ])
                        @endcomponent
                    </div>
                </div>
                <template x-if="loading">
                    @include('admin::components.spinner')
                </template>
            </div>

            <div class="btnRighBottom">
                <div class="pagination">
                    <span class="perPage">Items
                        per page : <span x-html="data?.per_page ? data.per_page : 0"></span></span>
                    <span class="totalPage"><span x-html="data?.from ? data.from : 0"></span>
                        - <span x-html="data?.to ? data.to : 0"></span> of <span
                            x-html="data?.total ? data.total : 0"></span></span>
                    <div class="btn-pagination">
                        <button mat-icon-button color="primary" x-bind:disabled="data?.prev_page_url == null"
                            @click="paginate(data?.current_page - 1)">
                            <i class='bx bx-chevron-left'></i>
                        </button>
                        <button mat-icon-button color="primary" x-bind:disabled="data?.next_page_url == null"
                            @click="paginate(data?.current_page + 1)">
                            <i class='bx bx-chevron-right'></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <template x-if="docLoading">
        @include('admin::components.spinner')
    </template>
</div>
