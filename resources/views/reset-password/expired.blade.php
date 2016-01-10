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
                            Token has expired!
                        </div>
                        <p>Your reset password request has expired. To reset your password you need to request a new token.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
