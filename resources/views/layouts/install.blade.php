<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>PHP Newsletter | {{ trans('str.install') }}</title>

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Bootstrap Core CSS -->

    {!! Html::style('/css/style.css') !!}

    {!! HTML::style('/css/app.css') !!}

    {!! Html::style('/css/vendor.css') !!}

    {!! Html::style('/css/bootstrap.min.css') !!}

    {!! HTML::style('/css/install.css') !!}

    {!! Html::style('/css/vendor.css') !!}

    {!! Html::style('/admin/js/plugin/bootstrap-select/css/bootstrap-select.min.css') !!}

    {!! Html::style('/admin/css/font-awesome.min.css') !!}

    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans:400italic,700italic,300,400,700">

    @yield('css')

    <script type="text/javascript">
        var SITE_URL = "{{ url('/') }}";
    </script>

</head>
<body>

<div class="container-fluid">

    <div class="row">
        <div class="col-md-6 offset-3">
            <div class="pull-right form-group">
                <select id="lang" class="selectpicker">
                    <option value="ru" {{ Config::get('app.locale') == 'ru' ? 'selected="selected"':'' }}>Русский (Russian)</option>
                    <option value="en" {{ Config::get('app.locale') == 'en' ? 'selected="selected"':'' }}>English</option>
                </select>
            </div>
        </div>

        <div class="col-md-6 offset-3 logo-wrapper">
            <img src="{{ url('/admin/img/logo.png') }}" alt="PHP Newsletter" class="logo">
        </div>
    </div>
    <div class="wizard col-md-6 offset-3">

        @yield('content')

    </div>
</div>

{!! Html::script('/js/libs/jquery-3.2.1.min.js') !!}

{!! Html::script('/admin/js/bootstrap/bootstrap.min.js') !!}

{!! Html::script('/admin/js/plugin/bootstrap-select/js/bootstrap-select.min.js') !!}

@yield('js')

<script>

    $(document).ready(function () {
        $('.selectpicker').selectpicker();

        $('#lang').on('change', function () {
            var Lng = $(this).val();

            var request = $.ajax({
                url: '{{ URL::route('install.ajax.action') }}',
                method: "POST",
                data: {
                    action: "change_lng",
                    locale: Lng,
                },
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                dataType: "json"
            });

            request.done(function (data) {
                if (data.result != null && data.result == true) {
                    location.reload();
                }
            });
        });
    });

</script>

</body>
</html>
