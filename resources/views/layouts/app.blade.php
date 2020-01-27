<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <title>{{ trans('frontend.str.admin_panel') }} | @yield('title')</title>
    <meta name="description" content="">
    <meta name="author" content="">

    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

    <!-- #CSS Links -->
    <!-- Basic Styles -->

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {!! Html::style('/css/bootstrap.min.css') !!}

    {!! Html::style('/admin/css/font-awesome.min.css') !!}

    {!! Html::style('/admin/css/admin.css') !!}

    <!-- SmartAdmin Styles : Caution! DO NOT change the order -->

    {!! Html::style('/admin/css/smartadmin-production-plugins.min.css') !!}

    {!! Html::style('/admin/css/smartadmin-production.min.css') !!}

    {!! Html::style('/admin/css/smartadmin-skins.min.css') !!}

    <!-- SmartAdmin RTL Support -->

    {!! Html::style('/admin/css/smartadmin-rtl.min.css') !!}

    {!! Html::style('/admin/js/plugin/daterangepicker/daterangepicker.css') !!}

    <!-- We recommend you use "your_style.css" to override SmartAdmin
         specific styles this will also ensure you retrain your customization with each SmartAdmin update.
    <link rel="stylesheet" type="text/css" media="screen" href="css/your_style.css"> -->

    {!! Html::style('/admin/js/plugin/sweetalert/sweetalert.css') !!}

    {!! Html::style('/admin/js/plugin/jquery-treeview-master/jquery.treeview.css') !!}

    {!! Html::style('/admin/js/plugin/datetimepicker/jquery.datetimepicker.css') !!}

    {!! Html::style('/admin/js/plugin/jquery-treeview-master/jquery.treeview.css') !!}

    <!-- #GOOGLE FONT -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans:400italic,700italic,300,400,700">

    @yield('css')

    <script type="text/javascript">
        var SITE_URL = "{{ url('/') }}";
    </script>

</head>

<!--

TABLE OF CONTENTS.

Use search to find needed section.

===================================================================

|  01. #CSS Links                |  all CSS links and file paths  |
|  02. #FAVICONS                 |  Favicon links and file paths  |
|  03. #GOOGLE FONT              |  Google font link              |
|  04. #APP SCREEN / ICONS       |  app icons, screen backdrops   |
|  05. #BODY                     |  body tag                      |
|  06. #HEADER                   |  header tag                    |
|  07. #PROJECTS                 |  project lists                 |
|  08. #TOGGLE LAYOUT BUTTONS    |  layout buttons and actions    |
|  09. #MOBILE                   |  mobile view dropdown          |
|  10. #SEARCH                   |  search field                  |
|  11. #NAVIGATION               |  left panel & navigation       |
|  12. #MAIN PANEL               |  main panel                    |
|  13. #MAIN CONTENT             |  content holder                |
|  14. #PAGE FOOTER              |  page footer                   |
|  15. #SHORTCUT AREA            |  dropdown shortcuts area       |
|  16. #PLUGINS                  |  all scripts and plugins       |

===================================================================

-->

<!-- #BODY -->
<!-- Possible Classes

    * 'smart-style-{SKIN#}'
    * 'smart-rtl'         - Switch theme mode to RTL
    * 'menu-on-top'       - Switch to top navigation (no DOM change required)
    * 'no-menu'			  - Hides the menu completely
    * 'hidden-menu'       - Hides the main menu but still accessable by hovering over left edge
    * 'fixed-header'      - Fixes the header
    * 'fixed-navigation'  - Fixes the main menu
    * 'fixed-ribbon'      - Fixes breadcrumb
    * 'fixed-page-footer' - Fixes footer
    * 'container'         - boxed layout mode (non-responsive: will not work with fixed-navigation & fixed-ribbon)
-->
<body class="">

<div id="overlay"></div>

