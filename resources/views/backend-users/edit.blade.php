@extends('nodes.backend::layouts.base')

@section('breadcrumbs')
    <li>
        <a href="{{ route('nodes.backend.users', ['page' => 1]) }}">Backend users</a>
    </li>
    <li class="active">Edit backend user</li>
@endsection

@section('page-header-top')
    <h3>
        @if (!empty($user))
            Edit backend user
        @else
            Create backend user
        @endif
    </h3>
@endsection

@section('content')
        @if (!empty($user))
            {!! Form::model($user, ['method' => 'patch', 'files' => true, 'route' => ['nodes.backend.users.update']]) !!}
            <input type="hidden" name="id" value="{{ $user->id }}">
        @else
            {!! Form::open(['method' => 'post', 'files' => true, 'route' => 'nodes.backend.users.store']) !!}
        @endif

        <div class="row">
            <div class="col-xs-12 col-md-6">
                <h4 class="margin-top">User details</h4>
                <hr/>
                <div class="margin-vertical-sm">
                    {{-- Name --}}
                    <div class="form-group">
                        <label for="backendUserFormName">Name</label>
                        <div class="@if($errors->has('name')) has-error @endif}}">
                            {!! Form::text('name', null, ['id' => 'backendUserFormName', 'class' => 'form-control']) !!}
                        </div>
                    </div>

                    {{-- E-mail --}}
                    <div class="form-group">
                        <label for="backendUserFormEmail">E-mail</label>
                        <div class="@if($errors->has('email')) has-error @endif}}">
                            {!!
                                Form::email(
                                    'email',
                                    null,
                                    [
                                        'id' => 'backendUserFormEmail',
                                        'class' => 'form-control',
                                        'autocomplete' => config('nodes.backend.general.disable_autocomplete', false) ? 'on' : 'off'
                                    ]
                                )
                            !!}
                        </div>
                    </div>

                    {{-- Role --}}
                    <div class="form-group">
                        <label for="backendUserFormRole">Role</label>
                        @if($errors->has('user_role'))
                            {!! Form::select('user_role', $roles, !empty($user) ? $user->user_role : $roleDefault, ['id' => 'backendUserFormRole', 'class' => 'form-control has-error']) !!}
                        @else
                            {!! Form::select('user_role', $roles, !empty($user) ? $user->user_role : $roleDefault, ['id' => 'backendUserFormRole', 'class' => 'form-control']) !!}
                        @endif
                    </div>
                    @if(empty($user))
                        <div class="form-group">
                            <input name="send_mail" value="false" type="hidden">
                            <label>
                                {!! Form::checkbox('send_mail', true, true, ['id' => 'backendUserFormSendMail']) !!} Send email with information
                            </label>
                        </div>
                    @endif
                </div>
                <br>
                @can('backend-edit-backend-user', !empty($user) ? $user : null)
                <h4 class="margin-top">
                    @if (!empty($user))
                        Change password
                    @else
                        Choose password <small class="text-gray-dark">(leave empty for random) / </small>
                    @endif
                    <br>
                    <small class="text-gray-dark">The password requires three of the following five categories and be min 8 chars:</small>
                    <br>
                    <small class="text-gray-dark">- English uppercase characters (A – Z)</small>
                    <br>
                    <small class="text-gray-dark">- English lowercase characters (a – z)</small>
                    <br>
                    <small class="text-gray-dark">- Base 10 digits (0 – 9)</small>
                    <br>
                    <small class="text-gray-dark">- Non-alphanumeric (For example: !, $, #, or %)</small>
                    <br>
                    <small class="text-gray-dark">- Unicode characters</small>
                </h4>
                <hr/>
                <div class="margin-top">
                    {{-- Password --}}
                    <div class="form-group">
                        <label for="backendUserFormPassword">Password</label>
                        <div class="@if($errors->has('password')) has-error @endif}}">
                            {!! Form::password('password', ['id' => 'backendUserFormPassword', 'class' => 'form-control']) !!}
                        </div>
                    </div>

                    {{-- Password confirm --}}
                    <div class="form-group">
                        <label for="backendUserFormRepeatPassword">Repeat password</label>
                        <div class="@if($errors->has('password')) has-error @endif}}">
                            {!! Form::password('password_confirmation', ['id' => 'backendUserFormRepeatPassword', 'class' => 'form-control']) !!}
                        </div>
                    </div>

                    {{-- Force user to reset pw on next login --}}
                    @can('backend-admin')
                    <div class="form-group">
                        <input name="should_reset_password" value="false" type="hidden">

                        <label class="@if($errors->has('change_password')) has-error @endif}}">
                            {!! Form::checkbox('change_password', true, empty($user) ? true : $user->change_password, ['id' => 'backendUserFormResetPwOnLogin']) !!} Reset password on login
                        </label>

                    </div>
                    @endcan
                </div>
                @endcan
            </div>
            <div class="col-xs-12 col-md-6">
                {{-- Image --}}
                <h4 class="margin-top">Image</h4>
                <hr>
                <div class="margin-vertical-sm">
                    <div class="form-group">
                        <label for="companyImage">Upload image</label>
                        <div class="@if ($errors->has('image')) has-error @endif">
                            {!! Form::file('image', null, ['id' => 'image', 'class' => 'form-control']) !!}
                        </div>
                    </div>
                    @if (!empty($user) && !empty($user->getImageUrl()))
                        <div class="form-group">
                            <img class="img-thumbnail" src="{{ $user->getImageUrl(250, 250) }}" alt="Image of {{ $user->name }}">
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <hr>
                @if (!empty($user))
                    <input type="submit" class="btn btn-primary form-control" value="Update backend user">
                @else
                    <input type="submit" class="btn btn-primary form-control" value="Create backend user">
                @endif
            </div>
        </div>
        {!! Form::close() !!}
@endsection
