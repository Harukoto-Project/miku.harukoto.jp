<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>ダッシュボード - Miku</title>

    @include('components.og-tags', [
        'title' => 'ダッシュボード - Miku',
        'description' => 'ようこそ、' . (auth()->user()->discord_display_name ?? auth()->user()->name) . 'さん！Discordボットサービスのダッシュボードです。',
        'image' => auth()->user()->avatar_url ?? asset('images/default-avatar.svg')
    ])

    
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body class="bg-gray-50 dark:bg-gray-900 transition-colors duration-150">
@include('components.header')

<main class="mx-auto max-w-7xl px-6 py-8 lg:px-8" x-data="{ loaded: false }" x-init="setTimeout(() => loaded = true, 100)">
    
    <div class="text-center mb-12">
        <h1 class="text-4xl font-bold tracking-tight text-gray-900 dark:text-gray-100 sm:text-5xl" 
            x-show="loaded" 
            x-transition:enter="transition ease-out duration-700"
            x-transition:enter-start="opacity-0 transform translate-y-8"
            x-transition:enter-end="opacity-100 transform translate-y-0">
            ダッシュボード
        </h1>
        <p class="mt-4 text-lg text-gray-600 dark:text-gray-400"
           x-show="loaded"
           x-transition:enter="transition ease-out duration-700 delay-200"
           x-transition:enter-start="opacity-0 transform translate-y-8"
           x-transition:enter-end="opacity-100 transform translate-y-0">
            ようこそ、{{ auth()->user()->discord_display_name ?? auth()->user()->name }}さん！
        </p>

        <div class="mt-6 w-full"
             x-show="loaded"
             x-transition:enter="transition ease-out duration-700 delay-400"
             x-transition:enter-start="opacity-0 transform translate-y-8 scale-95"
             x-transition:enter-end="opacity-100 transform translate-y-0 scale-100">
            <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700 hover:shadow-xl transition-all duration-300">
                <div class="p-8">
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        
                        <div class="lg:col-span-1">
                            <div class="text-center lg:text-left">
                                <div class="flex justify-center lg:justify-start">
                                    <img src="{{ auth()->user()->avatar_url }}" alt="Avatar" class="h-24 w-24 rounded-full border-4 border-gray-300 dark:border-gray-600 shadow-lg">
                                </div>
                                <h3 class="mt-4 text-xl font-semibold text-gray-900 dark:text-gray-100">
                                    {{ auth()->user()->discord_display_name ?? auth()->user()->name }}
                                </h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ auth()->user()->discord_username }}
                                </p>
                                <div class="mt-4">
                                    <a href="{{ route('account.settings') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-md transition-colors duration-200">
                                        <svg class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        アカウント設定
                                    </a>
                                </div>
                            </div>
                        </div>

                        
                        <div class="lg:col-span-2">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                
                                <div class="space-y-4">
                                    <h4 class="text-lg font-medium text-gray-900 dark:text-gray-100 border-b border-gray-200 dark:border-gray-700 pb-2">
                                        Harukoto Projectアカウント情報
                                    </h4>
                                    <div class="space-y-3">
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">ユーザーID</dt>
                                            <dd class="text-sm text-gray-900 dark:text-gray-100 font-mono">{{ auth()->user()->id }}</dd>
                                        </div>
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">作成日</dt>
                                            <dd class="text-sm text-gray-900 dark:text-gray-100">{{ auth()->user()->created_at->format('Y年m月d日') }}</dd>
                                        </div>
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">最終更新</dt>
                                            <dd class="text-sm text-gray-900 dark:text-gray-100">{{ auth()->user()->updated_at->format('Y年m月d日') }}</dd>
                                        </div>
                                    </div>
                                </div>

                                
                                <div class="space-y-4">
                                    <h4 class="text-lg font-medium text-gray-900 dark:text-gray-100 border-b border-gray-200 dark:border-gray-700 pb-2">
                                        Discord情報
                                    </h4>
                                    <div class="space-y-3">
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Discord ID</dt>
                                            <dd class="text-sm text-gray-900 dark:text-gray-100 font-mono">{{ auth()->user()->discord_id ?? '未連携' }}</dd>
                                        </div>
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">表示名</dt>
                                            <dd class="text-sm text-gray-900 dark:text-gray-100">{{ auth()->user()->discord_display_name ?? '未設定' }}</dd>
                                        </div>
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">ユーザー名</dt>
                                            <dd class="text-sm text-gray-900 dark:text-gray-100">{{ auth()->user()->discord_username ?? '未設定' }}</dd>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
    <div class="mb-12">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-8 text-center"
            x-show="loaded"
            x-transition:enter="transition ease-out duration-700 delay-600"
            x-transition:enter-start="opacity-0 transform translate-y-8"
            x-transition:enter-end="opacity-100 transform translate-y-0">
            管理可能なサーバー
        </h2>

        @if(empty($guilds))
            <div class="text-center py-12"
                 x-show="loaded"
                 x-transition:enter="transition ease-out duration-700 delay-800"
                 x-transition:enter-start="opacity-0 transform translate-y-8"
                 x-transition:enter-end="opacity-100 transform translate-y-0">
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-8 border border-gray-200 dark:border-gray-700">
                    <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                    <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-gray-100">サーバーが見つかりませんでした</h3>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                        管理者権限を持つDiscordサーバーがないか、権限の再取得が必要です。
                    </p>
                    <div class="mt-6">
                        <a href="{{ route('discord.login') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 transition-colors duration-200">
                            権限を再取得する
                        </a>
                    </div>
                </div>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($guilds as $index => $guild)
                    <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700 hover:shadow-xl transition-all duration-300 hover:scale-105"
                         x-show="loaded"
                         x-transition:enter="transition ease-out duration-700"
                         x-transition:enter-start="opacity-0 transform translate-y-8 scale-95"
                         x-transition:enter-end="opacity-100 transform translate-y-0 scale-100"
                         style="transition-delay: {{ 800 + ($index * 100) }}ms;">
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
                                    
                                    <div class="flex items-center mb-4">
                                        <svg class="h-5 w-5 text-green-400 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                        <span class="text-sm text-green-600 dark:text-green-400 font-medium">Miku導入済み</span>
                                    </div>
                                    <a href="{{ route('server.settings', $guild['id']) }}" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-md transition-colors duration-200 inline-block text-center">
                                        サーバー設定
                                    </a>
                                @else
                                    
                                    <div class="flex items-center mb-4">
                                        <svg class="h-5 w-5 text-orange-400 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
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

    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-12">
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 border border-gray-200 dark:border-gray-700 hover:shadow-lg transition-shadow duration-300"
             x-show="loaded"
             x-transition:enter="transition ease-out duration-700 delay-1000"
             x-transition:enter-start="opacity-0 transform translate-y-8"
             x-transition:enter-end="opacity-100 transform translate-y-0">
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

        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 border border-gray-200 dark:border-gray-700 hover:shadow-lg transition-shadow duration-300"
             x-show="loaded"
             x-transition:enter="transition ease-out duration-700 delay-1100"
             x-transition:enter-start="opacity-0 transform translate-y-8"
             x-transition:enter-end="opacity-100 transform translate-y-0">
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

        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 border border-gray-200 dark:border-gray-700 hover:shadow-lg transition-shadow duration-300"
             x-show="loaded"
             x-transition:enter="transition ease-out duration-700 delay-1200"
             x-transition:enter-start="opacity-0 transform translate-y-8"
             x-transition:enter-end="opacity-100 transform translate-y-0">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-8 w-8 text-orange-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
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
