<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $guild['name'] }} - サーバー設定 - Miku</title>

    @include('components.og-tags', [
        'title' => $guild['name'] . ' - サーバー設定 - Miku',
        'description' => $guild['name'] . 'のサーバー設定ページです。Welcomer、一般設定、モデレーションの設定が可能です。',
        'image' => $guild['icon_url'] ?? asset('images/default-avatar.svg')
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
<body class="bg-gray-50 dark:bg-gray-900 transition-colors duration-300">
@include('components.header')

<main class="mx-auto max-w-7xl px-6 py-8 lg:px-8">
    <!-- ヘッダーセクション -->
    <div class="mb-8">
        <div class="flex items-center space-x-4">
            <a href="{{ route('dashboard') }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div class="flex items-center space-x-4">
                <img src="{{ $guild['icon_url'] }}" alt="{{ $guild['name'] }} Icon" class="h-12 w-12 rounded-full border border-gray-300 dark:border-gray-600">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">{{ $guild['name'] }}</h1>
                    <p class="text-gray-600 dark:text-gray-400">サーバー設定</p>
                </div>
            </div>
        </div>
    </div>

    <div x-data="{ activeTab: 'welcomer' }">
        <!-- タブナビゲーション（青色ボーダーのモダンなタブ） -->
        <div class="mb-8">
            <div class="px-2 pb-0">
                <nav class="flex space-x-8" aria-label="Tabs">
                    <button
                        @click="activeTab = 'welcomer'"
                        :class="activeTab === 'welcomer' 
                            ? 'text-blue-600 dark:text-blue-400 border-b-2 border-blue-600 dark:border-blue-400' 
                            : 'text-gray-700 dark:text-gray-300 border-b-2 border-transparent hover:text-blue-600 dark:hover:text-blue-400 hover:border-blue-300'"
                        class="py-3 px-1 font-medium text-sm transition-colors duration-200 focus:outline-none">
                        Welcomer
                    </button>
                    <button
                        @click="activeTab = 'general'"
                        :class="activeTab === 'general' 
                            ? 'text-blue-600 dark:text-blue-400 border-b-2 border-blue-600 dark:border-blue-400' 
                            : 'text-gray-700 dark:text-gray-300 border-b-2 border-transparent hover:text-blue-600 dark:hover:text-blue-400 hover:border-blue-300'"
                        class="py-3 px-1 font-medium text-sm transition-colors duration-200 focus:outline-none">
                        一般設定
                    </button>
                    <button
                        @click="activeTab = 'moderation'"
                        :class="activeTab === 'moderation' 
                            ? 'text-blue-600 dark:text-blue-400 border-b-2 border-blue-600 dark:border-blue-400' 
                            : 'text-gray-700 dark:text-gray-300 border-b-2 border-transparent hover:text-blue-600 dark:hover:text-blue-400 hover:border-blue-300'"
                        class="py-3 px-1 font-medium text-sm transition-colors duration-200 focus:outline-none">
                        モデレーション
                    </button>
                </nav>
            </div>
        </div>

        <!-- タブコンテンツ（ふわっと浮き出るアニメーション） -->
        <!-- Welcomerタブ -->
        <div x-show="activeTab === 'welcomer'" x-transition.opacity.scale>
            @include('components.server-settings.welcomer-tab')
        </div>

        <!-- 一般設定タブ -->
        <div x-show="activeTab === 'general'" x-transition.opacity.scale>
            @include('components.server-settings.general-tab')
        </div>

        <!-- モデレーションタブ -->
        <div x-show="activeTab === 'moderation'" x-transition.opacity.scale>
            @include('components.server-settings.moderation-tab')
        </div>
    </div>
</main>

<script>
    // サーバーIDをグローバル変数として設定
    window.GUILD_ID = '{{ $guild['id'] }}';
</script>
</body>
</html>
