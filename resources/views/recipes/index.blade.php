<?php
/** @var \Illuminate\Database\Eloquent\Collection $breakfasts */
/** @var \Illuminate\Database\Eloquent\Collection $vegetarians */
?>

<x-app-layout>
    <header class="md:px-5 bg-yellow-700">
        <div class="p-4 text-center text-yellow-100">
            <h1 class="text-xl md:text-2xl">
                Welcome to our Web Kitchen.
            </h1>
            <p> Ready to compose your recipe?</p>
        </div>
    </header>

    <?php if ($breakfasts->count() === 0 || $vegetarians->count() === 0): ?>
        <div class="text-center text-gray-600 py-16 text-xl">
            There are no recipes published
        </div>
    <?php else: ?>
    <h2 class="font-bold text-2xl p-4">Breakfast</h2>
        <div
            class="grid gap-8 grig-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 p-5"
        >
            @foreach($breakfasts as $breakfast)
                <!-- Recipe Item -->
                <div
                    x-data="recipeItem({{ json_encode([
                        'id' => $breakfast->id,
                        'image' => $breakfast->image,
                        'name' => $breakfast->name,
                        'description' => $breakfast->description,
                    ]) }})"
                    class="rounded-md transition-colors"
                >
                    <a href="{{ route('recipes.show', $breakfast->id) }}"
                       class="aspect-w-3 aspect-h-2 block overflow-hidden">
                        <img
                            src="{{ $breakfast->image }}"
                            alt=""
                            class="object-cover rounded-lg hover:scale-98 transition-transform p-1"
                        />
                    </a>
                    <div class="p-2">
                        <h5 class="text-gray-600">{{$breakfast->category}}</h5>
                    </div>
                    <div class="py-0 px-2">
                        <h3 class="text-lg font-bold">
                            <a href="{{ route('recipes.show', $breakfast->id) }}">
                                {{$breakfast->name}}
                            </a>
                        </h3>
                    </div>
                    <div class="p-2">
                        <div class="flex items-center">
                            @for ($i = 1; $i <= 5; $i++)
                                @if ($i <= floor($breakfast->average_rating))
                                    <i class="fas fa-star text-pink-600"></i> <!-- Full star -->
                                @elseif ($i - $breakfast->average_rating < 1)
                                    <i class="fas fa-star-half-alt text-pink-600"></i> <!-- Half star -->
                                @else
                                    <i class="far fa-star text-gray-300"></i> <!-- Empty star -->
                                @endif
                            @endfor
                        <span class="ml-2 text-sm text-gray-700">
                            {{ number_format($breakfast->average_rating, 1) }}/5 ({{ $breakfast->review_count }} reviews)
                        </span>
                        </div>
                    </div>
                </div>
                <!--/ Recipe Item -->
            @endforeach
        </div>

    <h2 class="font-bold text-2xl p-4">Vegetarian</h2>
    <div
        class="grid gap-8 grig-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 p-5"
    >
        @foreach($vegetarians as $vegetarian)
            <!-- Recipe Item -->
            <div
                x-data="recipeItem({{ json_encode([
                        'id' => $vegetarian->id,
                        'image' => $vegetarian->image,
                        'name' => $vegetarian->name,
                        'description' => $vegetarian->description,
                    ]) }})"
                class="rounded-md transition-colors"
            >
                <a href="{{ route('recipes.show', $vegetarian->id) }}"
                   class="aspect-w-3 aspect-h-2 block overflow-hidden">
                    <img
                        src="{{ $vegetarian->image }}"
                        alt=""
                        class="object-cover rounded-lg hover:scale-98 transition-transform p-1"
                    />
                </a>
                <div class="p-2">
                    <h5 class="text-gray-600">{{$vegetarian->category}}</h5>
                </div>
                <div class="py-0 px-2">
                    <h3 class="text-lg font-bold">
                        <a href="{{ route('recipes.show', $vegetarian->id) }}">
                            {{$vegetarian->name}}
                        </a>
                    </h3>
                </div>
                <div class="p-2">
                    <div class="flex items-center">
                        @for ($i = 1; $i <= 5; $i++)
                            @if ($i <= floor($vegetarian->average_rating))
                                <i class="fas fa-star text-pink-600"></i> <!-- Full star -->
                            @elseif ($i - $vegetarian->average_rating < 1)
                                <i class="fas fa-star-half-alt text-pink-600"></i> <!-- Half star -->
                            @else
                                <i class="far fa-star text-gray-300"></i> <!-- Empty star -->
                            @endif
                        @endfor
                        <span class="ml-2 text-sm text-gray-700">
                            {{ number_format($vegetarian->average_rating, 1) }}/5 ({{ $vegetarian->review_count }} reviews)
                        </span>
                    </div>
                </div>
            </div>
            <!--/ Recipe Item -->
        @endforeach
    </div>
    <?php endif; ?>
</x-app-layout>
