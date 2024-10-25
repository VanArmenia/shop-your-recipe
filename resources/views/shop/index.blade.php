<?php
/** @var \Illuminate\Database\Eloquent\Collection $products */
?>

<x-app-layout>
    <header class="">
        <div class="p-4 text-center w-full" style="background:linear-gradient(90deg, rgba(218,216,167) 0%, rgba(127,199,175) 50%, rgba(218,216,167) 100%);">
            <h1 class="text-2xl">
                Welcome to our shop.
            </h1>
            <p> Ready to compose your recipe?</p>
        </div>
    </header>
    <main class="grid grid-cols-1 md:grid-cols-[minmax(250px,_25%)_1fr] min-h-screen">
        <aside class="hidden md:block relative bg-purple-600 p-2 border rounded-md text-white mr-2"
               style="background-color: #ff9e9d"
        >
            {{-- Call the recursive component with top-level categories --}}
            <x-category-list :categories="$categories" :prodCategory="($product->category->parent->id ?? 0)"/>
        </aside>
        <?php if ($products->count() === 0): ?>
        <div class="text-center text-gray-600 py-16 text-xl">
            There are no products published
        </div>
        <?php else: ?>
        <div
            class="grid gap-8 grig-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 p-5"
        >
            @foreach($products as $product)
                <!-- Product Item -->
                <div
                    x-data="productItem({{ json_encode([
                        'id' => $product->id,
                        'slug' => $product->slug,
                        'image' => $product->image,
                        'title' => $product->title,
                        'price' => $product->price,
                        'addToCartUrl' => route('cart.add', $product)
                    ]) }})"
                    class="border border-1 rounded-md transition-colors bg-white p-2"
                    style="border-color: #ff9e9d"
                >
                    <a href="{{ route('product.view', $product->slug) }}"
                       class="aspect-w-3 aspect-h-2 block overflow-hidden">
                        <img
                            src="{{ $product->image }}"
                            alt=""
                            class="object-cover rounded-lg hover:scale-105 hover:scale-110 transition-transform pt-2"
                        />
                    </a>
                    <div class="p-4">
                        <h5 class="font-bold">{{$product->category->name}}</h5>
                    </div>
                    <div class="p-4">
                        <h3 class="text-lg">
                            <a href="{{ route('product.view', $product->slug) }}">
                                {{$product->title}}
                            </a>
                        </h3>
                        <h5 class="font-bold">${{$product->price}}</h5>
                    </div>
                    <div class="flex justify-between py-3 px-4">
                        <button class="btn-primary" @click="addToCart()" style="background-color: #ff3d7f">
                            Add to Cart
                        </button>
                    </div>
                </div>
                <!--/ Product Item -->
            @endforeach
        </div>
        {{$products->links()}}
        <?php endif; ?>
    </main>


</x-app-layout>
