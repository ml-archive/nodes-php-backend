@if(!empty($__env->yieldContent('core-topbar-admin-menu')))
    @if($renderForMobile)
        <div class="sidebar__collapse">
            <button class="btn btn-transparent collapse-toggle" data-toggle="collapse" data-target="#adminCollapse">
                Admin Menu
            </button>
            <div id="adminCollapse" class="collapse">
                <div class="list-group list-group-inverse">
                    @yield('core-topbar-admin-menu')
                </div>
            </div>
        </div>
    @else
        <div class="dropdown dropdown-admin">
            <button class="btn btn-transparent dropdown-toggle" data-toggle="dropdown">
                <i class="fa fa-ellipsis-v"></i>
            </button>
            <ul class="dropdown-menu dropdown-inverse animate-fade align-right">
                @yield('core-topbar-admin-menu')
            </ul>
        </div>
    @endif
@endif


