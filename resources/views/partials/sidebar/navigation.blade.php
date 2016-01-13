{{--Dashboard--}}
<a class="list-group-item {{ backend_router_alias('nodes.backend.dashboard') }}"
   href="{{ route('nodes.backend.dashboard') }}">
    <i class="fa fa-dashboard"></i>
    Dashboard
</a>


@can('super-admin')
{{--NStack--}}
    <a class="list-group-item {{ backend_router_alias('nodes.backend.nstack') }}"
       href="{{ route('nodes.backend.nstack') }}" target="_blank">
        <i class="fa fa-nodes logo">
            @include('nodes.backend::partials.elements.logo-icon-svg')
        </i>
            {{--<span class="icon">--}}
                {{--<span class="fa">--}}
                    {{--<img style="width: 20px; height: 20px" src="/img/n-stack-logo.svg" class="logo logo-small"/>--}}
                {{--</span>--}}
            {{--</span>--}}
        <span>NStack</span>
    </a>
@endcan

@can('developer')
{{--Failed jobs--}}
    <a class="list-group-item {{ backend_router_alias('nodes.backend.failed-jobs') }}"
       href="{{ route('nodes.backend.failed-jobs') }}">
                <span class="icon">
                    <span class="fa fa-cogs"></span>
                </span>
        <span>Failed jobs</span>
    </a>
{{--Roles--}}

    <a class="list-group-item {{ backend_router_alias('nodes.backend.users.roles') }}"
       href="{{ route('nodes.backend.users.roles') }}">
            <span class="icon">
                <span class="fa fa-graduation-cap"></span>
            </span>
        <span>Roles</span>
    </a>

@endcan

@can('admin')
{{--Backend users--}}

    <a class="list-group-item {{ backend_router_alias(['nodes.backend.users', 'nodes.backend.users.create', 'nodes.backend.users.edit', 'nodes.backend.users.profile']) }}"
       href="{{ route('nodes.backend.users', ['page' => 1]) }}">
            <span class="icon">
                <span class="fa fa-street-view"></span>
            </span>
        <span>Backend users</span>
    </a>

@endcan

@yield('core-sidebar-navigation')

