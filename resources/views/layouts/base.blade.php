@extends('nodes.backend::base')

@section('layout')
    <div class="layout vertical core-layout">

        <div class="core-layout__topbar">
            <section class="top-bar">
                @include('nodes.backend::partials.core.core-topbar')
            </section>
        </div>

        <div class="core-layout__page">
            <div class="core-layout__sidebar-wrapper">
                <section class="core-layout__sidebar">
                    @include('nodes.backend::partials.core.core-sidebar')
                </section>
            </div>

            <section class="core-layout__content">
                @include('nodes.backend::partials.core.core-page-header')
                <div class="page-content">
                    <div class="container-fluid">
                        @yield('content')
                    </div>
                </div>
            </section>
        </div>

        <div class="page-toasts">
            @include('nodes.backend::partials.alerts')
        </div>

    </div>
@endsection