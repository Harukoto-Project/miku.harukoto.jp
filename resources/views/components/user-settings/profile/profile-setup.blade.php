<!-- ポートフォリオ未設定の場合 -->
<div class="text-center py-12">
    <div class="mx-auto h-24 w-24 text-gray-400 mb-6">
        <svg class="h-full w-full" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
        </svg>
    </div>
    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">ポートフォリオが未設定です</h3>
    <p class="text-gray-500 dark:text-gray-400 mb-6">ポートフォリオを作成して、あなたの情報を共有しましょう。</p>
    <button type="button" id="start_setup" onclick="startProfileSetup()" data-discord-display-name="{{ $user->discord_display_name ?? $user->name }}" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
        </svg>
        セットアップを開始
    </button>
</div>

@include('components.toast')
