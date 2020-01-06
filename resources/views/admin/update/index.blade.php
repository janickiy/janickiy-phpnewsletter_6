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

        $(document).ready(function(){

            $("#start_update").on("click", function(){
                $("#btn_refresh").html('<div id="progress_bar" class="progress progress-sm progress-striped active"><div class="progress-bar bg-color-darken" role="progressbar" style="width: 1%"></div></div><span style="padding: 10px" id="status_process">{{ trans('frontend.str.start_update') }}</span>');

                $.ajax({
                    type: "POST",
                    cache: false,
                    url: "{{ URL::route('admin.ajax.action') }}",
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    data: {
                        action: "start_update",
                        p: "start",
                    },
                    success: function(data){
                        if (data[0].result == true) {
                            $('.progress-bar').css('width', '20%');
                            $("#status_process").text(data[0].status);
                            updateFiles();
                        } else {
                            $("#btn_refresh").html('<a id="start_update" class="btn btn-outline btn-default" href="#"><i class="fa fa-refresh"></i> {!! $button_update !!}</a><span style="padding: 10px">' + data[0].status + '</span>');
                        }
                    }
                });
            });
        });

        function updateFiles()
        {
            $.ajax({
                type: "POST",
                cache: false,
                url: "{{ URL::route('admin.ajax.action') }}",
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                data: {
                    action: "start_update",
                    p: "update_files",
                },
                success: function(data){
                    if (data[0].result == true) {
                        $('.progress-bar').css('width', '60%');
                        updateBD();
                    } else {
                        $("#btn_refresh").html('<a id="start_update" class="btn btn-outline btn-default" href="#"><i class="fa fa-refresh"></i> {!! $button_update !!}</a><span style="padding: 10px">' + data[0].status + '</span>');
                    }
                }
            });
        }

        function updateBD()
        {
            $.ajax({
                type: "POST",
                cache: false,
                url: "{{ URL::route('admin.ajax.action') }}",
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                data: {
                    action: "start_update",
                    p: "update_bd",
                },
                success: function(data){
                    if (data[0].result == true) {
                        $('.progress-bar').css('width', '80%');
                        clearCache();
                    } else {
                        $("#btn_refresh").html('<a id="start_update" class="btn btn-outline btn-default" href="#"><i class="fa fa-refresh"></i> {!! $button_update !!}</a><span style="padding: 10px">' + data[0].status + '</span>');
                    }
                }
            });
        }

        function clearCache()
        {
            $.ajax({
                type: "POST",
                cache: false,
                url: "{{ URL::route('admin.ajax.action') }}",
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                data: {
                    action: "start_update",
                    p: "clear_cache",
                },
                success: function(data){
                    if (data[0].result == true) {
                        $('.progress-bar').css('width', '100%');
                        $('#progress_bar').delay(3000).fadeOut();
                        $('#status_process').delay(3000).text('{{ trans('frontend.msg.update_completed') }}');
                    } else {
                        $("#btn_refresh").html('<a id="start_update" class="btn btn-outline btn-default" href="#"><i class="fa fa-refresh"></i> {!! $button_update !!}</a><span style="padding: 10px">' + data[0].status + '</span>');
                    }
                }
            });
        }

    </script>

@endsection
