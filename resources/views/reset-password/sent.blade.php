@extends('nodes.backend::reset-password.reset-template')

@section('feedback-header')
    <h3 class="panel-title">Reset password</h3>
@endsection

@section('feedback-message')
    <p><strong>Almost there ...</strong></p>
    <p>We have sent you an e-mail with a link to where you can reset your password.</p>
    <p class="description text-center"><em>The link in the e-mail is only valid for 1 hour.</em></p>
@endsection