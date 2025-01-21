<?php
/** @var \Illuminate\Database\Eloquent\Collection $recipes */
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

    <?php if ($recipes->count() === 0): ?>
        <div class="text-center text-gray-600 py-16 text-xl">
            There are no products published
        </div>
    <?php else: ?>
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
                    <div class="p-2">
                        <h5 class="text-gray-600">{{$recipe->category}}</h5>
                    </div>
                    <div class="py-0 px-2">
                        <h3 class="text-lg font-bold">
                            <a href="{{ route('recipes.show', $recipe->id) }}">
                                {{$recipe->name}}
                            </a>
                        </h3>
                    </div>
                </div>
                <!--/ Product Item -->
            @endforeach
        </div>
        {{$recipes->links()}}
    <?php endif; ?>
</x-app-layout>
