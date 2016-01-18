@extends('nodes.backend::reset-password.reset-template')

@section('feedback-header')
    <h3 class="panel-title">Reset password</h3>
@endsection

@section('feedback-message')
    <div class="alert alert-danger text-center" role="alert">
        Invalid token!
    </div>
    <p class="padding-vertical">The token you're trying to use is invalid. Either this is because the token doesn't exist or because it has already been used.</p>
@endsection