@extends('layouts.app')

@section('title', $title)

@section('css')

@endsection

@section('content')

    <!-- START ROW -->
    <div class="row">

        <!-- NEW COL START -->
        <article class="col-sm-12 col-md-12 col-lg-12">

            <!-- Widget ID (each widget will need unique ID)-->
            <div class="jarviswidget" id="wid-id-0" data-widget-colorbutton="false" data-widget-editbutton="false"
                 data-widget-custombutton="false">
                <!-- widget options:
                usage: <div class="jarviswidget" id="wid-id-0" data-widget-editbutton="false">

                data-widget-colorbutton="false"
                data-widget-editbutton="false"
                data-widget-togglebutton="false"
                data-widget-deletebutton="false"
                data-widget-fullscreenbutton="false"
                data-widget-custombutton="false"
                data-widget-collapsed="true"
                data-widget-sortable="false"

                -->

                <!-- widget div-->
                <div>

                    <!-- widget edit box -->
                    <div class="jarviswidget-editbox">
                        <!-- This area used as dropdown edit box -->

                    </div>
                    <!-- end widget edit box -->

                    <!-- widget content -->
                    <div class="widget-body no-padding">

                        {!! Form::open(['url' => isset($smtp) ? URL::route('admin.smtp.update') : URL::route('admin.smtp.store'), 'method' => isset($smtp) ? 'put' : 'post', 'class' => "smart-form"]) !!}

                        {!! isset($smtp) ? Form::hidden('id', $smtp->id) : '' !!}

                            <header>
                                *-{{ trans('frontend.form.required_fields') }}
                            </header>

                            <fieldset>

                                <section>

                                    {!! Form::label('host', trans('frontend.form.smtp_server') . '*', ['class' => 'label']) !!}

                                    <label class="input">

                                        {!! Form::text('host', old('host', isset($smtp) ? $smtp->host : null), [ 'placeholder' => trans('frontend.form.smtp_server'), 'class' => 'form-control', 'id' => 'host']) !!}

                                    </label>

                                    @if ($errors->has('host'))
                                        <span class="text-danger">{{ $errors->first('host') }}</span>
                                    @endif

                                </section>

                                <section>

                                    {!! Form::label('email', 'E-mail*', ['class' => 'label']) !!}

                                    <label class="input">

                                        {!! Form::text('email', old('email', isset($smtp) ? $smtp->email : null), [ 'placeholder' => 'E-mail', 'class' => 'form-control', 'id' => 'email']) !!}

                                    </label>

                                    @if ($errors->has('email'))
                                        <span class="text-danger">{{ $errors->first('email') }}</span>
                                    @endif

                                </section>

                                <section>

                                    {!! Form::label('username', trans('frontend.form.login') . '*', ['class' => 'label']) !!}

                                    <label class="input">

                                        {!! Form::text('username', old('username', isset($smtp) ? $smtp->username : null), [ 'placeholder' => trans('frontend.form.login'), 'class' => 'form-control', 'id' => 'username']) !!}

                                    </label>

                                    @if ($errors->has('username'))
                                        <span class="text-danger">{{ $errors->first('username') }}</span>
                                    @endif

                                </section>

                                <section>

                                    {!! Form::label('password', trans('frontend.form.password'), ['class' => 'label']) !!}

                                    <label class="input">

                                        {!! Form::text('password', old('password', isset($smtp) ? $smtp->password : null), [ 'placeholder' => trans('frontend.form.password'), 'class' => 'form-control', 'id' => 'password']) !!}

                                    </label>

                                    @if ($errors->has('password'))
                                        <span class="text-danger">{{ $errors->first('password') }}</span>
                                    @endif

                                </section>

                                <section>

                                    {!! Form::label('port', trans('frontend.form.port') . '*', ['class' => 'label']) !!}

                                    <label class="input">

                                        {!! Form::text('port', old('port', isset($smtp) ? $smtp->port : 25), [ 'placeholder' => trans('frontend.form.port'), 'class' => 'form-control', 'id' => 'port']) !!}

                                    </label>

                                    @if ($errors->has('port'))
                                        <span class="text-danger">{{ $errors->first('port') }}</span>
                                    @endif

                                </section>

                                <section>

                                    {!! Form::label('timeout', trans('frontend.form.timeout') . '*', ['class' => 'label']) !!}

                                    <label class="input">

                                        {!! Form::text('timeout', old('timeout', isset($smtp) ? $smtp->timeout : 5), [ 'placeholder' => trans('frontend.form.timeout'), 'class' => 'form-control', 'id' => 'timeout']) !!}

                                    </label>

                                    @if ($errors->has('timeout'))
                                        <span class="text-danger">{{ $errors->first('timeout') }}</span>
                                    @endif

                                </section>

                                <section>

                                    {!! Form::label('secure', trans('frontend.form.secure_connection'), ['class' => 'label']) !!}

                                    <div class="inline-group">
                                        <label class="radio">

                                            {!! Form::radio('secure', 'no', old('secure', (isset($smtp) && ($smtp->secure == 'no') or !isset($smtp)) ? true : false )) !!}

                                            <i></i>{{ trans('frontend.str.no') }}</label>
                                        <label class="radio">

                                            {!! Form::radio('secure', 'ssl', old('secure', (isset($smtp) && ($smtp->secure == 'ssl')) ? true : false )) !!}

                                            <i></i>ssl</label>
                                        <label class="radio">

                                            {!! Form::radio('secure', 'tls', old('secure', (isset($smtp) && ($smtp->secure == 'tls')) ? true : false )) !!}

                                            <i></i>tls</label>
                                    </div>
                                </section>

                                <section>

                                    {!! Form::label('authentication', trans('frontend.authentication_method'), ['class' => 'label']) !!}

                                    <div class="inline-group">
                                        <label class="radio">

                                            {!! Form::radio('authentication', 'no', old('authentication', (isset($smtp) && ($smtp->authentication == 'no') or !isset($smtp)) ? true : false )) !!}

                                            <i></i>LOGIN ({{ trans('frontend.form.low_secrecy') }})</label>
                                        <label class="radio">

                                            {!! Form::radio('authentication', 'plain', old('authentication' , (isset($smtp) && ($smtp->authentication == 'plain')) ? true : false )) !!}

                                            <i></i>PLAIN ({{ trans('frontend.form.medium_secrecy') }})</label>
                                        <label class="radio">

                                            {!! Form::radio('authentication', 'crammd5', old('authentication' , (isset($smtp) && ($smtp->authentication == 'crammd5')) ? true : false )) !!}

                                            <i></i>CRAM-MD5 ({{ trans('frontend.form.high_secrecy') }})</label>
                                    </div>
                                </section>

                            </fieldset>

                            <footer>
                                <button type="submit" class="btn btn-primary">
                                    {{ trans('frontend.form.send') }}
                                </button>
                                <a class="btn btn-default" href="{{ URL::route('admin.smtp.index') }}">
                                    {{ trans('frontend.form.back') }}
                                </a>
                            </footer>

                        {!! Form::close() !!}

                    </div>
                    <!-- end widget content -->

                </div>
                <!-- end widget div -->

            </div>
            <!-- end widget -->

        </article>
        <!-- END COL -->

    </div>

    <!-- END ROW -->

@endsection

@section('js')


@endsection
