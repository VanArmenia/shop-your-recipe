@props(['products'])

<div
    class="grid gap-8 grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 p-5 auto-rows-min"
>
    @foreach($products as $product)
        <!-- Product Item -->
        <div
            x-show="productVisible({{ json_encode([
                        'price' => $product->price,
                        'manufacturer_id' => $product->manufacturer->id
                    ]) }})"
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