<!-- #HEADER -->
<header id="header">
    <div id="logo-group">

        <!-- PLACE YOUR LOGO HERE -->
        <span id="logo"> <img src="{{ url('/admin/img/logo.png') }}" alt="SmartAdmin"> </span>

    </div>

    <!-- #TOGGLE LAYOUT BUTTONS -->
    <!-- pulled right: nav area -->
    <div class="pull-right">

        <!-- collapse menu button -->
        <div id="hide-menu" class="btn-header pull-right">
            <span> <a href="javascript:void(0);" data-action="toggleMenu" title="Collapse Menu"><i class="fa fa-reorder"></i></a> </span>
        </div>
        <!-- end collapse menu -->

        <!-- #MOBILE -->
        <!-- Top menu profile link : this shows only when top menu is active -->
        <ul id="mobile-profile-img" class="header-dropdown-list hidden-xs padding-5">
            <li class="">
                <a href="#" class="dropdown-toggle no-margin userdropdown" data-toggle="dropdown">
                    <img src="{{ url('/admin/img/avatars/sunny.png') }}" alt="" class="online"/>
                </a>
                <ul class="dropdown-menu pull-right">
                    <li>
                        <a href="javascript:void(0);" class="padding-10 padding-top-0 padding-bottom-0" data-action="toggleShortcut"><i class="fa fa-arrow-down"></i>
                            <u>{{ trans('frontend.str.roll_up') }}</u>
                        </a>
                    </li>
                    <li class="divider"></li>
                    <li>
                        <a href="javascript:void(0);" class="padding-10 padding-top-0 padding-bottom-0" data-action="launchFullscreen">
                            <i class="fa fa-arrows-alt"></i><u>{{ trans('frontend.str.expand_full_screen') }}</u>
                        </a>
                    </li>
                    <li class="divider"></li>
                    <li>
                        <a href="{{ URL::route('logout') }}" class="padding-10 padding-top-5 padding-bottom-5" data-action="userLogout"><i class="fa fa-sign-out fa-lg"></i>
                            <strong><u>{{ trans('frontend.str.signout') }}</u></strong>
                        </a>
                    </li>
                </ul>
            </li>
        </ul>

        <!-- logout button -->
        <div id="logout" class="btn-header transparent pull-right">
            <span> <a href="{{ URL::route('logout') }}" title="{{ trans('frontend.str.signout') }}" data-action="userLogout"><i class="fa fa-sign-out"></i></a> </span>
        </div>
        <!-- end logout button -->

        <!-- search mobile button (this is hidden till mobile view port) -->
        <div id="search-mobile" class="btn-header transparent pull-right">
            <span> <a href="javascript:void(0)" title="Search"><i class="fa fa-search"></i></a> </span>
        </div>
        <!-- end search mobile button -->


        <!-- fullscreen button -->
        <div id="fullscreen" class="btn-header transparent pull-right">
            <span> <a href="javascript:void(0);" data-action="launchFullscreen" title="{{ trans('frontend.str.expand_full_screen') }}"><i class="fa fa-arrows-alt"></i></a> </span>
        </div>
        <!-- end fullscreen button -->

        <!-- multiple lang dropdown : find all flags in the flags page -->
        <ul class="header-dropdown-list hidden-xs">
            <li>

                @if( Config::get('app.locale') == 'ru')
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown"> <img src="{{ url('/admin/img/blank.gif') }}" class="flag flag-ru" alt="Русский (Russian)">
                        <span> Русский</span> <i class="fa fa-angle-down"></i>
                    </a>
                @else
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown"> <img src="{{ url('/admin/img/blank.gif') }}" class="flag flag-gb" alt="English">
                        <span> English</span> <i class="fa fa-angle-down"></i>
                    </a>
                @endif

                <ul id="select-lang" class="dropdown-menu pull-right">
                    <li data-id="en" class="active">
                        <a href="javascript:void(0);"><img src="{{ url('/admin/img/blank.gif') }}" class="flag flag-gb" alt="English"> English</a>
                    </li>
                    <li data-id="ru" class="">
                        <a href="javascript:void(0);"><img src="{{ url('/admin/img/blank.gif') }}" class="flag flag-ru" alt="Russia"> Русский (Russian)</a>
                    </li>
                </ul>
            </li>
        </ul>
        <!-- end multiple lang -->

    </div>
    <!-- end pulled right: nav area -->

</header>
<!-- END HEADER -->

