@extends('nodes.backend::base')

@section('layout')
    <a href="{{ route('nodes.backend.login.sso') }}" id="alternative-login" class="sso">
        <span class="sr-only">Nodes SSO</span>
    </a>
    <div id="login">
        <div class="col-xs-10 col-sm-3">
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="panel-heading">
                        <h3 class="panel-title">{{ ucfirst(config('nodes.project.name', 'Nodes Backend')) }}</h3>
                        <span class="panel-separator"></span>
                    </div>
                    @include('nodes.backend::partials.alerts')
                    {!! Form::open(['method' => 'post', 'route' => 'nodes.backend.login.authenticate']) !!}
                        <div class="form-group has-feedback">
                            {!! Form::label('login-email', 'E-mail address', ['class' => 'sr-only']) !!}
                            {!! Form::email('email', Session::get('email'), ['id' => 'login-email', 'class' => 'form-control', 'placeholder' => 'E-mail address']) !!}
                            <span class="fa fa-envelope-o fa-lg form-control-feedback" aria-hidden="true"></span>
                        </div>
                        <div class="form-group has-feedback">
                            {!! Form::label('login-password', 'Password', ['class' => 'sr-only']) !!}
                            {!! Form::password('password', ['id' => 'login-password', 'class' => 'form-control', 'placeholder' => 'Password']) !!}
                            <span class="fa fa-lock fa-lg form-control-feedback" aria-hidden="true"></span>
                        </div>
                        <div class="form-group clearfix">
                            <div class="checkbox pull-left">
                                {!! Form::checkbox('remember', true, null, ['id' => 'nodes-login-remember']) !!}
                                <label for="nodes-login-remember">Remember me</label>
                            </div>
                            <div class="pull-right">
                                <a href="{{ route('nodes.backend.reset-password.form') }}">Forgot password?</a>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::submit('Login', ['class' => 'btn btn-primary form-control']) !!}
                        </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@stop
