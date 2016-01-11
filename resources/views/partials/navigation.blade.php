{{--Dashboard--}}
<li class="{{ backend_router_alias('nodes.backend.dashboard') }}">
    <a href="{{ route('nodes.backend.dashboard') }}">
        <span class="icon">
            <span class="fa fa-dashboard"></span>
        </span>
        <span>Dashboard</span>
    </a>
</li>

@can('super-admin')
    {{--NStack--}}
    <li class="{{ backend_router_alias('nodes.backend.nstack') }}">
        <a href="{{ route('nodes.backend.nstack') }}" target="_blank">

            <span class="icon">
                <span class="fa">
                    <img style="width: 20px; height: 20px" src="/img/n-stack-logo.svg" class="logo logo-small"/>
                </span>
            </span>
            <span>NStack</span>
        </a>
    </li>
@endcan

@can('developer')
    {{--Failed jobs--}}
    <li class="{{ backend_router_alias('nodes.backend.failed-jobs') }}">
        <a href="{{ route('nodes.backend.failed-jobs') }}">
                <span class="icon">
                    <span class="fa fa-cogs"></span>
                </span>
            <span>Failed jobs</span>
        </a>
    </li>

    {{--Roles--}}
    <li class="{{ backend_router_alias('nodes.backend.users.roles') }}">
        <a href="{{ route('nodes.backend.users.roles') }}">
            <span class="icon">
                <span class="fa fa-graduation-cap"></span>
            </span>
            <span>Roles</span>
        </a>
    </li>
@endcan

@can('admin')
{{--Backend users--}}
<li class="{{ backend_router_alias(['nodes.backend.users', 'nodes.backend.users.create', 'nodes.backend.users.edit', 'nodes.backend.users.profile']) }}">
    <a href="{{ route('nodes.backend.users', ['page' => 1]) }}">
            <span class="icon">
                <span class="fa fa-street-view"></span>
            </span>
        <span>Backend users</span>
    </a>
</li>
@endcan

