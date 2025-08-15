<!-- ポートフォリオ削除 -->
<div class="mt-6 pt-4 border-t border-gray-200 dark:border-gray-600">
    <div class="text-center">
        <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">危険な操作</h4>
        <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">
            ポートフォリオを削除すると、すべての設定とデータが完全に削除されます。この操作は取り消せません。
        </p>
        <button type="button" 
                id="delete_portfolio"
                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
            </svg>
            ポートフォリオを削除
        </button>
    </div>
</div>

@include('components.toast')
@include('components.confirm-dialog')
