@extends('nodes.backend::reset-password.reset-template')

@section('feedback-header')
    <h3 class="panel-title">Reset password</h3>
@endsection

@section('feedback-message')
    <div class="alert alert-danger text-center" role="alert">
        Token has expired!
    </div>
    <p class="padding-vertical">Your reset password request has expired. To reset your password you need to request a new token.</>
@endsection