<div class="top-bar__left">
    <button href="{{route('nodes.backend.dashboard')}}" class="btn btn-transparent core__left-sidebar-toggle"><i class="fa fa-bars"></i></button>
    <div class="application-brand">
        <a class="logo" href="{{route('nodes.backend.dashboard')}}">
            @include('nodes.backend::partials.elements.logo-svg')
        </a>
        @if(config('nodes.backend.general.name', 'Backend'))
            <h1 class="application-title">
                {{ ucfirst(config('nodes.backend.general.name', 'Backend')) }}
            </h1>
        @endif
    </div>
</div>

<div class="top-bar__right">
    @include('nodes.backend::partials.core.core-topbar-left-actions')
    @include('nodes.backend::partials.core.core-topbar-right-actions')
</div>
