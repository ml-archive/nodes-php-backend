<div class="top-bar__left">
    <button href="#" class="btn btn-transparent core__left-sidebar-toggle"><i class="fa fa-bars"></i></button>

    <a class="logo" href="#">
        @include('nodes.backend::partials.elements.logo-svg')
    </a>
</div>

<div class="top-bar__right">
    @include('nodes.backend::partials.core.core-topbar-left-actions')
    @include('nodes.backend::partials.core.core-topbar-right-actions')
</div>