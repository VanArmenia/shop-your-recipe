<x-account-layout>
    <div x-data="{ currentView: 'profile' }" class="flex">
        <!-- Sidebar Menu -->
        <aside class="min-w-[200px] w-[200px] transition-all bg-orange-50 text-gray-800 py-4 px-2">
            <div class="inline-block">
                @if($customer->avatar)
                    <img src="{{ asset('storage/' . $customer->avatar) }}" alt="User Avatar" class="w-14 h-14 rounded-full inline">
                @else
                    <img src="{{ asset('images/default-avatar.png') }}" alt="Default Avatar" class="w-14 h-14 rounded-full inline">
                @endif
                    <p class="inline px-2">Hi, {{ $customer->first_name }}</p>
            </div>


            <a class="flex items-center p-2 rounded transition-colors hover:bg-black/30 mt-4">

                <button @click="currentView = 'profile'" class="text-lg"> Profile Details </button>

            </a>
            <a class="flex items-center p-2 rounded transition-colors hover:bg-black/30">

                <button @click="currentView = 'recipes'" class="text-lg"> My Recipes </button>

            </a>
        </aside>

        <div x-show="currentView === 'recipes'"
             class="container mx-auto lg:w-2/3 p-5"
        >
            <div class="flex justify-between">
                <h2 class="text-gray-800 text-xl font-bold"> My Recipes </h2>
                <button class="ml-2 p-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 px-3" aria-label="Click to search"
                        @click="currentView = 'recipe_form'"
                >
                    <i class="fa-solid fa-stroopwafel"></i>
                    Add Recipe
                </button>
            </div>

            <div
                class="grid gap-8 grig-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 p-5"
            >
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

        <div x-data="{
            flashMessage: '{{\Illuminate\Support\Facades\Session::get('flash_message')}}',
            init() {
                if (this.flashMessage) {
                    setTimeout(() => this.$dispatch('notify', {message: this.flashMessage}), 200)
                }
            }
        }"
             class="container mx-auto lg:w-2/3 p-5"
             x-show="currentView === 'profile'"
        >


            @if (session('error'))
                <div class="py-2 px-3 bg-red-500 text-white mb-2 rounded">
                    {{ session('error') }}
                </div>
            @endif
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-start">
                <div class="bg-white p-3 shadow rounded-lg md:col-span-2">

                    <form x-data="{
                    countries: {{ json_encode($countries) }},
                    billingAddress: {{ json_encode([
                        'address1' => old('billing.address1', $billingAddress->address1),
                        'address2' => old('billing.address2', $billingAddress->address2),
                        'city' => old('billing.city', $billingAddress->city),
                        'state' => old('billing.state', $billingAddress->state),
                        'country_code' => old('billing.country_code', $billingAddress->country_code),
                        'zipcode' => old('billing.zipcode', $billingAddress->zipcode),
                    ]) }},
                    shippingAddress: {{ json_encode([
                        'address1' => old('shipping.address1', $shippingAddress->address1),
                        'address2' => old('shipping.address2', $shippingAddress->address2),
                        'city' => old('shipping.city', $shippingAddress->city),
                        'state' => old('shipping.state', $shippingAddress->state),
                        'country_code' => old('shipping.country_code', $shippingAddress->country_code),
                        'zipcode' => old('shipping.zipcode', $shippingAddress->zipcode),
                    ]) }},
                    get billingCountryStates() {
                        const country = this.countries.find(c => c.code === this.billingAddress.country_code)
                        if (country && country.states) {
                            return JSON.parse(country.states);
                        }
                        return null;
                    },
                    get shippingCountryStates() {
                        const country = this.countries.find(c => c.code === this.shippingAddress.country_code)
                        if (country && country.states) {
                            return JSON.parse(country.states);
                        }
                        return null;
                    }
                }" action="{{ route('profile.update') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <h2 class="text-xl font-semibold mb-2">Profile Details</h2>
                        <div class="grid grid-cols-2 gap-3 mb-3">
                            <!-- Avatar input -->
                            <x-input
                                type="file"
                                name="avatar"
                                label="Upload Avatar"
                                class="w-full mt-2"
                            />

                            <x-input
                                type="text"
                                name="first_name"
                                value="{{old('first_name', $customer->first_name)}}"
                                placeholder="First Name"
                                class="w-full focus:border-purple-600 focus:ring-purple-600 border-gray-300 rounded"
                            />
                            <x-input
                                type="text"
                                name="last_name"
                                value="{{old('last_name', $customer->last_name)}}"
                                placeholder="Last Name"
                                class="w-full focus:border-purple-600 focus:ring-purple-600 border-gray-300 rounded"
                            />
                        </div>
                        <div class="mb-3">
                            <x-input
                                type="text"
                                name="email"
                                value="{{old('email', $user->email)}}"
                                placeholder="Your Email"
                                class="w-full focus:border-purple-600 focus:ring-purple-600 border-gray-300 rounded"
                            />
                        </div>
                        <div class="mb-3">
                            <x-input
                                type="text"
                                name="phone"
                                value="{{old('phone', $customer->phone)}}"
                                placeholder="Your Phone"
                                class="w-full focus:border-purple-600 focus:ring-purple-600 border-gray-300 rounded"
                            />
                        </div>

                        <h2 class="text-xl mt-6 font-semibold mb-2">Billing Address</h2>
                        <div class="grid grid-cols-2 gap-3 mb-3">
                            <div>
                                <x-input
                                    type="text"
                                    name="billing[address1]"
                                    x-model="billingAddress.address1"
                                    placeholder="Address 1"
                                    class="w-full focus:border-purple-600 focus:ring-purple-600 border-gray-300 rounded"
                                />
                            </div>
                            <div>
                                <x-input
                                    type="text"
                                    name="billing[address2]"
                                    x-model="billingAddress.address2"
                                    placeholder="Address 2"
                                    class="w-full focus:border-purple-600 focus:ring-purple-600 border-gray-300 rounded"
                                />
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-3 mb-3">
                            <div>
                                <x-input
                                    type="text"
                                    name="billing[city]"
                                    x-model="billingAddress.city"
                                    placeholder="City"
                                    class="w-full focus:border-purple-600 focus:ring-purple-600 border-gray-300 rounded"
                                />
                            </div>
                            <div>
                                <x-input
                                    type="text"
                                    name="billing[zipcode]"
                                    x-model="billingAddress.zipcode"
                                    placeholder="ZipCode"
                                    class="w-full focus:border-purple-600 focus:ring-purple-600 border-gray-300 rounded"
                                />
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-3 mb-3">
                            <div>
                                <x-input type="select"
                                         name="billing[country_code]"
                                         x-model="billingAddress.country_code"
                                         class="w-full focus:border-purple-600 focus:ring-purple-600 border-gray-300 rounded">
                                    <option value="">Select Country</option>
                                    <template x-for="country of countries" :key="country.code">
                                        <option :selected="country.code === billingAddress.country_code"
                                                :value="country.code" x-text="country.name"></option>
                                    </template>
                                </x-input>
                            </div>
                            <div>
                                <template x-if="billingCountryStates">
                                    <x-input type="select"
                                             name="billing[state]"
                                             x-model="billingAddress.state"
                                             class="w-full focus:border-purple-600 focus:ring-purple-600 border-gray-300 rounded">
                                        <option value="">Select State</option>
                                        <template x-for="[code, state] of Object.entries(billingCountryStates)"
                                                  :key="code">
                                            <option :selected="code === billingAddress.state"
                                                    :value="code" x-text="state"></option>
                                        </template>
                                    </x-input>
                                </template>
                                <template x-if="!billingCountryStates">
                                    <x-input
                                        type="text"
                                        name="billing[state]"
                                        x-model="billingAddress.state"
                                        placeholder="State"
                                        class="w-full focus:border-purple-600 focus:ring-purple-600 border-gray-300 rounded"
                                    />
                                </template>
                            </div>
                        </div>

                        <div class="flex justify-between mt-6 mb-2">
                            <h2 class="text-xl font-semibold">Shipping Address</h2>
                            <label for="sameAsBillingAddress" class="text-gray-700">
                                <input @change="$event.target.checked ? shippingAddress = {...billingAddress} : ''"
                                       id="sameAsBillingAddress" type="checkbox"
                                       class="text-purple-600 focus:ring-purple-600 mr-2"> Same as Billing
                            </label>
                        </div>
                        <div class="grid grid-cols-2 gap-3 mb-3">
                            <div>
                                <x-input
                                    type="text"
                                    name="shipping[address1]"
                                    x-model="shippingAddress.address1"
                                    placeholder="Address 1"
                                    class="w-full focus:border-purple-600 focus:ring-purple-600 border-gray-300 rounded"
                                />
                            </div>
                            <div>
                                <x-input
                                    type="text"
                                    name="shipping[address2]"
                                    x-model="shippingAddress.address2"
                                    placeholder="Address 2"
                                    class="w-full focus:border-purple-600 focus:ring-purple-600 border-gray-300 rounded"
                                />
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-3 mb-3">
                            <div>
                                <x-input
                                    type="text"
                                    name="shipping[city]"
                                    x-model="shippingAddress.city"
                                    placeholder="City"
                                    class="w-full focus:border-purple-600 focus:ring-purple-600 border-gray-300 rounded"
                                />
                            </div>
                            <div>
                                <x-input
                                    name="shipping[zipcode]"
                                    x-model="shippingAddress.zipcode"
                                    type="text"
                                    placeholder="ZipCode"
                                    class="w-full focus:border-purple-600 focus:ring-purple-600 border-gray-300 rounded"
                                />
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-3 mb-3">
                            <div>
                                <x-input type="select"
                                         name="shipping[country_code]"
                                         x-model="shippingAddress.country_code"
                                         class="w-full focus:border-purple-600 focus:ring-purple-600 border-gray-300 rounded">
                                    <option value="">Select Country</option>
                                    <template x-for="country of countries" :key="country.code">
                                        <option :selected="country.code === shippingAddress.country_code"
                                                :value="country.code" x-text="country.name"></option>
                                    </template>
                                </x-input>
                            </div>
                            <div>
                                <template x-if="shippingCountryStates">
                                    <x-input type="select"
                                             name="shipping[state]"
                                             x-model="shippingAddress.state"
                                             class="w-full focus:border-purple-600 focus:ring-purple-600 border-gray-300 rounded">
                                        <option value="">Select State</option>
                                        <template x-for="[code, state] of Object.entries(shippingCountryStates)"
                                                  :key="code">
                                            <option :selected="code === shippingAddress.state"
                                                    :value="code" x-text="state"></option>
                                        </template>
                                    </x-input>
                                </template>
                                <template x-if="!shippingCountryStates">
                                    <x-input
                                        type="text"
                                        name="shipping[state]"
                                        x-model="shippingAddress.state"
                                        placeholder="State"
                                        class="w-full focus:border-purple-600 focus:ring-purple-600 border-gray-300 rounded"
                                    />
                                </template>
                            </div>
                        </div>

                        <x-button class="w-full">Update</x-button>
                    </form>
                </div>
                <div class="bg-white p-3 shadow rounded-lg">
                    <form action="{{route('profile_password.update')}}" method="post">
                        @csrf
                        <h2 class="text-xl font-semibold mb-2">Update Password</h2>
                        <div class="mb-3">
                            <x-input
                                type="password"
                                name="old_password"
                                placeholder="Your Current Password"
                                class="w-full focus:border-purple-600 focus:ring-purple-600 border-gray-300 rounded"
                            />
                        </div>
                        <div class="mb-3">
                            <x-input
                                type="password"
                                name="new_password"
                                placeholder="New Password"
                                class="w-full focus:border-purple-600 focus:ring-purple-600 border-gray-300 rounded"
                            />
                        </div>
                        <div class="mb-3">
                            <x-input
                                type="password"
                                name="new_password_confirmation"
                                placeholder="Repeat New Password"
                                class="w-full focus:border-purple-600 focus:ring-purple-600 border-gray-300 rounded"
                            />
                        </div>
                        <x-button>Update</x-button>
                    </form>
                </div>
            </div>
        </div>
        {{--Recipe Form--}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-start m-4"
             x-show="currentView === 'recipe_form'"
        >
            <div class="bg-white p-3 shadow rounded-lg md:col-span-2">
                <form
                    action="{{ route('profile.update') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <h2 class="text-xl font-semibold mb-2">Recipe Form</h2>
                    <div class="grid grid-cols-2 gap-3 mb-3">
                        <!-- Avatar input -->
                        <x-input
                            type="file"
                            name="avatar"
                            label="Upload Avatar"
                            class="w-full mt-2"
                        />

                        <x-input
                            type="text"
                            name="first_name"
                            value="{{old('first_name', $customer->first_name)}}"
                            placeholder="First Name"
                            class="w-full focus:border-purple-600 focus:ring-purple-600 border-gray-300 rounded"
                        />
                        <x-input
                            type="text"
                            name="last_name"
                            value="{{old('last_name', $customer->last_name)}}"
                            placeholder="Last Name"
                            class="w-full focus:border-purple-600 focus:ring-purple-600 border-gray-300 rounded"
                        />
                    </div>
                    <div class="mb-3">
                        <x-input
                            type="text"
                            name="email"
                            value="{{old('email', $user->email)}}"
                            placeholder="Your Email"
                            class="w-full focus:border-purple-600 focus:ring-purple-600 border-gray-300 rounded"
                        />
                    </div>
                    <div class="mb-3">
                        <x-input
                            type="text"
                            name="phone"
                            value="{{old('phone', $customer->phone)}}"
                            placeholder="Your Phone"
                            class="w-full focus:border-purple-600 focus:ring-purple-600 border-gray-300 rounded"
                        />
                    </div>

                    <h2 class="text-xl mt-6 font-semibold mb-2">Billing Address</h2>
                    <div class="grid grid-cols-2 gap-3 mb-3">
                        <div>
                            <x-input
                                type="text"
                                name="billing[city]"
                                x-model="billingAddress.city"
                                placeholder="City"
                                class="w-full focus:border-purple-600 focus:ring-purple-600 border-gray-300 rounded"
                            />
                        </div>
                        <div>
                            <x-input
                                type="text"
                                name="billing[zipcode]"
                                x-model="billingAddress.zipcode"
                                placeholder="ZipCode"
                                class="w-full focus:border-purple-600 focus:ring-purple-600 border-gray-300 rounded"
                            />
                        </div>
                    </div>

                    <div class="flex justify-between mt-6 mb-2">
                        <h2 class="text-xl font-semibold">Shipping Address</h2>
                        <label for="sameAsBillingAddress" class="text-gray-700">
                            <input @change="$event.target.checked ? shippingAddress = {...billingAddress} : ''"
                                   id="sameAsBillingAddress" type="checkbox"
                                   class="text-purple-600 focus:ring-purple-600 mr-2"> Same as Billing
                        </label>
                    </div>
                    <div class="grid grid-cols-2 gap-3 mb-3">
                        <div>
                            <x-input type="select"
                                     name="shipping[country_code]"
                                     x-model="shippingAddress.country_code"
                                     class="w-full focus:border-purple-600 focus:ring-purple-600 border-gray-300 rounded">
                                <option value="">Select Country</option>
                                <template x-for="country of countries" :key="country.code">
                                    <option :selected="country.code === shippingAddress.country_code"
                                            :value="country.code" x-text="country.name"></option>
                                </template>
                            </x-input>
                        </div>
                        <div>
                            <template x-if="shippingCountryStates">
                                <x-input type="select"
                                         name="shipping[state]"
                                         x-model="shippingAddress.state"
                                         class="w-full focus:border-purple-600 focus:ring-purple-600 border-gray-300 rounded">
                                    <option value="">Select State</option>
                                    <template x-for="[code, state] of Object.entries(shippingCountryStates)"
                                              :key="code">
                                        <option :selected="code === shippingAddress.state"
                                                :value="code" x-text="state"></option>
                                    </template>
                                </x-input>
                            </template>
                            <template x-if="!shippingCountryStates">
                                <x-input
                                    type="text"
                                    name="shipping[state]"
                                    x-model="shippingAddress.state"
                                    placeholder="State"
                                    class="w-full focus:border-purple-600 focus:ring-purple-600 border-gray-300 rounded"
                                />
                            </template>
                        </div>
                    </div>

                    <x-button class="w-full">Update</x-button>
                </form>
            </div>
            <div class="bg-white p-3 shadow rounded-lg">
                <form action="{{route('profile_password.update')}}" method="post">
                    @csrf
                    <h2 class="text-xl font-semibold mb-2">Update Password</h2>
                    <div class="mb-3">
                        <x-input
                            type="password"
                            name="old_password"
                            placeholder="Your Current Password"
                            class="w-full focus:border-purple-600 focus:ring-purple-600 border-gray-300 rounded"
                        />
                    </div>
                    <div class="mb-3">
                        <x-input
                            type="password"
                            name="new_password"
                            placeholder="New Password"
                            class="w-full focus:border-purple-600 focus:ring-purple-600 border-gray-300 rounded"
                        />
                    </div>
                    <div class="mb-3">
                        <x-input
                            type="password"
                            name="new_password_confirmation"
                            placeholder="Repeat New Password"
                            class="w-full focus:border-purple-600 focus:ring-purple-600 border-gray-300 rounded"
                        />
                    </div>
                    <x-button>Update</x-button>
                </form>
            </div>
        </div>
    </div>

</x-account-layout>
