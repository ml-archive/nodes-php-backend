@extends('nodes.backend::base')

@section('layout')
    <div id="login">
        <div class="col-xs-10 col-sm-3">
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="panel-heading no-margin">
                        <h3 class="panel-title">Reset password</h3>
                    </div>
                    <div class="panel-body">
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
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
