<footer
    x-data="{
        mobileMenuOpen: false,
        cartItemsCount: {{ \App\Helpers\Cart::getCartItemsCount() }},
    }"
    @cart-change.window="cartItemsCount = $event.detail.count"
    class="flex justify-between md:justify-center bg-slate-800 shadow-md text-white"
>
    <div class="md:px-16">
        <a href="{{ route('home') }}" class="block pt-2 md:pt-4">
            <img
                src="{{ asset('storage/logo.png') }}"
                alt="Logo"
                width="50"
                class=""
            />
        </a>
    </div>
    <!-- Responsive Menu -->
    <div
        class="block fixed z-10 top-0 bottom-0 height h-full w-[220px] transition-all bg-slate-900 md:hidden"
        :class="mobileMenuOpen ? 'left-0' : '-left-[220px]'"
    >
        <ul>
            <li>
                <a
                    href="{{ route('about') }}"
                    class="relative flex items-center justify-between py-2 px-3 transition-colors hover:bg-slate-800"
                >
                    About
                </a>
                <a
                    href="{{ route('recipes.index') }}"
                    class="relative flex items-center justify-between py-2 px-3 transition-colors hover:bg-slate-800"
                >
                    Recipes
                </a>
                <a
                    href="{{ route('shop') }}"
                    class="relative flex items-center justify-between py-2 px-3 transition-colors hover:bg-slate-800"
                >
                    Shop
                </a>
            </li>

        </ul>
    </div>
    <!--/ Responsive Menu -->
    <nav class="hidden md:block px-16">
        <ul class="grid grid-flow-col items-center">
            <li>
                <a
                    href="{{ route('about') }}"
                    class="relative inline-flex items-center py-navbar-item px-navbar-item hover:bg-slate-900"
                >
                    About
                </a>
                <a
                    href="{{ route('recipes.index') }}"
                    class="relative inline-flex items-center py-navbar-item px-navbar-item hover:bg-slate-900"
                >
                    Recipes
                </a>
                <a
                    href="{{ route('shop') }}"
                    class="relative inline-flex items-center py-navbar-item px-navbar-item hover:bg-slate-900"
                >
                    Shop
                </a>
            </li>
        </ul>
    </nav>
    <button
        @click="mobileMenuOpen = !mobileMenuOpen"
        class="p-4 block md:hidden"
    >
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
</footer>
