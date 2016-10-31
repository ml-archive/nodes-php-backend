@extends('nodes.backend::base')

@section('layout')
    <div class="layout vertical fit page-invitation page-login background-secondary-dark">

        <div id="login" class="panel panel-secondary">
            <div class="panel-heading">
                <div class="logo">
                    @include('nodes.backend::partials.elements.logo-svg')
                </div>
                @yield('feedback-header')
            </div>
            <div class="panel-body">
                <div class="row no-gutter">
                    <div class="col-xs-12">
                        @include('nodes.backend::partials.alerts')
                    </div>
                    <div class="col-xs-12 text-secondary-light">
                        @yield('feedback-message')
                    </div>
                    <div class="col-xs-12 padding-vertical">
                        @yield('feedback-action-primary')
                        @yield('feedback-action-secondary')
                    </div>
                </div>
            </div>
        </div>

    </div>
@stop