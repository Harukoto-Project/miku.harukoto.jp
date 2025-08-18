<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>アカウント設定 - Miku</title>

    @include('components.og-tags', [
        'title' => 'アカウント設定 - Miku',
        'description' => ($user->discord_display_name ?? $user->name) . 'さんのアカウント設定ページです。',
        'image' => $user->avatar_url ?? asset('images/default-avatar.svg')
    ])

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
<body class="bg-gray-50 dark:bg-gray-900 transition-colors duration-150">
@include('components.header')

<main class="mx-auto max-w-7xl px-6 py-8 lg:px-8" x-data="{ activeTab: 'account', loaded: false }" x-init="setTimeout(() => loaded = true, 100)">
    <!-- ヘッダーセクション -->
    <div class="mb-8">
        <div class="flex items-center space-x-4">
            <a href="{{ route('dashboard') }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div class="flex items-center space-x-4">
                <img src="{{ $user->avatar_url }}" alt="{{ $user->discord_display_name ?? $user->name }} Avatar" class="h-12 w-12 rounded-full border border-gray-300 dark:border-gray-600">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">{{ $user->discord_display_name ?? $user->name }}</h1>
                    <p class="text-gray-600 dark:text-gray-400">アカウント設定</p>
                </div>
            </div>
        </div>
    </div>

    <!-- タブナビゲーション -->
    <div class="mb-8" x-show="loaded" x-transition:enter="transition ease-out duration-700 delay-400">
        <nav class="flex space-x-8 justify-center" aria-label="Tabs">
            <button @click="activeTab = 'account'" 
                    :class="activeTab === 'account' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300'"
                    class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm transition-colors duration-200">
                アカウント情報
            </button>
            <button @click="activeTab = 'services'" 
                    :class="activeTab === 'services' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300'"
                    class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm transition-colors duration-200">
                連携済みサービス
            </button>
            <button @click="activeTab = 'profile'" 
                    :class="activeTab === 'profile' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300'"
                    class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm transition-colors duration-200">
                プロフィール
            </button>
            <button @click="activeTab = 'module'" 
                    :class="activeTab === 'module' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300'"
                    class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm transition-colors duration-200">
                アチーブメント設定
            </button>
            <button @click="activeTab = 'form'" 
                    :class="activeTab === 'form' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300'"
                    class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm transition-colors duration-200">
                フォーム設定
            </button>
        </nav>
    </div>

    <!-- タブコンテンツ -->
    <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden"
         x-show="loaded"
         x-transition:enter="transition ease-out duration-700 delay-600"
         x-transition:enter-start="opacity-0 transform translate-y-8"
         x-transition:enter-end="opacity-100 transform translate-y-0">

                <!-- アカウント情報タブ -->
        <div x-show="activeTab === 'account'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
            <div class="px-6 py-8">
                @include('components.account-settings.account-tab')
            </div>
        </div>

        <!-- 連携済みサービスタブ -->
        <div x-show="activeTab === 'services'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
            <div class="px-6 py-8">
                @include('components.account-settings.services-tab')
            </div>
        </div>

        <!-- プロフィールタブ -->
        <div x-show="activeTab === 'profile'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
            <div class="px-6 py-8">
                @include('components.account-settings.profile-tab')
            </div>
        </div>

        <!-- モジュール設定タブ -->
        <div x-show="activeTab === 'module'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
            <div class="px-6 py-8">
                @include('components.account-settings.module-tab')
            </div>
        </div>

        <!-- フォーム設定タブ -->
        <div x-show="activeTab === 'form'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
            <div class="px-6 py-8">
                @include('components.account-settings.form-tab')
            </div>
        </div>
    </div>
</main>
</body>
</html>
