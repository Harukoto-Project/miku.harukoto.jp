<div class="space-y-4">
    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 border-b border-gray-200 dark:border-gray-700 pb-2">
        基本情報（読み取り専用）
    </h3>


    <div>
        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">
            表示名
        </label>
        <p class="text-sm text-gray-900 dark:text-gray-100 bg-gray-50 dark:bg-gray-700 px-3 py-2 rounded-md">
            {{ $user->discord_display_name ?? '未設定' }}
        </p>
    </div>


    <div>
        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">
            メールアドレス
        </label>
        <p class="text-sm text-gray-900 dark:text-gray-100 bg-gray-50 dark:bg-gray-700 px-3 py-2 rounded-md">
            {{ $user->email ?? $user->discord_email }}
        </p>
    </div>
</div>