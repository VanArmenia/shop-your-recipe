<x-app-layout>
    <nav aria-label="breadcrumb" class="flex p-4">
        <ol class="breadcrumb flex">
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
    <div class="grid grid-cols-1 md:grid-cols-[minmax(250px,_25%)_1fr] min-h-screen">

        <aside class="hidden md:block relative p-2 border rounded-md text-white mr-2"
        style="background-color: #7fc7af">
            {{-- Call the recursive component with top-level categories --}}
            <x-category-list :categories="$categories" :prodCategory="($product->category->parent->id ?? 0)"/>
        </aside>

        <div x-data="productItem({{ json_encode([
                    'id' => $product->id,
                    'slug' => $product->slug,
                    'image' => $product->image,
                    'title' => $product->title,
                    'price' => $product->price,
                    'quantity' => $product->quantity,
                    'addToCartUrl' => route('cart.add', $product)
                ]) }})" class="container mx-auto">
            <div class="grid gap-6 grid-cols-1 lg:grid-cols-6 px-4">
                <div class="md:col-span-3 px-4">
                    <div
                        x-data="{
                      images: {{$product->images->map(fn($im) => $im->url)}},
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
                <div class="md:col-span-3">
                    <h5 class="font-bold pb-4 ">{{$product->category->name}}</h5>
                    <h1 class="text-lg font-semibold">
                        {{$product->title}}
                    </h1>
                    <div class="text-xl font-bold mb-6">${{$product->price}}</div>
                    @if ($product->quantity === 0)
                        <div class="bg-red-400 text-white py-2 px-3 rounded mb-3">
                            The product is out of stock
                        </div>
                    @endif
                    <div class="flex items-center justify-between mb-5">
                        <label for="quantity" class="block font-bold mr-4">
                            Quantity
                        </label>
                        <input
                            type="number"
                            name="quantity"
                            x-ref="quantityEl"
                            value="1"
                            min="1"
                            class="w-32 focus:border-purple-500 focus:outline-none rounded"
                        />
                    </div>
                    <button
                        :disabled="product.quantity === 0"
                        @click="addToCart($refs.quantityEl.value)"
                        class="btn-primary py-4 text-lg flex justify-center min-w-0 w-full mb-6"
                        :class="product.quantity === 0 ? 'cursor-not-allowed' : 'cursor-pointer'"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-6 w-6 mr-2"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"
                            />
                        </svg>
                        Add to Cart
                    </button>
                    <div class="mb-6" x-data="{expanded: false}">
                        <div
                            x-show="expanded"
                            x-collapse.min.120px
                            class="text-gray-500 wysiwyg-content"
                        >
                            {!! $product->description !!}
                        </div>

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
            </div>
            <div x-data="{ currentTab: 1 }" class="border-b border-gray-200 dark:border-gray-700 my-4">
                <ul class="flex flex-wrap -mb-px text-sm font-medium text-center text-gray-500 dark:text-gray-400 mb-1" id="tabs-example" role="tablist">
                    @if ($product->manufacturer)
                        <li @click="currentTab = 1">
                            <button href="#" :class="currentTab === 1 ? 'text-white activeTab' : ''"
                                    class="inline-block rounded-lg border-2 p-3 hover:border-green-100">
                                Manufacturer
                            </button>
                        </li>
                    @endif
                    @if ($product->allergens)
                        <li @click="currentTab = 2">
                            <button href="#"  :class="currentTab === 2 ? 'text-white activeTab' : ''"
                                    class="inline-block rounded-lg border-2 p-3 hover:border-green-100">
                                Allergens
                            </button>
                        </li>
                    @endif
                    @if ($product->composition)
                        <li @click="currentTab = 3">
                            <button href="#"  :class="currentTab === 3 ? 'text-white activeTab' : ''"
                                    class="inline-block rounded-lg border-2 p-3 hover:border-green-100">
                                Composition
                            </button>
                        </li>
                    @endif
                    @if ($product->storing)
                        <li @click="currentTab = 4">
                            <button href="#"  :class="currentTab === 4 ? 'text-white activeTab' : ''"
                                    class="inline-block rounded-lg border-2 p-3 hover:border-green-100">
                                Storing
                            </button>
                        </li>
                    @endif
                    @if ($product->nutritional)
                        <li @click="currentTab = 5">
                            <button href="#"  :class="currentTab === 5 ? 'text-white activeTab' : ''"
                                    class="inline-block rounded-lg border-2 p-3 hover:border-green-100">
                                Nutritional
                            </button>
                        </li>
                    @endif
                </ul>
                <div class="rounded-lg bg-gray-50 p-4 dark:bg-gray-800 min-h-24">
                    <div x-show="currentTab === 1">
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            {!! $product->manufacturer !!}
                        </p>
                    </div>
                    <div x-show="currentTab === 2">
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            {!! $product->allergens !!}
                        </p>
                    </div>
                    <div x-show="currentTab === 3">
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            {!! $product->composition !!}
                        </p>
                    </div>
                    <div x-show="currentTab === 4">
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            {!! $product->storing !!}
                        </p>
                    </div>
                    <div x-show="currentTab === 5">
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            {!! $product->nutritional !!}
                        </p>
                    </div>
                </div>
            </div>
     </div>
    </div>

    {{--    Similar products --}}
    <?php if ($simProducts->count() > 0): ?>
    <div class="w-full flex flex-col sliderDiv">
        <h3 class="text-xl pt-2">Similar products</h3>
        <div class="splide p-4 w-full flex-grow-1" role="group">
            <div class="splide__track w-full">
                <ul class="splide__list">
                    @foreach($simProducts as $product)
                        <!-- Product Item -->
                        <li class="splide__slide px-2">
                            <div
                                x-data="productItem({{ json_encode([
                    'id' => $product->id,
                    'slug' => $product->slug,
                    'image' => $product->image,
                    'title' => $product->title,
                    'price' => $product->price,
                    'addToCartUrl' => route('cart.add', $product)
                ]) }})"
                                class="border border-1 border-gray-200 rounded-md hover:border-purple-200 transition-colors bg-white"
                            >
                                <a href="{{ route('product.view', $product->slug) }}"
                                   class="aspect-w-3 aspect-h-2 block overflow-hidden">
                                    <img
                                        src="{{ $product->image }}"
                                        alt=""
                                        class="object-cover rounded-lg hover:scale-105 hover:rotate-1 transition-transform pt-2"
                                    />
                                </a>
                                <div class="p-4">
                                    <h3 class="text-sm h-10 pb-12">
                                        <a href="{{ route('product.view', $product->slug) }}">
                                            {{$product->title}}
                                        </a>
                                    </h3>
                                    <h5 class="font-bold text-sm">${{$product->price}}</h5>
                                </div>
                                <div class="flex justify-center pb-2 px-4">
                                    <button class="btn-primary w-full" @click="addToCart()">
                                        Add to Cart
                                    </button>
                                </div>

                            </div>
                        </li>
                        <!--/ Product Item -->
                    @endforeach
                </ul>
            </div>
        </div>
        {{$simProducts->links()}}
    </div>
    <?php endif; ?>

    @push('scripts')
        <script type="module">
        </script>
    @endpush
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
