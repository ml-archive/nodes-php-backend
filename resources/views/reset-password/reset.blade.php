@extends('nodes.backend::reset-password.reset-template')

@section('feedback-header')
    <h3 class="panel-title">Forgot password</h3>
@endsection

@section('feedback-message')
    <p class="description text-center">Enter the e-mail address of the user who's password you wish to reset. Here after enter the user's new password.</p>

    {!! Form::open(['method' => 'post', 'route' => 'nodes.backend.reset-password.change']) !!}
    <input type="hidden" name="token" value="{{ $token }}">
    <div class="form-group">
        {!! Form::label('nodesResetPasswordEmail', 'E-mail address') !!}
        {!!
            Form::email(
                'email',
                Session::get('email'),
                [
                    'id' => 'nodesResetPasswordEmail',
                    'class' => 'form-control',
                    'placeholder' => 'your@email.com',
                    'autocomplete' => config('nodes.backend.general.disable_autocomplete', false) ? 'on' : 'off'
                ]
            )
        !!}
    </div>
    <div class="form-group">
        {!! Form::label('nodesResetPasswordNew', 'New password') !!}
        {!! Form::password('password', ['id' => 'nodesResetPasswordNew', 'class' => 'form-control']) !!}
    </div>
    <div class="form-group">
        {!! Form::label('nodesResetPasswordConfirmation', 'New password confirmation') !!}
        {!! Form::password('password_confirmation', ['id' => 'nodesResetPasswordConfirmation', 'class' => 'form-control']) !!}
    </div>
    <div class="form-group">
        {!! Form::submit('Change password', ['class' => 'btn btn-primary form-control']) !!}
    </div>
    {!! Form::close() !!}
@endsection