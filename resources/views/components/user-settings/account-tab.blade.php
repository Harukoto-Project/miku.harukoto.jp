<div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 border border-gray-200 dark:border-gray-700">
    <div class="mb-6">
        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">アカウント設定</h3>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">アカウントの基本情報やセキュリティ設定を行います</p>
    </div>
    
    <div class="space-y-6">
        <!-- 基本情報セクション -->
        <div>
            <h4 class="text-md font-medium text-gray-900 dark:text-gray-100 mb-4">基本情報</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">表示名</label>
                    <input type="text" value="{{ $user->discord_display_name ?? $user->name }}" 
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-gray-100 dark:bg-gray-600 text-gray-500 dark:text-gray-400" 
                           readonly>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Discordから取得された表示名（変更不可）</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">ユーザー名</label>
                    <input type="text" value="{{ $user->discord_username }}" 
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-gray-100 dark:bg-gray-600 text-gray-500 dark:text-gray-400" 
                           readonly>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Discordから取得されたユーザー名（変更不可）</p>
                </div>
            </div>
        </div>

        <!-- セキュリティセクション -->
        <div>
            <h4 class="text-md font-medium text-gray-900 dark:text-gray-100 mb-4">セキュリティ</h4>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">メールアドレス</label>
                    <input type="email" value="{{ $user->email }}" 
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-gray-100 dark:bg-gray-600 text-gray-500 dark:text-gray-400" 
                           readonly>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Discordから取得されたメールアドレス（変更不可）</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Discord ID</label>
                    <input type="text" value="{{ $user->discord_id }}" 
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-gray-100 dark:bg-gray-600 text-gray-500 dark:text-gray-400" 
                           readonly>
                </div>
            </div>
        </div>

        <!-- 保存ボタン -->
        <div class="pt-4">
            <button type="button" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800">
                設定を保存
            </button>
        </div>
    </div>
</div>
