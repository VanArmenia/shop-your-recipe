<?php
/** @var \Illuminate\Database\Eloquent\Collection $products */
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
                        'addToCartUrl' => route('cart.add', $product)
                    ]) }})"
                    class="border border-1 border-gray-200 rounded-md transition-colors bg-white"
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
                        <button class="btn-primary" @click="addToCart()">
                            Add to Cart
                        </button>
                    </div>
                </div>
                <!--/ Product Item -->
            @endforeach
        </div>
        {{$products->links()}}
    <?php endif; ?>
</x-app-layout>
