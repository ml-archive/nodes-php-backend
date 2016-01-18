@if(!empty($__env->yieldContent('core-topbar-shortcuts')))
    @if($renderForMobile)
        <div class="sidebar__collapse">
            <button class="btn btn-transparent collapse-toggle" data-toggle="collapse" data-target="#shortcutCollapse">
                Shortcuts
            </button>
            <div id="shortcutCollapse" class="collapse">
                <div class="list-group list-group-inverse">
                    @yield('core-topbar-shortcuts')
                </div>
            </div>
        </div>
    @else
        <div class="dropdown">
            <button class="btn btn-transparent" data-toggle="dropdown">
                Shortcuts
                <span class="caret"></span>
            </button>
            <div class="dropdown-menu dropdown-inverse animate-fade align-right">
                <div class="dropdown-content">
                    <ul class="list-group list-group-inverse">
                        @yield('core-topbar-shortcuts')
                    </ul>
                </div>
            </div>
        </div>

    @endif
@endif


