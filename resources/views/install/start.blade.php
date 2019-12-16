@extends('layouts.install')

@section('content')

    @include('install.steps', ['steps' => ['welcome' => 'selected']])

    <div class="step-content">
        <h3>{{ trans('install.str.license_agreement') }}</h3>
        <hr>
        <fieldset>
            <div class="form-group">
                <textarea class="form-control" name="readonly" rows="13">{!! trans('license.agreement') !!}</textarea>
            </div>

            <div class="form-group">
                <label class="checkbox-inline" for="accept_license">
                    <input type="checkbox" id="accept_license" /> {{ trans('install.str.accept_terms') }}
                </label>
            </div>
        </fieldset>

        <a href="{{ route('install.requirements') }}" id="next_button" class="btn btn-primary float-right disabled" role="button">
            {{ trans('install.button.next') }}
            <i class="fa fa-arrow-right"></i>
        </a>

        <div class="clearfix"></div>

    </div>

@endsection

@section('js')

    <script>

        $( "#accept_license" ).click(function() {
            var checked = $('#accept_license').is(":checked");

            if (checked) {
                $("#next_button").removeClass("disabled");
            } else {
                $("#next_button").addClass("disabled");
            }
        });

    </script>

@endsection
