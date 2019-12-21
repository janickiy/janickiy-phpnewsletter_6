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

                        {!! Form::open(['url' => isset($subscriber) ? URL::route('admin.subscribers.update') : URL::route('admin.subscribers.store'), 'method' => isset($subscriber) ? 'put' : 'post', 'class' => "smart-form"]) !!}

                        {!! isset($subscriber) ? Form::hidden('id', $subscriber->id) : '' !!}

                            <header>
                                *-{{ trans('frontend.form.required_fields') }}
                            </header>

                            <fieldset>

                                <section>

                                    {!! Form::label('name', trans('frontend.form.name'), ['class' => 'label']) !!}

                                    <label class="input">

                                        {!! Form::text('name', old('name', isset($subscriber) ? $subscriber->name : null), ['class' => 'form-control', 'id' => 'name']) !!}

                                    </label>

                                    @if ($errors->has('name'))
                                        <p class="text-danger">{{ $errors->first('name') }}</p>
                                    @endif

                                </section>

                                <section>

                                    {!! Form::label('email', 'Email*', ['class' => 'label']) !!}

                                    <label class="input">

                                        {!! Form::text('email', old('email', isset($subscriber) ? $subscriber->email : null), ['class' => 'form-control', 'id' => 'email']) !!}

                                    </label>

                                    @if ($errors->has('email'))
                                        <p class="text-danger">{{ $errors->first('email') }}</p>
                                    @endif

                                </section>

                                <section>

                                    {!! Form::label('categoryId[]',  trans('frontend.form.subscribers_category'), ['class' => 'label']) !!}

                                    <label class="input">

                                        {!! Form::select('categoryId[]', $options, isset($subscriber) ? $subscriberCategoryId : null, ['multiple' => 'multiple', 'placeholder' => trans('frontend.form.select_category'), 'class' => 'form-control custom-scroll']) !!}

                                    </label>

                                    @if ($errors->has('categoryId'))
                                        <p class="text-danger">{{ $errors->first('categoryId') }}</p>
                                    @endif

                                </section>

                            </fieldset>

                            <footer>
                                <button type="submit" class="btn btn-primary">
                                    {{ trans('frontend.form.send') }}
                                </button>

                                <a class="btn btn-default" href="{{ URL::route('admin.subscribers.index') }}">
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
