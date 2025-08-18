<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>コマンド一覧 - Miku Bot</title>

    @include('components.og-tags', [
        'title' => 'コマンド一覧 - Miku Bot',
        'description' => 'Miku Botの全スラッシュコマンド一覧と詳細情報',
        'image' => asset('images/default-avatar.svg')
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
<body class="bg-white dark:bg-gray-900 transition-colors duration-300">

@include('components.header', ['avatarUrl' => 'https://cdn.discordapp.com/avatars/949873067620966490/b178d5be3d02d1263c80002ad20363f1.png'])

<main class="mx-auto max-w-7xl px-6 py-12 lg:px-8" x-data="commandsPage()">
    <!-- ページヘッダー -->
    <div class="text-center mb-12">
        <h1 class="text-4xl font-bold tracking-tight text-gray-900 dark:text-gray-100 sm:text-5xl mb-4">
            コマンド一覧
        </h1>
        <p class="text-lg text-gray-600 dark:text-gray-400 max-w-3xl mx-auto">
            Miku Botの全スラッシュコマンド一覧です。各コマンドの詳細情報、使用方法、必要な権限を確認できます。
        </p>
    </div>

    <!-- ローディング表示 -->
    <div x-show="loading" class="flex items-center justify-center py-16">
        <div class="text-center">
            <svg class="animate-spin h-12 w-12 text-indigo-600 mx-auto mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <p class="text-gray-600 dark:text-gray-400">コマンド情報を読み込み中...</p>
        </div>
    </div>

    <!-- エラー表示 -->
    <div x-show="error && error.title && error.message" class="bg-red-50 dark:bg-red-900/50 border border-red-200 dark:border-red-700 rounded-md p-6 text-center">
        <div class="flex items-center justify-center mb-4">
            <svg class="h-8 w-8 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
            </svg>
        </div>
        <h3 class="text-lg font-medium text-red-800 dark:text-red-200 mb-2" x-text="error?.title || 'エラー'"></h3>
        <p class="text-red-700 dark:text-red-300" x-text="error?.message || '不明なエラーが発生しました'"></p>
        <button @click="loadCommands()" class="mt-4 bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors duration-200">
            再試行
        </button>
    </div>

    <!-- コマンド一覧 -->
    <div x-show="!loading && !error" class="space-y-8">
        <!-- 統計情報 -->
        <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-center">
                <div>
                    <div class="text-3xl font-bold text-indigo-600 dark:text-indigo-400" x-text="commands.length"></div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">総コマンド数</div>
                </div>
                <div>
                    <div class="text-3xl font-bold text-green-600 dark:text-green-400" x-text="categories.length"></div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">カテゴリ数</div>
                </div>
                <div>
                    <div class="text-3xl font-bold text-purple-600 dark:text-purple-400" x-text="totalOptions"></div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">総オプション数</div>
                </div>
            </div>
        </div>

        <!-- カテゴリ別タブ -->
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm">
            <div class="border-b border-gray-200 dark:border-gray-700">
                <nav class="flex flex-wrap -mb-px overflow-x-auto">
                    <template x-for="category in categories" :key="category.key">
                        <button @click="activeCategory = category.key" 
                                :class="activeCategory === category.key ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300'" 
                                class="py-3 px-6 border-b-2 font-medium text-sm whitespace-nowrap transition-colors duration-200">
                            <span x-text="category.displayName"></span>
                            <span class="ml-2 text-xs bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 px-2 py-1 rounded-full" 
                                x-text="getCommandsByCategory(category.key).length"></span>
                        </button>
                    </template>
                </nav>
            </div>

            <!-- コマンド一覧 -->
            <div class="p-6">
                <div x-show="getCommandsByCategory(activeCategory).length === 0" class="text-center py-12">
                    <p class="text-gray-500 dark:text-gray-400">このカテゴリにコマンドはありません</p>
                    <p class="text-xs text-gray-400 mt-2">カテゴリ: <span x-text="activeCategory"></span></p>
                    <p class="text-xs text-gray-400">総コマンド数: <span x-text="commands.length"></span></p>
                </div>

                <div x-show="getCommandsByCategory(activeCategory).length > 0" class="space-y-4">
                    <template x-for="command in getCommandsByCategory(activeCategory)" :key="command.id">
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600">
                            <!-- コマンドヘッダー -->
                            <button class="w-full px-4 py-3 text-left flex items-center justify-between hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors duration-200" 
                                    @click="toggleCommand(command.id)">
                                <div class="flex items-center space-x-3">
                                    <div class="text-lg text-indigo-600 dark:text-indigo-400">/</div>
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-900 dark:text-gray-100" x-text="command.name"></h4>
                                        <p class="text-xs text-gray-600 dark:text-gray-400" x-text="command.description"></p>
                                        <!-- サブコマンド情報表示 -->
                                        <div x-show="hasSubcommands(command)" class="mt-1">
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200">
                                                サブコマンド対応
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <!-- 権限表示 -->
                                    <div class="flex space-x-1">
                                        <template x-for="permission in command.defaultMemberPermissions" :key="permission">
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200" 
                                                x-text="getPermissionDisplayName(permission)"></span>
                                        </template>
                                    </div>
                                    <!-- DM許可表示 -->
                                    <span x-show="command.dmPermission" class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200">
                                        DM可
                                    </span>
                                    <!-- 展開アイコン -->
                                    <svg :class="expandedCommands.includes(command.id) ? 'rotate-180' : ''" 
                                        class="w-5 h-5 text-gray-500 dark:text-gray-400 transform transition-transform duration-200" 
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </div>
                            </button>

                            <!-- コマンド詳細 -->
                            <div x-show="expandedCommands.includes(command.id)" 
                                x-transition:enter="transition ease-out duration-300" 
                                x-transition:enter-start="opacity-0 transform -translate-y-2" 
                                x-transition:enter-end="opacity-100 transform translate-y-0" 
                                class="px-4 pb-4 border-t border-gray-200 dark:border-gray-600">
                                
                                <!-- 基本情報 -->
                                <div class="mt-4 space-y-3">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="text-xs font-medium text-gray-500 dark:text-gray-400">コマンドID</label>
                                            <p class="text-sm text-gray-900 dark:text-gray-100 font-mono" x-text="command.id"></p>
                                        </div>
                                        <div>
                                            <label class="text-xs font-medium text-gray-500 dark:text-gray-400">作成日時</label>
                                            <p class="text-sm text-gray-900 dark:text-gray-100" x-text="formatDate(command.createdAt)"></p>
                                        </div>
                                    </div>
                                </div>

                                                                <!-- サブコマンド構造の表示 -->
                                <div x-show="hasSubcommands(command)" class="mt-6">
                                    <h5 class="text-sm font-medium text-gray-900 dark:text-gray-100 mb-3">サブコマンド構造</h5>
                                    <div class="space-y-3">
                                        <template x-for="option in command.options" :key="option.name">
                                            <!-- サブコマンドグループ (type: 2) -->
                                            <div x-show="option.type === 2" class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-600 shadow-sm hover:shadow-md transition-all duration-200">
                                                <div class="w-full px-4 py-3 flex items-center justify-between">
                                                    <div class="flex items-center space-x-3">
                                                        <div class="text-lg text-blue-600 dark:text-blue-400">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                                            </svg>
                                                        </div>
                                                        <div>
                                                            <h4 class="text-sm font-medium text-gray-900 dark:text-gray-100" x-text="option.name"></h4>
                                                            <p class="text-xs text-gray-600 dark:text-gray-400" x-text="option.description"></p>
                                                            <span class="text-xs text-gray-500 dark:text-gray-400">(グループ)</span>
                                                        </div>
                                                    </div>
                                                    <div class="flex items-center space-x-2">
                                                        <span class="text-xs text-gray-500 dark:text-gray-400" x-text="option.typeName"></span>
                                                    </div>
                                                </div>
                                                
                                                <!-- サブコマンドグループ内のサブコマンド（常に表示） -->
                                                <div class="px-4 pb-4 border-t border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 rounded-b-lg">
                                                    <div class="mt-4 space-y-3">
                                                        <template x-for="subOption in option.options" :key="subOption.name">
                                                            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-600 shadow-sm hover:shadow-md transition-all duration-200">
                                                                <div class="w-full px-4 py-3 flex items-center justify-between">
                                                                    <div class="flex items-center space-x-3">
                                                                        <div class="text-lg text-green-600 dark:text-green-400">
                                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                                                            </svg>
                                                                        </div>
                                                                        <div>
                                                                            <h4 class="text-sm font-medium text-gray-900 dark:text-gray-100" x-text="subOption.name"></h4>
                                                                            <p class="text-xs text-gray-600 dark:text-gray-400" x-text="subOption.description"></p>
                                                                        </div>
                                                                    </div>
                                                                    <div class="flex items-center space-x-2">
                                                                        <span x-show="subOption.required" class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200">
                                                                            必須
                                                                        </span>
                                                                        <span class="text-xs text-gray-500 dark:text-gray-400" x-text="subOption.typeName"></span>
                                                                    </div>
                                                                </div>
                                                                
                                                                <!-- サブコマンドの引数（常に表示） -->
                                                                <div class="px-4 pb-4 border-t border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 rounded-b-lg">
                                                                    <div class="mt-4 space-y-4">
                                                                        <!-- サブコマンドの引数 -->
                                                                        <div x-show="subOption.options && subOption.options.length > 0">
                                                                            <h5 class="text-sm font-medium text-gray-900 dark:text-gray-100 mb-3 flex items-center space-x-2">
                                                                                <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                                                                </svg>
                                                                                <span>引数</span>
                                                                            </h5>
                                                                            <div class="space-y-2">
                                                                                <template x-for="argument in subOption.options" :key="argument.name">
                                                                                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3 border border-gray-200 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors duration-200">
                                                                                        <div class="flex items-center justify-between mb-2">
                                                                                            <div class="flex items-center space-x-2">
                                                                                                <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                                                                                </svg>
                                                                                                <span class="text-sm font-medium text-gray-900 dark:text-gray-100" x-text="argument.name"></span>
                                                                                                <span x-show="argument.required" class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200">
                                                                                                    必須
                                                                                                </span>
                                                                                            </div>
                                                                                            <span class="text-xs text-gray-500 dark:text-gray-400" x-text="argument.typeName"></span>
                                                                                        </div>
                                                                                        <p class="text-xs text-gray-600 dark:text-gray-400 mb-2" x-text="argument.description"></p>
                                                                                        
                                                                                        <!-- 引数の詳細 -->
                                                                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-xs text-gray-500 dark:text-gray-400">
                                                                                            <template x-if="argument.minLength !== undefined">
                                                                                                <span>最小文字数: <span x-text="argument.minLength"></span></span>
                                                                                            </template>
                                                                                            <template x-if="argument.maxLength !== undefined">
                                                                                                <span>最大文字数: <span x-text="argument.maxLength"></span></span>
                                                                                            </template>
                                                                                            <template x-if="argument.minValue !== undefined">
                                                                                                <span>最小値: <span x-text="argument.minValue"></span></span>
                                                                                            </template>
                                                                                            <template x-if="argument.maxValue !== undefined">
                                                                                                <span>最大値: <span x-text="argument.maxValue"></span></span>
                                                                                            </template>
                                                                                            <template x-if="argument.autocomplete">
                                                                                                <span class="text-blue-600 dark:text-blue-400">オートコンプリート対応</span>
                                                                                            </template>
                                                                                            <template x-if="argument.choices && argument.choices.length > 0">
                                                                                <span class="text-purple-600 dark:text-purple-400">選択肢あり</span>
                                                                                            </template>
                                                                                        </div>
                                                                                    </div>
                                                                                </template>
                                                                            </div>
                                                                        </div>
                                                                        
                                                                        <!-- 引数がない場合 -->
                                                                        <div x-show="!subOption.options || subOption.options.length === 0" class="text-center py-3">
                                                                            <p class="text-sm text-gray-500 dark:text-gray-400">このサブコマンドには引数がありません</p>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </template>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <!-- サブコマンド（直接のサブコマンド、type: 1） -->
                                            <div x-show="option.type === 1" class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-600 shadow-sm hover:shadow-md transition-all duration-200">
                                                <div class="w-full px-4 py-3 flex items-center justify-between">
                                                    <div class="flex items-center space-x-3">
                                                        <div class="text-lg text-green-600 dark:text-green-400">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                                            </svg>
                                                        </div>
                                                        <div>
                                                            <h4 class="text-sm font-medium text-gray-900 dark:text-gray-100" x-text="option.name"></h4>
                                                            <p class="text-xs text-gray-600 dark:text-gray-400" x-text="option.description"></p>
                                                        </div>
                                                    </div>
                                                    <div class="flex items-center space-x-2">
                                                        <span x-show="option.required" class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200">
                                                                            必須
                                                                        </span>
                                                        <span class="text-xs text-gray-500 dark:text-gray-400" x-text="option.typeName"></span>
                                                    </div>
                                                </div>
                                                
                                                <!-- サブコマンドの引数（常に表示） -->
                                                <div class="px-4 pb-4 border-t border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 rounded-b-lg">
                                                    <div class="mt-4 space-y-4">
                                                                        <!-- サブコマンドの引数 -->
                                                                        <div x-show="option.options && option.options.length > 0">
                                                                            <h5 class="text-sm font-medium text-gray-900 dark:text-gray-100 mb-3 flex items-center space-x-2">
                                                                                <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                                                                </svg>
                                                                                <span>引数</span>
                                                                            </h5>
                                                                            <div class="space-y-2">
                                                                                <template x-for="argument in option.options" :key="argument.name">
                                                                                    <div class="bg-white dark:bg-gray-800 rounded-lg p-3 border border-gray-200 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors duration-200">
                                                                                        <div class="flex items-center justify-between mb-2">
                                                                                            <div class="flex items-center space-x-2">
                                                                                                <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                                                                                </svg>
                                                                                                <span class="text-sm font-medium text-gray-900 dark:text-gray-100" x-text="argument.name"></span>
                                                                                <span x-show="argument.required" class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200">
                                                                                    必須
                                                                                </span>
                                                                            </div>
                                                                            <span class="text-xs text-gray-500 dark:text-gray-400" x-text="argument.typeName"></span>
                                                                        </div>
                                                                        <p class="text-xs text-gray-600 dark:text-gray-400 mb-2" x-text="argument.description"></p>
                                                                        
                                                                        <!-- 引数の詳細 -->
                                                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-xs text-gray-500 dark:text-gray-400">
                                                                            <template x-if="argument.minLength !== undefined">
                                                                                <span>最小文字数: <span x-text="argument.minLength"></span></span>
                                                                            </template>
                                                                            <template x-if="argument.maxLength !== undefined">
                                                                                <span>最大文字数: <span x-text="argument.maxLength"></span></span>
                                                                            </template>
                                                                            <template x-if="argument.minValue !== undefined">
                                                                                <span>最小値: <span x-text="argument.minValue"></span></span>
                                                                            </template>
                                                                            <template x-if="argument.maxValue !== undefined">
                                                                                <span>最大値: <span x-text="argument.maxValue"></span></span>
                                                                            </template>
                                                                            <template x-if="argument.autocomplete">
                                                                                <span class="text-blue-600 dark:text-blue-400">オートコンプリート対応</span>
                                                                            </template>
                                                                            <template x-if="argument.choices && argument.choices.length > 0">
                                                                                <span class="text-purple-600 dark:text-purple-400">選択肢あり</span>
                                                                            </template>
                                                                        </div>
                                                                    </div>
                                                                </template>
                                                            </div>
                                                        </div>
                                                        
                                                        <!-- 引数がない場合 -->
                                                        <div x-show="!option.options || option.options.length === 0" class="text-center py-3">
                                                            <p class="text-sm text-gray-500 dark:text-gray-400">このサブコマンドには引数がありません</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </div>

                                <!-- 通常のオプション情報（サブコマンド以外） -->
                                <div x-show="!hasSubcommands(command) && command.options && command.options.length > 0" class="mt-4">
                                    <h5 class="text-sm font-medium text-gray-900 dark:text-gray-100 mb-3">オプション</h5>
                                    <div class="space-y-3">
                                        <template x-for="option in command.options" :key="option.name">
                                            <div class="bg-white dark:bg-gray-800 rounded-lg p-3 border border-gray-200 dark:border-gray-600">
                                                <div class="flex items-center justify-between mb-2">
                                                    <div class="flex items-center space-x-2">
                                                        <span class="text-sm font-medium text-gray-900 dark:text-gray-100" x-text="option.name"></span>
                                                        <span x-show="option.required" class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200">
                                                            必須
                                                        </span>
                                                    </div>
                                                    <span class="text-xs text-gray-500 dark:text-gray-400" x-text="option.typeName"></span>
                                                </div>
                                                <p class="text-xs text-gray-600 dark:text-gray-400 mb-2" x-text="option.description"></p>
                                            </div>
                                        </template>
                                    </div>
                                </div>

                                <!-- オプションなしの場合 -->
                                <div x-show="!hasSubcommands(command) && (!command.options || command.options.length === 0)" class="mt-4 text-center py-3">
                                    <p class="text-sm text-gray-500 dark:text-gray-400">このコマンドにはオプションがありません</p>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
function commandsPage() {
    return {
        loading: true,
        error: null,
        commands: [],
        categories: [],
        activeCategory: '',
        expandedCommands: [],

        init() {
            // エラーオブジェクトを適切に初期化
            this.error = {
                title: '',
                message: ''
            };
            
            // 配列を確実に初期化
            this.expandedCommands = [];
            
            this.loadCommands();
        },

        async loadCommands() {
            this.loading = true;
            this.error = null;
            
            try {
                const response = await fetch('/api/commands', {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                if (!response.ok) {
                    const errorData = await response.json();
                    throw new Error(errorData.message || 'コマンドの取得に失敗しました');
                }

                const data = await response.json();
                
                if (data.commands && Array.isArray(data.commands)) {
                    this.commands = data.commands;
                } else {
                    this.commands = [];
                }
                
                if (data.categories && Array.isArray(data.categories)) {
                    this.categories = data.categories;
                    
                    if (this.categories.length > 0) {
                        this.activeCategory = this.categories[0].key;
                    }
                } else {
                    this.categories = [];
                }
                
            } catch (error) {
                this.error = {
                    title: 'コマンドの読み込みに失敗しました',
                    message: error.message
                };
            } finally {
                this.loading = false;
            }
        },

        getCommandsByCategory(categoryKey) {
            // カテゴリマッピング（コマンド名と説明文に基づく分類）
            const categoryMapping = {
                'auth': ['login', 'logout', 'register', 'verify', 'auth', 'authentication', 'token', 'session'],
                'context': ['context', 'search', 'find', 'lookup', 'query', 'resolve', 'mention'],
                'custom_role': ['role', 'reactionrole', 'reaction-role', 'customrole', 'reaction'],
                'dev': ['dev', 'debug', 'test', 'development', 'eval', 'console'],
                'info': ['info', 'userinfo', 'serverinfo', 'botinfo', 'user', 'server', 'bot', 'ping', 'invite'],
                'moderation': ['ban', 'kick', 'mute', 'warn', 'clear', 'moderation', 'mod', 'module'],
                'music': ['play', 'stop', 'skip', 'queue', 'volume', 'music', 'song', 'track', 'np', 'nowplaying'],
                'reader': ['read', 'tts', 'translate', 'reader', 'text', 'speak', 'voice', 'audio'],
                'util': ['ping', 'help', 'snowflake', 'utility', 'util', 'tools', 'general']
            };
            
            // コマンドを適切なカテゴリに分類
            const categorizedCommands = this.commands.map(cmd => {
                let assignedCategory = cmd.category;
                
                // カテゴリが'other'または未定義の場合、マッピングで分類
                if (!assignedCategory || assignedCategory === 'other') {
                    // より具体的なマッチングを優先
                    let bestMatch = null;
                    let bestScore = 0;
                    
                    for (const [catKey, keywords] of Object.entries(categoryMapping)) {
                        let score = 0;
                        
                        // コマンド名でのマッチング（高優先度）
                        if (keywords.some(keyword => cmd.name.toLowerCase().includes(keyword.toLowerCase()))) {
                            score += 10;
                        }
                        
                        // 説明文でのマッチング（中優先度）
                        if (cmd.description && keywords.some(keyword => 
                            cmd.description.toLowerCase().includes(keyword.toLowerCase())
                        )) {
                            score += 5;
                        }
                        
                        // 完全一致（最高優先度）
                        if (keywords.includes(cmd.name.toLowerCase())) {
                            score += 20;
                        }
                        
                        if (score > bestScore) {
                            bestScore = score;
                            bestMatch = catKey;
                        }
                    }
                    
                    if (bestMatch) {
                        assignedCategory = bestMatch;
                    }
                }
                
                return {
                    ...cmd,
                    assignedCategory: assignedCategory
                };
            });
            
            // 指定されたカテゴリに一致するコマンドをフィルタリング
            const filteredCommands = categorizedCommands.filter(cmd => {
                return cmd.assignedCategory === categoryKey;
            });
            
            return filteredCommands;
        },

        // サブコマンドを持つかどうかを判定
        hasSubcommands(command) {
            return command.options && command.options.some(option => 
                option.type === 1 || option.type === 2
            );
        },

        toggleCommand(commandId) {
            const index = this.expandedCommands.indexOf(commandId);
            if (index > -1) {
                this.expandedCommands.splice(index, 1);
            } else {
                this.expandedCommands.push(commandId);
            }
        },



        getPermissionDisplayName(permission) {
            const permissionNames = {
                'SendMessages': 'メッセージ送信',
                'Connect': '接続',
                'Speak': '発言',
                'ManageMessages': 'メッセージ管理',
                'ManageRoles': 'ロール管理',
                'KickMembers': 'メンバーをキック',
                'BanMembers': 'メンバーをBAN',
                'Administrator': '管理者',
                'ViewChannel': 'チャンネルを表示',
                'ReadMessageHistory': 'メッセージ履歴を読む'
            };
            return permissionNames[permission] || permission;
        },

        formatDate(dateString) {
            if (!dateString) return '不明';
            try {
                return new Date(dateString).toLocaleDateString('ja-JP', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                });
            } catch (error) {
                return '不明';
            }
        },

        get totalOptions() {
            const total = this.commands.reduce((total, cmd) => {
                return total + (cmd.options ? cmd.options.length : 0);
            }, 0);
            return total;
        }
    }
}
</script>

</body>
</html>

