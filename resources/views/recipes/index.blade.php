<?php
/** @var \Illuminate\Database\Eloquent\Collection $breakfasts */
/** @var \Illuminate\Database\Eloquent\Collection $vegetarians */
?>

<x-app-layout>
    <header class="md:px-5 bg-indigo-100">
        <nav class="md:block">
            <ul class="grid grid-flow-col items-center w-1/4">

                <li class="relative group">
                    <a
                        href="{{ route('recipes.index') }}"
                        class="relative flex items-center justify-between py-2 px-3 transition-colors hover:bg-indigo-300"
                    >
                        Cuisines
                    </a>

                    <!-- Dropdown Content -->
                    <ul class="absolute hidden bg-white shadow-md rounded-md w-40 z-[9999] group-hover:block">
                        @foreach ($rootRegions as $region)
                            <li class="relative group/item">
                                <!-- Parent Region Menu Item -->
                                <a href="{{ route('recipe.region', $region->name) }}" class="block px-4 py-2 hover:bg-gray-200">
                                    {{ $region->name }}
                                    @if($region->children->count() > 0)
                                        <i class="fa-solid fa-chevron-right text-xs"></i>
                                    @endif
                                </a>

                                <!-- Second-Level Dropdown -->
                                @if($region->children->count() > 0)
                                    <ul class="absolute left-full top-0 hidden bg-white shadow-md rounded-md w-40 group-hover/item:block">
                                        @foreach ($region->children as $child)
                                            <li class="relative">
                                                <a href="{{ route('recipe.region', $child->name) }}" class="block px-4 py-2 hover:bg-gray-200">
                                                    {{ $child->name }}
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </li>

                <!-- Ingredients Dropdown -->
                <li class="relative group">
                    <a
                        href="{{ route('recipes.index') }}"
                        class="relative flex items-center justify-between py-2 px-3 transition-colors hover:bg-slate-800"
                    >
                        Ingredients
                    </a>

                    <div class="absolute hidden bg-white shadow-md rounded-md w-[320px] z-[9999] group-hover:block p-4" x-data="{ showAll: false }">
                        <div class="grid grid-cols-2 gap-2">
                            @foreach ($ingredients as $ingredient)
                                <div class="relative group/item" x-show="showAll || {{ $loop->index }} < 10" x-cloak>
                                    <a href="{{ route('recipe.ingredient', $ingredient) }}"
                                       class="block px-2 py-1 hover:bg-gray-200 rounded">
                                        {{ $ingredient->normalized_name }}
                                    </a>
                                </div>
                            @endforeach
                        </div>

                        @if($ingredients->count() > 10)
                            <div class="mt-4 flex justify-center" :class="!showAll ? 'shadow-md shadow-gray-300 rounded-md' : ''">
                                <button @click="showAll = !showAll"
                                        class="text-blue-500 px-4 py-2 w-full flex items-center justify-center gap-2">
                                    <span x-text="showAll ? 'View Less' : 'View More'"></span>
                                    <i class="fa text-xs text-yellow-400" :class="showAll ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                                </button>
                            </div>
                        @endif
                    </div>

                </li>
                <!-- Shop Link (No Dropdown) -->
                <li>
                    <a
                        href="{{ route('shop') }}"
                        class="relative flex items-center justify-between py-2 px-3 transition-colors hover:bg-slate-800"
                    >
                        Shop
                    </a>
                </li>
            </ul>

        </nav>
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
    <div class="grid md:grid-cols-[3fr_1fr] gap-8 grid-cols-[1fr] z-1">
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
                            <a href="{{ route('recipe.category', $breakfast->category) }}" class="text-lg font-bold">
                               <h5 class="text-gray-600">{{$breakfast->category->name}}</h5>
                            </a>
                        </div>
                        <div class="py-0 px-2">
                            <h3 class="text-lg font-bold">
                                <a href="{{ route('recipes.show', $breakfast->id) }}">
                                    {{$breakfast->name}}
                                </a>
                            </h3>
                        </div>
                        <div class="p-2">
                            <x-rating :average_rating="$breakfast->average_rating" :review_count="$breakfast->review_count"/>
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
                            @if ($vegetarian->category)
                            <a href="{{ route('recipe.category', $vegetarian->category) }}" class="text-lg font-bold">
                               <h5 class="text-gray-600">{{$vegetarian->category->name}}</h5>
                            </a>
                            @else
                                <p class="text-red-500">No category assigned!</p>
                            @endif
                        </div>
                        <div class="py-0 px-2">
                            <h3 class="text-lg font-bold">
                                <a href="{{ route('recipes.show', $vegetarian->id) }}">
                                    {{$vegetarian->name}}
                                </a>
                            </h3>
                        </div>
                        <div class="p-2">
                            <x-rating :average_rating="$vegetarian->average_rating" :review_count="$vegetarian->review_count"/>
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
                                @if ($latestRecipe->category)
                                <a href="{{ route('recipe.category', $latestRecipe->category) }}" class="text-lg font-bold">
                                    <h5 class="text-gray-600">{{$latestRecipe->category->name}}</h5>
                                </a>
                                @else
                                    <p class="text-red-500">No category assigned!</p>
                                @endif

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
                })
                 .catch(error => {
                    console.error('Error fetching recipes:', error);
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
        class="border border-gray-400 m-2 p-4 w-3/4 md:mx-auto mx-1 w-full"
    >
        <div class="relative p-4 w-64">
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

        <div class="mt-2 p-4 w-60">
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
        <div x-show="recipes.length > 0" class="mt-4 md:p-4 p-2 grid gap-8 grid-cols-1 md:grid-cols-2 lg:grid-cols-3">
            <template x-for="recipe in recipes" :key="recipe.id">
                <div class="flex space-x-4 border-b py-2 flex-col md:flex-row">
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
                        <p class="text-gray-600" x-text="recipe.category.name"></p>
                    </div>
                </div>
            </template>

        </div>

        <!-- No Results Message -->
        <div x-show="searchPerformed && recipes.length === 0" class="mt-4 text-gray-500 p-4">
            No recipes found.
        </div>

        <!-- Pagination Controls (All Page Links) -->
        <div class="justify-center space-x-4 mt-4 w-60 overflow-hidden">
            <!-- Display Page Links -->
            <template x-for="page in pageLinks" :key="page.label">
                <button
                    @click="goToPage(page.url.split('?page=')[1])"
                    :class="{'bg-indigo-500 text-white': currentPage == page.label, 'px-4 py-2 rounded-full': true, 'bg-indigo-200': currentPage != page.label}"
                    :disabled="currentPage === page.label"
                    class="m-1"
                >
                    <span x-text="page.label"></span>
                </button>
            </template>
        </div>
    </div>
</x-app-layout>
