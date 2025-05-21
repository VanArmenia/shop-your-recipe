{{--Rating--}}
@props(['average_rating', 'review_count'])
<div class="flex items-center">
    @for ($i = 1; $i <= 5; $i++)
        @if ($i <= floor($average_rating))
            <i class="fas fa-star text-red-500"></i> <!-- Full star -->
        @elseif ($i - $average_rating < 1)
            <i class="fas fa-star-half-alt text-red-500"></i> <!-- Half star -->
        @else
            <i class="far fa-star text-gray-300"></i> <!-- Empty star -->
        @endif
    @endfor
    <span class="ml-2 text-sm text-gray-700">
        {{ number_format($average_rating, 1) }}/5 ({{ $review_count }} reviews)
    </span>
</div>
