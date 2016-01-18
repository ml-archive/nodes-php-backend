@extends('nodes.backend::reset-password.reset-template')

@section('feedback-header')
    <h3 class="panel-title">Forgot password</h3>
@endsection

@section('feedback-message')
    {!! Form::open(['method' => 'post', 'route' => 'nodes.backend.reset-password.token']) !!}
    <div class="form-group has-feedback">
        {!! Form::label('login-email', 'E-mail address', ['class' => 'sr-only']) !!}
        {!! Form::email('email', Session::get('email'), ['id' => 'login-email', 'class' => 'form-control', 'placeholder' => 'E-mail address']) !!}
        <span class="fa fa-envelope-o fa-lg form-control-feedback" aria-hidden="true"></span>
    </div>
    <div class="form-group">
        {!! Form::submit('Reset my password', ['class' => 'btn btn-primary form-control']) !!}
    </div>
    {!! Form::close() !!}
@endsection