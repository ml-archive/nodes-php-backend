@extends('nodes.backend::reset-password.reset-template')

@section('feedback-header')
    <h3 class="panel-title">Forgot password</h3>
@endsection

@section('feedback-message')
    @if (Session::has('error'))
        <div class="alert alert-danger text-center" role="alert">
            {{ Session::get('error') }}
        </div>
    @endif

    <p class="description text-center">Enter the e-mail address of the user who's password you wish to reset. Here after enter the user's new password.</p>

    {!! Form::open(['method' => 'post', 'route' => 'nodes.backend.reset-password.change']) !!}
    <input type="hidden" name="token" value="{{ $token }}">
    <div class="form-group">
        {!! Form::label('nodesResetPasswordEmail', 'E-mail address') !!}
        {!! Form::email('email', Session::get('email'), ['id' => 'nodesResetPasswordEmail', 'class' => 'form-control', 'placeholder' => 'your@email.com']) !!}
    </div>
    <div class="form-group">
        {!! Form::label('nodesResetPasswordNew', 'New password') !!}
        {!! Form::password('password', ['id' => 'nodesResetPasswordNew', 'class' => 'form-control']) !!}
    </div>
    <div class="form-group">
        {!! Form::label('nodesResetPasswordRepeat', 'Repeat password') !!}
        {!! Form::password('repeat-password', ['id' => 'nodesResetPasswordRepeat', 'class' => 'form-control']) !!}
    </div>
    <div class="form-group">
        {!! Form::submit('Change password', ['class' => 'btn btn-primary form-control']) !!}
    </div>
    {!! Form::close() !!}
@endsection