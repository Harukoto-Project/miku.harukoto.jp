<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Miku - Discordボットサービス</title>

    @include('components.og-tags', [
        'title' => 'Miku - 多機能で便利なコミュニティ向けDiscord Bot',
        'description' => '多機能で便利なコミュニティ向けDiscord Bot。ログインして機能をお楽しみください。',
        'image' => asset('images/default-avatar.svg')
    ])

    
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body class="bg-white dark:bg-gray-900 transition-colors duration-300">

@if (session('success'))
    <div class="bg-green-50 dark:bg-green-900/50 border border-green-200 dark:border-green-700 rounded-md p-4 mx-auto max-w-7xl px-6 mt-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-green-800 dark:text-green-200">
                    {{ session('success') }}
                </p>
            </div>
        </div>
    </div>
@endif

@if (session('error'))
    <div class="bg-red-50 dark:bg-red-900/50 border border-red-200 dark:border-red-700 rounded-md p-4 mx-auto max-w-7xl px-6 mt-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-red-800 dark:text-red-200">
                    {{ session('error') }}
                </p>
            </div>
        </div>
    </div>
@endif

@include('components.header', ['avatarUrl' => 'https://cdn.discordapp.com/avatars/949873067620966490/b178d5be3d02d1263c80002ad20363f1.png'])


<main class="mx-auto max-w-7xl px-6 py-12 lg:px-8">
    <div class="text-center">
        <h1 class="text-4xl font-bold tracking-tight text-gray-900 dark:text-gray-100 sm:text-6xl">
            Mikuへようこそ
        </h1>
        <p class="mt-6 text-xl leading-8 text-gray-600 dark:text-gray-400">
            <span class="font-semibold text-indigo-600 dark:text-indigo-400">「Making Everything Easy」</span>をモットーに
        </p>
        <p class="mt-4 text-lg leading-8 text-gray-600 dark:text-gray-400">
            Discordサーバーの管理とコミュニティ活動をサポートする多機能Bot
        </p>
        <div class="mt-10 flex items-center justify-center gap-x-6">
            @auth
                <a href="{{ route('dashboard') }}" class="rounded-md bg-indigo-600 px-6 py-3 text-lg font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 transition-colors duration-200">
                    ダッシュボードへ
                </a>
            @else
                <a href="{{ route('discord.login') }}" class="rounded-md bg-indigo-600 px-6 py-3 text-lg font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 transition-colors duration-200">
                    Discordでログイン
                </a>
            @endauth
        </div>
    </div>
</main>


<section class="py-16 bg-gray-50 dark:bg-gray-800">
    <div class="mx-auto max-w-7xl px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl font-bold tracking-tight text-gray-900 dark:text-gray-100 sm:text-4xl">
                主要機能
            </h2>
            <p class="mt-4 text-lg text-gray-600 dark:text-gray-400">
                Mikuが提供する豊富な機能で、あなたのDiscordサーバーをより楽しく、管理しやすくします
            </p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            
            <div class="bg-white dark:bg-gray-700 rounded-lg shadow-lg p-6 hover:shadow-xl transition-shadow duration-300">
                <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-3">音楽・音声機能</h3>
                <p class="text-gray-600 dark:text-gray-400 mb-4">VOICEVOX統合による日本語音声合成と読み上げ機能</p>
                <ul class="text-sm text-gray-500 dark:text-gray-400 space-y-1">
                    <li>• 複数話者対応</li>
                    <li>• リアルタイム読み上げ</li>
                    <li>• カスタマイズ可能</li>
                </ul>
            </div>

            
            <div class="bg-white dark:bg-gray-700 rounded-lg shadow-lg p-6 hover:shadow-xl transition-shadow duration-300">
                <div class="w-12 h-12 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-3">ゲーミフィケーション</h3>
                <p class="text-gray-600 dark:text-gray-400 mb-4">レベルシステムと実績システムでサーバーを活性化</p>
                <ul class="text-sm text-gray-500 dark:text-gray-400 space-y-1">
                    <li>• 経験値・レベルアップ</li>
                    <li>• リーダーボード</li>
                    <li>• 多様な実績</li>
                </ul>
            </div>

            
            <div class="bg-white dark:bg-gray-700 rounded-lg shadow-lg p-6 hover:shadow-xl transition-shadow duration-300">
                <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900 rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-3">サーバー管理</h3>
                <p class="text-gray-600 dark:text-gray-400 mb-4">チケットシステム、ロール管理、モデレーション機能</p>
                <ul class="text-sm text-gray-500 dark:text-gray-400 space-y-1">
                    <li>• サポートチケット</li>
                    <li>• カスタムロール</li>
                    <li>• 自動モデレーション</li>
                </ul>
            </div>

            
            <div class="bg-white dark:bg-gray-700 rounded-lg shadow-lg p-6 hover:shadow-xl transition-shadow duration-300">
                <div class="w-12 h-12 bg-yellow-100 dark:bg-yellow-900 rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-3">AI・自動化機能</h3>
                <p class="text-gray-600 dark:text-gray-400 mb-4">ChatAI統合と自動化ツールで効率化</p>
                <ul class="text-sm text-gray-500 dark:text-gray-400 space-y-1">
                    <li>• 多モデル対応</li>
                    <li>• リマインダー</li>
                    <li>• 自動公開</li>
                </ul>
            </div>

            
            <div class="bg-white dark:bg-gray-700 rounded-lg shadow-lg p-6 hover:shadow-xl transition-shadow duration-300">
                <div class="w-12 h-12 bg-red-100 dark:bg-red-900 rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4V2a1 1 0 011-1h8a1 1 0 011 1v2m-9 0h10m-10 0a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V6a2 2 0 00-2-2M9 4v2h6V4M9 4h6"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-3">ユーティリティ機能</h3>
                <p class="text-gray-600 dark:text-gray-400 mb-4">コミュニティツールとゲーム・エンターテイメント</p>
                <ul class="text-sm text-gray-500 dark:text-gray-400 space-y-1">
                    <li>• 引用作成</li>
                    <li>• 投票システム</li>
                    <li>• サイコロ・おみくじ</li>
                </ul>
            </div>

            
            <div class="bg-white dark:bg-gray-700 rounded-lg shadow-lg p-6 hover:shadow-xl transition-shadow duration-300">
                <div class="w-12 h-12 bg-indigo-100 dark:bg-indigo-900 rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-3">統計・分析</h3>
                <p class="text-gray-600 dark:text-gray-400 mb-4">サーバーとユーザーの活動を詳細に分析</p>
                <ul class="text-sm text-gray-500 dark:text-gray-400 space-y-1">
                    <li>• メンバー活動追跡</li>
                    <li>• チャンネル分析</li>
                    <li>• レポート生成</li>
                </ul>
            </div>
        </div>
    </div>
