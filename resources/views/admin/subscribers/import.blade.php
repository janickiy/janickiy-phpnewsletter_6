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

                    {!! Form::open(['url' => URL::route('admin.subscribers.import_subscribers'), 'files' => true, 'method' => 'post', 'class' => "smart-form"]) !!}

                        <header>
                            *-{{ trans('frontend.form.required_fields') }}
                        </header>

                        <fieldset>

                            <section>

                                {!! Form::label('import', trans('frontend.form.attach_files') . '*', ['class' => 'label']) !!}

                                <div class="input input-file">
                                    <span class="button">

                                        {!! Form::file('import',  ['id' => 'import', 'onchange' => "this.parentNode.nextSibling.value = this.value"]) !!}{{ trans('frontend.form.browse') }}

                                    </span><input type="text" placeholder="{{ trans('frontend.form.select_files') }}" readonly="">

                                </div>

                                @if ($errors->has('import'))
                                    <span class="text-danger">{{ $errors->first('import') }}</span>
                                @endif

                                <div class="note">
                                    {{ trans('frontend.form.maximum_size') }}: <strong>{{ $maxUploadFileSize }}</strong>
                                </div>

                            </section>

                            <section>

                                {!! Form::label('charset', trans('frontend.form.charset'), ['class' => 'label']) !!}

                                <label class="select">

                                    {!! Form::select('charset', $charsets, null, ['placeholder' => '--' . trans('frontend.form.select') . '--', 'id' => 'charset']) !!}

                                     <i></i>
                                </label>

                                @if ($errors->has('charset'))
                                    <span class="text-danger">{{ $errors->first('charset') }}</span>
                                @endif

                            </section>

                            <section>

                                {!! Form::label('categoryId[]', trans('frontend.form.subscribers_category'), ['class' => 'label']) !!}

                                <label class="input">

                                    {!! Form::select('categoryId[]', $category_options, null, ['multiple'=>'multiple', 'placeholder' => trans('frontend.form.select_category'), 'class' => 'form-control custom-scroll']) !!}

                                </label>

                                @if ($errors->has('categoryIdt'))
                                    <span class="text-danger">{{ $errors->first('categoryId') }}</span>
                                @endif

                            </section>

                            <footer>
                                <button type="submit" class="btn btn-primary">
                                    {{ trans('frontend.form.send') }}
                                </button>
                                <a class="btn btn-default" href="{{ URL::route('admin.subscribers.index') }}">
                                    {{ trans('frontend.form.back') }}
                                </a>
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