<!-- #NAVIGATION -->
<!-- Left panel : Navigation area -->
<!-- Note: This width of the aside area can be adjusted through LESS variables -->
<aside id="left-panel">

    <!-- User info -->
    <div class="login-info">
				<span> <!-- User image size is adjusted inside CSS, it should stay as it -->
					<a>
						<span>
							{{ Auth::user()->name }}
						</span>
					</a>
				</span>
    </div>
    <!-- end user info -->

    <nav>
        <!--
        NOTE: Notice the gaps after each icon usage <i></i>..
        Please note that these links work a bit different than
        traditional href="" links. See documentation for details.
        -->

        <ul>

            <li {{ Request::is('template*') ? ' class=active' : '' }}>
                <a href="{{ URL::route('admin.template.index') }}">
                    <i class="fa fa-fw fa-envelope"></i> <span class="menu-item-parent">{{ trans('frontend.menu.templates') }}</span>
                </a>
            </li>

            <li {{ Request::is('schedule*') ? ' class=active' : '' }}>
                <a href="{{ URL::route('admin.schedule.index') }}" title="{{ trans('frontend.menu.schedule') }}">
                    <i class="fa fa-fw fa-hourglass"></i> <span class="menu-item-parent">{{ trans('frontend.menu.schedule') }}</span>
                </a>
            </li>

            @if(Helpers::has_permission(Auth::user()->role,'admin|moderator'))

                <li {{ Request::is('subscribers*') ? ' class=active' : '' }}>
                    <a href="{{ URL::route('admin.subscribers.index') }}" title="{{ trans('frontend.menu.subscribers') }}">
                        <i class="fa fa-fw fa-users"></i> <span class="menu-item-parent">{{ trans('frontend.menu.subscribers') }}</span>
                    </a>
                </li>

            @endif

            @if(Helpers::has_permission(Auth::user()->role,'admin|moderator'))

                <li {{ Request::is('category*') ? ' class=active' : '' }}>
                    <a href="{{ URL::route('admin.category.index') }}" title="{{ trans('frontend.menu.subscribers_category') }}">
                        <i class="fa fa-fw fa-th-list"></i> <span class="menu-item-parent">{{ trans('frontend.menu.subscribers_category') }}</span>
                    </a>
                </li>

            @endif

            @if(Helpers::has_permission(Auth::user()->role,'admin'))

                <li {{ Request::is('smtp*') ? ' class=active' : '' }}>
                    <a href="{{ URL::route('admin.smtp.index') }}" title="SMTP">
                        <i class="fa fa-fw fa-database"></i> <span class="menu-item-parent">SMTP </span>
                    </a>
                </li>

            @endif

            <li class="">
                <a href="#">
                    <i class="fa fa-fw fa-area-chart"></i> <span class="menu-item-parent">{{ trans('frontend.menu.logs') }}</span>
                </a>

                <ul class="treeview-menu">

                    <li {{ Request::is('log*') ? ' class=active' : '' }}>
                        <a href="{{ URL::route('admin.log.index') }}" title="{{ trans('frontend.menu.mailing_log') }}">
                            <i class="fa fa-fw fa-list"></i> <span class="menu-item-parent">{{ trans('frontend.menu.mailing_log') }}</span>
                        </a>
                    </li>

                    <li {{ Request::is('redirect*') ? ' class=active' : '' }}>
                        <a href="{{ URL::route('admin.redirect.index') }}" title="{{ trans('frontend.menu.referrens_log') }}">
                            <i class="fa fa-fw fa-list"></i> <span class="menu-item-parent">{{ trans('frontend.menu.referrens_log') }}</span>
                        </a>
                    </li>
                </ul>
            </li>

            @if(Helpers::has_permission(Auth::user()->role,'admin'))

                <li {{  Request::is('settings*') ? ' class=active' : '' }}>
                    <a href="{{ URL::route('admin.settings.index') }}" title="{{ trans('frontend.menu.settings') }}">
                        <i class="fa fa-fw fa-gear"></i> <span class="menu-item-parent">{{ trans('frontend.menu.settings') }}</span>
                    </a>
                </li>

            @endif

            @if(Helpers::has_permission(Auth::user()->role,'admin'))

                <li {{ Request::is('users*') ? ' class=active' : '' }}>
                    <a href="{{ URL::route('admin.users.index') }}" title="{{ trans('frontend.menu.users') }}">
                        <i class="fa fa-fw fa-group"></i> <span class="menu-item-parent">{{ trans('frontend.menu.users') }}</span>
                    </a>
                </li>

            @endif

            <li {{ Request::is('update*') ? ' class=active' : '' }}>
                <a href="{{ URL::route('admin.update.index') }}" title="FAQ">
                    <i class="fa fa-fw fa-refresh"></i> <span class="menu-item-parent">{{ trans('frontend.menu.update') }}</span>
                </a>
            </li>

            <li {{ Request::is('faq*') ? ' class=active' : '' }}>
                <a href="{{ URL::route('admin.faq.index') }}" title="FAQ">
                    <i class="fa fa-fw fa-question-circle"></i> <span class="menu-item-parent">FAQ</span>
                </a>
            </li>

            @if(Helpers::has_permission(Auth::user()->role,'admin|moderator'))

                <li class="">
                    <a href="#">
                        <i class="fa fa-fw fa-bookmark"></i> <span class="menu-item-parent">{{ trans('frontend.menu.miscellaneous') }}</span>
                    </a>

                    <ul class="treeview-menu">

                        <li {{ Request::is('miscellaneous/cron_job_list*') ? ' class=active' : '' }}>
                            <a href="{{ URL::route('admin.miscellaneous.cron_job_list') }}" title="{{ trans('frontend.menu.cron_job_list') }}">
                                <i class="fa fa-fw fa-list"></i> <span class="menu-item-parent">{{ trans('frontend.menu.cron_job_list') }}</span>
                            </a>
                        </li>

                        <li {{ Request::is('miscellaneous/phpinfo*') ? ' class=active' : '' }}>
                            <a href="{{ URL::route('admin.miscellaneous.phpinfo') }}" title="PHP Info">
                                <i class="fa fa-fw fa-list"></i> <span class="menu-item-parent">PHP Info</span>
                            </a>
                        </li>
                    </ul>
                </li>

            @endif

        </ul>
    </nav>

    <span class="minifyme" data-action="minifyMenu"><i class="fa fa-arrow-circle-left hit"></i></span>

