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

                        {!! Form::open(['url' => isset($category) ? URL::route('admin.category.update') : URL::route('admin.category.store'), 'method' => isset($category) ? 'put' : 'post', 'class' => "smart-form"]) !!}

                        {!! isset($category) ? Form::hidden('id', $category->id) : '' !!}

                            <header>
                                *-{{ trans('frontend.form.required_fields') }}
                            </header>

                            <fieldset>

                                <section>

                                    {!! Form::label('name', trans('frontend.form.name') . '*', ['class' => 'label']) !!}

                                    <label class="input">

                                        {!! Form::text('name', old('name', isset($category) ? $category->name : null), ['class' => 'form-control', 'id' => 'name']) !!}

                                    </label>

                                    @if ($errors->has('name'))
                                        <p class="text-danger">{{ $errors->first('name') }}</p>
                                    @endif

                                </section>

                            </fieldset>

                            <footer>
                                <button type="submit" class="btn btn-primary">
                                    {{ trans('frontend.form.send') }}
                                </button>
                                <a class="btn btn-default" href="{{ URL::route('admin.category.index') }}">
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