<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title') - {{ config('app.name', 'POS') }}</title> 

    <!-- Styles -->
    <!-- Bootstrap 3.3.6 -->
    <link rel="stylesheet" href="{{ asset('bootstrap/css/bootstrap.min.css?v=' . $asset_v) }}">
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('AdminLTE/plugins/select2/select2.min.css?v=' . $asset_v) }}">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('plugins/font-awesome/css/font-awesome.min.css?v=' . $asset_v) }}">

    <!-- Ionicons -->
    <link rel="stylesheet" href="{{ asset('plugins/ionicons/css/ionicons.min.css?v=' . $asset_v) }}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('AdminLTE/css/AdminLTE.min.css?v=' . $asset_v) }}">
    <!-- iCheck -->
    <link rel="stylesheet" href="{{ asset('AdminLTE/plugins/iCheck/square/blue.css?v=' . $asset_v) }}">
    <!-- bootstrap datepicker -->
    <link rel="stylesheet" href="{{ asset('AdminLTE/plugins/datepicker/bootstrap-datepicker.min.css?v='.$asset_v) }}">
    
    <!-- Bootstrap file input -->
    <link rel="stylesheet" href="{{ asset('plugins/bootstrap-fileinput/fileinput.min.css?v=' . $asset_v) }}">

    <!-- Jquery Steps -->
    <link rel="stylesheet" href="{{ asset('plugins/jquery.steps/jquery.steps.css?v=' . $asset_v) }}">

    <!-- Toastr -->
    <link rel="stylesheet" href="{{ asset('plugins/toastr/toastr.min.css?v='.$asset_v) }}">

    <!-- app css -->
    <link rel="stylesheet" href="{{ asset('css/app.css?v=' . $asset_v) }}">

    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>

<body class="hold-transition register-page">
    @if (session('status'))
        <input type="hidden" id="status_span" data-status="{{ session('status.success') }}" data-msg="{{ session('status.msg') }}">
    @endif

    @yield('content')

    <!-- jQuery 2.2.3 -->
    <script src="{{ asset('AdminLTE/plugins/jQuery/jquery-2.2.3.min.js?v=' . $asset_v) }}"></script>
    <!-- Bootstrap 3.3.6 -->
    <script src="{{ asset('bootstrap/js/bootstrap.min.js?v=' . $asset_v) }}"></script>

    <script src="{{ asset('js/lang/' . config('app.locale') . '.js?v=' . $asset_v) }}"></script>
    <!-- iCheck -->
    <script src="{{ asset('AdminLTE/plugins/iCheck/icheck.min.js?v=' . $asset_v) }}"></script>
    <!-- Select2 -->
    <script src="{{ asset('AdminLTE/plugins/select2/select2.full.min.js?v=' . $asset_v) }}"></script>

    <!-- bootstrap datepicker -->
    <script src="{{ asset('AdminLTE/plugins/datepicker/bootstrap-datepicker.min.js?v=' . $asset_v) }}"></script>

    <!-- jQuery Validator -->
    <script src="{{ asset('js/jquery-validation-1.16.0/dist/jquery.validate.min.js?v=' . $asset_v) }}"></script>
    <script src="{{ asset('js/jquery-validation-1.16.0/dist/additional-methods.min.js?v=' . $asset_v) }}"></script>
    @php
        $validation_lang_file = 'messages_' . config('app.locale') . '.js';
    @endphp
    @if(file_exists(public_path() . '/js/jquery-validation-1.16.0/src/localization/' . $validation_lang_file))
        <script src="{{ asset('js/jquery-validation-1.16.0/src/localization/' . $validation_lang_file . '?v=' . $asset_v) }}"></script>
    @endif
    <!-- Bootstrap file input -->
    <script src="{{ asset('plugins/bootstrap-fileinput/fileinput.min.js?v=' . $asset_v) }}"></script>
    <script src="{{ asset('plugins/jquery.steps/jquery.steps.min.js?v=' . $asset_v) }}"></script>

    <!-- Toastr -->
    <script src="{{ asset('plugins/toastr/toastr.min.js?v=' . $asset_v) }}"></script>

    <script>
        base_path = "{{url('/')}}";

        $(document).ready(function(){
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                beforeSend: function(jqXHR, settings) {
                    if(settings.url.indexOf('http') === -1){
                        settings.url = base_path + settings.url;
                    }
                }
            });
        });
    </script>

    <!-- Scripts -->
    <script src="{{ asset('js/login.js?v=' . $asset_v) }}"></script>
    @yield('javascript')
</body>

</html>