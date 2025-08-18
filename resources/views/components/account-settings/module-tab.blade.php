<!-- アチーブメント設定セクション -->
<div class="space-y-6" x-data="achievementModule()">
    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 border-b border-gray-200 dark:border-gray-700 pb-2">
        アチーブメント設定
    </h3>
    
    <!-- アチーブメントの詳細設定カード -->
    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm">
        <button class="w-full px-4 py-3 text-left flex items-center justify-between hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200" @click="toggleAchievementSettings()">
            <span class="text-sm font-medium text-gray-900 dark:text-gray-100">アチーブメントの詳細設定</span>
            <svg :class="achievementSettingsOpen ? 'rotate-180' : ''" class="w-5 h-5 text-gray-500 dark:text-gray-400 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
        </button>
        <div x-show="achievementSettingsOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform -translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0" class="px-4 pb-6">
            <!-- 設定フォーム -->
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">アチーブメント獲得の有効化</label>
                        <p class="text-xs text-gray-500 dark:text-gray-400">アチーブメントの獲得を有効にします</p>
                    </div>
                    <button @click="toggleAchievementsEnabled()" :class="settings.achievementsEnabled ? 'bg-indigo-600' : 'bg-gray-200 dark:bg-gray-700'" class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors duration-200">
                        <span :class="settings.achievementsEnabled ? 'translate-x-6' : 'translate-x-1'" class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform duration-200"></span>
                    </button>
                </div>
                
                <div class="flex items-center justify-between">
                    <div>
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">アチーブメント獲得通知の有効化</label>
                        <p class="text-xs text-gray-500 dark:text-gray-400">アチーブメント獲得時の通知を有効にします</p>
                    </div>
                    <button @click="toggleNotificationsEnabled()" :class="settings.notificationsEnabled ? 'bg-indigo-600' : 'bg-gray-200 dark:bg-gray-700'" class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors duration-200">
                        <span :class="settings.notificationsEnabled ? 'translate-x-6' : 'translate-x-1'" class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform duration-200"></span>
                    </button>
                </div>
                
                <div class="pt-2">
                    <button @click="saveSettings()" :disabled="saving" class="w-full bg-indigo-600 hover:bg-indigo-700 disabled:bg-indigo-400 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors duration-200">
                        <span x-show="!saving">設定を保存</span>
                        <span x-show="saving" class="flex items-center justify-center">
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            保存中...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- アチーブメント一覧カード -->
    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm">
        <button class="w-full px-4 py-3 text-left flex items-center justify-between hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200" @click="toggleAchievementList()">
            <span class="text-sm font-medium text-gray-900 dark:text-gray-100">アチーブメント一覧</span>
            <svg :class="achievementListOpen ? 'rotate-180' : ''" class="w-5 h-5 text-gray-500 dark:text-gray-400 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
        </button>
        <div x-show="achievementListOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform -translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0" class="px-4 pb-6">
            <!-- タブ切り替え -->
            <div class="border-b border-gray-200 dark:border-gray-700 mb-4">
                <nav class="flex space-x-8">
                    <button @click="activeTab = 'unlocked'" :class="activeTab === 'unlocked' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300'" class="py-2 px-1 border-b-2 font-medium text-sm transition-colors duration-200">
                        獲得済み
                    </button>
                    <button @click="activeTab = 'locked'" :class="activeTab === 'locked' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300'" class="py-2 px-1 border-b-2 font-medium text-sm transition-colors duration-200">
                        未獲得
                    </button>
                </nav>
            </div>

            <!-- アチーブメント一覧 -->
            <div x-show="activeTab === 'unlocked'" class="space-y-3">
                <div x-show="loading" class="flex items-center justify-center py-8">
                    <svg class="animate-spin h-8 w-8 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
                
                <div x-show="!loading && unlockedAchievements.length === 0" class="text-center py-8">
                    <p class="text-gray-500 dark:text-gray-400">獲得済みのアチーブメントはありません</p>
                </div>
                
                <template x-for="achievement in unlockedAchievements" :key="achievement.achievement.id">
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 border border-gray-200 dark:border-gray-600">
                        <div class="flex items-center space-x-3">
                            <div class="text-2xl" x-text="achievement.tier.badge || '🏆'"></div>
                            <div class="flex-1">
                                <h4 class="text-sm font-medium text-gray-900 dark:text-gray-100" x-text="achievement.achievement.name"></h4>
                                <p class="text-xs text-gray-600 dark:text-gray-400" x-text="achievement.achievement.description"></p>
                                <p class="text-xs text-indigo-600 dark:text-indigo-400 mt-1" x-text="achievement.tier.title"></p>
                            </div>
                            <div class="text-right">
                                <div class="text-xs text-gray-500 dark:text-gray-400" x-text="'進行度: ' + achievement.currentProgress"></div>
                                <div class="text-xs text-gray-500 dark:text-gray-400" x-text="'獲得日: ' + new Date(achievement.unlockedAt).toLocaleDateString('ja-JP')"></div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            <div x-show="activeTab === 'locked'" class="space-y-3">
                <div x-show="loading" class="flex items-center justify-center py-8">
                    <svg class="animate-spin h-8 w-8 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
                
                <div x-show="!loading && lockedAchievements.length === 0" class="text-center py-8">
                    <p class="text-gray-500 dark:text-gray-400">未獲得のアチーブメントはありません</p>
                </div>
                
                <template x-for="achievement in lockedAchievements" :key="achievement.achievement.id">
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 border border-gray-200 dark:border-gray-600">
                        <div class="flex items-center space-x-3">
                            <div class="text-2xl opacity-50" x-text="achievement.nextTier.badge || '🔒'"></div>
                            <div class="flex-1">
                                <h4 class="text-sm font-medium text-gray-900 dark:text-gray-100" x-text="achievement.achievement.name"></h4>
                                <p class="text-xs text-gray-600 dark:text-gray-400" x-text="achievement.achievement.description"></p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1" x-text="achievement.nextTier.title"></p>
                            </div>
                            <div class="text-right">
                                <div class="text-xs text-gray-500 dark:text-gray-400" x-text="'進行度: ' + achievement.progress"></div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <!-- 保存状態表示 -->
    <div x-show="saveStatus.show" x-transition class="fixed bottom-4 right-4">
        <div :class="saveStatus.type === 'success' ? 'bg-green-500' : 'bg-red-500'" 
             class="text-white px-6 py-3 rounded-lg shadow-lg">
            <span x-text="saveStatus.message"></span>
        </div>
    </div>
