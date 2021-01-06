@extends('layouts.app')

@section('title', $title)

@section('css')

@endsection

@section('content')

    <!-- widget grid -->
    <section id="widget-grid" class="">

        <!-- row -->
        <div class="row">
            <article class="col-sm-12">
                <!-- new widget -->
                <div class="jarviswidget" id="wid-id-0" data-widget-togglebutton="false" data-widget-editbutton="false"
                     data-widget-fullscreenbutton="false" data-widget-colorbutton="false"
                     data-widget-deletebutton="false">
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
                    <header>

                        <ul class="nav nav-tabs pull-right in" id="myTab">
                            <li class="active">
                                <a data-toggle="tab" href="#s1"><i class="fa fa-gear"></i>
                                    <span class="hidden-mobile hidden-tablet">{{ trans('frontend.str.interface_settings') }}</span>
                                </a>
                            </li>
                            <li>
                                <a data-toggle="tab" href="#s2"><i class="fa fa-cogs"></i>
                                    <span class="hidden-mobile hidden-tablet">{{ trans('frontend.str.mailing_options') }}</span>
                                </a>
                            </li>
                            <li>
                                <a data-toggle="tab" href="#s3"><i class="fa fa-file-code-o"></i>
                                    <span class="hidden-mobile hidden-tablet">{{ trans('frontend.str.additional_headers') }}</span>
                                </a>
                            </li>
                        </ul>

                    </header>

                    <!-- widget div-->
                    <div class="no-padding">
                        <!-- widget edit box -->

                        <!-- end widget edit box -->

                    {!! Form::open(['url' => URL::route('admin.settings.update'), 'method' => 'put', 'class' => "form-horizontal"]) !!}

                    <!-- content -->

                        <div id="myTabContent" class="tab-content">
                            <div class="tab-pane fade active in padding-10 no-padding-bottom" id="s1">

                                <fieldset>

                                    <div class="form-group">

                                        {!! Form::label('EMAIL', trans('frontend.str.sender_email'), ['class' => 'col-md-3 control-label']) !!}

                                        <div class="col-md-9">

                                            {!! Form::text('EMAIL', SettingsHelpers::getSetting('EMAIL'), ['placeholder' => "Email", 'class' => 'form-control']) !!}

                                            @if ($errors->has('EMAIL'))
                                                <span class="text-danger">{{ $errors->first('EMAIL') }}</span>
                                            @endif

                                        </div>
                                    </div>

                                    <div class="form-group">

                                        {!! Form::label('FROM', trans('frontend.str.sender_name'), ['class' => 'col-md-3 control-label']) !!}

                                        <div class="col-md-9">

                                            {!! Form::text('FROM', SettingsHelpers::getSetting('FROM'), ['placeholder' => trans("frontend.str.sender_name"), 'class' => 'form-control']) !!}

                                            @if ($errors->has('FROM'))
                                                <span class="text-danger">{{ $errors->first('FROM') }}</span>
                                            @endif

                                        </div>
                                    </div>

                                    <div class="form-group">

                                        {!! Form::label('RETURN_PATH', trans('frontend.form.return_path'), ['class' => 'col-md-3 control-label']) !!}


                                        <div class="col-md-9">

                                            {!! Form::text('RETURN_PATH', SettingsHelpers::getSetting('RETURN_PATH'), ['placeholder' => trans("frontend.form.return_path"), 'class' => 'form-control']) !!}

                                            @if ($errors->has('RETURN_PATH'))
                                                <span class="text-danger">{{ $errors->first('RETURN_PATH') }}</span>
                                            @endif

                                        </div>
                                    </div>

                                    <div class="form-group">

                                        {!! Form::label('LIST_OWNER', trans('frontend.form.list_owner'), ['class' => 'col-md-3 control-label']) !!}

                                        <div class="col-md-9">

                                            {!! Form::text('LIST_OWNER', SettingsHelpers::getSetting('LIST_OWNER'), ['placeholder' => trans('frontend.form.list_owner'), 'class' => 'form-control']) !!}

                                            @if ($errors->has('LIST_OWNER'))
                                                <span class="text-danger">{{ $errors->first('LIST_OWNER') }}</span>
                                            @endif

                                        </div>
                                    </div>

                                    <div class="form-group">

                                        {!! Form::label('ORGANIZATION', trans('frontend.form.organization'), ['class' => 'col-md-3 control-label']) !!}


                                        <div class="col-md-9">

                                            {!! Form::text('ORGANIZATION', SettingsHelpers::getSetting('ORGANIZATION'), ['placeholder' => trans("frontend.form.organization"), 'class' => 'form-control']) !!}

                                            @if ($errors->has('ORGANIZATION'))
                                                <span class="text-danger">{{ $errors->first('ORGANIZATION') }}</span>
                                            @endif

                                        </div>
                                    </div>

                                    <div class="form-group">

                                        {!! Form::label('SUBJECT_TEXT_CONFIRM', trans('frontend.form.subject_text_confirm'), ['class' => 'col-md-3 control-label']) !!}

                                        <div class="col-md-9">

                                            {!! Form::text('SUBJECT_TEXT_CONFIRM', SettingsHelpers::getSetting('SUBJECT_TEXT_CONFIRM'), ['placeholder' => trans("frontend.form.subject_text_confirm"), 'class' => 'form-control']) !!}

                                            @if ($errors->has('SUBJECT_TEXT_CONFIRM'))
                                                <span class="text-danger">{{ $errors->first('SUBJECT_TEXT_CONFIRM') }}</span>
                                            @endif

                                        </div>
                                    </div>

                                    <div class="form-group">

                                        {!! Form::label('TEXT_CONFIRMATION', trans('frontend.form.text_confirmation'), ['class' => 'col-md-3 control-label']) !!}


                                        <div class="col-md-9">

                                            {!! Form::textarea('TEXT_CONFIRMATION', SettingsHelpers::getSetting('TEXT_CONFIRMATION'), ['rows' => "4", 'placeholder' => trans("frontend.form.text_confirmation"), 'class' => 'form-control']) !!}

                                            @if ($errors->has('TEXT_CONFIRMATION'))
                                                <span class="text-danger">{{ $errors->first('TEXT_CONFIRMATION') }}</span>
                                            @endif

                                        </div>
                                    </div>

                                    <div class="form-group">

                                        {!! Form::label('REQUIRE_SUB_CONFIRMATION', trans('frontend.form.require_subscription_confirmation'), ['class' => 'col-md-3 control-label']) !!}

                                        <div class="col-md-9">
                                            <label class="checkbox-inline">

                                                {!! Form::checkbox('REQUIRE_SUB_CONFIRMATION', 1, SettingsHelpers::getSetting('REQUIRE_SUB_CONFIRMATION') == 1 ? true : false, ['class' => 'checkbox style-0']) !!}

                                                <span></span>
                                            </label>

                                            @if ($errors->has('REQUIRE_SUB_CONFIRMATION'))
                                                <span class="text-danger">{{ $errors->first('REQUIRE_SUB_CONFIRMATION') }}</span>
                                            @endif

                                        </div>
                                    </div>

                                    <div class="form-group">

                                        {!! Form::label('UNSUBLINK', trans('frontend.form.unsublink_text'), ['class' => 'col-md-3 control-label']) !!}

                                        <div class="col-md-9">

                                            {!! Form::textarea('UNSUBLINK', SettingsHelpers::getSetting('UNSUBLINK'), ['rows' => "4", 'placeholder' => trans('frontend.form.unsublink_text'), 'class' => 'form-control']) !!}

                                        </div>

                                        @if ($errors->has('UNSUBLINK'))
                                            <span class="text-danger">{{ $errors->first('UNSUBLINK') }}</span>
                                        @endif

                                    </div>

                                </fieldset>

                            </div>
                            <!-- end s1 tab pane -->

                            <div class="tab-pane fade in padding-10" id="s2">

                                <fieldset>

                                    <div class="form-group">

                                        {!! Form::label('SHOW_UNSUBSCRIBE_LINK', trans('frontend.form.show_unsubscribe_link'), ['class' => 'col-md-3 control-label']) !!}

                                        <div class="col-md-9">
                                            <label class="checkbox-inline">

                                                {!! Form::checkbox('SHOW_UNSUBSCRIBE_LINK', 1, SettingsHelpers::getSetting('SHOW_UNSUBSCRIBE_LINK') == 1 ? true : false, ['class' => 'checkbox style-0']) !!}

                                                <span></span>
                                            </label>

                                            @if ($errors->has('SHOW_UNSUBSCRIBE_LINK'))
                                                <span class="text-danger">{{ $errors->first('SHOW_UNSUBSCRIBE_LINK') }}</span>
                                            @endif

                                        </div>
                                    </div>

                                    <div class="form-group">

                                        {!! Form::label('REQUEST_REPLY', trans('frontend.form.request_reply'), ['class' => 'col-md-3 control-label']) !!}

                                        <div class="col-md-9">
                                            <label class="checkbox-inline">

                                                {!! Form::checkbox('REQUEST_REPLY', 1, SettingsHelpers::getSetting('REQUEST_REPLY') == 1 ? true : false, ['class' => 'checkbox style-0']) !!}

                                                <span></span>
                                            </label>

                                            @if ($errors->has('REQUEST_REPLY'))
                                                <span class="text-danger">{{ $errors->first('REQUEST_REPLY') }}</span>
                                            @endif

                                        </div>
                                    </div>

                                    <div class="form-group">

                                        {!! Form::label('NEW_SUBSCRIBER_NOTIFY', trans('frontend.form.new_subscriber_notify'), ['class' => 'col-md-3 control-label']) !!}

                                        <div class="col-md-9">
                                            <label class="checkbox-inline">

                                                {!! Form::checkbox('NEW_SUBSCRIBER_NOTIFY', 1, SettingsHelpers::getSetting('NEW_SUBSCRIBER_NOTIFY') == 1 ? true : false, ['class' => 'checkbox style-0']) !!}

                                                <span></span>
                                            </label>

                                            @if ($errors->has('NEW_SUBSCRIBER_NOTIFYY'))
                                                <span class="text-danger">{{ $errors->first('NEW_SUBSCRIBER_NOTIFY') }}</span>
                                            @endif

                                        </div>
                                    </div>

                                    <div class="form-group">

                                        {!! Form::label('INTERVAL_NUMBER', trans('frontend.form.interval_number'), ['class' => 'col-md-3 control-label']) !!}

                                        <div class="col-md-6">

                                            {!! Form::text('INTERVAL_NUMBER', SettingsHelpers::getSetting('INTERVAL_NUMBER'), ['class' => 'form-control']) !!}

                                            @if ($errors->has('INTERVAL_NUMBER'))
                                                <span class="text-danger">{{ $errors->first('INTERVAL_NUMBER') }}</span>
                                            @endif

                                        </div>
                                        <div class="col-md-3">

                                            {!! Form::select('INTERVAL_TYPE', [
                                                             'no' => trans('frontend.str.no'),
                                                             'minute' => trans('frontend.form.minute'),
                                                             'hour' => trans('frontend.form.hour'),
                                                             'day' => trans('frontend.form.day'),
                                                             ], SettingsHelpers::getSetting('INTERVAL_TYPE') ? SettingsHelpers::getSetting('INTERVAL_TYPE') : 'no', ['class' => 'form-control']
                                                             ) !!}

                                        </div>
                                    </div>

                                    <div class="form-group">

                                        {!! Form::label('LIMIT_NUMBER', trans('frontend.form.limit_number'), ['class' => 'col-md-3 control-label']) !!}

                                        <div class="col-md-6">

                                            {!! Form::text('LIMIT_NUMBER', SettingsHelpers::getSetting('LIMIT_NUMBER'), ['class' => 'form-control']) !!}

                                            @if ($errors->has('LIMIT_NUMBER'))
                                                <span class="text-danger">{{ $errors->first('LIMIT_NUMBER') }}</span>
                                            @endif

                                        </div>
                                        <div class="col-md-3">
                                            <label class="checkbox-inline">

                                                {!! Form::checkbox('LIMIT_SEND', 1, SettingsHelpers::getSetting('LIMIT_SEND') == 1 ? true : false, ['class' => 'checkbox style-0']) !!}

                                                <span></span>
                                            </label>
                                        </div>
                                    </div>

                                    <div class="form-group">

                                        {!! Form::label('SLEEP', trans('frontend.form.sleep'), ['class' => 'col-md-3 control-label']) !!}

                                        <div class="col-md-9">

                                            {!! Form::text('SLEEP', SettingsHelpers::getSetting('SLEEP'), ['placeholder' => trans('frontend.form.sleep'), 'class' => 'form-control']) !!}

                                            @if ($errors->has('SLEEP'))
                                                <span class="text-danger">{{ $errors->first('SLEEP') }}</span>
                                            @endif

                                        </div>
                                    </div>

                                    <div class="form-group">

                                        {!! Form::label('DAYS_FOR_REMOVE_SUBSCRIBER', trans('frontend.form.days_for_remove_subscriber'), ['class' => 'col-md-3 control-label']) !!}

                                        <div class="col-md-6">

                                            {!! Form::text('DAYS_FOR_REMOVE_SUBSCRIBER', SettingsHelpers::getSetting('DAYS_FOR_REMOVE_SUBSCRIBER'), ['placeholder' => trans('frontend.form.days_for_remove_subscriber'), 'class' => 'form-control']) !!}

                                            @if ($errors->has('DAYS_FOR_REMOVE_SUBSCRIBER'))
                                                <span class="text-danger">{{ $errors->first('DAYS_FOR_REMOVE_SUBSCRIBER') }}</span>
                                            @endif

                                        </div>
                                        <div class="col-md-3">
                                            <label class="checkbox-inline">

                                                {!! Form::checkbox('REMOVE_SUBSCRIBER', 1, SettingsHelpers::getSetting('REMOVE_SUBSCRIBER') == 1 ? true : false, ['class' => 'checkbox style-0']) !!}

                                                <span></span>

                                            </label>
                                        </div>
                                    </div>

                                    <div class="form-group">

                                        {!! Form::label('RANDOM_SEND', trans('frontend.form.random_send'), ['class' => 'col-md-3 control-label']) !!}

                                        <div class="col-md-9">
                                            <label class="checkbox-inline">

                                                {!! Form::checkbox('RANDOM_SEND', 1, SettingsHelpers::getSetting('RANDOM_SEND') == 1 ? true : false, ['class' => 'checkbox style-0']) !!}

                                                <span></span>

                                            </label>

                                            @if ($errors->has('RANDOM_SEND'))
                                                <span class="text-danger">{{ $errors->first('RANDOM_SEND') }}</span>
                                            @endif

                                        </div>
                                    </div>

                                    <div class="form-group">

                                        {!! Form::label('RENDOM_REPLACEMENT_SUBJECT', trans('frontend.form.rendom_replacement_subject'), ['class' => 'col-md-3 control-label']) !!}

                                        <div class="col-md-9">
                                            <label class="checkbox-inline">

                                                {!! Form::checkbox('RENDOM_REPLACEMENT_SUBJECT', 1, SettingsHelpers::getSetting('RENDOM_REPLACEMENT_SUBJECT') == 1 ? true : false, ['class' => 'checkbox style-0']) !!}

                                                <span></span>
                                            </label>

                                            @if ($errors->has('RENDOM_REPLACEMENT_SUBJECT'))
                                                <span class="text-danger">{{ $errors->first('RENDOM_REPLACEMENT_SUBJECT') }}</span>
                                            @endif

                                        </div>
                                    </div>

                                    <div class="form-group">

                                        {!! Form::label('RANDOM_REPLACEMENT_BODY', trans('frontend.form.random_replacement_body'), ['class' => 'col-md-3 control-label']) !!}

                                        <div class="col-md-9">
                                            <label class="checkbox-inline">

                                                {!! Form::checkbox('RANDOM_REPLACEMENT_BODY', 1, SettingsHelpers::getSetting('RANDOM_REPLACEMENT_BODY') == 1 ? true : false, ['class' => 'checkbox style-0']) !!}

                                                <span></span>

                                            </label>

                                            @if ($errors->has('RANDOM_REPLACEMENT_BODY'))
                                                <span class="text-danger">{{ $errors->first('RANDOM_REPLACEMENT_BODY') }}</span>
                                            @endif

                                        </div>
                                    </div>

                                    <div class="form-group">

                                        {!! Form::label('PRECEDENCE', 'Precedence', ['class' => 'col-md-3 control-label']) !!}

                                        <div class="col-md-9">

                                            {!! Form::select('PRECEDENCE', [
                                                            'no' => trans('frontend.str.no'),
                                                            'bulk' => 'bulk',
                                                            'junk' => 'junk',
                                                            'list' => 'list',
                                                            ], SettingsHelpers::getSetting('PRECEDENCE') ? SettingsHelpers::getSetting('PRECEDENCE') : 'no', ['class' => 'form-control']
                                                            ) !!}

                                            @if ($errors->has('PRECEDENCE'))
                                                <span class="text-danger">{{ $errors->first('PRECEDENCE') }}</span>
                                            @endif

                                        </div>
                                    </div>

                                    <div class="form-group">

                                        {!! Form::label('CHARSETE', trans('frontend.form.charset'), ['class' => 'col-md-3 control-label']) !!}

                                        <div class="col-md-9">

                                            {!! Form::select('CHARSET', $option_charset, SettingsHelpers::getSetting('CHARSET') ? SettingsHelpers::getSetting('CHARSET') : 'no', ['class' => 'form-control'] ) !!}

                                            @if ($errors->has('CHARSET'))
                                                <span class="text-danger">{{ $errors->first('CHARSET') }}</span>
                                            @endif

                                        </div>
                                    </div>

                                    <div class="form-group">

                                        {!! Form::label('CONTENT_TYPE', trans('frontend.form.content_type'), ['class' => 'col-md-3 control-label']) !!}

                                        <div class="col-md-9">
                                            <label class="radio radio-inline">

                                                {!! Form::radio('CONTENT_TYPE', 'html', SettingsHelpers::getSetting('CONTENT_TYPE') == 'html' or SettingsHelpers::getSetting('CONTENT_TYPE') == '' ? true : false, ['class' => 'radiobox style-0'] ) !!}

                                                <span>HTML</span>
                                            </label>
                                            <label class="radio radio-inline">

                                                {!! Form::radio('CONTENT_TYPE', 'plain', SettingsHelpers::getSetting('CONTENT_TYPE') == 'plain' ? true : false, ['class' => 'radiobox style-0'] ) !!}

                                                <span>Plain</span>
                                            </label>
                                        </div>
                                    </div>

                                    <div class="form-group">

                                        {!! Form::label('HOW_TO_SEND', trans('frontend.form.how_to_send'), ['class' => 'col-md-3 control-label']) !!}

                                        <div class="col-md-9">
                                            <label class="radio radio-inline">

                                                {!! Form::radio('HOW_TO_SEND', 'php', SettingsHelpers::getSetting('HOW_TO_SEND') == 'php' or \SettingsHelpers::getSetting('HOW_TO_SEND') == '' ? true : false, ['class' => 'radiobox style-0'] ) !!}

                                                <span>PHP Mail</span>
                                            </label>
                                            <label class="radio radio-inline">

                                                {!! Form::radio('HOW_TO_SEND', 'smtp', SettingsHelpers::getSetting('HOW_TO_SEND') == 'smtp' ? true : false, ['class' => 'radiobox style-0'] ) !!}

                                                <span>SMTP</span>
                                            </label>
                                            <label class="radio radio-inline">

                                                {!! Form::radio('HOW_TO_SEND', 'sendmail', SettingsHelpers::getSetting('HOW_TO_SEND') == 'sendmail' ? true : false, ['class' => 'radiobox style-0'] ) !!}

                                                <span>Sendmail</span>
                                            </label>
                                        </div>
                                    </div>

                                    <div class="form-group">

                                        {!! Form::label('SENDMAIL_PATH', trans('frontend.form.sendmail_path'), ['class' => 'col-md-3 control-label']) !!}

                                        <div class="col-md-9">

                                            {!! Form::text('SENDMAIL_PATH', SettingsHelpers::getSetting('SENDMAIL_PATH'), ['placeholder' => trans('frontend.form.sendmail_path'), 'class' => 'form-control']) !!}

                                            @if ($errors->has('SENDMAIL_PATH'))
                                                <span class="text-danger">{{ $errors->first('SENDMAIL_PATHT') }}</span>
                                            @endif

                                        </div>
                                    </div>

                                    <div class="form-group">

                                        {!! Form::label('URL', 'URL', ['class' => 'col-md-3 control-label']) !!}

                                        <div class="col-md-9">

                                            {!! Form::text('URL', SettingsHelpers::getSetting('URL'), ['placeholder' => 'URL', 'class' => 'form-control']) !!}

                                            @if ($errors->has('URL'))
                                                <span class="text-danger">{{ $errors->first('URL') }}</span>
                                            @endif

                                        </div>
                                    </div>

                                    <div class="form-group">

                                        {!! Form::label('ADD_DKIM', trans('frontend.form.add_dkim'), ['class' => 'col-md-3 control-label']) !!}

                                        <div class="col-md-9">
                                            <label class="checkbox-inline">

                                                {!! Form::checkbox('ADD_DKIM', 1, SettingsHelpers::getSetting('ADD_DKIM') == 1 ? true : false, ['class' => 'checkbox style-0']) !!}

                                                <span></span>
                                            </label>

                                            @if ($errors->has('ADD_DKIM'))
                                                <span class="text-danger">{{ $errors->first('ADD_DKIM') }}</span>
                                            @endif

                                        </div>
                                    </div>

                                    <div class="form-group">

                                        {!! Form::label('DKIM_DOMAI', trans('frontend.form.dkim_domain'), ['class' => 'col-md-3 control-label']) !!}

                                        <div class="col-md-9">

                                            {!! Form::text('DKIM_DOMAIN', SettingsHelpers::getSetting('DKIM_DOMAIN'), ['placeholder' => trans('frontend.form.dkim_domain'), 'class' => 'form-control']) !!}

                                            @if ($errors->has('DKIM_DOMAIN'))
                                                <span class="text-danger">{{ $errors->first('DKIM_DOMAIN') }}</span>
                                            @endif

                                        </div>
                                    </div>

                                    <div class="form-group">

                                        {!! Form::label('DKIM_SELECTOR', trans('frontend.form.dkim_selector'), ['class' => 'col-md-3 control-label']) !!}

                                        <div class="col-md-9">

                                            {!! Form::text('DKIM_SELECTOR', SettingsHelpers::getSetting('DKIM_SELECTOR'), ['placeholder' => trans('frontend.form.dkim_selector'), 'class' => 'form-control']) !!}

                                            @if ($errors->has('DKIM_SELECTOR'))
                                                <span class="text-danger">{{ $errors->first('DKIM_SELECTOR') }}</span>
                                            @endif

                                        </div>
                                    </div>

                                    <div class="form-group">

                                        {!! Form::label('DKIM_PRIVATE', trans('frontend.form.dkim_private'), ['class' => 'col-md-3 control-label']) !!}

                                        <div class="col-md-9">

                                            {!! Form::textarea('DKIM_PRIVATE', SettingsHelpers::getSetting('DKIM_PRIVATE'), ['rows' => 5, 'placeholder' => trans('frontend.form.dkim_private'), 'class' => 'form-control']) !!}

                                            @if ($errors->has('DKIM_PRIVATE'))
                                                <span class="text-danger">{{ $errors->first('DKIM_SELECTOR') }}</span>
                                            @endif

                                        </div>
                                    </div>

                                    <div class="form-group">

                                        {!! Form::label('DKIM_PASSPHRASE', trans('frontend.form.dkim_passphras'), ['class' => 'col-md-3 control-label']) !!}

                                        <div class="col-md-9">

                                            {!! Form::text('DKIM_PASSPHRASE', SettingsHelpers::getSetting('DKIM_PASSPHRASE'), ['placeholder' => trans('frontend.form.dkim_passphras'), 'class' => 'form-control']) !!}

                                            @if ($errors->has('DKIM_PASSPHRASE'))
                                                <span class="text-danger">{{ $errors->first('DKIM_PASSPHRASE') }}</span>
                                            @endif

                                        </div>
                                    </div>

                                    <div class="form-group">

                                        {!! Form::label('DKIM_IDENTITY', trans('frontend.form.dkim_identity'), ['class' => 'col-md-3 control-label']) !!}

                                        <div class="col-md-9">

                                            {!! Form::text('DKIM_IDENTITY', SettingsHelpers::getSetting('DKIM_IDENTITY'), ['placeholder' => trans('frontend.form.dkim_identity'), 'class' => 'form-control']) !!}

                                            @if ($errors->has('DKIM_IDENTITYS'))
                                                <span class="text-danger">{{ $errors->first('DKIM_IDENTITY') }}</span>
                                            @endif

                                        </div>
                                    </div>

                                </fieldset>

                            </div>
                            <!-- end s2 tab pane -->

                            <div class="tab-pane fade in padding-10" id="s3">

                                <div id="headerslist">

                                    @foreach($customheaders as $c)

                                    <div class="row">
                                        <div class="col-lg-4">
                                            <div class="form-group">

                                                {!! Form::label('header_name[]', trans('frontend.form.name'), ['class' => 'col-lg-4 control-label']) !!}

                                                <div class="col-lg-8">

                                                    {!! Form::text('header_name[]', $c->name, ['class' => 'form-control']) !!}

                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-8">
                                            <div class="form-group">

                                                {!! Form::label('header_value[]', trans('frontend.form.value'), ['class' => 'col-lg-4 control-label']) !!}

                                                <div class="col-lg-6">

                                                    {!! Form::text('header_value[]', $c->value, ['class' => 'form-control']) !!}

                                                </div>
                                                <div class="col-lg-2">
                                                    <a class="btn btn-outline btn-danger removeBlock" title="{{ trans('frontend.form.remove') }}"> - </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    @endforeach

                                    <div class="form-group">
                                        <div class="col-lg-12">
                                            <input class="btn btn-default" id="add_field" type="button" value="+ {{ trans('frontend.form.add') }}">
                                        </div>
                                    </div>

                                </div>

                            </div>

                        </div>

                        <!-- end s3 tab pane -->

                        <!-- end content -->

                        <div class="padding-10">

                            <button type="submit" class="btn btn-primary pull-right margin-bottom-10">
                                {{ trans('frontend.str.apply') }}
                            </button>
                        </div>

                        {!! Form::close() !!}

                    </div>

                    <!-- end widget div -->
                </div>
                <!-- end widget -->

            </article>
        </div>

    </section>
    <!-- end widget grid -->

