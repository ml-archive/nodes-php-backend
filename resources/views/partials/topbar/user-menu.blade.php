@if($renderForMobile)
    <div class="sidebar__collapse">
        <button class="btn btn-transparent collapse-toggle user__info-button" data-toggle="collapse" data-target="#userCollapse">
            <img class="user__info-avatar img-responsive img-circle" src="{{backend_user()->getImageUrl()}}"/>
            <span class="user__info-name" title="John Doe">
                {{backend_user()->name}}
            </span>
        </button>
        <div id="userCollapse" class="collapse">
            <div class="list-group list-group-inverse">
                <a href="{{ route('nodes.backend.users.profile') }}" class="list-group-item">
                    <i class="fa fa-pencil"></i>
                    Edit Profile
                </a>
                <a href="{{ route('nodes.backend.login.logout') }}" class="list-group-item">
                    <i class="fa fa-sign-out"></i>
                    Sign out
                </a>
                @yield('core-topbar-user-menu')
            </div>
        </div>
    </div>
@else
    <div class="dropdown dropdown-user">
        <button class="btn btn-transparent dropdown-toggle user__info-button" data-toggle="dropdown">
            <img class="user__info-avatar img-responsive img-circle" src="{{backend_user()->getImageUrl()}}"/>
            <span class="user__info-name" title="John Doe">
                {{backend_user()->name}}
                <span class="caret"></span>
            </span>
        </button>
        <div class="dropdown-menu dropdown-inverse animate-expand align-right">
            <div class="dropdown-content">
                <p class="dropdown-content__title">
                    User
                </p>
                <div class="user">
                    <div class="user__profile">
                        <img class="img-responsive" src="//placehold.it/300x300"/>
                    </div>
                    <div class="user__info">
                        <span class="user__info-name" title="John Doe">
                            {{backend_user()->name}}
                        </span>
                        <span class="user__info-email" title="{{backend_user()->email}}">
                            {{backend_user()->email}}
                        </span>
                    </div>
                </div>
                <div class="user-actions">
                    <a href="{{ route('nodes.backend.login.logout') }}" class="btn btn-sm btn-transparent user__sign-out">
                        <i class="fa fa-sign-out"></i>
                        Sign out
                    </a>
                    <a href="{{ route('nodes.backend.users.profile') }}" data-toggle="tooltip" data-placement="left" title="Edit user"
                            class="btn btn-sm btn-transparent user__settings">
                        <i class="fa fa-pencil"></i>
                    </a>
                </div>
                @yield('core-topbar-user-menu')
            </div>
        </div>
    </div>
@endif



