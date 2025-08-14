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
<body class="bg-gray-50 dark:bg-gray-900 transition-colors duration-300">
@include('components.header')

<main class="mx-auto max-w-7xl px-6 py-8 lg:px-8">
    <!-- ユーザー情報セクション -->
    <div class="text-center mb-12">
        <h1 class="text-4xl font-bold tracking-tight text-gray-900 dark:text-gray-100 sm:text-5xl">
            ダッシュボード
        </h1>
        <p class="mt-4 text-lg text-gray-600 dark:text-gray-400">
            ようこそ、{{ auth()->user()->discord_username }}さん！
        </p>

        <div class="mt-6 flex justify-center">
            <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg p-6 border border-gray-200 dark:border-gray-700">
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

    <!-- サーバー選択セクション -->
    <div class="mb-12">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-8 text-center">
            管理可能なサーバー
        </h2>

        @if(empty($guilds))
            <div class="text-center py-12">
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-8 border border-gray-200 dark:border-gray-700">
                    <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                    <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-gray-100">サーバーが見つかりませんでした</h3>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                        管理者権限を持つDiscordサーバーがないか、権限の再取得が必要です。
                    </p>
                    <div class="mt-6">
                        <a href="{{ route('discord.login') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700">
                            権限を再取得する
                        </a>
                    </div>
                </div>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($guilds as $guild)
                    <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700 hover:shadow-xl transition-shadow duration-300">
                        <div class="p-6">
                            <div class="flex items-center space-x-4">
                                <div class="flex-shrink-0">
                                    <img src="{{ $guild['icon_url'] }}" alt="{{ $guild['name'] }} Icon" class="h-16 w-16 rounded-full border border-gray-300 dark:border-gray-600">
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 truncate">
                                        {{ $guild['name'] }}
                                    </h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        ID: {{ $guild['id'] }}
                                    </p>
                                </div>
                            </div>

                            <div class="mt-6">
                                @if($guild['has_miku'])
                                    <!-- Mikuが導入済みの場合 -->
                                    <div class="flex items-center mb-4">
                                        <svg class="h-5 w-5 text-green-400 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                        <span class="text-sm text-green-600 dark:text-green-400 font-medium">Miku導入済み</span>
                                    </div>
                                    <button onclick="location.href='#'" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-md transition-colors duration-200">
                                        サーバー設定
                                    </button>
                                @else
                                    <!-- Mikuが未導入の場合 -->
                                    <div class="flex items-center mb-4">
                                        <svg class="h-5 w-5 text-orange-400 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.268 18.5c-.77.833.192 2.5 1.732 2.5z" />
                                        </svg>
                                        <span class="text-sm text-orange-600 dark:text-orange-400 font-medium">Miku未導入</span>
                                    </div>
                                    <form action="{{ route('dashboard.invite-bot') }}" method="POST" class="w-full">
                                        @csrf
                                        <input type="hidden" name="guild_id" value="{{ $guild['id'] }}">
                                        <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-md transition-colors duration-200">
                                            Mikuを招待する
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- 統計情報セクション -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-12">
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-8 w-8 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                </div>
                <div class="ml-4">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">管理可能サーバー</dt>
                        <dd class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ count($guilds) }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-8 w-8 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <div class="ml-4">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Miku導入済み</dt>
                        <dd class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ count(array_filter($guilds, function($guild) { return $guild['has_miku']; })) }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-8 w-8 text-orange-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.268 18.5c-.77.833.192 2.5 1.732 2.5z" />
                    </svg>
                </div>
                <div class="ml-4">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">導入待ち</dt>
                        <dd class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ count(array_filter($guilds, function($guild) { return !$guild['has_miku']; })) }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</main>
</body>
</html>
