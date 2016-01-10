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
                        @if (Session::has('success'))
                        <div class="alert alert-success text-center" role="alert">
                            {{ Session::get('success') }}
                        </div>
                        @endif
                        <p><strong>Congratulations!</strong></p>
                        <p>Your password has been now been updated and you can now delete the before sent e-mail.</p>
                        <a href="{{ route('nodes.backend.login.form') }}" class="btn btn-primary form-control">Go to login</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
