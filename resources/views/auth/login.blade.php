<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <title>PHP Newsletter | {{ trans('frontend.title.auth') }}</title>

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {!! Html::style('/css/bootstrap.min.css') !!}

    {!! Html::style('/css/style.css') !!}

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    {!! Html::script('/admin/js/libs/jquery-3.2.1.min.js') !!}

</head>

<body class="login-page">
<main>
    <div class="login-block">
        <img src="{{ url('/admin/img/logo.png') }}" alt="">

        <h1>Admin area PHP Newsletter</h1>

        {!! Form::open(['url' => URL::route('login'), 'method' => 'post']) !!}

        <div class="form-group">
            <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-user ti-user"></i></span>

                {!! Form::text('login', old('login'), [ 'placeholder' => trans('frontend.form.login'), 'class' => 'form-control']) !!}

                @if ($errors->has('login'))
                    <p class="text-danger">{{ $errors->first('login') }}</p>
                @endif

            </div>
        </div>

        <hr class="hr-xs">

        <div class="form-group">
            <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-lock ti-unlock"></i></span>

                {!! Form::password('password',['class' => 'form-control', 'placeholder' => trans('frontend.form.password'), 'type' => 'password']) !!}

                @if ($errors->has('password'))
                    <p class="text-danger">{{ $errors->first('password') }}</p>
                @endif

            </div>
        </div>

        <div class="form-group row">
            <div class="col-md-6 offset-md-4">
                <div class="form-check">
                    <div class="chiller_cb">

                        {!! Form::checkbox('remember', 1, old('remember') ? true : false, ['id' => "myCheckbox"] ) !!}

                        {!! Form::label('myCheckbox', trans('frontend.str.remember_me'), ['class' => 'form-check-label']) !!}

                        <span></span>
                    </div>
                </div>
            </div>
        </div>


        {!! Form::submit(trans('frontend.str.singin'), ['class' => 'btn btn-primary btn-block']) !!}

        {!! Form::close() !!}

    </div>

</main>

</body>
</html>
