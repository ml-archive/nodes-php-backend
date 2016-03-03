@extends('nodes.backend::layouts.base')

@section('breadcrumbs')
    <li>
        <a href="{{ route('nodes.backend.users', ['page' => 1]) }}">Backend users</a>
    </li>
    <li class="active">Edit backend user</li>
@endsection

@section('page-header-top')
    <h1>
        @if (!empty($user))
            Edit backend user
        @else
            Create backend user
        @endif
    </h1>
@endsection

@section('content')
    <div class="row">

        @if (!empty($user))
            {!! Form::model($user, ['method' => 'patch', 'files' => true, 'route' => ['nodes.backend.users.update']]) !!}
            <input type="hidden" name="id" value="{{ $user->id }}">
        @else
            {!! Form::open(['method' => 'post', 'files' => true, 'route' => 'nodes.backend.users.store']) !!}
        @endif

            <div class="col-xs-12 col-md-6">
                <h4 class="margin-top">User details</h4>
                <hr/>

                <div class="margin-vertical-sm">
                    {{-- Name --}}
                    <div class="form-group">
                        <label for="backendUserFormName">Name</label>
                        <div class="@if(validation_key_failed('name')) has-error @endif}}">
                            {!! Form::text('name', null, ['id' => 'backendUserFormName', 'class' => 'form-control']) !!}
                        </div>
                    </div>

                    {{-- E-mail --}}
                    <div class="form-group">
                        <label for="backendUserFormEmail">E-mail</label>
                        <div class="@if(validation_key_failed('email')) has-error @endif}}">
                            {!! Form::email('email', null, ['id' => 'backendUserFormEmail', 'class' => 'form-control']) !!}
                        </div>
                    </div>

                    {{-- Image --}}
                    @if (!empty($user))
                        {{-- Edit --}}
                        @include('nodes.backend::partials.components.file-picker', [
                            'label' => 'Image',
                            'image' => assets_get($user->image)
                        ])
                    @else
                        {{-- Create --}}
                        @include('nodes.backend::partials.components.file-picker', [
                            'label' => 'Image'
                        ])
                    @endif

                    {{-- Role --}}
                    <div class="form-group">
                        <label for="backendUserFormRole">Role</label>
                        @if(validation_key_failed('user_role'))
                            {!! Form::select('user_role', $roles, !empty($user) ? $user->user_role : $roleDefault, ['id' => 'backendUserFormRole', 'class' => 'form-control has-error']) !!}
                        @else
                            {!! Form::select('user_role', $roles, !empty($user) ? $user->user_role : $roleDefault, ['id' => 'backendUserFormRole', 'class' => 'form-control']) !!}
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-xs-12 col-md-6">
                @can('backend-edit-backend-user', !empty($user) ? $user : null)
                <h4 class="margin-top">
                    @if (!empty($user))
                        Change password
                    @else
                        Choose password <small class="text-gray-dark">(leave empty for random) / </small>
                    @endif
                    <small class="text-gray-dark">(min. 6 characters)</small>
                </h4>

                <hr/>

                <div class="margin-top">
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

                    {{-- Force user to reset pw on next login --}}
                    @can('backend-admin')
                    <div class="form-group">
                        <input name="should_reset_password" value="false" type="hidden">

                        <label class="@if(validation_key_failed('change_password')) has-error @endif}}">
                            {!! Form::checkbox('change_password', true, empty($user) ? true : $user->change_password, ['id' => 'backendUserFormResetPwOnLogin']) !!} Reset password on login
                        </label>

                    </div>
                    @endcan
                </div>
                @endcan
            </div>

            <div class="col-xs-12 margin-top">
                {{-- Send mail with info --}}
                @if (empty($user))
                    <div class="form-group">
                        <input name="send_mail" value="false" type="hidden">
                        <label>
                            {!! Form::checkbox('send_mail', true, true, ['id' => 'backendUserFormSendMail']) !!} Send email with information
                        </label>
                    </div>
                @endif

                <div class="form-group">
                    @if (!empty($user))
                        <input type="submit" class="btn btn-primary" value="Update backend user">
                    @else
                        <input type="submit" class="btn btn-primary" value="Create backend user">
                    @endif
                </div>
            </div>
        {!! Form::close() !!}

    </div>
@endsection
