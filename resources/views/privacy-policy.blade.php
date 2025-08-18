<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>プライバシーポリシー - Miku</title>

    @include('components.og-tags', [
        'title' => 'プライバシーポリシー - Miku Bot',
        'description' => 'Miku Botのプライバシーポリシーです。個人情報の取り扱いについてご確認ください。',
        'image' => asset('images/default-avatar.svg')
    ])

    @include('components.ga4')

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body class="bg-white dark:bg-gray-900 transition-colors duration-300">

  @include('components.header')

<main class="min-h-screen bg-gray-50 dark:bg-gray-800 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-700 shadow-lg rounded-lg overflow-hidden">
            <!-- ヘッダー -->
            <div class="bg-gradient-to-r from-green-600 to-teal-600 px-6 py-8 text-white">
                <h1 class="text-3xl font-bold text-center">プライバシーポリシー</h1>
                <p class="text-center mt-2 text-green-100">Privacy Policy</p>
            </div>

            <!-- コンテンツ -->
            <div class="px-6 py-8 prose prose-lg max-w-none dark:prose-invert">
                <p class="text-gray-700 dark:text-gray-300 mb-6">
                    このプライバシーポリシー（以下、「本ポリシー」）は、Harukoto（以下、「開発者」）がDiscord（以下、「提供元」）で提供するサービス（以下、「本サービス」）における、ユーザーの個人情報の取扱いについて定めるものです。
                </p>

                <!-- 1. データの収集・利用目的 -->
                <section class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200 mb-4 border-b-2 border-green-200 dark:border-green-600 pb-2">1. データの収集・利用目的</h2>
                    <p class="text-gray-700 dark:text-gray-300 mb-3">開発者が個人情報を収集・利用する目的は、以下のとおりです。</p>
                    
                    <div class="bg-green-50 dark:bg-green-900/50 border-l-4 border-green-400 dark:border-green-500 p-4 rounded-r-lg">
                        <ol class="list-decimal list-inside space-y-2 text-gray-700 dark:text-gray-300 ml-4">
                            <li>本サービスの提供・運営のため</li>
                            <li>ユーザーからのお問い合わせに回答するため</li>
                            <li>利用規約に違反したユーザーや、不正・不当な目的でサービスを利用しようとするユーザーを特定するため</li>
                            <li>上記の利用目的に付随する目的</li>
                        </ol>
                    </div>
                </section>

                <!-- 2. 利用目的の変更 -->
                <section class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200 mb-4 border-b-2 border-green-200 dark:border-green-600 pb-2">2. 利用目的の変更</h2>
                    <ul class="list-disc list-inside space-y-3 text-gray-700 dark:text-gray-300 ml-4">
                        <li>開発者は、利用目的が変更前と関連性を有すると合理的に認められる場合に限り、個人情報の利用目的を変更するものとします。</li>
                        <li>利用目的の変更を行った場合には、変更後の目的について、開発者所定の方法により、ユーザーに通知するものとします。</li>
                    </ul>
                </section>

                <!-- 3. 第三者への提供 -->
                <section class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200 mb-4 border-b-2 border-green-200 dark:border-green-600 pb-2">3. 第三者への提供</h2>
                    <ul class="list-disc list-inside space-y-3 text-gray-700 dark:text-gray-300 ml-4 mb-4">
                        <li>開発者は、あらかじめユーザーの同意を得ることなく、第三者に個人情報を提供することはありません。ただし、個人情報保護法、又はその他の法令で認められる場合を除きます。</li>
                        <li>開発者は以下の場合には、個人情報の提供先は第三者に該当しないものとします。</li>
                    </ul>
                    
                    <div class="bg-blue-50 dark:bg-blue-900/50 border-l-4 border-blue-400 dark:border-blue-500 p-4 rounded-r-lg">
                        <ol class="list-decimal list-inside space-y-2 text-gray-700 dark:text-gray-300 ml-4">
                            <li>開発者が利用目的の達成に必要な範囲内において個人情報の取扱いの全部または一部を委託する場合</li>
                            <li>合併その他の事由による事業の承継に伴って個人情報が提供される場合</li>
                        </ol>
                    </div>
                </section>

                <!-- 4. 個人情報の開示 -->
                <section class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200 mb-4 border-b-2 border-green-200 dark:border-green-600 pb-2">4. 個人情報の開示</h2>
                    <p class="text-gray-700 dark:text-gray-300 mb-3">開発者は、本人から個人情報の開示を求められたときは、本人に対し、遅滞なくこれを開示します。</p>
                    
                    <p class="text-gray-700 dark:text-gray-300 mb-3">開示することにより開発者は以下の場合には、その全部または一部を開示しないこともあり、開示しない決定をした場合には、その旨を遅滞なく通知します。</p>
                    
                    <div class="bg-yellow-50 dark:bg-yellow-900/50 border-l-4 border-yellow-400 dark:border-yellow-500 p-4 rounded-r-lg">
                        <ol class="list-decimal list-inside space-y-2 text-gray-700 dark:text-gray-300 ml-4">
                            <li>本人または第三者の生命、身体、財産その他の権利利益を害するおそれがある場合</li>
                            <li>開発者の業務の適正な実施に著しい支障を及ぼすおそれがある場合</li>
                            <li>その他法令に違反することとなる場合</li>
                        </ol>
                    </div>
                </section>

                <!-- 5. 個人情報の利用停止 -->
                <section class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200 mb-4 border-b-2 border-green-200 dark:border-green-600 pb-2">5. 個人情報の利用停止</h2>
                    <ul class="list-disc list-inside space-y-3 text-gray-700 dark:text-gray-300 ml-4">
                        <li>開発者は、本人から個人情報が、利用目的の範囲を超えて取り扱われているという理由、または不正の手段により取得されたものであるという理由により、その利用の停止または消去（以下、「利用停止等」）を求められた場合には、遅滞なく必要な調査を行います。</li>
                        <li>上記より請求に応じる必要があると判断した場合には、遅滞なく、当該個人情報の利用停止等を行います。</li>
                        <li>上記の規定に基づき利用停止等を行った場合、または利用停止等を行わない旨の決定をしたときは、遅滞なく、これをユーザーに通知します。</li>
                    </ul>
                </section>

                <!-- 6. プライバシーポリシーの変更 -->
                <section class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200 mb-4 border-b-2 border-green-200 dark:border-green-600 pb-2">6. プライバシーポリシーの変更</h2>
                    <p class="text-gray-700 dark:text-gray-300 mb-3">開発者は以下の場合には、ユーザーの個別の同意を要せず、本ポリシーを変更することができるものとします。</p>
                    
                    <div class="bg-purple-50 dark:bg-purple-900/50 border-l-4 border-purple-400 dark:border-purple-500 p-4 rounded-r-lg mb-4">
                        <ol class="list-decimal list-inside space-y-2 text-gray-700 dark:text-gray-300 ml-4">
                            <li>本ポリシーの変更がユーザーの一般の利益に適合するとき。</li>
                            <li>本ポリシーの変更が本サービス利用契約の目的に反せず、かつ、変更の必要性、変更後の内容の相当性その他の変更に係る事情に照らして合理的なものであるとき。</li>
                        </ol>
                    </div>
                    
                    <p class="text-gray-700 dark:text-gray-300">開発者はユーザーに対し、前項による本ポリシーの変更にあたり、事前に、本ポリシーを変更する旨及び変更後の本ポリシーの内容並びにその効力発生時期を通知します。</p>
                </section>

                <!-- お問い合わせ -->
                <section class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200 mb-4 border-b-2 border-green-200 dark:border-green-600 pb-2">お問い合わせ</h2>
                    <div class="bg-gray-50 dark:bg-gray-600 rounded-lg p-6">
                        <p class="text-gray-700 dark:text-gray-300 mb-3">本ポリシーに関するお問い合わせがございましたら、以下の方法でお気軽にお問い合わせください。</p>
                        <div class="flex items-center space-x-2 text-blue-600 dark:text-blue-400">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path>
                                <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path>
                            </svg>
                            <a class="font-medium hover:text-blue-500" href="https://x.com/Harukoto_" target="_blank">X: Harukoto_</a>
                        </div>
                    </div>
                </section>
            </div>

            <!-- フッター -->
            <div class="bg-gray-100 dark:bg-gray-600 px-6 py-4 text-center">
                <p class="text-sm text-gray-600 dark:text-gray-400">最終更新日: 2024年2月28日</p>
            </div>
        </div>
    </div>
</main>

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
            <p>&copy; 2023-2025 Harukoto Project All rights reserved.</p>
        </div>
    </div>
</footer>

</body>
</html>
