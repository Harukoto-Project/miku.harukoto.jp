<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>ダッシュボード - Laravel</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Styles / Scripts -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body class="bg-white dark:bg-gray-900 transition-colors duration-300">
@include('components.header')

<main class="mx-auto max-w-7xl px-6 py-12 lg:px-8">
    <div class="text-center">
        <h1 class="text-4xl font-bold tracking-tight text-gray-900 dark:text-gray-100 sm:text-6xl">
            ダッシュボード
        </h1>
        <p class="mt-6 text-lg leading-8 text-gray-600 dark:text-gray-400">
            ようこそ、{{ auth()->user()->discord_username }}さん！
        </p>

        <div class="mt-8 flex justify-center">
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 border border-gray-200 dark:border-gray-700">
                <div class="flex items-center space-x-4">
                    <img src="{{ auth()->user()->avatar_url }}" alt="Avatar" class="h-16 w-16 rounded-full border border-gray-300 dark:border-gray-600">
                    <div class="text-left">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ auth()->user()->name }}</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Discord ID: {{ auth()->user()->discord_id }}</p>
                        @if(auth()->user()->email)
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ auth()->user()->email }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
</body>
</html>
