<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@lang('app.title') @yield('title')</title>
    <link rel="shortcut icon" href="{!! asset('images/logo/imageCfocn.png') !!}" type="image/x-icon">
    {!! HTML::style('admin-public/css/app.css') !!}
    {!! HTML::style('admin-public/css/materialIcon.css') !!}
    {!! HTML::style('admin-public/css/select2.min.css') !!}
    {!! HTML::style('admin-public/css/Material_Symbols.css') !!}
    {!! HTML::style('plugin/toastr.min.css') !!}

    @stack('css')

    {!! HTML::script('admin-public/js/app.js') !!}
    {!! HTML::script('plugin/toastr.min.js') !!}
    {!! HTML::script('admin-public/js/jQuery.print.min.js') !!}
    {!! HTML::script('admin-public/js/feather.min.js') !!}
    {!! HTML::script('admin-public/js/select2.min.js') !!}
    {!! HTML::script('admin-public/js/jqueryUi.js') !!}
    {!! HTML::script('admin-public/js/icheck.min.js') !!}
    {!! HTML::script('admin-public/js/printJS.min.js') !!}
    {!! HTML::script('admin-public/js/print.js') !!}
    {!! HTML::script('admin-public/js/tinymce/tinymce.min.js') !!}

</head>

<body>
    @yield('index')
    @include('admin::components.toast')
    <script lang="ts">
        $(document).ready(function() {
            @if (Session::has('success'))
                 Toast({
                     title: 'Success Message',
                     message: '{!! Session::get('success') !!}',
                     status: 'success',
                     size: 'small',
                     duration: 5000,
                 });
             @elseif(Session::has('error'))
                 Toast({
                     title: 'Error Message',
                     message: '{!! Session::get('error') !!}',
                     status: 'danger',
                     size: 'small',
                     duration: 5000,
                 });
             @elseif(Session::has('warning'))
                 Toast({
                     title: 'Warning Message',
                     message: '{!! Session::get('warning') !!}',
                     status: 'warning',
                     size: 'small',
                     duration: 5000,
                 });
             @endif
        });
    </script>
    @yield('script')
    {!! HTML::script('admin-public/js/body.js') !!}
</body>

</html>
