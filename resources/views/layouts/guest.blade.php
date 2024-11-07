<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!--追加のスタイルシート-->
    <link rel="stylesheet" href="{{ asset('css/forum.css') }}">
</head>

<body>
    <div class="font-sans text-gray-900 antialiased">
        <div class="w-full container mx-auto p-6">
            <div class="w-full flex items-center justify-between">
                <a href="{{ route('top') }}">
                    <img src="{{ asset('logo/top_cat.png') }}" alt="" style="max-height:40px;">
                </a>
                <div class="flex w-1/2 justify-end content-center">
                    {{-- ログイン・登録部分 --}}
                    @if (Route::has('login'))
                        <div class="fixed top-0 right-0 px-6 py-4 sm:block">
                            @auth
                                <a href="{{ url('/post') }}"
                                    class="text-sm text-gray-700 dark:text-gray-500 underline">HOME</a>
                            @else
                                <a href="{{ route('login') }}"
                                    class="text-sm text-gray-700 dark:text-gray-500 underline font-bold text-base">ログイン</a>

                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}"
                                        class="ml-4 text-sm text-gray-700 dark:text-gray-500 underline font-bold text-base">登録</a>
                                @endif
                            @endauth
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="w-full container mx-auto">
        {{ $slot }}
    </div>
    </div>
</body>

</html>
