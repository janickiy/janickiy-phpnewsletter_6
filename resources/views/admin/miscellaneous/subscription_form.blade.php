@extends('layouts.app')

@section('title', $title)

@section('css')

@endsection

@section('content')

    <div class="row-fluid">

        <div class="col">

            <!-- Widget ID (each widget will need unique ID)-->
            <div class="jarviswidget jarviswidget-color-blueDark" data-widget-editbutto="false">

                <!-- widget div-->
                <div>

                    @include('include.subform')

                    <div class="form-group">

                        <textarea rows="3" id="myInput" name="body" cols="50">@include('include.subform')</textarea>

                    </div>

                    <button type="submit" class="btn btn-primary margin-bottom-10" onclick="myFunction()"
                            onmouseout="outFunc()">
                        <span id="myTooltip">{{ trans('frontend.str.copy_to_clipboard') }}</span>
                    </button>

                </div>
                <!-- end widget content -->

            </div>
            <!-- end widget div -->

        </div>
        <!-- end widget -->

    </div>

@endsection

@section('include')

    <script>
        function myFunction() {
            var copyText = document.getElementById("myInput");
            copyText.select();
            document.execCommand("copy");

            var tooltip = document.getElementById("myTooltip");
            tooltip.innerHTML = "Copied: " + copyText.value;
        }

        function outFunc() {
            var tooltip = document.getElementById("myTooltip");
            tooltip.innerHTML = "{{ trans('frontend.str.copy_to_clipboard') }}";
        }

    </script>

    @include('include.subform_js')

@endsection
