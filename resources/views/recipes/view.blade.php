<x-app-layout>
    <!-- Add Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />

    <script src="https://cdn.jsdelivr.net/npm/osmtogeojson@3.0.0-beta.5/osmtogeojson.min.js"></script>

    <!-- Add Leaflet JS -->
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>



    <nav aria-label="breadcrumb" class="flex p-4">
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
    </nav>
    <div class="grid grid-cols-1 md:grid-cols-2 min-h-screen md:p-12 p-4 gap-8">

        <!-- Include the aside Blade component here -->
{{--        <x-aside :categories="$categories" :prodCategory="($product->category->parent->id ?? 0)"/>--}}

        <div x-data="recipeItem({{ json_encode([
                    'id' => $recipe->id,
                    'image' => $recipe->image,
                    'name' => $recipe->name,
                    'fetchReviews' => route('fetch-recipe-reviews', $recipe),
                    'addReview' => route('add-recipe-review', $recipe),
                ]) }})" class="container mx-auto">
            <div>
                <h1 class="text-lg font-semibold pb-2">
                    {{$recipe->name}}
                </h1>
                <div class="md:col-span-3">
                    <div
                        x-data="{
                      images: {{$recipe->images->map(fn($im) => $im->url)}},
                      activeImage: null,
                      prev() {
                          let index = this.images.indexOf(this.activeImage);
                          if (index === 0)
                              index = this.images.length;
                          this.activeImage = this.images[index - 1];
                      },
                      next() {
                          let index = this.images.indexOf(this.activeImage);
                          if (index === this.images.length - 1)
                              index = -1;
                          this.activeImage = this.images[index + 1];
                      },
                      init() {
                          this.activeImage = this.images.length > 0 ? this.images[0] : null
                      }
                    }"
                    >
                        <div class="relative">
                            <template x-for="image in images">
                                <div
                                    x-show="activeImage === image"
                                    class="aspect-w-3 aspect-h-2"
                                >
                                    <img :src="image" alt="" class="w-auto mx-auto"/>
                                </div>
                            </template>
                        </div>
                        <div class="flex pt-2">
                            <template x-for="image in images">
                                <a
                                    @click.prevent="activeImage = image"
                                    class="cursor-pointer w-[80px] h-[80px] border border-gray-300  flex items-center justify-center"
                                >
                                    <img :src="image" alt="" class="w-auto max-auto max-h-full hover:border-pink-300 border-2"
                                         :class="{'border-pink-300': activeImage === image}"
                                    />
                                </a>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        <div>
            <div class="md:col-span-3 bg-indigo-50 p-2 pt-1 mt-6 rounded-md border-indigo-50 border-t-amber-500 border border-t-8">
                <div class="flex justify-between gap-4 mx-1 pt-4">
                    <div class="">
                        <p class="">Prep Time:</p>
                        <p class="font-bold"> {{ $recipe->prep_time }} </p>
                    </div>
                    <div>
                        <p>Cook Time:</p>
                        <p class="font-bold"> {{ $recipe->cook_time }} </p>
                    </div>
                   <div>
                       <p class="">Servings</p>
                       <p class="font-bold"> {{ $recipe->servings }} </p>
                   </div>
                </div>
            </div>
            </div>
            <div class="md:col-span-3">
                <div class="mb-6 pt-2" x-data="{expanded: false}">
                    <h3 class="py-2 text-xl font-bold m-0">Directions</h3>
                    <div
                        x-show="expanded"
                        x-collapse.min.120px
                        class="text-gray-500"
                    >
                        {!! $recipe->description !!}
                    </div>

                    <p class="text-right mt-2">
                        <a
                            @click="expanded = !expanded"
                            href="javascript:void(0)"
                            class="text-white w-full bg-amber-500 hover:bg-amber-600 text-white py-1.5 px-2 rounded-md transition-colors"
                            x-text="expanded ? 'Read Less' : 'Read More'"
                        ></a>
                    </p>
                </div>
                {{--map--}}
                <h3 class="text-sm font-semibold mb-2">
                    <span class="font-normal">Original recipe from -</span>
                    <a href="{{ route('recipe.region', $recipe->region->name) }}" class="block py-2 inline-block">
                        {{ $recipe->region->name }}
                    </a>
                </h3>
                @if ($recipe->region && $recipe->region->latitude && $recipe->region->longitude)
                    <div id="map" style="width: 300px; height: 200px;"></div>

                    <script>
                        const lat = {{ $recipe->region->latitude }};
                        const lng = {{ $recipe->region->longitude }};
                        const zoom = 3;

                        const map = L.map('map', {
                            zoomControl: false
                        }).setView([lat, lng], zoom);

                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            attribution: '&copy;'
                        }).addTo(map);

                        map.scrollWheelZoom.disable();
                        map.touchZoom.disable();
                        map.doubleClickZoom.disable();
                    </script>
                @else
                    <p>Map data not available.</p>
                @endif
                {{--map--}}
            </div>
            <div class="md:col-span-3 mt-6">
                <hr class="border-t border-gray-400">
                <h3 class="py-2 text-xl font-bold m-0">Ingredients</h3>
                <ul>
                    @foreach($recipe->ingredients()->get() as $ingredient)
                        <li class="leading-relaxed">
                            <i class="fa-solid fa-utensils text-sm px-1 text-amber-500"></i> &nbsp;
                            {{$ingredient->name}} <i class="fas fa-chevron-right text-[8px] px-1 text-amber-600 align-middle"></i> &nbsp; &nbsp;
                            {{$ingredient->pivot->measurement}}
                        </li>
                    @endforeach
                </ul>
            </div>
            <div class="md:col-span-3 p-4 pt-1 mt-6 rounded-lg bg-indigo-50 pt-1 mt-6 rounded-md border-indigo-50 border-t-amber-500 border border-t-8">
                <h3 class="py-2 text-xl font-bold m-0">Nutrition Facts</h3>
                <div class="flex justify-start gap-12 mx-1">
                    <div class="">
                        <p class="font-bold"> {{ $recipe->calories }} </p>
                        <p class="mb-4">Calories</p>
                        <p class="font-bold"> {{ $recipe->protein }} </p>
                        <p>Protein</p>
                    </div>
                    <div class="">
                        <p class="font-bold"> {{ $recipe->carbohydrates }} </p>
                        <p class="mb-4">Carbs</p>
                        <p class="font-bold"> {{ $recipe->fats }} </p>
                        <p>Fats</p>
                    </div>
                </div>
            </div>

            <x-reviews handler="recipeItem" />
        </div>
    </div>
        {{--    Similar products --}}
        <?php if ($simRecipes->count() > 0): ?>
        <div class="w-full flex flex-col sliderDiv">
            <hr class="border-t border-gray-800">
            <h3 class="text-xl p-3">More
                <a href="{{ route('recipe.category', $recipe->category) }}" class="text-lg font-bold">
                    <h5 class="font-bold pb-4 inline">{{$recipe->category->name}}</h5>
                </a>
                recipes</h3>
            <div class="splide w-full flex-grow-1" role="group">
                <div class="splide__track w-full">
                    <ul class="splide__list">
                        @foreach($simRecipes as $recipe)
                            <!-- Product Item -->
                            <li class="splide__slide px-2">
                                <div
                                    x-data="recipeItem({{ json_encode([
                                        'id' => $recipe->id,
                                        'image' => $recipe->image,
                                        'name' => $recipe->name,
                                        'fetchReviews' => route('fetch-recipe-reviews', $recipe),
                                        'addReview' => route('add-recipe-review', $recipe),
                                    ]) }})"
                                    class="border border-1 border-gray-200 rounded-md hover:border-purple-200 transition-colors bg-white"
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
                                    <a href="{{ route('recipe.category', $recipe->category) }}" class="text-lg font-bold">
                                        <h5 class="text-gray-600">{{$recipe->category->name}}</h5>
                                    </a>
                                </div>
                                <div class="py-0 px-2">
                                    <h3 class="text-lg font-bold">
                                        <a href="{{ route('recipes.show', $recipe->id) }}">
                                            {{$recipe->name}}
                                        </a>
                                    </h3>
                                </div>
                                <div class="p-2">
                                    <x-rating :average_rating="$recipe->average_rating" :review_count="$recipe->review_count"/>
                                </div>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    <?php endif; ?>

</div>
<style>
    .sliderDiv {
        width: 100% !important;
    }
    .splide__arrow {                                /* Custom styles for Splide arrows */
        background-color: rgb(220 38 38);
    }
    .splide__arrow--prev {
        left: 0;
    }
    .splide__arrow--next {
        right: 0;
    }

    .category-list {
        list-style: none;               /* Remove bullets */
    padding-left: 0;                    /* Remove default padding */
    }

    .category-list li {
        margin: 5px 0;                  /* Spacing between items */
    }

    .category-list ul {
    padding-left: 20px;                 /* Indent child categories */
    }
    .activeTab {
        background-color: #3fb8af;
    }
</style>
</x-app-layout>
