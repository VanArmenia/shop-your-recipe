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
    <div class="grid grid-cols-1 md:grid-cols-2 min-h-screen p-12 gap-8">

        <!-- Include the aside Blade component here -->
{{--        <x-aside :categories="$categories" :prodCategory="($product->category->parent->id ?? 0)"/>--}}

        <div x-data="recipeItem({{ json_encode([
                    'id' => $recipe->id,
                    'image' => $recipe->image,
                    'name' => $recipe->name,
                    'fetchReviews' => route('fetch-recipe-reviews', $recipe),
                    'addReview' => route('add-recipe-review', $recipe),
                ]) }})" class="container mx-auto">
            <div class="p-4 py-8">
                <div class="md:col-span-3 px-4">
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
                            <a
                                @click.prevent="prev"
                                class="cursor-pointer bg-black/30 text-white absolute left-0 top-1/2 -translate-y-1/2"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-10 w-10"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                    stroke-width="2"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M15 19l-7-7 7-7"
                                    />
                                </svg>
                            </a>
                            <a
                                @click.prevent="next"
                                class="cursor-pointer bg-black/30 text-white absolute right-0 top-1/2 -translate-y-1/2"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-10 w-10"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                    stroke-width="2"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M9 5l7 7-7 7"
                                    />
                                </svg>
                            </a>
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
            <x-reviews :reviews="$recipe->reviews" handler="recipeItem" />
        </div>
        <div>
            <div class="md:col-span-3">
                <h5 class="font-bold pb-4 ">{{$recipe->category->name}}</h5>
                <h1 class="text-lg font-semibold">
                    {{$recipe->name}}
                </h1>
                <h3 class="text-sm font-semibold mb-2">
                    <span class="font-normal">Original recipe from - </span> {{$recipe->region->name}}
                </h3>
                <div class="mb-6" x-data="{expanded: false}">
                    <div
                        x-show="expanded"
                        x-collapse.min.120px
                        class="text-gray-500 wysiwyg-content"
                    >
                        {!! $recipe->description !!}
                    </div>

                    {{--map--}}
                    <div id="map" style="width: 300px; height: 200px;"></div>

                    <p class="text-right">
                        <a
                            @click="expanded = !expanded"
                            href="javascript:void(0)"
                            class="text-purple-500 hover:text-purple-700"
                            x-text="expanded ? 'Read Less' : 'Read More'"
                        ></a>
                    </p>
                </div>
            </div>
            <div class="md:col-span-3">
                <ul>
                    @foreach($recipe->ingredients()->get() as $ingredient)
                        <li class="leading-relaxed">
                            {{$ingredient->name}} - {{$ingredient->pivot->measurement}}
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
        <script>
            // Dynamically pass the latitude, longitude, and zoom level from PHP to JavaScript
            var lat = {{ $recipe->region->latitude }}; // Replace with actual data
            var lng = {{ $recipe->region->longitude }}; // Replace with actual data
            var zoom = 4; // Replace with desired zoom level
            // Create the map object and set the initial view (latitude, longitude, zoom level)
            var map = L.map('map', {
                zoomControl: false // Disable the zoom controls entirely when initializing
            }).setView([lat, lng], zoom);

            // Add OpenStreetMap tile layer to the map
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy;'
            }).addTo(map);

            // Disable zooming entirely (mouse scroll and touch gestures)
            map.scrollWheelZoom.disable();
            map.touchZoom.disable();
            map.doubleClickZoom.disable();

            fetch('/geojson')
                .then(response => response.json())
                .then(data => {

                    // Be careful with filtering raw OSM data
                    // Make sure to preserve the structure
                    const filteredData = {
                        ...data,  // Keep all the original properties
                        elements: data.elements.filter(element => {
                            // Only filter out nodes that aren't part of ways
                            if (element.type === 'node') {
                                // Check if this node is referenced by any way
                                const isUsedInWay = data.elements.some(e =>
                                    e.type === 'way' &&
                                    e.nodes &&
                                    e.nodes.includes(element.id)
                                );
                                return isUsedInWay;
                            }
                            // Keep all ways and relations
                            return true;
                        })
                    };

                    // Then convert the filtered data
                    const geojsonData = osmtogeojson(filteredData);

                    // Convert OSM data to GeoJSON
                    var geoJson = osmtogeojson(geojsonData);

                    L.geoJSON(geoJson, {
                        style: {
                            color: "#ff7800", // Border color
                            weight: 3,        // Border thickness
                            opacity: 0.7      // Border opacity
                        }
                    }).addTo(map);
                })
                .catch(error => console.error('Error fetching GeoJSON:', error));

        </script>

    </div>

    {{--    Similar products --}}
{{--    <?php if ($recipe->count() > 0): ?>--}}
{{--    <div class="w-full flex flex-col sliderDiv">--}}
{{--        <h3 class="text-xl pt-2">Similar products</h3>--}}
{{--        <div class="splide p-4 w-full flex-grow-1" role="group">--}}
{{--            <div class="splide__track w-full">--}}
{{--                <ul class="splide__list">--}}
{{--                    @foreach($simProducts as $product)--}}
{{--                        <!-- Product Item -->--}}
{{--                        <li class="splide__slide px-2">--}}
{{--                            <div--}}
{{--                                x-data="productItem({{ json_encode([--}}
{{--                    'id' => $product->id,--}}
{{--                    'slug' => $product->slug,--}}
{{--                    'image' => $product->image,--}}
{{--                    'title' => $product->title,--}}
{{--                    'price' => $product->price,--}}
{{--                    'addToCartUrl' => route('cart.add', $product)--}}
{{--                ]) }})"--}}
{{--                                class="border border-1 border-gray-200 rounded-md hover:border-purple-200 transition-colors bg-white"--}}
{{--                            >--}}
{{--                                <a href="{{ route('product.view', $product->slug) }}"--}}
{{--                                   class="aspect-w-3 aspect-h-2 block overflow-hidden">--}}
{{--                                    <img--}}
{{--                                        src="{{ $product->image }}"--}}
{{--                                        alt=""--}}
{{--                                        class="object-cover rounded-lg hover:scale-105 hover:rotate-1 transition-transform pt-2"--}}
{{--                                    />--}}
{{--                                </a>--}}
{{--                                <div class="p-4">--}}
{{--                                    <h3 class="text-sm h-10 pb-12">--}}
{{--                                        <a href="{{ route('product.view', $product->slug) }}">--}}
{{--                                            {{$product->title}}--}}
{{--                                        </a>--}}
{{--                                    </h3>--}}
{{--                                    <h5 class="font-bold text-sm">${{$product->price}}</h5>--}}
{{--                                </div>--}}
{{--                                <div class="flex justify-center pb-2 px-4">--}}
{{--                                    <button class="btn-primary w-full" @click="addToCart()">--}}
{{--                                        Add to Cart--}}
{{--                                    </button>--}}
{{--                                </div>--}}

{{--                            </div>--}}
{{--                        </li>--}}
{{--                        <!--/ Product Item -->--}}
{{--                    @endforeach--}}
{{--                </ul>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--        {{$simProducts->links()}}--}}
{{--    </div>--}}
{{--    <?php endif; ?>--}}

    <style>
        .sliderDiv {
            width: 100% !important;
        }
        /* Custom styles for Splide arrows */
        .splide__arrow {
            background-color: rgb(255,61,127);
        }

        .splide__arrow--prev {
            left: 7px;                     /* Adjust position */
        }

        /* Style the next arrow */
        .splide__arrow--next {
            right: 7px;                    /* Adjust position */
        }

        .category-list {
            list-style: none; /* Remove bullets */
            padding-left: 0; /* Remove default padding */
        }

        .category-list li {
            margin: 5px 0; /* Spacing between items */
        }

        .category-list ul {
            padding-left: 20px; /* Indent child categories */
        }
        .activeTab {
            background-color: #3fb8af;
        }
    </style>
</x-app-layout>
