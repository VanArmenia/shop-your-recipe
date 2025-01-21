<x-app-layout>
{{--    <nav aria-label="breadcrumb" class="flex p-4">--}}
{{--        <ol class="breadcrumb flex">--}}
{{--            @foreach ($breadcrumbs as $breadcrumb)--}}
{{--                <li class="breadcrumb-item">--}}
{{--                    <a href="{{ $breadcrumb['url'] }}">--}}
{{--                        {{ $breadcrumb['name'] }}--}}
{{--                        @if (!$loop->last) <!-- Only show '>' if it's not the last item -->--}}
{{--                        <i class="fas fa-chevron-right text-sm px-1 text-gray-600"></i>--}}
{{--                        @endif--}}
{{--                    </a>--}}
{{--                </li>--}}
{{--            @endforeach--}}
{{--        </ol>--}}
{{--    </nav>--}}
    <div class="grid grid-cols-1 md:grid-cols-[minmax(250px,_25%)_1fr] min-h-screen">

        <!-- Include the aside Blade component here -->
{{--        <x-aside :categories="$categories" :prodCategory="($product->category->parent->id ?? 0)"/>--}}

        <div x-data="recipeItem({{ json_encode([
                    'id' => $recipe->id,
                    'image' => $recipe->image,
                    'name' => $recipe->name,
                    'fetchReviews' => route('fetch-reviews', $recipe)
                ]) }})" class="container mx-auto">
            <div class="grid gap-6 grid-cols-1 lg:grid-cols-6 p-4">
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
                <div class="md:col-span-3">
                    <h5 class="font-bold pb-4 ">{{$recipe->category}}</h5>
                    <h1 class="text-lg font-semibold">
                        {{$recipe->name}}
                    </h1>
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
                    <div class="mb-6" x-data="{expanded: false}">
                        <div
                            x-show="expanded"
                            x-collapse.min.120px
                            class="text-gray-500 wysiwyg-content"
                        >
                            {!! $recipe->description !!}
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
            {{--Reviews--}}
            <div class="border-t-2">
                <div x-data="reviewHandler()">
                    <h3 class="p-4 text-lg">Reviews</h3>
                    @if(auth()->check())
                        <form @submit.prevent="submitReview">
                            <label for="rating">Rating:</label>
                            <select name="rating" x-model="rating" required>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="5">5</option>
                            </select>

                            <label for="review_text">Review:</label>
                            <textarea name="review_text" x-model="reviewText" rows="3"></textarea>

                            <button
                                type="submit"
                                class="btn-primary py-4 text-lg flex justify-center min-w-0 w-48 m-6">
                                Submit Review
                            </button>
                        </form>
                    @else
                        <p>Please <a href="{{ route('login') }}">log in</a> to submit a review.</p>
                    @endif
                    <div>
                        <template x-for="review in reviews" :key="review.id">
                            <div class="review mb-4 p-2 border-b">
                                <div class="flex w-1/2 items-center mb-2">
                                    <!-- Conditionally render the avatar -->
                                    <template x-if="review.user.customer.avatar">
                                        <img :src="'/storage/' + review.user.customer.avatar" alt="User Avatar" class="w-16 h-16 rounded-full">
                                    </template>
                                    <template x-if="!review.user.customer.avatar">
                                        <img src="{{ asset('images/default-avatar.png') }}" alt="Default Avatar" class="w-20 h-20 rounded-full">
                                    </template>
                                    <p class="font-bold p-4" x-text="review.user.name"></p>
                                </div>

                                <div class="flex items-center">
                                    <template x-for="n in review.rating" :key="n">
                                        <span class="start-icon text-l m-1">★</span> <!-- Star icon -->
                                    </template>
                                </div>
                                <p x-text="review.review_text"></p>
                                <p class="text-sm text-gray-500" x-text="new Date(review.created_at).toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' })"></p>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
     </div>
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
