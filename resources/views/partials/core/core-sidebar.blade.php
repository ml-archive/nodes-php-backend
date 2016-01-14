<div class="core-layout__sidebar-mobile-navigation">
    <div class="core-layout__sidebar-mobile-navigation-toolbar">
        <p>Menu</p>
        <button href="#" class="btn btn-transparent core__left-sidebar-toggle"><i class="fa fa-times"></i></button>
    </div>

    @include('nodes.backend::partials.topbar.user-menu', [
        'renderForMobile' => true
    ])
    @include('nodes.backend::partials.topbar.admin-menu', [
        'renderForMobile' => true
    ])
    @include('nodes.backend::partials.topbar.shortcuts', [
        'renderForMobile' => true
    ])
</div>

<div class="sidebar sidebar__navigation">
    {{--Must be in one line--}}
    <div class="sidebar__navigation-top">@include('nodes.backend::partials.sidebar.top-actions')</div>

    <div class="sidebar__navigation-middle list-group list-group-inverse">
        @include('nodes.backend::partials.sidebar.navigation')
    </div>

    {{--Must be in one line--}}
    <div class="sidebar__navigation-bottom">@include('nodes.backend::partials.sidebar.bottom-actions')</div>
</div>