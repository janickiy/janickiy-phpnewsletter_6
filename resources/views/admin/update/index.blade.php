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
                    <div class="row">
                        <div class="col-lg-12">
                            @if (!empty($button_update))

                                <div id="btn_refresh">
                                    <a id="start_update" class="btn btn-outline btn-default" href="#">
                                        <i class="fa fa-refresh"></i> {!! $button_update !!}
                                    </a>
                                </div>

                            @endif

                            @if (!empty($msg_no_update))

                                <a class="btn btn-outline btn-default" disabled>
                                    <i class="fa fa-refresh"></i> {!! $msg_no_update !!}
                                </a>

                            @endif
                        </div>
                    </div>

                    <!-- widget edit box -->
                    <div class="jarviswidget-editbox">
                        <!-- This area used as dropdown edit box -->
                    </div>
                    <!-- end widget edit box -->

                    <!-- widget content -->
                    <div class="widget-body no-padding">

                        {!! Form::open(['url' => URL::route('admin.update.add_license_key'), 'method' => 'post', 'class' => "smart-form"]) !!}

                        <fieldset>

                            <section>

                                {!! Form::label('license key', trans('frontend.form.license_key'), ['class' => 'label']) !!}

                                <label class="input">

                                    {!! Form::text('license key', old('license key', env('LICENSE_KEY', null)), ['class' => 'form-control']) !!}

                                </label>

                                @if ($errors->has('license_key'))
                                    <p class="text-danger">{{ $errors->first('license_key') }}</p>
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
