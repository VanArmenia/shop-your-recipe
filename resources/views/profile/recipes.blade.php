<x-account-layout>
    <div x-data="{ currentView: '{{ $errors->any() ? 'recipe_form' : 'recipes' }}' }" class="flex">
        <div x-show="currentView === 'recipes'"
             class="container mx-auto lg:w-2/3 p-6"
        >
            <div class="flex justify-between">
                <h2 class="text-gray-800 text-xl font-bold"> My Recipes </h2>
                <button
                    class="ml-2 p-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 px-3"
                    aria-label="Click to search"
                    @click="currentView = 'recipe_form'"
                >
                    <i class="fa-solid fa-stroopwafel"></i>
                    Add Recipe
                </button>
            </div>

            <div class="grid gap-8 grig-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 p-5">
                @foreach($user->recipes as $recipe)
                    <!-- Recipe Item -->
                    <div class="rounded-md transition-colors">
                        <a href="{{ route('recipes.show', $recipe->id) }}"
                           class="aspect-w-3 aspect-h-2 block overflow-hidden">
                            <img
                                src="{{ $recipe->image }}"
                                alt=""
                                class="object-cover rounded-lg hover:scale-98 transition-transform p-1"
                            />
                        </a>
                        <div class="p-2">
                            <a href="{{ route('recipe.category', $recipe->category) }}" class="text-lg font-bold">
                                <h5 class="text-gray-600">{{$recipe->category->name}}</h5>
                            </a>
                        </div>
                        <div class="py-0 px-2">
                            <h3 class="text-lg font-bold">
                                <a href="{{ route('recipes.show', $recipe->id) }}">
                                    {{$recipe->name}}
                                </a>
                            </h3>
                        </div>
                        <div class="p-2">
                            <x-rating :average_rating="$recipe->average_rating" :review_count="$recipe->review_count"/>
                        </div>
                    </div>
                        <!--/ Recipe Item -->
                @endforeach
            </div>
        </div>

        {{--Recipe Form--}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-start m-5"
             x-show="currentView === 'recipe_form'"
        >
            <div class="bg-white p-3 shadow rounded-lg md:col-span-2">
                <form
                    action="{{ route('recipes.store') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <h2 class="text-xl font-semibold mb-2">Recipe Form</h2>
                    <div class="grid grid-cols-2 gap-3 mb-3">
                        <!-- Images -->
                        <x-input
                            type="file"
                            name="images[]"
                            label="Upload Image"
                            class="w-full mt-2"
                        />
                        {{--Categories--}}
                        <select name="category_id"
                                class="inline-flex items-center px-3 rounded-md border border-gray-300 text-gray-500 text-sm mb-2 h-10">
                            <option disabled selected value="">Select a Category:</option>

                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>

                        {{--Regions--}}
                        <select name="region_id"
                                class="inline-flex items-center px-3 rounded-md border border-gray-300 text-gray-500 text-sm mb-2 h-10">
                            <option disabled selected value="">Select a Region:</option>

                            @foreach ($regions as $region)
                                <option value="{{ $region->id }}">{{ $region->name }}</option>
                            @endforeach
                        </select>

                        <x-input
                            type="text"
                            name="name"
                            placeholder="Recipe Name"
                            class="w-full focus:border-purple-600 focus:ring-purple-600 border-gray-300 rounded"
                        />
                        <x-input
                            type="text"
                            name="description"
                            placeholder="Recipe Description"
                            class="w-full focus:border-purple-600 focus:ring-purple-600 border-gray-300 rounded"
                        />
                    </div>

                    <div class="mb-3">
                        <x-input
                            type="text"
                            name="prep_time"
                            placeholder="Prep Time"
                            class="w-full focus:border-purple-600 focus:ring-purple-600 border-gray-300 rounded"
                        />
                    </div>
                    <div class="mb-3">
                        <x-input
                            type="text"
                            name="cook_time"
                            placeholder="Cook Time"
                            class="w-full focus:border-purple-600 focus:ring-purple-600 border-gray-300 rounded"
                        />
                    </div>
                    <div class="mb-3">
                        <x-input
                            type="text"
                            name="servings"
                            placeholder="Servings"
                            class="w-full focus:border-purple-600 focus:ring-purple-600 border-gray-300 rounded"
                        />
                    </div>
                    <div class="mb-3">
                        <x-input
                            type="text"
                            name="calories"
                            placeholder="Calories"
                            class="w-full focus:border-purple-600 focus:ring-purple-600 border-gray-300 rounded"
                        />
                    </div>
                    <div class="mb-3">
                        <x-input
                            type="text"
                            name="protein"
                            placeholder="Protein"
                            class="w-full focus:border-purple-600 focus:ring-purple-600 border-gray-300 rounded"
                        />
                    </div>
                    <div class="mb-3">
                        <x-input
                            type="text"
                            name="carbohydrates"
                            placeholder="Carbohydrates"
                            class="w-full focus:border-purple-600 focus:ring-purple-600 border-gray-300 rounded"
                        />
                    </div>
                    <div class="mb-3">
                        <x-input
                            type="text"
                            name="fats"
                            placeholder="Fats"
                            class="w-full focus:border-purple-600 focus:ring-purple-600 border-gray-300 rounded"
                        />
                    </div>
                    <div class="mb-3">
                        <x-input
                            type="text"
                            name="cook_time"
                            placeholder="Cook Time"
                            class="w-full focus:border-purple-600 focus:ring-purple-600 border-gray-300 rounded"
                        />
                    </div>
                    <div id="ingredients-wrapper">
                        <div class="mb-3 flex gap-2">
                            <x-input type="text" name="ingredients[0][name]" placeholder="Ingredient" class="w-full" />
                            <x-input type="text" name="ingredients[0][measurement]" placeholder="Measurement" class="w-full" />
                        </div>
                    </div>
                    <button type="button" onclick="addIngredientField()" class="text-sm text-purple-600">+ Add Ingredient</button>

                    <script>
                        let ingredientIndex = 1;
                        function addIngredientField() {
                            const wrapper = document.getElementById('ingredients-wrapper');
                            const newField = document.createElement('div');
                            newField.className = 'mb-3 flex gap-2';
                            newField.innerHTML = `
        <input type="text" name="ingredients[${ingredientIndex}][name]" placeholder="Ingredient" class="w-full border border-gray-300 rounded p-2" />
        <input type="text" name="ingredients[${ingredientIndex}][measurement]" placeholder="Measurement" class="w-full border border-gray-300 rounded p-2" />
    `;
                            wrapper.appendChild(newField);
                            ingredientIndex++;
                        }

                    </script>

                    <x-button class="w-full">Create</x-button>
                </form>

                @if ($errors->any())
                    <div class="text-red-600">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

            </div>
        </div>
    </div>
</x-account-layout>
