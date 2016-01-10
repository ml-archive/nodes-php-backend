@extends('nodes.backend::base')

@section('layout')
    <div id="login">
        <div class="col-xs-10 col-sm-3">
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="panel-heading">
                        <h3 class="panel-title">Forgot password</h3>
                        <span class="panel-separator"></span>
                    </div>

                    @include('nodes.backend::partials.alerts')
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
                </div>
            </div>
        </div>
    </div>
@stop

