@if ($paginator->hasPages())
    <ul class="pagination pagination">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <li class="pagination_prev_arrow disabled">
{{--                <span>--}}
                    @svg("img/svg/arrow-left-noshaft-rounded.svg")
{{--                    <img src="{{asset("img/svg/arrow-left-noshaft-rounded.svg")}}" alt="arrow-left">--}}
{{--                </span>--}}
            </li>
        @else
            <li class="pagination_prev_arrow"><a href="{{ $paginator->previousPageUrl() }}" rel="prev">
                    @svg("img/svg/arrow-left-noshaft-rounded.svg")
{{--                    <img src="{{asset("img/svg/arrow-left-noshaft-rounded.svg")}}" alt="arrow-left">--}}
                </a></li>
        @endif

        @if($paginator->currentPage() > 3)
            <li class="hidden-xs"><a href="{{ $paginator->url(1) }}">1</a></li>
        @endif
        @if($paginator->currentPage() > 4)
            <li class="disabled hidden-xs"><span>...</span></li>
        @endif
        @foreach(range(1, $paginator->lastPage()) as $i)
            @if($i >= $paginator->currentPage() - 2 && $i <= $paginator->currentPage() + 2)
                @if ($i == $paginator->currentPage())
                    <li class="active"><span>{{ $i }}</span></li>
                @else
                    <li><a href="{{ $paginator->url($i) }}">{{ $i }}</a></li>
                @endif
            @endif
        @endforeach
        @if($paginator->currentPage() < $paginator->lastPage() - 3)
            <li class="disabled hidden-xs"><span>...</span></li>
        @endif
        @if($paginator->currentPage() < $paginator->lastPage() - 2)
            <li class="hidden-xs"><a href="{{ $paginator->url($paginator->lastPage()) }}">{{ $paginator->lastPage() }}</a></li>
        @endif

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <li class="pagination_next_arrow"><a href="{{ $paginator->nextPageUrl() }}" rel="next">
                    @svg("img/svg/arrow-right-noshaft-rounded.svg")
{{--                    <img src="{{asset("img/svg/arrow-right-noshaft-rounded.svg")}}" alt="arrow-right">--}}
                </a></li>
        @else
            <li class="pagination_next_arrow disabled">
{{--                <span>--}}
                @svg("img/svg/arrow-right-noshaft-rounded.svg")
{{--                <img src="{{asset("img/svg/arrow-right-noshaft-rounded.svg")}}" alt="arrow-right">--}}
{{--                </span>--}}
            </li>
        @endif
    </ul>
@endif