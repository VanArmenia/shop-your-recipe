<?php
/** @var \Illuminate\Database\Eloquent\Collection $products */
?>

<x-app-layout>
    <header class="">
        <div class="p-4 text-center w-full">
            <h1 class="text-2xl">
                Welcome to our shop.
            </h1>
            <p> Ready to compose your recipe?</p>
        </div>
    </header>
    <main class="grid grid-cols-1 md:grid-cols-[minmax(250px,_25%)_1fr] min-h-screen"
          x-data="{...  productFilter(), filterShow: 1 }"
          x-init="initSlider()"
    >
        <div class="flex justify-between bg-emerald-500 text-white text-lg font-bold p-2 headerBg mb-2 md:hidden cursor-pointer"
            @click="filterShow = !filterShow"
        >
           <h3> Filters </h3>
            <button class="block md:hidden">
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-6 w-6"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="2"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M4 6h16M4 12h16M4 18h16"
                    />
                </svg>
            </button>
        </div>
        <!-- Include the aside Blade component here -->
        <x-aside :categories="$categories" :prodCategory="($product->category->parent->id ?? 0)" :manufacturers="$manufacturers"/>
        <?php if ($products->count() === 0): ?>
        <div class="text-center text-gray-600 py-16 text-xl">
            There are no products published
        </div>
        <?php else: ?>
        <x-products :products="$products"/>
        <?php endif; ?>
    </main>

    <!-- Alpine.js Script for Product Filter Logic -->
    <script>
        function productFilter() {
            return {
                minPrice: 0,
                maxPrice: 500,
                selectedManufacturers: [],

                // Show products based on price and selected manufacturers
                productVisible(product) {
                    const priceMatch = product.price >= this.minPrice && product.price <= this.maxPrice;
                    const manufacturerMatch = this.selectedManufacturers.length === 0
                        || this.selectedManufacturers.includes(product.manufacturer_id);
                    return priceMatch && manufacturerMatch;
                },

                // Toggle manufacturer selection
                toggleManufacturer(manufacturerId) {
                    if (this.selectedManufacturers.includes(manufacturerId)) {
                        this.selectedManufacturers = this.selectedManufacturers.filter(id => id !== manufacturerId);
                    } else {
                        this.selectedManufacturers.push(manufacturerId);
                    }
                },

                initSlider() {
                    // Use Alpine's $refs to access the slider
                    const slider = this.$refs.slider;

                    noUiSlider.create(slider, {
                        start: [this.minPrice, this.maxPrice],
                        connect: true,
                        range: {
                            'min': 0,
                            'max': 500
                        }
                    });

                    slider.noUiSlider.on('update', (values, handle) => {
                        this.minPrice = Math.round(values[0]);
                        this.maxPrice = Math.round(values[1]);
                    });
                }
            };
        }
    </script>

</x-app-layout>
