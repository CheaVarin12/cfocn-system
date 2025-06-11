@extends('admin::shared.layout')
@section('layout')
    <div class="form-admin">
        <div class="form-bg"></div>
        <div id="form" class="form-wrapper">
            <div class="form-header">
                <h3>
                <i data-feather="arrow-left" s-click-link="{!! route('admin-purchase-list', 1) !!}"></i>
                   {{ $purchase->pac_number }}  Document 
                </h3>
            </div>
            <div class="card">
                <template x-data="{}" x-if="$store.page.active == 'all_files'">
                    @include('admin::pages.purchase.document.all_files')
                </template>
                <template x-data="{}" x-if="$store.page.active == 'trash_bin'">
                    @include('admin::pages.purchase.document.trash_bin')
                </template>
            </div>
        </div>
    </div>
@stop
@section('script')
    <script>
        Alpine.store('page', {
            active: 'all_files',
            full_page: true,
            options: {
                multiple: {{ request('multiple') ?? 'false' }},
                returnUrl: `{{ request('returnUrl') ?? null }}`,
                afterClose: (data, base_url) => {
                    const returnUrl = Alpine.store('page').options.returnUrl;
                    if (returnUrl) {
                        const files = JSON.stringify({
                            data: data,
                            base_url: base_url
                        });
                        window.location.href = returnUrl + '?data=' + files;
                    }
                },
            },
        });
        Alpine.store('animate', {
            enter: (target, fn) => {
                if (!target) return;
                anime({
                    targets: target, //this.$root.children[0]
                    scale: [0.9, 1],
                    opacity: [0, 1],
                    direction: 'forwards',
                    easing: 'easeInSine',
                    duration: 200,
                    complete: (res) => {
                        fn ? fn(res) : null;
                    },
                });
            },
            leave: (target, fn) => {
                if (!target) return;
                anime({
                    targets: target,
                    scale: [1, 0.9],
                    opacity: [1, 0],
                    direction: 'forwards',
                    easing: 'easeOutSine',
                    duration: 200,
                    complete: (res) => {
                        fn ? fn(res) : null;
                    },
                });
            }
        });
    </script>
@stop
