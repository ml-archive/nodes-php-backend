@extends('nodes.backend::base')

@section('layout')
    <div class="layout vertical core-layout">

        <div class="core-layout__topbar">
            <div class="top-bar">
                @include('nodes.backend::partials.core.core-topbar')
            </div>
        </div>

        <div class="core-layout__page">
            <div class="core-layout__sidebar-wrapper">
                <div class="core-layout__sidebar">
                    @include('nodes.backend::partials.core.core-sidebar')
                </div>
            </div>

            <div class="core-layout__content">
                @include('nodes.backend::partials.alerts')
                @yield('content')
            </div>
        </div>

    </div>
@endsection