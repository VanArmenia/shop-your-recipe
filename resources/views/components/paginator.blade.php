@props(['paginator'])

@if ($paginator->lastPage() > 1)
    <ul class="flex flex-wrap items-center justify-center gap-2 my-4">
        {{-- Previous Button --}}
        @if ($paginator->currentPage() > 1)
            <li>
                <a href="{{ $paginator->previousPageUrl() }}"
                   class="px-3 py-2 text-gray-600 bg-yellow-200 rounded-md hover:bg-gray-300 transition">
                    &laquo;
                </a>
            </li>
        @endif

        {{-- Page Numbers --}}
        @for ($i = 1; $i <= $paginator->lastPage(); $i++)
            <li>
                <a href="{{ $paginator->url($i) }}"
                   class="px-4 py-2 rounded-md {{ $paginator->currentPage() == $i ? 'bg-yellow-700 text-white' : 'text-gray-700 bg-gray-100 hover:bg-yellow-300 transition' }}">
                    {{ $i }}
                </a>
            </li>
        @endfor

        {{-- Next Button --}}
        @if ($paginator->currentPage() < $paginator->lastPage())
            <li>
                <a href="{{ $paginator->nextPageUrl() }}"
                   class="px-3 py-2 text-gray-600 bg-yellow-200 rounded-md hover:bg-gray-300 transition">
                    &raquo;
                </a>
            </li>
        @endif
    </ul>
@endif