@endsection

@section('js')

    <script>
        $(function () {
            // Tabs
            $('#tabs').tabs();

            //hover states on the static widgets
            $('#dialog_link, #modal_link, ul#icons li').hover(
                function () {
                    $(this).addClass('ui-state-hover');
                }, function () {
                    $(this).removeClass('ui-state-hover');
                });
        });

        $(document).on("click", '#add_field', function () {
            var html = '<div class="row">';
            html += '<div class="col-lg-4">';
            html += '<div class="form-group">';
            html += '<label class="col-lg-4 control-label">{{ trans('frontend.str.name') }}</label>';
            html += '<div class="col-lg-8"><input class="form-control" type="text" value="" name="header_name[]"></div>';
            html += '</div>';
            html += '</div>';
            html += '<div class="col-lg-8">';
            html += '<div class="form-group">';
            html += '<label class="col-lg-4 control-label">{{ trans('frontend.str.value') }}</label>';
            html += '<div class="col-lg-6"><input class="form-control" type="text" value="" name="header_value[]"></div>';
            html += '<div class="col-lg-2"><a class="btn btn-outline btn-danger removeBlock" title="{{ trans('frontend.str.remove') }}"> - </a></div>';
            html += '</div>';
            html += '</div>';
            html += '</div>';

            $('#headerslist').prepend(html);

        });

        $(document).on("click", '.removeBlock', function () {
            var parent = $(this).closest('div[class^="row"]');
            parent.remove();
        });

    </script>

@endsection
