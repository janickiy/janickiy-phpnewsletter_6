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

                        {!! Form::open(['url' => isset($template) ? URL::route('admin.template.update') : URL::route('admin.template.store'), 'files' => true, 'method' => isset($template) ? 'put' : 'post', 'id' => 'tmplForm']) !!}

                            <div class="smart-form">

                                {!! isset($template) ? Form::hidden('id', $template->id) : '' !!}

                                <header>
                                    *-{{ trans('frontend.form.required_fields') }}
                                </header>

                                <fieldset>

                                    <section>

                                        {!! Form::label('name', trans('frontend.form.name') . '*', ['class' => 'label']) !!}

                                        <label class="input">

                                            {!! Form::text('name', old('name', isset($template) ? $template->name : null), ['class' => 'form-control', 'id' => 'name']) !!}

                                        </label>

                                        @if ($errors->has('name'))
                                            <p class="text-danger">{{ $errors->first('name') }}</p>
                                        @endif

                                    </section>

                                    <section>

                                        {!! Form::label('body', trans('frontend.form.template') . '*', ['class' => 'label']) !!}

                                        <label class="textarea textarea-resizable">

                                            {!! Form::textarea('body', old('name', isset($template) ? $template->body : null), ['rows' => "3", 'placeholder' => trans('frontend.form.template'), 'class' => 'custom-scroll', 'id'=> 'body']) !!}

                                        </label>

                                        @if ($errors->has('body'))
                                            <p class="text-danger">{{ $errors->first('body') }}</p>
                                        @endif

                                        <div class="note">
                                            {{ trans('frontend.note.personalization') }}
                                        </div>
                                    </section>

                                    <section>

                                        {!! Form::label('attachfile[]', trans('frontend.form.attach_files'), ['class' => 'label']) !!}

                                        <div class="input input-file">
                                            <span class="button">
                                                {!! Form::file('attachfile[]', ['multiple' => "true", 'id' => 'attachfile', 'onchange' => "this.parentNode.nextSibling.value = this.value", 'readonly' => ""]) !!}{{ trans('frontend.form.browse') }}
                                            </span>
                                            <input type="text" placeholder="{{ trans('frontend.form.select_files') }}" readonly="">

                                        </div>

                                        @if ($errors->has('attachfile'))
                                            <p class="text-danger">{{ $errors->first('attachfile') }}</p>
                                        @endif

                                    </section>

                                    <section>

                                        {!! Form::label('attachments', trans('frontend.str.attachments'), ['class' => 'label']) !!}

                                        <div class="inline-group">
                                           @if(isset($attachment))
                                            @foreach($attachment as $a)
                                            <span id="attach_{{ $a->id }}">{{ $a->file_name }}
                                                <a href="#" data-num="{{ $a->id }}" class="remove_attach" title="{{ trans('frontend.str.remove') }}"> X </a>&nbsp;&nbsp;
                                            </span>
                                            @endforeach
                                           @endif
                                        </div>
                                    </section>

                                    <section>

                                        {!! Form::label('prior', trans('frontend.form.prior'), ['class' => 'label']) !!}

                                        <div class="inline-group">
                                            <label class="radio">

                                                {!! Form::radio('prior', 3, (isset($template) && $template->prior == 3) or !isset($template)) !!}

                                                <i></i>{{ trans('frontend.form.normal') }}
                                            </label>
                                            <label class="radio">

                                                {!! Form::radio('prior', 2, isset($template) && $template->prior == 2) !!}

                                                <i></i>{{ trans('frontend.form.low') }}
                                            </label>
                                            <label class="radio">

                                                {!! Form::radio('prior', 1, isset($template) && $template->prior == 1) !!}

                                                <i></i>{{ trans('frontend.form.high') }}
                                            </label>

                                            @if ($errors->has('prior'))
                                                <p class="text-danger">{{ $errors->first('prior') }}</p>
                                            @endif

                                        </div>

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

                            </div>

                            <div class="well bg-color-blueLight">
                                <div id="resultSend"></div>
                                <h3>{{ trans('frontend.str.send_test_letter') }}</h3>
                                <div class="input-group">

                                    {!! Form::text('email', null, ['class' => 'form-control', 'placeholder' => 'Email', 'id' => 'email']) !!}

                                    <div class="input-group-btn">
                                        <button class="btn btn-default" id="send_test" type="button">
                                            <i class="fa fa-send"></i> {{ trans('frontend.str.send') }}
                                        </button>
                                    </div>
                                </div>
                            </div>

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

    {!! Html::script('/admin/js/plugin/ckeditor/ckeditor.js') !!}

    <script>

        $(document).ready(function () {
            CKEDITOR.replace('body', {height: '380px', startupFocus: true});

            $(document).on("click", ".remove_attach", function () {
                var idAttach = $(this).attr('data-num');
                var request = $.ajax({
                    url: '{{ URL::route('admin.ajax.action') }}',
                    method: "POST",
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    data: {
                        action: "remove_attach",
                        id: idAttach,
                    },

                    dataType: "json"
                });

                request.done(function (data) {
                    if (data.result != null && data.result == true) {
                        $("#attach_" + idAttach).remove();
                    }
                });
            });

            $(document).on("click", "#send_test", function () {

                var bodyContent = CKEDITOR.instances["body"].getData();
                var arr = $("#tmplForm").serializeArray();
                var aParams = [];
                var sParam;

                for (var i = 0, count = arr.length; i < count; i++) {
                    sParam = encodeURIComponent(arr[i].name);

                    if (sParam == 'body') {
                        sParam += "=";
                        sParam += encodeURIComponent(bodyContent);
                    } else {
                        sParam += "=";
                        sParam += encodeURIComponent(arr[i].value);
                    }

                    aParams.push(sParam);
                }

                sParam = 'action';
                sParam += "=";
                sParam += encodeURIComponent('send_test_email');
                aParams.push(sParam);

                var sendData = aParams.join("&");
                var request = $.ajax({
                    url: '{{ URL::route('admin.ajax.action') }}',
                    method: "POST",
                    data: sendData,
                    dataType: "json"
                });

                request.done(function (data) {
                    if (data.result != null) {
                        var alert_msg = '';

                        if (data.result == 'success'){
                            alert_msg += '<div class="alert alert-success fade in">';
                            alert_msg += '<button class="close" data-dismiss="alert">×</button>';
                            alert_msg += '<i class="fa-fw fa fa-check"></i>';
                            alert_msg += data.msg;
                            alert_msg += '</div>';
                        } else if (data.result == 'error'){
                            alert_msg += '<div class="alert alert-danger fade in">';
                            alert_msg += '<button class="close" data-dismiss="alert">×</button>';
                            alert_msg += '<strong>{{ trans('frontend.str.error_alert') }} </strong>';
                            alert_msg += data.msg;
                            alert_msg += '</div>';
                        } else if (data.result == 'errors'){
                            alert_msg += '<div class="alert alert-danger fade in">';
                            alert_msg += '<button class="close" data-dismiss="alert">×</button>';
                            alert_msg += '<strong>{{ trans('frontend.str.error_alert') }} </strong>';
                            alert_msg += '<ul>';

                            var arr = data.msg.split(',');

                            for (var i = 0; i < arr.length; i++){
                                alert_msg += '<li> ' + arr[i] + '</li>';
                            }

                            alert_msg += '</ul>';
                            alert_msg += '</div>';
                        }

                        $("#resultSend").html(alert_msg);
                    }
                });
            });
        });

    </script>

@endsection
