<div class="top-bar__actions-right">
    @yield('core-topbar-right-actions')
    @include('nodes.backend::partials.topbar.shortcuts', [
        'renderForMobile' => false
    ])
    @include('nodes.backend::partials.topbar.user-menu', [
        'renderForMobile' => false
    ])
    @include('nodes.backend::partials.topbar.admin-menu', [
        'renderForMobile' => false
    ])
</div>