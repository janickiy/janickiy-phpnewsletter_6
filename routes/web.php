<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(['middleware' => ['install']], function () {

    Route::get('pic/{subscriber}/{template}', 'FrontendController@pic')->name('frontend.pic')->where('subscriber', '[0-9]+')->where('template', '[0-9]+');
    Route::get('referral/{ref}/{subscriber}', 'FrontendController@redirectLog')->name('frontend.referral')->where('subscriber', '[0-9]+');
    Route::get('unsubscribe/{subscriber}/{token}', 'FrontendController@unsubscribe')->name('frontend.unsubscribe')->where('subscriber', '[0-9]+')->where('token', '[a-z0-9]+');
    Route::get('subscribe/{subscriber}/{token}', 'FrontendController@subscribe')->name('frontend.subscribe')->where('subscriber', '[0-9]+')->where('token', '[a-z0-9]+');
    Route::any('form', 'FrontendController@form')->name('frontend.form');
    Route::post('addsub', 'FrontendController@addSub')->name('frontend.addsub');

    Auth::routes();

    Route::group(['middleware' => ['auth']], function () {
        Route::get('/', 'TemplateController@index')->name('admin.template.index');
        Route::any('ajax', 'AjaxController@action')->name('admin.ajax.action');
        Route::group(['prefix' => 'template'], function () {
            Route::get('create', 'TemplateController@create')->name('admin.template.create');
            Route::post('store', 'TemplateController@store')->name('admin.template.store');
            Route::get('edit/{id}', 'TemplateController@edit')->name('admin.template.edit')->where('id', '[0-9]+');
            Route::put('update', 'TemplateController@update')->name('admin.template.update');
            Route::delete('destroy/{id}', 'TemplateController@destroy')->name('admin.template.destroy')->where('id', '[0-9]+');
            Route::post('status', 'TemplateController@status')->name('admin.template.status');
        });

        Route::group(['prefix' => 'subscribers'], function () {
            Route::get('', 'SubscribersController@index')->name('admin.subscribers.index')->middleware(['permission:admin|moderator']);
            Route::get('create', 'SubscribersController@create')->name('admin.subscribers.create')->middleware(['permission:admin|moderator']);
            Route::post('store', 'SubscribersController@store')->name('admin.subscribers.store')->middleware(['permission:admin|moderator']);
            Route::get('edit/{id}', 'SubscribersController@edit')->name('admin.subscribers.edit')->where('id', '[0-9]+')->middleware(['permission:admin|moderator']);
            Route::put('update', 'SubscribersController@update')->name('admin.subscribers.update')->middleware(['permission:admin|moderator']);
            Route::delete('destroy/{id}', 'SubscribersController@destroy')->name('admin.subscribers.destroy')->middleware(['permission:admin|moderator']);
            Route::get('import', 'SubscribersController@import')->name('admin.subscribers.import')->middleware(['permission:admin|moderator']);
            Route::post('import-subscribers', 'SubscribersController@importSubscribers')->name('admin.subscribers.import_subscribers')->middleware(['permission:admin|moderator']);
            Route::get('export', 'SubscribersController@export')->name('admin.subscribers.export')->middleware(['permission:admin|moderator']);
            Route::post('export-subscribers', 'SubscribersController@exportSubscribers')->name('admin.subscribers.export_subscribers')->middleware(['permission:admin|moderator']);
            Route::get('remove-all', 'SubscribersController@removeAll')->name('admin.subscribers.remove_all')->middleware(['permission:admin|moderator']);
            Route::post('status', 'SubscribersController@status')->name('admin.subscribers.status')->middleware(['permission:admin|moderator']);
        });

        Route::group(['prefix' => 'category'], function () {
            Route::get('', 'CategoryController@index')->name('admin.category.index')->middleware(['permission:admin|moderator']);
            Route::get('create', 'CategoryController@create')->name('admin.category.create')->middleware(['permission:admin|moderator']);
            Route::post('store', 'CategoryController@store')->name('admin.category.store')->middleware(['permission:admin|moderator']);
            Route::get('edit/{id}', 'CategoryController@edit')->name('admin.category.edit')->where('id', '[0-9]+')->middleware(['permission:admin|moderator']);
            Route::put('update', 'CategoryController@update')->name('admin.category.update')->middleware(['permission:admin|moderator']);
            Route::delete('destroy/{id}', 'CategoryController@destroy')->name('admin.category.destroy')->where('id', '[0-9]+')->middleware(['permission:admin|moderator']);
        });

        Route::group(['prefix' => 'smtp'], function () {
            Route::get('', 'SmtpController@index')->name('admin.smtp.index')->middleware(['permission:admin']);
            Route::get('create', 'SmtpController@create')->name('admin.smtp.create')->middleware(['permission:admin']);
            Route::post('store', 'SmtpController@store')->name('admin.smtp.store')->middleware(['permission:admin']);
            Route::get('edit/{id}', 'SmtpController@edit')->name('admin.smtp.edit')->where('id', '[0-9]+')->middleware(['permission:admin']);
            Route::put('update', 'SmtpController@update')->name('admin.smtp.update')->middleware(['permission:admin']);
            Route::delete('destroy/{id}', 'SmtpController@destroy')->name('admin.smtp.destroy')->where('id', '[0-9]+')->middleware(['permission:admin']);
            Route::post('status', 'SmtpController@status')->name('admin.smtp.status')->middleware(['permission:admin']);
        });

        Route::group(['prefix' => 'users'], function () {
            Route::get('', 'UsersController@index')->name('admin.users.index')->middleware(['permission:admin']);
            Route::get('create', 'UsersController@create')->name('admin.users.create')->middleware(['permission:admin']);
            Route::post('store', 'UsersController@store')->name('admin.users.store')->middleware(['permission:admin']);
            Route::get('edit/{id}', 'UsersController@edit')->name('admin.users.edit')->where('id', '[0-9]+');
            Route::put('update', 'UsersController@update')->name('admin.users.update')->middleware(['permission:admin']);
            Route::delete('destroy/{id}', 'UsersController@destroy')->name('admin.users.destroy')->where('id', '[0-9]+')->middleware(['permission:admin']);
        });

        Route::group(['prefix' => 'update'], function () {
            Route::get('', 'UpdateController@index')->name('admin.update.index');
            Route::post('', 'UpdateController@addLicenseKey')->name('admin.update.add_license_key');
        });

        Route::group(['prefix' => 'schedule'], function () {
            Route::get('', 'ScheduleController@index')->name('admin.schedule.index');
            Route::get('create', 'ScheduleController@create')->name('admin.schedule.create');
            Route::post('store', 'ScheduleController@store')->name('admin.schedule.store');
            Route::get('edit/{id}', 'ScheduleController@edit')->name('admin.schedule.edit')->where('id', '[0-9]+');
            Route::put('update', 'ScheduleController@update')->name('admin.schedule.update');
            Route::delete('destroy/{id}', 'ScheduleController@destroy')->name('admin.schedule.destroy')->where('id', '[0-9]+');
        });

        Route::group(['prefix' => 'log'], function () {
            Route::get('', 'LogController@index')->name('admin.log.index');
            Route::get('clear', 'LogController@clear')->name('admin.log.clear');
            Route::get('download/{id}', 'LogController@download')->name('admin.log.report')->where('id', '[0-9]+');
            Route::get('info/{id}', 'LogController@info')->name('admin.log.info')->where('id', '[0-9]+');
        });

        Route::group(['prefix' => 'redirect'], function () {
            Route::get('', 'RedirectController@index')->name('admin.redirect.index');
            Route::get('clear', 'RedirectController@clear')->name('admin.redirect.clear');
            Route::get('download/{url}', 'RedirectController@download')->name('admin.redirect.download');
            Route::get('info/{url}', 'RedirectController@info')->name('admin.redirect.info');
        });

        Route::group(['prefix' => 'settings'], function () {
            Route::get('', 'SettingsController@index')->name('admin.settings.index')->middleware(['permission:admin']);
            Route::put('update', 'SettingsController@update')->name('admin.settings.update')->middleware(['permission:admin']);
        });

        Route::group(['prefix' => 'faq'], function () {
            Route::get('', 'FaqController@index')->name('admin.faq.index');
        });

        Route::group(['prefix' => 'miscellaneous'], function () {
            Route::get('cron_job_list', 'MiscellaneousController@cron_job_list')->name('admin.miscellaneous.cron_job_list')->middleware(['permission:admin|moderator']);
            Route::get('phpinfo', 'MiscellaneousController@phpinfo')->name('admin.miscellaneous.phpinfo')->middleware(['permission:admin|moderator']);
        });

        Route::group(['prefix' => 'datatable'], function () {
            Route::any('templates', 'DataTableController@getTemplates')->name('admin.datatable.templates');
            Route::any('category', 'DataTableController@getCategory')->name('admin.datatable.category')->middleware(['permission:admin|moderator']);
            Route::any('subscribers', 'DataTableController@getSubscribers')->name('admin.datatable.subscribers')->middleware(['permission:admin|moderator']);
            Route::any('settings', 'DataTableController@getSettings')->name('admin.datatable.settings');
            Route::any('users', 'DataTableController@getUsers')->name('admin.datatable.users')->middleware(['permission:admin']);
            Route::any('smtp', 'DataTableController@getSmtp')->name('admin.datatable.smtp')->middleware(['permission:admin']);
            Route::any('log', 'DataTableController@getLog')->name('admin.datatable.log');
            Route::any('info-log/{id?}', 'DataTableController@getInfoLog')->name('admin.datatable.info_log')->where('id', '[0-9]+');
            Route::any('redirect-log', 'DataTableController@getRedirectLog')->name('admin.datatable.redirect');
            Route::any('info-redirect-log/{url}', 'DataTableController@getInfoRedirectLog')->name('admin.datatable.info_redirect');
        });
    });

    //отображение формы аутентификации
    Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');

    //POST запрос аутентификации на сайте
    Route::post('login', 'Auth\LoginController@login')->name('singin');
    Route::get('logout', 'Auth\LoginController@logout')->name('logout');
});

Route::group(['prefix' => 'install'], function () {
    Route::get('/', 'InstallController@index')->name('install.start');
    Route::get('requirements', 'InstallController@requirements')->name('install.requirements');
    Route::get('permissions', 'InstallController@permissions')->name('install.permissions');
    Route::get('database', 'InstallController@databaseInfo')->name('install.database');
    Route::get('start-installation', 'InstallController@installation')->name('install.installation');
    Route::post('start-installation', 'InstallController@installation')->name('install.installation');
    Route::post('install-app', 'InstallController@install')->name('install.install');
    Route::get('complete', 'InstallController@complete')->name('install.complete');
    Route::get('error', 'InstallController@error')->name('install.error');
    Route::any('ajax', 'InstallController@ajax')->name('install.ajax.action');
});
