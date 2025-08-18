<!-- モジュール設定セクション -->
<div class="space-y-6">
    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 border-b border-gray-200 dark:border-gray-700 pb-2">
        モジュール設定
    </h3>
    
    <!-- アチーブメントの詳細設定カード -->
    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm">
        <button class="w-full px-4 py-3 text-left flex items-center justify-between hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200" onclick="toggleAchievementSettings()">
            <span class="text-sm font-medium text-gray-900 dark:text-gray-100">アチーブメントの詳細設定</span>
            <svg id="achievement-arrow" class="w-5 h-5 text-gray-500 dark:text-gray-400 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
        </button>
        <div id="achievement-settings" class="overflow-hidden transition-all duration-300 ease-in-out max-h-0">
            <div class="px-4 pb-6">
                <div class="text-sm text-gray-600 dark:text-gray-400 bg-gray-50 dark:bg-gray-700 px-4 py-6 rounded-md border border-gray-200 dark:border-gray-600">
                    <div class="flex items-center justify-center h-20">
                        <span class="text-gray-500 dark:text-gray-400">開発中</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- フォーム設定カード -->
    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm">
        <button class="w-full px-4 py-3 text-left flex items-center justify-between hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200" onclick="toggleFormSettings()">
            <span class="text-sm font-medium text-gray-900 dark:text-gray-100">フォーム設定</span>
            <svg id="form-arrow" class="w-5 h-5 text-gray-500 dark:text-gray-400 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
        </button>
        <div id="form-settings" class="overflow-hidden transition-all duration-300 ease-in-out max-h-0">
            <div class="px-4 pb-6">
                <div class="text-sm text-gray-600 dark:text-gray-400 bg-gray-50 dark:bg-gray-700 px-4 py-6 rounded-md border border-gray-200 dark:border-gray-600">
                    <div class="flex items-center justify-center h-20">
                        <span class="text-gray-500 dark:text-gray-400">開発中</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function toggleAchievementSettings() {
    const content = document.getElementById('achievement-settings');
    const arrow = document.getElementById('achievement-arrow');
    
    if (content.classList.contains('max-h-0')) {
        content.classList.remove('max-h-0');
        content.classList.add('max-h-96');
        arrow.classList.add('rotate-180');
    } else {
        content.classList.remove('max-h-96');
        content.classList.add('max-h-0');
        arrow.classList.remove('rotate-180');
    }
}

function toggleFormSettings() {
    const content = document.getElementById('form-settings');
    const arrow = document.getElementById('form-arrow');
    
    if (content.classList.contains('max-h-0')) {
        content.classList.remove('max-h-0');
        content.classList.add('max-h-96');
        arrow.classList.add('rotate-180');
    } else {
        content.classList.remove('max-h-96');
        content.classList.add('max-h-0');
        arrow.classList.remove('rotate-180');
    }
}
</script>
