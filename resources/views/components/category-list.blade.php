@props(['categories','depth' => 0])

<div x-data="{ catShow: 1 }" class="category-list">
    @foreach ($categories as $category)
        <div class="breadcrumb-item">
            <div class="flex justify-between items-center p-1" :class="catShow === {{ $category->id }} ? 'bg-purple-500 font-bold' : ''">
                <!-- Category name link -->
                <a href="{{ route('category', $category) }}" class="text-xl" style="padding-left: {{ $depth }}rem;">
                    {{ $category->name }}
                </a>

                @if($category->children->count() > 0)
                    <!-- Toggle button for expanding/collapsing children -->
                    <button
                        @click="catShow === {{ $category->id }} ? catShow = 0 : catShow = {{ $category->id }}"
                        class="ml-2">
                        <span x-show="catShow !== {{ $category->id }}" class="text-2xl">+</span>
                        <span x-show="catShow === {{ $category->id }}" class="text-2xl">-</span>
                    </button>
                @endif
            </div>

            @if($category->children->count() > 0)
                <!-- Separate container for child categories, not affected by flex -->
                <div x-show="catShow === {{ $category->id }}" x-collapse>
                    <x-category-list :categories="$category->children" :depth="$depth + 1"/>
                </div>
            @endif
        </div>
    @endforeach
</div>

