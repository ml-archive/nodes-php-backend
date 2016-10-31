@if(
    !empty($__env->yieldContent('page-header-actions')) ||
    !empty($__env->yieldContent('page-header-top')) ||
    !empty($__env->yieldContent('page-header-bottom')) ||
    !empty($__env->yieldContent('breadcrumbs'))
)
<div class="page-header">

    {{--
        This space is reserved for future implementation of:
        @include('nodes.backend::partials.alerts')
    --}}

    @include('nodes.backend::partials.breadcrumbs')

    @if(!empty($__env->yieldContent('page-header-actions')))
        @include('nodes.backend::partials.page-header.page-header-actions')
    @endif

    @if(!empty($__env->yieldContent('page-header-top')))
        @include('nodes.backend::partials.page-header.page-header-top')
    @endif

    @if(!empty($__env->yieldContent('page-header-bottom')))
        @include('nodes.backend::partials.page-header.page-header-bottom')
    @endif

</div>
@endif