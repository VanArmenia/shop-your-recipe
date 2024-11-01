@props(['categories', 'depth' => 0, 'prodCategory' => 0, 'catID' => 0])

<div x-data="{ catShow: {{ $prodCategory !== 0 ? $prodCategory : $catID }} }"
     class="category-list"
     style="margin-left: {{ $depth }}rem;"
>
    @foreach ($categories as $category)
        <div class="breadcrumb-item">
            <div class="flex justify-between items-center p-1">
                <!-- Category name link -->
                <a href="{{ route('category', $category) }}" class="text-lg font-bold">
                    {{ $category->name }}
                </a>

                @if($category->children->count() > 0)
                    <!-- Toggle button for expanding/collapsing children -->
                    <button
                        @click="catShow === {{ $category->id }} ? catShow = 0 : catShow = {{ $category->id }}"
                        class="ml-2">
                        @if ($category->id !== 1)
                        <span x-show="catShow !== {{ $category->id }}" class="text-2xl">+</span>
                        <span x-show="catShow === {{ $category->id }}" class="text-2xl">-</span>
                        @endif
                    </button>
                @endif
            </div>

            @if($category->children->count() > 0)
                <!-- Separate container for child categories, not affected by flex -->
                <div x-show="catShow === {{ $category->id }} || {{ $depth }} === 0"
                     x-collapse
                     :class="catShow === {{ $category->id }} ? 'activeCategory font-bold' : ''"
                     style="margin-left: {{ $depth + 1 }}rem;"
                >
                    <x-category-list :categories="$category->children" :depth="$depth + 1" :prodCategory="$prodCategory" :catID="$catID"/>
                </div>
            @endif
        </div>
    @endforeach
</div>
<style>
    .activeCategory {
        background-color: #3fb8af;
    }
</style>

