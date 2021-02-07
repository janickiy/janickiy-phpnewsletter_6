
<div class="alert alert-warning alert-dismissable" id="alert_msg_block" style="display:none;">
    <button class="close" aria-hidden="true" data-dismiss="alert" onClick="$.cookie('alertshow', 'no');" type="button">×</button>
    <strong>{{ trans('frontend.str.warning_alert') }}</strong>
    <span id="alert_warning_msg"></span>
</div>

@if (\App\Helpers\StringHelpers::expiredDayAlert())
    <div class="alert alert-warning">
        <i class="fa-fw fa fa-warning"></i>
        {!! \App\Helpers\StringHelpers::expiredDayAlert() !!}
    </div>
@endif

@if (isset($infoAlert))
    <div class="alert alert-info">
        <i class="fa-fw fa fa-warning"></i>
        {!! $infoAlert !!}
    </div>
@endif

@if (Session::has('message'))
    <div class="alert alert-warning fade in">
        <button class="close" data-dismiss="alert">
            ×
        </button>
        <i class="fa-fw fa fa-warning"></i>
        {{ Session::get('message') }}.
    </div>
@endif

@if (session('success'))
    <div class="alert alert-success fade in">
        <button class="close" data-dismiss="alert">
            ×
        </button>
        <i class="fa-fw fa fa-check"></i>
        {{ session('success') }}
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger fade in">
        <button class="close" data-dismiss="alert">
            ×
        </button>
        <i class="fa-fw fa fa-check"></i>
        {{ session('error') }}
    </div>
@endif

@if ($errors->any())
    <div class="alert alert-danger fade in">
        <button class="close" data-dismiss="alert">
            ×
        </button>
        <i class="fa-fw fa fa-times"></i>
        <strong>{{ trans('frontend.str.error_alert') }}</strong>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

