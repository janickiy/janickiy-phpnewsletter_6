@extends('layouts.install')

@section('content')

    @include('install.steps', ['steps' => [
        'welcome' => 'selected done',
        'requirements' => 'selected done',
        'permissions' => 'selected done',
        'database' => 'selected'
    ]])

    @include('layouts.notifications')

    {!! Form::open(['route' => 'install.installation']) !!}

    <div class="step-content">
        <h3>{{ trans('install.str.database_information') }}</h3>
        <hr>
        <div class="form-group">
            <label for="host">{{ trans('install.str.database_host') }}</label>
            <input type="text" class="form-control" id="host" name="host" placeholder="" value="{{ old('host') }}">
            <small>{{ trans('install.hint.database_host') }}</small>
            @if ($errors->has('host'))
                <p class="text-danger">{{ $errors->first('host') }}</p>
            @endif
        </div>
        <div class="form-group">
            <label for="username">{{ trans('install.str.database_username') }}</label>
            <input type="text" class="form-control" id="username" name="username" placeholder="" value="{{ old('username') }}">
            <small>{{ trans('install.hint.database_username') }}</small>
            @if ($errors->has('host'))
                <p class="text-danger">{{ $errors->first('username') }}</p>
            @endif
        </div>
        <div class="form-group">
            <label for="password">{{ trans('install.str.password') }}</label>
            <input type="password" class="form-control" id="password" name="password">
            <small>{{ trans('install.hint.database_password') }}</small>
            @if ($errors->has('password'))
                <p class="text-danger">{{ $errors->first('password') }}</p>
            @endif
        </div>
        <div class="form-group">
            <label for="database">{{ trans('install.str.database_name') }}</label>
            <input type="text" class="form-control" id="database" name="database" placeholder="" value="{{ old('database') }}">
            <small>{{ trans('install.hint.database_name') }}</small>
            @if ($errors->has('database'))
                <p class="text-danger">{{ $errors->first('database') }}</p>
            @endif
        </div>
        <button class="btn btn-primary float-right mt-3">
            {{ trans('install.button.next') }}
            <i class="fa fa-arrow-right"></i>
        </button>
        <div class="clearfix"></div>
    </div>

    {!! Form::close() !!}

@endsection

@section('js')

@endsection
