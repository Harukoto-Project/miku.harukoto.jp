<div class="space-y-6">
    <div class="text-center mb-8">
        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">連携済みサービス</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400">現在連携されているサービス一覧です</p>
    </div>


    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-6">
        <div class="flex items-start space-x-4">
            <div class="flex-shrink-0">
                <div class="w-12 h-12 bg-blue-100 dark:bg-blue-800 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M20.317 4.37a19.791 19.791 0 0 0-4.885-1.515a.074.074 0 0 0-.079.037c-.21.375-.444.864-.608 1.25a18.27 18.27 0 0 0-5.487 0a12.64 12.64 0 0 0-.617-1.25a.077.077 0 0 0-.079-.037A19.736 19.736 0 0 0 3.677 4.37a.07.07 0 0 0-.032.027C.533 9.046-.32 13.58.099 18.057a.082.082 0 0 0 .031.057a19.9 19.9 0 0 0 5.993 3.03a.078.078 0 0 0 .084-.028a14.09 14.09 0 0 0 1.226-1.994a.076.076 0 0 0-.041-.106a13.107 13.107 0 0 1-1.872-.892a.077.077 0 0 1-.008-.128a10.2 10.2 0 0 0 .372-.292a.074.074 0 0 1 .077-.01c3.928 1.793 8.18 1.793 12.062 0a.074.074 0 0 1 .078.01c.12.098.246.198.373.292a.077.077 0 0 1-.006.127a12.81 12.81 0 0 1-1.873.892a.077.077 0 0 0-.041.107c.36.698.772 1.362 1.225 1.993a.076.076 0 0 0 .084.028a19.839 19.839 0 0 0 6.002-3.03a.077.077 0 0 0 .032-.054c.5-5.177-.838-9.674-3.549-13.66a.061.061 0 0 0-.031-.03zM8.02 15.33c-1.183 0-2.157-1.085-2.157-2.419c0-1.333.956-2.419 2.157-2.419c1.21 0 2.176 1.096 2.157 2.42c0 1.333-.956 2.418-2.157 2.418zm7.975 0c-1.183 0-2.157-1.085-2.157-2.419c0-1.333.955-2.419 2.157-2.419c1.21 0 2.176 1.096 2.157 2.42c0 1.333-.946 2.418-2.157 2.418z" />
                    </svg>
                </div>
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between">
                    <div>
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Discord</h4>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Discordアカウントと連携中</p>
                    </div>
                    <span
                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                        連携中
                    </span>
                </div>

                <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">
                            Discord ID
                        </label>
                        <p
                            class="text-sm text-gray-900 dark:text-gray-100 font-mono bg-white dark:bg-gray-800 px-3 py-2 rounded-md border border-blue-200 dark:border-blue-700">
                            {{ $user->discord_id ?? '未連携' }}
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">
                            ユーザー名
                        </label>
                        <p
                            class="text-sm text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-800 px-3 py-2 rounded-md border border-blue-200 dark:border-blue-700">
                            {{ $user->discord_username ?? '未設定' }}
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">
                            表示名
                        </label>
                        <p
                            class="text-sm text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-800 px-3 py-2 rounded-md border border-blue-200 dark:border-blue-700">
                            {{ $user->discord_id ?? '未設定' }}
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">
                            連携日時
                        </label>
                        <p
                            class="text-sm text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-800 px-3 py-2 rounded-md border border-blue-200 dark:border-blue-700">
                            {{ $user->created_at->format('Y年m月d日 H:i') }}
                        </p>
                    </div>
                </div>

                <div
                    class="mt-4 p-3 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-md">
                    <div class="flex">
                        <svg class="h-5 w-5 text-yellow-400 flex-shrink-0" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                        </svg>
                        <p class="ml-2 text-sm text-yellow-800 dark:text-yellow-200">
                            <strong>注意:</strong> Discord連携は解除できません。セキュリティ上の理由により、連携解除には管理者の承認が必要です。
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-6">
        <div class="flex items-start space-x-4">
            <div class="flex-shrink-0">
                <div class="w-12 h-12 bg-blue-100 dark:bg-blue-800 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                </div>
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between">
                    <div>
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Harukoto Project</h4>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Harukoto Projectアカウントと自動連携中</p>
                    </div>
                    <span
                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                        自動連携
                    </span>
                </div>

                <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">
                            アカウントID
                        </label>
                        <p
                            class="text-sm text-gray-900 dark:text-gray-100 font-mono bg-white dark:bg-gray-800 px-3 py-2 rounded-md border border-blue-200 dark:border-blue-700">
                            {{ $user->id }}
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">
                            ユーザー名
                        </label>
                        <p
                            class="text-sm text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-800 px-3 py-2 rounded-md border border-blue-200 dark:border-blue-700">
                            {{ $user->discord_display_name ?? $user->name ?? '未設定' }}
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">
                            アカウント作成日
                        </label>
                        <p
                            class="text-sm text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-800 px-3 py-2 rounded-md border border-blue-200 dark:border-blue-700">
                            {{ $user->created_at->format('Y年m月d日 H:i') }}
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">
                            最終更新日
                        </label>
                        <p
                            class="text-sm text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-800 px-3 py-2 rounded-md border border-blue-200 dark:border-blue-700">
                            {{ $user->updated_at->format('Y年m月d日 H:i') }}
                        </p>
                    </div>
                </div>

                <div
                    class="mt-4 p-3 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-md">
                    <div class="flex">
                        <svg class="h-5 w-5 text-yellow-400 flex-shrink-0" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                        </svg>
                        <p class="ml-2 text-sm text-yellow-800 dark:text-yellow-200">
                            <strong>注意:</strong> Harukoto Projectアカウントは自動的に連携され、解除できません。これはシステムの基盤となる重要な連携です。
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6">
        <div class="text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500 mb-4" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <h4 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">連携解除について</h4>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                セキュリティとサービスの安定性を保つため、現在の連携は解除できません。
            </p>
            <p class="text-sm text-gray-500 dark:text-gray-400">
                連携解除が必要な場合は、管理者にお問い合わせください。
            </p>
        </div>
    </div>
</div>