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

                        {!! Form::open(['url' => URL::route('admin.expired.add_license_key'), 'method' => 'post', 'class' => "smart-form"]) !!}

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
    <script>

    </script>
@endsection
