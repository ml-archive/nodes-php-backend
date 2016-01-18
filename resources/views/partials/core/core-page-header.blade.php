@if(
    !empty($__env->yieldContent('page-header-actions')) ||
    !empty($__env->yieldContent('page-header-top')) ||
    !empty($__env->yieldContent('page-header-bottom'))
)
<div class="page-header">
    
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