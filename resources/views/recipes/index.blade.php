<?php
/** @var \Illuminate\Database\Eloquent\Collection $breakfasts */
/** @var \Illuminate\Database\Eloquent\Collection $vegetarians */
?>

<x-app-layout>
    <header class="md:px-5 bg-indigo-100">
        <div class="p-4 text-center text-yellow-700">
            <h1 class="text-xl md:text-2xl">
                Welcome to our Web Kitchen.
            </h1>
            <p> <span class="text-yellow-800 font-bold">{{ $countRecipes }}</span> Original Recipes</p>
            <p> <span class="text-yellow-800 font-bold">{{ $countReviews }}</span> Reviews</p>
        </div>
    </header>

    <?php if ($breakfasts->count() === 0 || $vegetarians->count() === 0): ?>
        <div class="text-center text-gray-600 py-16 text-xl">
            There are no recipes published
        </div>
    <?php else: ?>
    <div class="grid md:grid-cols-[3fr_1fr] gap-8 grid-cols-[1fr]">
        <div>
            <h2 class="font-bold text-2xl p-4 relative inline-block">Breakfast
                <span class="absolute bottom-0 left-1/2 w-1/2 border-b-2 border-yellow-300 -translate-x-1/2"></span>
            </h2>
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

            <h2 class="font-bold text-2xl p-4 relative inline-block">Vegetarian
                <span class="absolute bottom-0 left-1/2 w-1/2 border-b-2 border-yellow-300 -translate-x-1/2"></span>
            </h2>

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
        </div>

        <div>
            <h2 class="font-bold text-2xl p-4 relative inline-block">Latest
                <span class="absolute bottom-0 left-1/2 w-1/2 border-b-2 border-yellow-300 -translate-x-1/2"></span>
            </h2>

            <div
                class="grid gap-8 grid-cols-1 p-5"
            >
                @foreach($latestRecipes as $latestRecipe)
                    <!-- Recipe Item -->
                    <div
                        x-data="recipeItem({{ json_encode([
                        'id' => $latestRecipe->id,
                        'image' => $latestRecipe->image,
                        'name' => $latestRecipe->name,
                        'description' => $latestRecipe->description,
                    ]) }})"
                        class="rounded-md transition-colors flex items-start gap-2"
                    >
                        <div class="w-[120px] flex-shrink-0">
                            <a href="{{ route('recipes.show', $latestRecipe->id) }}">
                                <img
                                    src="{{ $latestRecipe->image }}"
                                    alt=""
                                    class="w-full h-full overflow-cover rounded-lg hover:scale-98 transition-transform p-1"
                                />
                            </a>
                        </div>
                        <div class="flex-1">
                            <div class="p-2">
                                <h5 class="text-gray-600">{{$latestRecipe->category}}</h5>
                            </div>
                            <div class="py-0 px-2">
                                <h3 class="text-lg font-bold">
                                    <a href="{{ route('recipes.show', $latestRecipe->id) }}">
                                        {{$latestRecipe->name}}
                                    </a>
                                </h3>
                            </div>
                        </div>
                    </div>
                    <!--/ Recipe Item -->
                @endforeach
            </div>
        </div>
    </div>
    <?php endif; ?>

    <div
        x-data="{
        query: '',
        recipes: [],
        currentPage: 1,
        totalPages: 0,
        pageLinks: [],
        nextUrl: null,
        prevUrl: null,
        searchPerformed: false,
        search(page = 1) {
            this.searchPerformed = true;
            fetch('{{ route('recipes.search') }}?q=' + this.query + '&page=' + page)
                .then(response => response.json())
                .then(data => {
                    this.recipes = data.data;
                    this.currentPage = data.current_page;
                    this.totalPages = data.last_page;
                    this.pageLinks = data.links.filter(link => {
                      return !link.label.includes('Previous') && !link.label.includes('Next');
                    });
                    this.nextUrl = data.next_page_url;
                    this.prevUrl = data.prev_page_url;
                });
        },
         goToPage(page) {
            this.search(page);  // Fetch data for the selected page
         },
         predefinedSearch(term) {
            this.query = term;
            this.search();
        }
    }"
        class="border border-gray-400 m-2 p-4 w-3/4 mx-auto"
    >
        <div class="relative p-4">
            <div class="flex items-center space-x-2">
                <i class="fas fa-blender text-gray-700 text-lg"></i>
                <div class="text-lg font-semibold px-1">What would you like to cook?</div>
            </div>

            <div id="related-category-search__form_1-0" class="mt-4">
                <form class="flex items-center" role="search" action="/search" method="get">
                    <div class="flex w-full">
                        <label for="related-category-search__form__search-input" class="sr-only">Search the site</label>
                        <input
                            x-model="query"
                            x-on:input.debounce.500ms="search"
                            type="text"
                            class="w-full py-2 px-4 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500" placeholder="Search here..." required="required" autocomplete="off"
                        >
                        <button class="ml-2 p-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 px-3" aria-label="Click to search">
                            <i class="fas fa-search text-gray-200"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="mt-2 p-4">
            <div class="text-xl font-bold mb-4">Popular Searches</div>
            <div
                @click="predefinedSearch('Chicken')"
                class="text-indigo-700 hover:bg-indigo-400 hover:text-indigo-100 px-4 py-2 bg-indigo-200 inline-block my-1 cursor-pointer">Chicken
            </div>
            <div
                @click="predefinedSearch('Smoothies')"
                class="text-indigo-700 hover:bg-indigo-400 hover:text-indigo-100 px-4 py-2 bg-indigo-200 inline-block my-1 cursor-pointer">Smoothies
            </div>
            <div
                @click="predefinedSearch('Banana Bread')"
                class="text-indigo-700 hover:bg-indigo-400 hover:text-indigo-100 px-4 py-2 bg-indigo-200 inline-block my-1 cursor-pointer">Banana Bread
            </div>
            <div
                @click="predefinedSearch('Lasagna')"
                class="text-indigo-700 hover:bg-indigo-400 hover:text-indigo-100 px-4 py-2 bg-indigo-200 inline-block my-1 cursor-pointer">Lasagna
            </div>
            <div
                @click="predefinedSearch('Pancakes')"
                class="text-indigo-700 hover:bg-indigo-400 hover:text-indigo-100 px-4 py-2 bg-indigo-200 inline-block my-1 cursor-pointer">Pancakes
            </div>
            <div
                @click="predefinedSearch('Meatloaf')"
                class="text-indigo-700 hover:bg-indigo-400 hover:text-indigo-100 px-4 py-2 bg-indigo-200 inline-block my-1 cursor-pointer">Meatloaf
            </div>
        </div>

        <!-- Recipe Results -->
        <div x-show="recipes.length > 0" class="mt-4 p-4 grid gap-8 grid-cols-1 md:grid-cols-2 lg:grid-cols-3">
            <template x-for="recipe in recipes" :key="recipe.id">
                <div class="flex space-x-4 border-b py-2">
                    <!-- Image -->
                    <div class="w-[120px] flex-shrink-0">
                        <a :href="'/recipes/' + recipe.id" class="text-blue-500 hover:underline">
                            <img
                                :src="recipe.image"
                                alt=""
                                class="w-full h-full object-cover rounded-lg hover:scale-98 transition-transform p-1"
                            />
                        </a>
                    </div>
                    <!-- Recipe Details -->
                    <div class="flex-1">
                        <a :href="'/recipes/' + recipe.id" class="text-blue-500 hover:underline">
                          <h3 class="text-lg font-bold text-gray-800" x-text="recipe.name"></h3>
                        </a>
                        <p class="text-gray-600" x-text="recipe.category"></p>
                    </div>
                </div>
            </template>

        </div>

        <!-- No Results Message -->
        <div x-show="searchPerformed && recipes.length === 0" class="mt-4 text-gray-500 p-4">
            No recipes found.
        </div>

        <!-- Pagination Controls (All Page Links) -->
        <div class="flex justify-center space-x-4 mt-4">
            <!-- Display Page Links -->
            <template x-for="page in pageLinks" :key="page.label">
                <button
                    @click="goToPage(page.url.split('?page=')[1])"
                    :class="{'bg-indigo-500 text-white': currentPage == page.label, 'px-4 py-2 rounded-full': true, 'bg-indigo-200': currentPage != page.label}"
                    :disabled="currentPage === page.label"
                >
                    <span x-text="page.label"></span>
                </button>
            </template>
        </div>
    </div>
</x-app-layout>
