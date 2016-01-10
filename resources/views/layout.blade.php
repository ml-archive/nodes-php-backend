@extends('nodes.backend::base')

@section('layout')

    <div class="layout horizontal fit core-layout">

        <div class="flex-1 core-layout__sidebar">
            <header role="banner" class="flex-1 sidebar__logo">
                <h1>{{ ucfirst(config('nodes.project.name', 'Backend')) }}</h1>
            </header>
            <ul class="flex-2 sidebar">
                @include('nodes.backend::partials.navigation')
            </ul>
        </div>

        <div class="flex-2 core-layout__content core-content layout vertical">
            <div class="flex-1 core-layout__topbar">
                <div class="dropdown user-account">
                    <span class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                        <span class="fa fa-user"></span>
                        {{backend_user()->name}}
                        <span class="caret"></span>
                    </span>
                    <ul class="dropdown-menu dropdown-menu-right" role="menu">
                        @include('nodes.backend::partials.user-actions')
                    </ul>
                </div>
            </div>

            <div class="core-layout__page core-content__page scroll">
                <div class="page-content">
                    @include('nodes.backend::partials.breadcrumbs')
                    @include('nodes.backend::partials.alerts')
                    @yield('content')
                </div>
            </div>
        </div>
    </div>
@endsection