</aside>
<!-- END NAVIGATION -->

<!-- MAIN PANEL -->
<div id="main" role="main">

    <!-- RIBBON -->
    <div id="ribbon">

        <!-- RIBBON -->
        <div id="ribbon">

            <!-- breadcrumb -->
            <ol class="breadcrumb">
                <li>{{ trans('frontend.str.admin_panel') }}</li>
                <li>@yield('title')</li>
            </ol>
            <!-- end breadcrumb -->

        </div>
        <!-- END RIBBON -->

        <!-- You can also add more buttons to the
        ribbon for further usability

        Example below:

        <span class="ribbon-button-alignment pull-right">
        <span id="search" class="btn btn-ribbon hidden-xs" data-title="search"><i class="fa-grid"></i> Change Grid</span>
        <span id="add" class="btn btn-ribbon hidden-xs" data-title="add"><i class="fa-plus"></i> Add</span>
        <span id="search" class="btn btn-ribbon" data-title="search"><i class="fa-search"></i> <span class="hidden-mobile">Search</span></span>
        </span> -->

    </div>

    <!-- MAIN CONTENT -->
    <div id="content">

        @if (isset($title))<h2>{!! $title !!}</h2>@endif

        @include('layouts.notifications')

        @yield('content')

    </div>
    <!-- END MAIN CONTENT -->

</div>
<!-- END MAIN PANEL -->

<!-- PAGE FOOTER -->
<div class="page-footer">
    <div class="row">
        <div class="col-xs-12 col-sm-6">
            <p class="txt-color-white">© 2006-{{ date('Y') }} <a  href="http://janicky.com">PHP Newsletter</a> <span class="footer_version">{{ env('VERSION') }}</span>, {{ trans('frontend.str.author') }}</p>
        </div>

    </div>
</div>
<!-- END PAGE FOOTER -->

<!--================================================== -->

