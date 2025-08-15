<div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 border border-gray-200 dark:border-gray-700">
    <div class="mb-6">
        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">連携済みアカウント設定</h3>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">外部サービスとの連携設定を管理します</p>
    </div>
    
    <div class="space-y-6">
        <!-- Discord連携セクション -->
        <div>
            <h4 class="text-md font-medium text-gray-900 dark:text-gray-100 mb-4">Discord連携</h4>
            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="flex-shrink-0">
                            <svg class="h-8 w-8 text-indigo-600" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M20.317 4.3698a19.7913 19.7913 0 00-4.8851-1.5152.0741.0741 0 00-.0785.0371c-.211.3753-.4447.8648-.6083 1.2495-1.8447-.2762-3.68-.2762-5.4868 0-.1636-.3933-.4058-.8742-.6177-1.2495a.077.077 0 00-.0785-.037 19.7363 19.7363 0 00-4.8852 1.515.0699.0699 0 00-.0321.0277C.5334 9.0458-.319 13.5799.0992 18.0578a.0824.0824 0 00.0312.0561c2.0528 1.5076 4.0413 2.4228 5.9929 3.0294a.0777.0777 0 00.0842-.0276c.4616-.6304.8731-1.2952 1.226-1.9942a.076.076 0 00-.0416-.1057c-.6528-.2476-1.2743-.5495-1.8722-.8923a.077.077 0 01-.0076-.1277c.1258-.0943.2517-.1923.3718-.2914a.0743.0743 0 01.0776-.0105c3.9278 1.7933 8.18 1.7933 12.0614 0a.0739.0739 0 01.0785.0095c.1202.099.246.1981.3728.2924a.077.077 0 01-.0066.1276 12.2986 12.2986 0 01-1.873.8914.0766.0766 0 00-.0407.1067c.3604.698.7719 1.3628 1.225 1.9932a.076.076 0 00.0842.0286c1.961-.6067 3.9495-1.5219 6.0023-3.0294a.077.077 0 00.0313-.0552c.5004-5.177-.8382-9.6739-3.5485-13.6604a.061.061 0 00-.0312-.0286zM8.02 15.3312c-1.1825 0-2.1569-1.0857-2.1569-2.419 0-1.3332.9555-2.4189 2.157-2.4189 1.2108 0 2.1757 1.0952 2.1568 2.419-.0002 1.3332-.9555 2.4189-2.1569 2.4189zm7.9748 0c-1.1825 0-2.1569-1.0857-2.1569-2.419 0-1.3332.9554-2.4189 2.1569-2.4189 1.2108 0 2.1757 1.0952 2.1568 2.419 0 1.3332-.9555 2.4189-2.1568 2.4189Z"/>
                            </svg>
                        </div>
                        <div>
                            <h5 class="text-sm font-medium text-gray-900 dark:text-gray-100">Discord</h5>
                            <p class="text-sm text-gray-500 dark:text-gray-400">連携済み</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                            連携中
                        </span>
                        <span class="text-gray-400 text-sm font-medium cursor-not-allowed" title="Discord連携は解除できません">
                            連携解除不可
                        </span>
                    </div>
                </div>
                <div class="mt-3 text-sm text-gray-600 dark:text-gray-300">
                    <p>ユーザー名: {{ $user->discord_username }}</p>
                    <p>Discord ID: {{ $user->discord_id }}</p>
                </div>
            </div>
        </div>

        <!-- Harukoto Project連携セクション（ポートフォリオ使用時のみ表示） -->
        @if(isset($profile) && $profile->exists)
        <div>
            <h4 class="text-md font-medium text-gray-900 dark:text-gray-100 mb-4">Harukoto Project連携</h4>
            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                            </div>
                        </div>
                        <div>
                            <h5 class="text-sm font-medium text-gray-900 dark:text-gray-100">Harukoto Project</h5>
                            <p class="text-sm text-gray-500 dark:text-gray-400">連携済み</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                            連携中
                        </span>
                        <span class="text-gray-400 text-sm font-medium cursor-not-allowed" title="Harukoto Project連携は解除できません">
                            連携解除不可
                        </span>
                    </div>
                </div>
                <div class="mt-3 text-sm text-gray-600 dark:text-gray-300">
                    <p>ポートフォリオ: 有効</p>
                    <p>カスタムURL: {{ $profile->custom_url ?: '未設定' }}</p>
                </div>
            </div>
        </div>
        @endif

        <!-- その他の連携サービスセクション -->
        <div>
            <h4 class="text-md font-medium text-gray-900 dark:text-gray-100 mb-4">その他の連携サービス</h4>
            <div class="space-y-3">
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                                </svg>
                            </div>
                            <div>
                                <h5 class="text-sm font-medium text-gray-900 dark:text-gray-100">GitHub</h5>
                                <p class="text-sm text-gray-500 dark:text-gray-400">未連携</p>
                            </div>
                        </div>
                        <button type="button" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 text-sm font-medium">
                            連携する
                        </button>
                    </div>
                </div>

                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                                </svg>
                            </div>
                            <div>
                                <h5 class="text-sm font-medium text-gray-900 dark:text-gray-100">Google</h5>
                                <p class="text-sm text-gray-500 dark:text-gray-400">未連携</p>
                            </div>
                        </div>
                        <button type="button" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 text-sm font-medium">
                            連携する
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- 注意事項 -->
        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800 dark:text-blue-200">連携について</h3>
                    <div class="mt-2 text-sm text-blue-700 dark:text-blue-300">
                        <p>Discord連携とHarukoto Project連携は必須のため解除できません。その他の外部サービスとの連携により、より便利な機能を利用できます。</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
