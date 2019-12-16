<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>PHP Newsletter | Установка</title>

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Bootstrap Core CSS -->

    {!! Html::style('/css/style.css') !!}

    {!! HTML::style('/css/app.css') !!}

    {!! HTML::style('/css/install.css') !!}

    {!! Html::style('/css/vendor.css') !!}

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
        <div class="col-md-6 offset-3 logo-wrapper">
            <img src="{{ url('/admin/img/logo.png') }}" alt="PHP Newsletter" class="logo">
        </div>
    </div>
    <div class="wizard col-md-6 offset-3">
        @yield('content')
    </div>
</div>

{!! Html::script('/js/libs/jquery-3.2.1.min.js') !!}

@yield('js')

</body>
</html>
