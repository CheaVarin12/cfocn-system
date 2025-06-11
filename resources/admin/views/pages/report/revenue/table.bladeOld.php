<div class="tableCustomScroll">
    <div class="table">
        <div class="excel-content">
            <div class="excel-tab">
                <div class="excel-tab-content">
                    <div class="excel-tab-item" :class="{ active: tab === 1 }" @click="onChangeTab(1)">
                        Subtotal License
                    </div>
                    {{-- <div class="excel-tab-item" :class="{ active: tab === 2 }" @click="onChangeTab(2)">
                        Optical Revenue
                    </div>
                    <div class="excel-tab-item" :class="{ active: tab === 3 }" @click="onChangeTab(3)">
                        Optical Items List</div>
                    <div class="excel-tab-item" :class="{ active: tab === 4 }" @click="onChangeTab(4)">
                        Submarine Revenue
                    </div> --}}
                </div>
            </div>
            <div class="excel-wrapper">
                <template x-if="tab == 1">
                    @include('admin::pages.report.revenue.tab.subtotal-license')
                </template>
                <template x-if="tab == 2">
                    @include('admin::pages.report.revenue.tab.optical-revenue')
                </template>
                <template x-if="tab == 3">
                    @include('admin::pages.report.revenue.tab.optical-items-list')
                </template>
                <template x-if="tab == 4">
                    @include('admin::pages.report.revenue.tab.submarine-revenue')
                </template>
                <template x-if="data.length <= 0 && !loading">
                    @component('admin::components.emptyReport', [
                        'name' => 'Revenue empty',
                        'msg' => 'There is no data.',
                        'style' => 'padding: 0 0 80px 0;',
                    ])
                    @endcomponent
                </template>
            </div>
        </div>
    </div>
</div>