</section>


<section class="py-16 bg-indigo-600">
    <div class="mx-auto max-w-7xl px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8 text-center">
            <div>
                <div class="text-4xl font-bold text-white mb-2">1000+</div>
                <div class="text-indigo-100">アクティブサーバー</div>
            </div>
            <div>
                <div class="text-4xl font-bold text-white mb-2">50,000+</div>
                <div class="text-indigo-100">ユーザー</div>
            </div>
            <div>
                <div class="text-4xl font-bold text-white mb-2">99.9%</div>
                <div class="text-indigo-100">稼働率</div>
            </div>
            <div>
                <div class="text-4xl font-bold text-white mb-2">24/7</div>
                <div class="text-indigo-100">サポート</div>
            </div>
        </div>
    </div>
</section>


<section class="py-16 bg-white dark:bg-gray-900">
    <div class="mx-auto max-w-7xl px-6 lg:px-8 text-center">
        <h2 class="text-3xl font-bold tracking-tight text-gray-900 dark:text-gray-100 sm:text-4xl mb-6">
            今すぐMikuを始めましょう
        </h2>
        <p class="text-lg text-gray-600 dark:text-gray-400 mb-8 max-w-2xl mx-auto">
            数分でセットアップ完了。あなたのDiscordサーバーをより楽しく、管理しやすくします。
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            @auth
                <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center px-8 py-3 text-lg font-semibold text-white bg-indigo-600 rounded-md hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 transition-colors duration-200">
                    ダッシュボードへ
                </a>
            @else
                <a href="{{ route('discord.login') }}" class="inline-flex items-center justify-center px-8 py-3 text-lg font-semibold text-white bg-indigo-600 rounded-md hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 transition-colors duration-200">
                    Discordでログイン
                </a>
            @endauth
            <a href="https://discord.gg/A2dYWKYtXe" target="_blank" class="inline-flex items-center justify-center px-8 py-3 text-lg font-semibold text-indigo-600 bg-white border border-indigo-600 rounded-md hover:bg-indigo-50 dark:bg-gray-800 dark:text-indigo-400 dark:border-indigo-400 dark:hover:bg-gray-700 transition-colors duration-200">
                サポートサーバー
            </a>
        </div>
    </div>
</section>


<footer class="bg-gray-900 text-white">
    <div class="mx-auto max-w-7xl px-6 py-12 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div>
                <h3 class="text-lg font-semibold mb-4">Miku Bot</h3>
                <p class="text-gray-400 text-sm">
                    「Making Everything Easy」をモットーに、Discordサーバーの管理とコミュニティ活動をサポートする多機能Botです。
                </p>
            </div>
            <div>
                <h3 class="text-lg font-semibold mb-4">リンク</h3>
                <ul class="space-y-2 text-sm text-gray-400">
                    <li><a href="https://miku.harukoto.jp" class="hover:text-white transition-colors duration-200">公式サイト</a></li>
                    <li><a href="https://discord.gg/A2dYWKYtXe" target="_blank" class="hover:text-white transition-colors duration-200">サポートサーバー</a></li>
                    <li><a href="https://discord.gg/yu-rundesu-1149350818747781120" target="_blank" class="hover:text-white transition-colors duration-200">コミュニティ</a></li>
                </ul>
            </div>
            <div>
                <h3 class="text-lg font-semibold mb-4">機能</h3>
                <ul class="space-y-2 text-sm text-gray-400">
                    <li>• 音声合成・読み上げ</li>
                    <li>• レベル・実績システム</li>
                    <li>• サーバー管理ツール</li>
                    <li>• AI・自動化機能</li>
                </ul>
            </div>
        </div>
        <div class="border-t border-gray-800 mt-8 pt-8 text-center text-sm text-gray-400">
            <p>&copy; 2024 Miku Bot. All rights reserved.</p>
        </div>
    </div>
</footer>

</body>
</html>
