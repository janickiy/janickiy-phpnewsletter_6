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

                        {!! Form::open(['url' => isset($user) ? URL::route('admin.users.update') : URL::route('admin.users.store'), 'method' => isset($user) ? 'put' : 'post', 'class' => "smart-form"]) !!}

                        {!! isset($user) ? Form::hidden('id', $user->id) : '' !!}

                        <header>
                            *-{{ trans('frontend.form.required_fields') }}
                        </header>

                        <fieldset>

                            <section>

                                {!! Form::label('name', trans('frontend.form.name'), ['class' => 'label']) !!}

                                <label class="input">

                                    {!! Form::text('name', old('name', isset($user) ? $user->name : null), [ 'placeholder' => trans('frontend.form.name'), 'class' => 'form-control', 'id' => 'name']) !!}

                                </label>

                                @if ($errors->has('name'))
                                    <p class="text-danger">{{ $errors->first('name') }}</p>
                                @endif

                            </section>

                            <section>

                                {!! Form::label('login', trans('frontend.form.login'), ['class' => 'label']) !!}

                                <label class="input">

                                    {!! Form::text('login', old('login', isset($user) ? $user->login : null), [ 'placeholder' => trans('frontend.form.login'), 'class' => 'form-control', 'id' => 'login']) !!}

                                </label>

                                @if ($errors->has('login'))
                                    <p class="text-danger">{{ $errors->first('login') }}</p>
                                @endif

                            </section>

                            <section>

                                {!! Form::label('description', trans('frontend.form.description'), ['class' => 'label']) !!}

                                <label class="textarea textarea-resizable">

                                    {!! Form::textarea('description', old('description', isset($user) ? $user->description : null), [ 'placeholder' => trans('frontend.form.description'), 'rows' => 3, 'class' => 'custom-scroll', 'id' => 'description']) !!}

                                </label>

                                @if ($errors->has('description'))
                                    <p class="text-danger">{{ $errors->first('description') }}</p>
                                @endif

                            </section>

                           @if ((isset($user->id) && $user->id != Auth::user()->id) || !isset($user->id))

                            <section>

                                {!! Form::label('role', trans('frontend.form.role'), ['class' => 'label']) !!}

                                <label class="input">

                                    {!! Form::select('role', $options, isset($user) ? $user->role : 'admin', ['placeholder' => trans('frontend.form.select_role'), 'id'=> "role", "class" => 'form-control custom-scroll']) !!}

                                </label>

                                @if ($errors->has('role'))
                                    <p class="text-danger">{{ $errors->first('role') }}</p>
                                @endif

                            </section>

                            @endif

                            <section>

                                {!! Form::label('password', trans('frontend.form.password'), ['class' => 'label']) !!}

                                <label class="input">

                                    {!! Form::password('password', ['class' => 'form-control', 'id'=> "password"]) !!}

                                </label>

                                @if ($errors->has('password'))
                                    <p class="text-danger">{{ $errors->first('password') }}</p>
                                @endif

                            </section>

                            <section>

                                {!! Form::label('password_again', trans('frontend.form.password_again'), ['class' => 'label']) !!}

                                <label class="input">

                                    {!! Form::password('password_again', ['class' => 'form-control', 'id'=> "password_again"]) !!}

                                </label>

                                @if ($errors->has('password_again'))
                                    <p class="text-danger">{{ $errors->first('password_again') }}</p>
                                @endif

                            </section>

                        </fieldset>

                        <footer>
                            <button type="submit" class="btn btn-primary">
                                {{ isset($user) ? trans('frontend.form.edit') : trans('frontend.form.add') }}
                            </button>
                            <a class="btn btn-default" href="{{ URL::route('admin.users.index') }}">
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
