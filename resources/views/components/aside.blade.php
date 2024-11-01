@props(['categories', 'prodCategory', 'manufacturers', 'catID' => 0])

<aside class="hidden md:block relative bg-purple-600 p-2 border rounded-md text-white mr-2" style="background-color: #ff9e9d">
    @if ($prodCategory == 0)
    <div class="price-filter mb-8">
        <label class="p-2 bg-greenSp w-full block text-white font-bold text-lg">Price</label>

        <!-- Bind the slider div to Alpine's initSlider function -->
        <div x-ref="slider" id="slider" class="m-6"></div>

        <div class="text-center">
            <p class="inline p-2 border-2 px-4">$<span x-text="minPrice"></span></p>
            - <p class="inline p-2 border-2 px-4">$<span x-text="maxPrice"></span></p>
        </div>
    </div>

    <div class="price-filter mb-8">
        <label class="p-2 bg-greenSp w-full block text-white font-bold text-lg mb-2">Producer</label>

        @foreach ($manufacturers as $manufacturer)
            <div class="p-1 pl-4">
                <label>
                    <input type="checkbox"
                           :value="{{ $manufacturer->id }}"
                           @change="toggleManufacturer({{ $manufacturer->id }})">
                    {{$manufacturer->name}}
                </label>
            </div>
        @endforeach

    </div>
    @endif

    <div class="mt-2">
        <label class="p-2 bg-greenSp w-full block text-white font-bold text-lg">Categories</label>

        {{-- Call the recursive component with top-level categories --}}
        <x-category-list :categories="$categories" :prodCategory="$prodCategory" :catID="$catID"/>
    </div>
</aside>
