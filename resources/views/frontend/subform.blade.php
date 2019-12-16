@extends('layouts.frontend')

@section('title', $title)

@section('css')

@endsection

@section('content')

    <div id="resultSub"></div>

    {!! Form::open(['id' => 'addsub', 'autocomplete' => "off"]) !!}

    @foreach($category as $row)

        <div class="form-check">
            <label class="form-check-label">
                {!! Form::checkbox('categoryId[]', $row['id'], ['class' => "form-check-input"]) !!} {!! $row['name'] !!}
            </label>
        </div>
    @endforeach

    <div class="form-group">
        {!! Form::label('name', trans('frontend.str.name')) !!}
        {!! Form::text('name',old('name'),['class'=>"form-control", 'autocomplete'=>"off"]) !!}
    </div>

    <div class="form-group">
        {!! Form::label('email', 'E-mail') !!}
        {!! Form::text('email',old('email'),['class'=>"form-control",'autocomplete'=>"off"]) !!}

        <div id="error-email" class="text-danger"></div>

    </div>

    {{ Form::button(trans('frontend.str.subscribe'), ['id' => "sub",'class' => 'btn btn-primary']) }}

    {!! Form::close() !!}

@endsection

@section('js')

    <script>

        $(document).on("click", "#sub", function () {
            var arr = $("#addsub").serializeArray();
            var aParams = [];
            var sParam;

            for (var i = 0, count = arr.length; i < count; i++) {
                sParam = encodeURIComponent(arr[i].name);
                sParam += "=";
                sParam += encodeURIComponent(arr[i].value);
                aParams.push(sParam);
            }

            sParam = 'action';
            sParam += "=";
            sParam += encodeURIComponent('send_test_email');
            aParams.push(sParam);

            var sendData = aParams.join("&");

            $.ajax({
                url: "{{ URL::route('frontend.addsub') }}",
                method: "POST",
                data: sendData,
                dataType: "json",
                success: function (data) {
                    if (data.result != null) {
                        var alert_msg = '';
                        if (data.result == 'success') {
                            alert_msg += '<div class="alert alert-success alert-dismissable">';
                            alert_msg += '<button class="close" aria-hidden="true" data-dismiss="alert" type="button">×</button>';
                            alert_msg += data.msg;
                            alert_msg += '</div>';
                        } else if (data.result == 'error') {
                            alert_msg += '<div class="alert alert-danger alert-dismissable">';
                            alert_msg += '<button class="close" aria-hidden="true" data-dismiss="alert" type="button">×</button>';
                            alert_msg += data.msg;
                            alert_msg += '</div>';
                        } else if (data.result == 'errors') {

                            $.each(data.msg, function (index, val) {

                                alert_msg += '<div class="alert alert-danger alert-dismissable">';
                                alert_msg += '<button class="close" aria-hidden="true" data-dismiss="alert" type="button">×</button>';

                                var arr = data.msg[index];
                                alert_msg += '<ul>';

                                for (var i = 0; i < arr.length; i++) {
                                    alert_msg += '<li>' + arr[i] + '</li>';
                                }

                                alert_msg += '</ul>';
                            });
                        }

                        $("#resultSub").html(alert_msg);
                    }
                }
            });
        });

    </script>

@endsection
