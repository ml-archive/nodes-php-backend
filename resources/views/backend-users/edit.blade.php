@extends('nodes.backend::layout')

@section('breadcrumbs')
    @parent
    <li>
        <a href="{{ route('nodes.backend.users') }}">
            <span class="fa fa-street-view"></span>
            Backend users
        </a>
    </li>
    @if (!empty($user))
        <li class="active">
            <span class="fa fa-pencil"></span>
            {{ $user->name }}
        </li>
    @else
        <li class="active">
            <span class="fa fa-user-plus"></span>
            Create backend user
        </li>
    @endif
@endsection

@section('content')
    <section class="panel panel-default">
        <header class="panel-heading border clearfix">
            @if (!empty($user))
                <h3 class="panel-title">Edit backend user</h3>
            @else
                <h3 class="panel-title">Create backend user</h3>
            @endif
        </header>
        <div class="panel-body">
            <div class="row">
                <div class="col-xs-12 col-sm-6">
                    @if (!empty($user))
                        {!! Form::model($user, ['method' => 'patch', 'files' => true, 'route' => ['nodes.backend.users.update'], 'class' => 'form-horizontal']) !!}
                        <input type="hidden" name="id" value="{{ $user->id }}">
                    @else
                        {!! Form::open(['method' => 'post', 'files' => true, 'route' => 'nodes.backend.users.store', 'class' => 'form-horizontal']) !!}
                    @endif

                    {{--Name--}}
                    <div class="form-group">
                        <label for="backendUserFormName" class="col-sm-4 control-label">Name</label>
                        <div class="col-sm-8 @if(validation_key_failed('name')) has-error @endif}}">
                            {!! Form::text('name', null, ['id' => 'backendUserFormName', 'class' => 'form-control']) !!}
                        </div>
                    </div>

                    {{--Email--}}
                    <div class="form-group">
                        <label for="backendUserFormEmail" class="col-sm-4 control-label">E-mail</label>
                        <div class="col-sm-8 @if(validation_key_failed('email')) has-error @endif}}">
                            {!! Form::email('email', null, ['id' => 'backendUserFormEmail', 'class' => 'form-control']) !!}
                        </div>
                    </div>

                    {{--Image--}}
                    <div class="form-group">
                        <label for="backendUserFormEmail" class="col-sm-4 control-label">Image</label>
                        <div class="col-sm-8 @if(validation_key_failed('image')) has-error @endif}}">
                            {!! Form::file('image', null, ['id' => 'backendUserFormImage', 'class' => 'form-control']) !!}
                        </div>
                    </div>

                    {{--Role--}}
                    <div class="form-group">
                        <label for="backendUserFormRole" class="col-sm-4 control-label">Role</label>
                        <div class="col-sm-8 @if(validation_key_failed('user_role')) has-error @endif}}">
                            {!! Form::select('user_role', $roles, !empty($user) ? $user->user_role : $roleDefault, ['id' => 'backendUserFormRole', 'class' => 'form-control']) !!}
                        </div>
                    </div>

                    @can('edit-user', !empty($user) ? $user : null)
                        <br/>
                        <h4 class="bordered">
                            @if (!empty($user))
                                Change password
                            @else
                                Choose password <small>(leave empty for random)</small>
                            @endif
                            <small><em>(min. 6 characters)</em></small>
                        </h4>

                        {{--Password--}}
                        <div class="form-group">
                            <label for="backendUserFormPassword" class="col-sm-4 control-label">Password</label>
                            <div class="col-sm-8 @if(validation_key_failed('password')) has-error @endif}}">
                                {!! Form::password('password', ['id' => 'backendUserFormPassword', 'class' => 'form-control']) !!}
                            </div>
                        </div>

                        {{--Password confirm--}}
                        <div class="form-group">
                            <label for="backendUserFormRepeatPassword" class="col-sm-4 control-label">Repeat password</label>
                            <div class="col-sm-8 @if(validation_key_failed('password')) has-error @endif}}">
                                {!! Form::password('password_confirmation', ['id' => 'backendUserFormRepeatPassword', 'class' => 'form-control']) !!}
                            </div>
                        </div>

                        {{--Force user to reset pw on next login--}}
                        @can('admin')
                            <div class="form-group">
                                <input name="should_reset_password" value="false" type="hidden">
                                <label for="backendUserFormResetPwOnLogin" class="col-sm-4 control-label">Reset password on login</label>
                                <div class="col-sm-8 @if(validation_key_failed('change_password')) has-error @endif}}">
                                    <div class="checkbox">
                                        {!! Form::checkbox('change_password', true, empty($user) ? true : $user->change_password, ['id' => 'backendUserFormResetPwOnLogin']) !!}
                                    </div>
                                </div>
                            </div>
                        @endcan
                    @endcan

                    {{--Send mail with info--}}
                    @if (empty($user))
                        <div class="form-group">
                            <input name="send_mail" value="false" type="hidden">
                            <label for="backendUserFormSendMail" class="col-sm-4 control-label">Send email with information</label>
                            <div class="col-sm-8">
                                <div class="checkbox">
                                    {!! Form::checkbox('send_mail', true, true, ['id' => 'backendUserFormSendMail']) !!}
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="form-group">
                        <div class="col-sm-offset-4 col-sm-8">
                            @if (!empty($user))
                                <input type="submit" class="btn btn-primary form-control" value="Update backend user">
                            @else
                                <input type="submit" class="btn btn-primary form-control" value="Create backend user">
                            @endif
                        </div>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </section>
@endsection