<!-- PACE LOADER - turn this on if you want ajax loading to show (caution: uses lots of memory on iDevices)
<script data-pace-options='{ "restartOnRequestAfter": true }' src="js/plugin/pace/pace.min.js"></script>-->

<!-- #PLUGINS -->
<!-- Link to Google CDN's jQuery + jQueryUI; fall back to local -->
<script src="//ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script>

    if (!window.jQuery) {
        document.write('<script src="{{ url('/js/libs/jquery-3.2.1.min.js') }}"><\/script>');
    }

</script>

<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>

<script>
    if (!window.jQuery.ui) {
        document.write('<script src="{{ url('/js/libs/jquery-ui.min.js') }}"><\/script>');
    }
</script>

<!-- IMPORTANT: APP CONFIG -->

{!! Html::script('/admin/js/app.config.js') !!}

<!-- BOOTSTRAP JS -->

{!! Html::script('/admin/js/bootstrap/bootstrap.min.js') !!}

<!--[if IE 8]>
<h1>Your browser is out of date, please update your browser by going to www.microsoft.com/download</h1>
<![endif]-->

<!-- MAIN APP JS FILE -->

{!! Html::script('/admin/js/app.min.js') !!}

{!! Html::script('/admin/js/plugin/datatables/jquery.dataTables.min.js') !!}

{!! Html::script('/admin/js/plugin/datatables/dataTables.colVis.min.js') !!}

{!! Html::script('/admin/js/plugin/datatables/dataTables.tableTools.min.js') !!}

{!! Html::script('/admin/js/plugin/datatables/dataTables.bootstrap.min.js') !!}

{!! Html::script('/admin/js/plugin/datatable-responsive/datatables.responsive.min.js') !!}

{!! Html::script('/admin/js/plugin/sweetalert/sweetalert-dev.js') !!}

{!! Html::script('/admin/js/plugin/datetimepicker/jquery.datetimepicker.full.js') !!}

{!! Html::script('/admin/js/plugin/jquery-treeview-master/jquery.treeview.js') !!}

{!! Html::script('/admin/js/plugin/cookie/jquery.cookie.js') !!}

<script>

    $(function() {

        $.ajax({
            cache: false,
            url: '{{ URL::route('admin.ajax.action') }}',
            method: "POST",
            data: {
                action: "alert_update",
            },
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            dataType: "json",
            success: function(data){
                if (data.msg != '' && $.cookie('alertshow') != 'no'){
                    $('#alert_msg_block').fadeIn('700');
                    $("#alert_warning_msg").append(data.msg);
                }
            }
        });

        $('ul.dropdown-menu li').on('click', function() {
            $(this).parent().find('li.active').removeClass('active');
            $(this).addClass('active');

            var Lng = $(this).attr('data-id');

            var request = $.ajax({
                url: '{{ URL::route('admin.ajax.action') }}',
                method: "POST",
                data: {
                    action: "change_lng",
                    locale: Lng,
                },
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                dataType: "json"
            });

            request.done(function (data) {
                if (data.result != null && data.result == true) {
                    location.reload();
                }
            });
        });

        $('.close').on('click', function(){
            var deleted_block = $(this).parent(),
                bl_h = deleted_block.outerHeight(),
                bk_index = deleted_block.index(),
                next_bl = deleted_block.siblings(':eq('+bk_index+')'),
                marg = parseInt(deleted_block.css('margin-bottom'));

            deleted_block.fadeOut(500);

            setTimeout(function(){
                $(next_bl).css('margin-top', bl_h+marg);
                $(next_bl).animate({
                    marginTop: 0
                },400);
            }, 505);

            setTimeout(function(){
                deleted_block.remove();
            }, 700);
            return false;
        });

        setTimeout(function(){
            setTimeout(function(){$('.alert-success').fadeOut('700')},5000);
        });
    });

    $(document).on( "click", "a.opislink:not(.active)", function() {
        $(this).addClass('active');
        $(this).parent().find('div.opis').slideDown(760);
        return false;
    });

    $(document).on( "click", "a.opislink.active", function() {
        $(this).removeClass('active');
        $(this).parent().find('div.opis').slideUp(760);
        return false;
    });

</script>

@yield('js')

</body>

</html>
