<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>利用規約 - Miku</title>

    @include('components.og-tags', [
        'title' => '利用規約 - Miku Bot',
        'description' => 'Miku Botの利用規約です。サービス利用の条件をご確認ください。',
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
            <div class="bg-gradient-to-r from-blue-600 to-purple-600 px-6 py-8 text-white">
                <h1 class="text-3xl font-bold text-center">利用規約</h1>
                <p class="text-center mt-2 text-blue-100">Terms of Service</p>
            </div>

            <!-- コンテンツ -->
            <div class="px-6 py-8 prose prose-lg max-w-none dark:prose-invert">
                <p class="text-gray-700 dark:text-gray-300 mb-6">
                    この利用規約（以下、「本規約」）は、Harukoto（以下、「開発者」）がDiscord（以下、「提供元」）で提供するサービス（以下、「本サービス」）の利用条件を定めるものです。ユーザーの皆さま（以下、「ユーザー」）には、本規約に従って、本サービスをご利用いただきます。
                </p>

                <!-- 1. 適用 -->
                <section class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200 mb-4 border-b-2 border-blue-200 dark:border-blue-600 pb-2">1. 適用</h2>
                    <ul class="list-disc list-inside space-y-2 text-gray-700 dark:text-gray-300 ml-4">
                        <li>本規約は、ユーザーと開発者との間の本サービスの利用に関わる一切の関係に適用されるものとします。</li>
                        <li>開発者は本サービスに関し、本規約のほか、ご利用にあたってのルール等、各種の定め（以下，「個別規定」）をすることがあります。これら個別規定はその名称のいかんに関わらず、本規約の一部を構成するものとします。</li>
                        <li>本規約の規定が前条の個別規定の規定と矛盾する場合には、個別規定において特段の定めなき限り、個別規定の規定が優先されるものとします。</li>
                    </ul>
                </section>

                <!-- 2. 利用及び制限 -->
                <section class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200 mb-4 border-b-2 border-blue-200 dark:border-blue-600 pb-2">2. 利用及び制限</h2>
                    <ul class="list-disc list-inside space-y-2 text-gray-700 dark:text-gray-300 ml-4 mb-4">
                        <li>本規約は、サーバーの所有者又は管理者がボットの導入または、ユーザーが本サービスを利用されているサーバーに属することで適用されるものとします。</li>
                        <li>開発者は、ユーザーが以下条件のいずれかに該当する場合は、事前の通知なくユーザーに対して、本サービスの全部もしくは一部の利用を制限することができるものとします。</li>
                    </ul>
                    
                    <div class="bg-gray-50 dark:bg-gray-600 rounded-lg p-4 mb-4">
                        <ol class="list-decimal list-inside space-y-1 text-gray-700 dark:text-gray-300 ml-4">
                            <li>開発者のガイドラインや本規約のいずれかの条項に違反した場合</li>
                            <li>その他、本サービスの利用を適切でないと判断した場合</li>
                        </ol>
                    </div>

                    <h3 class="text-xl font-semibold text-gray-700 dark:text-gray-300 mb-3">禁止事項</h3>
                    <p class="text-gray-700 dark:text-gray-300 mb-3">ユーザーは、本サービスの利用にあたり、以下の行為をしないものとします。</p>
                    
                    <div class="bg-red-50 dark:bg-red-900/50 border-l-4 border-red-400 dark:border-red-500 p-4 rounded-r-lg">
                        <ol class="list-decimal list-inside space-y-2 text-gray-700 dark:text-gray-300 ml-4">
                            <li>提供先のガイドラインや利用規約に違反する行為</li>
                            <li>他のユーザーに関する個人情報等を収集または蓄積する行為</li>
                            <li>他のユーザーに許可なく成りすます行為</li>
                            <li>他のユーザーまたはその他の第三者に不利益、損害、不快感を与える行為</li>
                            <li>本サービスに対し、不正アクセスをし、またはこれを試みる行為</li>
                            <li>本サービスの運営を妨害し、またはこれを試みる行為</li>
                            <li>本サービスに対して故意に負荷をかける行為</li>
                            <li>本サービスの不具合・バグなどを悪用する行為</li>
                            <li>本サービスの機能などをコピーまたは、全部あるいは一部を許可なく利用する行為</li>
                            <li>不正な目的を持って本サービスを利用する行為</li>
                            <li>本サービス上のコンテンツを悪用する行為</li>
                            <li>本サービスで得た情報を営利目的に利用する行為</li>
                            <li>運営チームが許諾しない本サービス上での宣伝、広告、勧誘、または営業行為</li>
                            <li>法令または公序良俗に違反する行為</li>
                            <li>犯罪行為に関連する行為</li>
                            <li>開発者のサービスに関連して、反社会的勢力に対して直接または間接に利益を供与する行為</li>
                            <li>開発者が不適切と判断する行為</li>
                        </ol>
                    </div>
                </section>

                <!-- 4. 本サービスの提供の停止等 -->
                <section class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200 mb-4 border-b-2 border-blue-200 dark:border-blue-600 pb-2">4. 本サービスの提供の停止等</h2>
                    <p class="text-gray-700 dark:text-gray-300 mb-3">開発者は、以下のいずれかの事由があると判断した場合、ユーザーに事前に通知することなく本サービスの全部または一部の提供を停止または中断することができるものとします。</p>
                    
                    <div class="bg-yellow-50 dark:bg-yellow-900/50 border-l-4 border-yellow-400 dark:border-yellow-500 p-4 rounded-r-lg mb-4">
                        <ol class="list-decimal list-inside space-y-2 text-gray-700 dark:text-gray-300 ml-4">
                            <li>本サービスにかかるコンピュータシステムの保守点検または更新を行う場合</li>
                            <li>地震、落雷、火災、停電または天災などの不可抗力により、本サービスの提供が困難となった場合</li>
                            <li>コンピュータまたは通信回線等が事故により停止した場合</li>
                            <li>その他、開発者が本サービスの提供が困難と判断した場合</li>
                        </ol>
                    </div>
                    
                    <p class="text-gray-700 dark:text-gray-300">開発者は、本サービスの提供の停止または中断により、ユーザーまたは第三者が被ったいかなる不利益または損害についても、一切の責任を負わないものとします。</p>
                </section>

                <!-- 5. 利用制限 -->
                <section class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200 mb-4 border-b-2 border-blue-200 dark:border-blue-600 pb-2">5. 利用制限</h2>
                    <p class="text-gray-700 dark:text-gray-300 mb-3">開発者は、ユーザーが以下のいずれかに該当する場合には、事前の通知なく、ユーザーに対して、本サービスの全部もしくは一部の利用を制限することができるものとします。</p>
                    
                    <div class="bg-orange-50 dark:bg-orange-900/50 border-l-4 border-orange-400 dark:border-orange-500 p-4 rounded-r-lg mb-4">
                        <ol class="list-decimal list-inside space-y-2 text-gray-700 dark:text-gray-300 ml-4">
                            <li>提供先のガイドラインや利用規約のいずれかの条項に違反した場合</li>
                            <li>本規約やガイドラインのいずれかの条項に違反した場合</li>
                            <li>提供先の登録事項に虚偽の事実があることが判明した場合</li>
                            <li>開発者からの連絡に対し、一定期間返答がない場合</li>
                            <li>その他、開発者が本サービスの利用を適当でないと判断した場合</li>
                        </ol>
                    </div>
                    
                    <p class="text-gray-700 dark:text-gray-300">開発者は、本条に基づき開発者が行った行為によりユーザーに生じた損害について、一切の責任を負いません。</p>
                </section>

                <!-- 6. 保証の否認及び免責事項 -->
                <section class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200 mb-4 border-b-2 border-blue-200 dark:border-blue-600 pb-2">6. 保証の否認及び免責事項</h2>
                    <ul class="list-disc list-inside space-y-3 text-gray-700 dark:text-gray-300 ml-4">
                        <li>開発者は、本サービスに事実上または法律上の瑕疵（安全性、信頼性、正確性、完全性、有効性、特定の目的への適合性、セキュリティなどに関する欠陥、エラーやバグ、権利侵害などを含みます）がないことを明示的にも黙示的にも保証しておりません。</li>
                        <li>開発者は、本サービスに起因してユーザーに生じたあらゆる損害について、開発者の故意又は重過失による場合を除き、一切の責任を負いません。ただし、本サービスに関する開発者とユーザーとの間の契約（本規約を含みます）が消費者契約法に定める消費者契約となる場合、この免責規定は適用されません。</li>
                        <li>前項ただし書に定める場合であっても、開発者は、開発者の過失（重過失を除きます）による債務不履行または不法行為によりユーザーに生じた損害のうち特別な事情から生じた損害（開発者またはユーザーが損害発生につき予見し、または予見し得た場合を含みます）について一切の責任を負いません。また、開発者の過失（重過失を除きます）による債務不履行または不法行為によりユーザーに生じた損害の賠償は、ユーザーから当該損害が発生した月に受領した利用料の額を上限とします。</li>
                        <li>開発者は、本サービスに関して、ユーザーと他のユーザーまたは第三者との間において生じた取引、連絡または紛争等について一切責任を負いません。</li>
                    </ul>
                </section>

                <!-- 7. サービス内容の変更等 -->
                <section class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200 mb-4 border-b-2 border-blue-200 dark:border-blue-600 pb-2">7. サービス内容の変更等</h2>
                    <p class="text-gray-700 dark:text-gray-300">開発者は、ユーザーに通知することなく、本サービスの内容を変更、追加または廃止することができるものとし、これによってユーザーに生じた損害について一切の責任を負いません。</p>
                </section>

                <!-- 8. 利用規約の変更 -->
                <section class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200 mb-4 border-b-2 border-blue-200 dark:border-blue-600 pb-2">8. 利用規約の変更</h2>
                    <p class="text-gray-700 dark:text-gray-300 mb-3">開発者は以下の場合には、ユーザーの個別の同意を要せず、本規約を変更することができるものとします。</p>
                    
                    <div class="bg-green-50 dark:bg-green-900/50 border-l-4 border-green-400 dark:border-green-500 p-4 rounded-r-lg mb-4">
                        <ol class="list-decimal list-inside space-y-2 text-gray-700 dark:text-gray-300 ml-4">
                            <li>本規約の変更がユーザーの一般の利益に適合するとき。</li>
                            <li>本規約の変更が本サービス利用契約の目的に反せず、かつ、変更の必要性、変更後の内容の相当性その他の変更に係る事情に照らして合理的なものであるとき。</li>
                        </ol>
                    </div>
                    
                    <p class="text-gray-700 dark:text-gray-300">開発者はユーザーに対し、前項による本規約の変更にあたり、事前に、本規約を変更する旨及び変更後の本規約の内容並びにその効力発生時期を通知します。</p>
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
