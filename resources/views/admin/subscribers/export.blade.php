@extends('layouts.app')

@section('title', $title)

@section('css')

@endsection

@section('content')

    <div class="row-fluid">
        <div class="col">

            <!-- Widget ID (each widget will need unique ID)-->
            <div class="jarviswidget jarviswidget-color-blueDark" data-widget-editbutton="false">

                <!-- widget div-->
                <div>

                    {!! Form::open(['url' =>  URL::route('admin.subscribers.export_subscribers'), 'method' => 'post', 'class' => "smart-form"]) !!}

                        <fieldset>

                            <section>

                                {!! Form::label('export_type', trans('frontend.form.format'), ['class' => 'label']) !!}

                                <div class="inline-group">
                                    <label class="radio">

                                        {{ Form::radio('export_type', 'text', true) }}

                                        <i></i>{{ trans('frontend.form.text') }}
                                    </label>
                                    <label class="radio">

                                        {{ Form::radio('export_type', 'excel', false) }}

                                        <i></i>MS Excel
                                    </label>
                                </div>
                            </section>

                            <section>

                                {!! Form::label('compress', trans('frontend.form.format'), ['class' => 'label']) !!}

                                <div class="inline-group">
                                    <label class="radio">

                                        {{ Form::radio('compress', 'none', true) }}

                                        <i></i>{{ trans('frontend.str.no') }}
                                    </label>
                                    <label class="radio">

                                        {{ Form::radio('compress', 'zip', true) }}

                                        <i></i>zip
                                    </label>
                                </div>
                            </section>

                            <section>

                                {!! Form::label('categoryId[]', trans('frontend.form.subscribers_category'), ['class' => 'label']) !!}

                                <label class="input">

                                    {!! Form::select('categoryId[]', $options, null, ['multiple'=>'multiple', 'placeholder' => trans('frontend.form.select_category'), 'class' => 'form-control custom-scroll']) !!}

                                </label>

                                @if ($errors->has('categoryId'))
                                    <p class="text-danger">{{ $errors->first('categoryId') }}</p>
                                @endif

                            </section>

                            <footer>
                                <button type="submit" class="btn btn-primary">
                                    {{ trans('frontend.form.send') }}
                                </button>
                                <button type="button" class="btn btn-default" onclick="window.history.back();">
                                    {{ trans('frontend.form.back') }}
                                </button>
                            </footer>

                        </fieldset>

                    {!! Form::close() !!}

                </div>
            </div>
        </div>
    </div>

@endsection

@section('js')


@endsection
