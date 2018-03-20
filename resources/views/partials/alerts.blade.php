{{-- Success alert --}}
@if (Session::has('success'))
    <div class="alert alert-success alert-dismissible fade in to-be-animated-in" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        @if (is_object(Session::get('success')))
            @if (Session::get('success')->count() > 1)
                <ul>
                    @foreach (Session::get('success')->all() as $message)
                        <li>{!! $message !!}</li>
                    @endforeach
                </ul>
            @else
                <span class="fa fa-check-circle"></span>
                {!! Session::get('success')->first() !!}
            @endif
        @elseif (is_array(Session::get('success')))
            <ul>
                @foreach (Session::get('success') as $message)
                    <li>{!! $message !!}</li>
                @endforeach
            </ul>
        @else
            <span class="fa fa-check-circle"></span>
            {!! Session::get('success') !!}
        @endif
    </div>
@endif

{{-- Errors alert --}}
@if (Session::has('error'))
    <div class="alert alert-danger alert-dismissible fade in to-be-animated-in" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        @if (is_object(Session::get('error')))
            @if (Session::get('error')->count() > 1)
                <ul>
                    @foreach (Session::get('error')->all() as $message)
                        <li>{!! $message !!}</li>
                    @endforeach
                </ul>
            @else
                <span class="fa fa-exclamation-circle"></span>
                {!! Session::get('error')->first() !!}
            @endif
        @elseif (is_array(Session::get('error')))
            <ul>
                @foreach (Session::get('error') as $message)
                    <li>{!! $message !!}</li>
                @endforeach
            </ul>
        @else
            <span class="fa fa-exclamation-circle"></span>
            {!! Session::get('error') !!}
        @endif
    </div>
@endif

{{-- Error alert --}}
@if(Session::has('errors'))
    <div class="alert alert-danger alert-dismissible fade in to-be-animated-in" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        @if (is_object(Session::get('errors')))
            @if (Session::get('errors')->count() > 1)
                <ul>
                    @foreach (Session::get('errors')->all() as $message)
                        <li>{!! $message !!}</li>
                    @endforeach
                </ul>
            @else
                <span class="fa fa-exclamation-circle"></span>
                {!! Session::get('errors')->first() !!}
            @endif
        @elseif (is_array(Session::get('errors')))
            <ul>
                @foreach (Session::get('errors') as $message)
                    <li>{!! $message !!}</li>
                @endforeach
            </ul>
        @else
            <span class="fa fa-exclamation-circle"></span>
            {!! Session::get('errors') !!}
        @endif
    </div>
@endif

{{-- Warning alert --}}
@if (Session::has('warning'))
    <div class="alert alert-warning alert-dismissible fade in to-be-animated-in" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        @if (is_object(Session::get('warning')))
            @if (Session::get('warning')->count() > 1)
                <ul>
                    @foreach (Session::get('warning')->all() as $message)
                        <li>{!! $message !!}</li>
                    @endforeach
                </ul>
            @else
                <span class="fa fa-warning"></span>
                {!! Session::get('warning')->first() !!}
            @endif
        @elseif (is_array(Session::get('warning')))
            <ul>
                @foreach (Session::get('warning') as $message)
                    <li>{!! $message !!}</li>
                @endforeach
            </ul>
        @else
            <span class="fa fa-warning"></span>
            {!! Session::get('warning') !!}
        @endif
    </div>
@endif

{{-- Info alert --}}
@if (Session::has('info'))
    <div class="alert alert-info alert-dismissible fade in to-be-animated-in" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        @if (is_object(Session::get('info')))
            @if (Session::get('info')->count() > 1)
                <ul>
                    @foreach (Session::get('info')->all() as $message)
                        <li>{!! $message !!}</li>
                    @endforeach
                </ul>
            @else
                <span class="fa fa-info-circle"></span>
                {!! Session::get('info')->first() !!}
            @endif
        @elseif (is_array(Session::get('info')))
            <ul>
                @foreach (Session::get('info') as $message)
                    <li>{!! $message !!}</li>
                @endforeach
            </ul>
        @else
            <span class="fa fa-info-circle"></span>
            {!! Session::get('info') !!}
        @endif
    </div>
@endif