</div>

<script>
function achievementModule() {
    return {
        achievementSettingsOpen: false,
        achievementListOpen: false,
        activeTab: 'unlocked',
        loading: false,
        saving: false,
        settings: {
            achievementsEnabled: true,
            notificationsEnabled: true
        },
        unlockedAchievements: [],
        lockedAchievements: [],
        saveStatus: {
            show: false,
            message: '',
            type: ''
        },

        init() {
            this.loadSettings();
            this.loadAchievements();
        },

        toggleAchievementSettings() {
            this.achievementSettingsOpen = !this.achievementSettingsOpen;
        },

        toggleAchievementList() {
            this.achievementListOpen = !this.achievementListOpen;
        },

        toggleAchievementsEnabled() {
            this.settings.achievementsEnabled = !this.settings.achievementsEnabled;
        },

        toggleNotificationsEnabled() {
            this.settings.notificationsEnabled = !this.settings.notificationsEnabled;
        },

        async loadSettings() {
            try {
                const response = await fetch('/api/achievements/settings', {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                if (response.ok) {
                    const data = await response.json();
                    if (data.success) {
                        this.settings = data.data;
                    }
                }
            } catch (error) {
                console.error('設定の読み込みに失敗しました:', error);
            }
        },

        async saveSettings() {
            this.saving = true;
            try {
                const response = await fetch('/api/achievements/settings', {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(this.settings)
                });

                if (response.ok) {
                    const data = await response.json();
                    if (data.success) {
                        this.showNotification('設定が保存されました', 'success');
                    }
                }
            } catch (error) {
                console.error('設定の保存に失敗しました:', error);
                this.showNotification('設定の保存に失敗しました', 'error');
            } finally {
                this.saving = false;
            }
        },

        async loadAchievements() {
            this.loading = true;
            try {
                // 獲得済みアチーブメントを読み込み
                const unlockedResponse = await fetch('/api/achievements/unlocked', {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                if (unlockedResponse.ok) {
                    const data = await unlockedResponse.json();
                    if (data.success) {
                        this.unlockedAchievements = data.data;
                    }
                }

                // 未獲得アチーブメントを読み込み
                const lockedResponse = await fetch('/api/achievements/locked', {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                if (lockedResponse.ok) {
                    const data = await lockedResponse.json();
                    if (data.success) {
                        this.lockedAchievements = data.data;
                    }
                }
            } catch (error) {
                console.error('アチーブメントの読み込みに失敗しました:', error);
            } finally {
                this.loading = false;
            }
        },

        showNotification(message, type) {
            this.saveStatus.message = message;
            this.saveStatus.type = type;
            this.saveStatus.show = true;
            setTimeout(() => {
                this.saveStatus.show = false;
            }, 3000); // 3秒後に非表示
        }
    }
}
</script>
