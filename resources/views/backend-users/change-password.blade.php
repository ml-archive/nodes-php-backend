@extends('nodes.backend::layouts.base')

@section('breadcrumbs')
    <li>
        <a href="{{ route('nodes.backend.users', ['page' => 1]) }}">Backend users</a>
    </li>
    <li class="active">Update password</li>
@endsection

@section('page-header-top')
    <h3>
        Update password
        <small class="text-gray-dark">(min. 6 characters)</small>
    </h3>
@endsection

@section('content')
    <div class="row">
        <div class="col-xs-12 col-md-6 margin-top">
            {!! Form::model(backend_user(), ['method' => 'patch', 'route' => ['nodes.backend.users.update-password']]) !!}
            <input type="hidden" name="id" value="{{ backend_user()->id }}">

            {{-- Password --}}
            <div class="form-group">
                <label for="backendUserFormPassword">Password</label>
                <div class="@if(validation_key_failed('password')) has-error @endif}}">
                    {!! Form::password('password', ['id' => 'backendUserFormPassword', 'class' => 'form-control']) !!}
                </div>
            </div>
            {{-- Password confirm --}}
            <div class="form-group">
                <label for="backendUserFormRepeatPassword">Repeat password</label>
                <div class="@if(validation_key_failed('password')) has-error @endif}}">
                    {!! Form::password('password_confirmation', ['id' => 'backendUserFormRepeatPassword', 'class' => 'form-control']) !!}
                </div>
            </div>

            <div class="form-group">
                <input type="submit" class="btn btn-primary form-control" value="Save">
            </div>
            {!! Form::close() !!}
        </div>
    </div>

@endsection
