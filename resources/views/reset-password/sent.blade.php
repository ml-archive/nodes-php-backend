@extends('nodes.backend::base')

@section('layout')
    <div id="login">
        <div class="col-xs-10 col-sm-3">
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="panel-heading">
                        <h3 class="panel-title">Reset password</h3>
                    </div>
                    <div class="panel-body">
                        @include('nodes.backend::partials.alerts')
                        <p><strong>Almost there ...</strong></p>
                        <p>We have sent you an e-mail with a link to where you can reset your password.</p>
                        <p class="description text-center"><em>The link in the e-mail is only valid for 1 hour.</em></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop