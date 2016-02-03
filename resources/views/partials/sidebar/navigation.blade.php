<nav class="list-group list-group-inverse">
    <ul>
        {{--Dashboard--}}
        <li class="list-group-item {{ backend_router_alias('nodes.backend.dashboard') }}">
            <a href="{{ route('nodes.backend.dashboard') }}">
                <i class="fa fa-dashboard"></i>
                Dashboard
            </a>
        </li>

        @can('backend-super-admin')
            <li class="list-group-heading">
                Super Admin
            </li>
            {{--NStack--}}
            <li class="list-group-item {{ backend_router_alias('nodes.backend.nstack') }}">
                <a href="{{ route('nodes.backend.nstack') }}" target="_blank">
                    <i class="fa fa-nodes logo">
                        @include('nodes.backend::partials.elements.logo-icon-svg')
                    </i>
                    NStack
                </a>
            </li>
        @endcan


        @can('backend-developer')
            <li class="list-group-heading">
                Developer
            </li>
            {{--Failed jobs--}}
            <li class="list-group-item {{ backend_router_alias('nodes.backend.failed-jobs') }}">
                <a href="{{ route('nodes.backend.failed-jobs') }}">
                    <i class="fa fa-cogs"></i>
                    Failed jobs
                </a>
            </li>

            {{--Roles--}}
            <li class="list-group-item {{ backend_router_alias('nodes.backend.users.roles') }}">
                <a href="{{ route('nodes.backend.users.roles') }}">
                    <i class="fa fa-graduation-cap"></i>
                    Roles
                </a>
            </li>
        @endcan

        @can('backend-admin')
            <li class="list-group-heading">
                Admin
            </li>
            {{--Backend users--}}
            <li class="list-group-item {{ backend_router_alias(['nodes.backend.users', 'nodes.backend.users.create', 'nodes.backend.users.edit', 'nodes.backend.users.profile']) }}">
                <a href="{{ route('nodes.backend.users', ['page' => 1]) }}">
                    <i class="fa fa-street-view"></i>
                    Backend users
                </a>
            </li>
        @endcan

        @yield('core-sidebar-navigation')

    </ul>
</nav>
