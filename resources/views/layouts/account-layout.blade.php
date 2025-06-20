<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/14.6.3/nouislider.min.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/14.6.3/nouislider.min.js"></script>

    <title>{{ config('app.name', 'Laravel E-commerce Website') }}</title>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/css/splide.min.css', 'resources/js/app.js', 'resources/js/slide.js'])
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
</head>
<body class="flex flex-col justify-between h-screen">
@include('layouts.navigation')

<main class="relative flex"
      x-data="{sidebarOpened: true}"
>
    <!-- Sidebar Menu -->
    <aside
        x-data="menuHandler()" x-init="loadMenu()"
        :class="{'-ml-[200px]': !sidebarOpened}"
        class="min-w-[200px] w-[200px] transition-all bg-orange-50 text-gray-800 py-4 px-2 h-screen">
        <div class="inline-block">
            @if($customer->avatar)
                <img src="{{ asset('storage/' . $customer->avatar) }}" alt="User Avatar" class="w-14 h-14 rounded-full inline">
            @else
                <img src="{{ asset('images/default-avatar.png') }}" alt="Default Avatar" class="w-14 h-14 rounded-full inline">
            @endif
            <p class="inline px-2">Hi, {{ $customer->first_name }}</p>
        </div>


        <a href="{{ route('profile') }}" class="flex items-center p-2 rounded transition-colors hover:bg-black/10 mt-4 text-lg font-bold"
           @click="setActiveMenu(1)"
        >
            <h5 :class="activeMenu === 1 ? 'text-red-500' : 'text-gray-500'">
                Profile Details
            </h5>
        </a>
        <a href="{{ route('profile.recipes') }}" class="flex items-center p-2 rounded transition-colors hover:bg-black/10 text-lg font-bold"
           @click="setActiveMenu(2)"
        >
            <h5 :class="activeMenu === 2 ? 'text-red-500' : 'text-gray-500'">
                My Recipes
            </h5>
        </a>
    </aside>
    <button
        @click="sidebarOpened = !sidebarOpened"
        class="p-4 pt-0 block md:hidden absolute top-0 left-48"
        :class="{'-ml-[200px]': !sidebarOpened}"
    >
        <i class="fa-solid fa-left-right text-xl text-red-500"></i>
    </button>

    {{ $slot }}
</main>

<!-- Toast -->
<div
    x-data="toast"
    x-show="visible"
    x-transition
    x-cloak
    @notify.window="show($event.detail.message, $event.detail.type || 'success')"
    @review-submitted.window="show($event.detail.message, $event.detail.type || 'success')"
    class="fixed w-[400px] left-1/2 -ml-[200px] top-16 py-2 px-4 pb-4 text-white"
    :class="type === 'success' ? 'bg-emerald-500' : 'bg-red-500'"
>
    <div class="font-semibold" x-text="message"></div>
    <button
        @click="close"
        class="absolute flex items-center justify-center right-2 top-2 w-[30px] h-[30px] rounded-full hover:bg-black/10 transition-colors"
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
                d="M6 18L18 6M6 6l12 12"
            />
        </svg>
    </button>
    <!-- Progress -->
    <div>
        <div
            class="absolute left-0 bottom-0 right-0 h-[6px] bg-black/10"
            :style="{'width': `${percent}%`}"
        ></div>
    </div>
</div>
<!--/ Toast -->
@include('layouts.footer')
<script>
    function menuHandler() {
        return {
            activeMenu: 1,
            loadMenu() {
                const saved = localStorage.getItem('activeMenu');
                if (saved) this.activeMenu = parseInt(saved);
            },
            setActiveMenu(value) {
                this.activeMenu = value;
                localStorage.setItem('activeMenu', value);
            }
        };
    }
</script>
</body>
</html>
