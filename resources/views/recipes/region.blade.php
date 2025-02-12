<?php
/** @var \Illuminate\Database\Eloquent\Collection $recipes */
?>

<x-app-layout>
    <header class="md:px-5 bg-indigo-100">
        <ol class="breadcrumb flex">
            <li class="breadcrumb-item">
                <a href="/recipes/">
                    Recipes
                    <i class="fas fa-chevron-right text-sm px-1 text-gray-600"></i>
                </a>
            </li>
            @foreach ($breadcrumbs as $breadcrumb)
                <li class="breadcrumb-item">
                    <a href="{{ $breadcrumb['url'] }}">
                        {{ $breadcrumb['name'] }}
                        @if (!$loop->last) <!-- Only show '>' if it's not the last item -->
                        <i class="fas fa-chevron-right text-sm px-1 text-gray-600"></i>
                        @endif
                    </a>
                </li>
            @endforeach
        </ol>
        <div class="p-4 text-center text-yellow-700">
            <h1 class="text-xl md:text-2xl">
                Recipes for {{ $region->name }}.
            </h1>
        </div>
    </header>

    <?php if ($recipes->count() === 0): ?>
        <div class="text-center text-gray-600 py-16 text-xl">
            There are no recipes published
        </div>
    <?php else: ?>
    <div class="grid md:grid-cols-[3fr_1fr] gap-8 grid-cols-[1fr]">
        <div>
            <h2 class="font-bold text-2xl p-4 relative inline-block">  Recipes for {{ $region->name }}.
                <span class="absolute bottom-0 left-1/2 w-1/2 border-b-2 border-yellow-300 -translate-x-1/2"></span>
            </h2>
            <div
                class="grid gap-8 grig-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 p-5"
            >
                @foreach($recipes as $recipe)
                    <!-- Recipe Item -->
                    <div
                        x-data="recipeItem({{ json_encode([
                        'id' => $recipe->id,
                        'image' => $recipe->image,
                        'name' => $recipe->name,
                        'description' => $recipe->description,
                    ]) }})"
                        class="rounded-md transition-colors"
                    >
                        <a href="{{ route('recipes.show', $recipe->id) }}"
                           class="aspect-w-3 aspect-h-2 block overflow-hidden">
                            <img
                                src="{{ $recipe->image }}"
                                alt=""
                                class="object-cover rounded-lg hover:scale-98 transition-transform p-1"
                            />
                        </a>
                        <div class="py-0 px-2">
                            <h3 class="text-lg font-bold">
                                <a href="{{ route('recipes.show', $recipe->id) }}">
                                    {{$recipe->name}}
                                </a>
                            </h3>
                        </div>
                        <div class="p-2">
                            <div class="flex items-center">
                                @for ($i = 1; $i <= 5; $i++)
                                    @if ($i <= floor($recipe->average_rating))
                                        <i class="fas fa-star text-pink-600"></i> <!-- Full star -->
                                    @elseif ($i - $recipe->average_rating < 1)
                                        <i class="fas fa-star-half-alt text-pink-600"></i> <!-- Half star -->
                                    @else
                                        <i class="far fa-star text-gray-300"></i> <!-- Empty star -->
                                    @endif
                                @endfor
                                <span class="ml-2 text-sm text-gray-700">
                            {{ number_format($recipe->average_rating, 1) }}/5 ({{ $recipe->review_count }} reviews)
                        </span>
                            </div>
                        </div>
                    </div>
                    <!--/ Recipe Item -->
                @endforeach
            </div>
            {{$recipes->links()}}
        </div>
    </div>
    <?php endif; ?>
</x-app-layout>
