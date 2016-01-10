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
                        <div class="alert alert-danger text-center" role="alert">
                            Invalid token!
                        </div>
                        <p>The token you're trying to use is invalid. Either this is because the token doesn't exist or because it has already been used.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
