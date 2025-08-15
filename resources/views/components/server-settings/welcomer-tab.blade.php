<div x-data="welcomerSettings()" x-init="init()" class="space-y-8">
    <!-- ローディング・スケルトン -->
    <template x-if="isLoading">
        <div class="space-y-8 animate-pulse">
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 border border-gray-200 dark:border-gray-700">
                <div class="h-6 bg-gray-200 dark:bg-gray-700 rounded w-40 mb-4"></div>
                <div class="space-y-4">
                    <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded"></div>
                    <div class="h-10 bg-gray-200 dark:bg-gray-700 rounded"></div>
                    <div class="h-20 bg-gray-200 dark:bg-gray-700 rounded"></div>
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 border border-gray-200 dark:border-gray-700">
                <div class="h-6 bg-gray-200 dark:bg-gray-700 rounded w-48 mb-4"></div>
                <div class="space-y-4">
                    <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded"></div>
                    <div class="h-10 bg-gray-200 dark:bg-gray-700 rounded"></div>
                    <div class="h-20 bg-gray-200 dark:bg-gray-700 rounded"></div>
                </div>
            </div>
        </div>
    </template>

    <div x-show="!isLoading" x-transition.opacity.scale class="space-y-8">
        <!-- 参加時の設定 -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">参加時のメッセージ</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">新しいメンバーが参加した際に送信されるメッセージを設定します</p>
                </div>
                <div class="flex items-center">
                    <input 
                        type="checkbox" 
                        x-model="joinSettings.isEnabled"
                        :disabled="busy"
                        class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                    <label class="ml-2 text-sm text-gray-700 dark:text-gray-300">有効にする</label>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        チャンネル
                    </label>
                    <select
                        x-model="joinSettings.channelId"
                        :disabled="busy"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white">
                        <option value="" disabled>チャンネルを選択してください</option>
                        <template x-for="ch in channels" :key="ch.id">
                            <option :value="ch.id" x-text="'#' + ch.name"></option>
                        </template>
                    </select>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                        メッセージを送信するテキストチャンネルを選択してください
                    </p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        メッセージ内容
                    </label>
                    <textarea 
                        x-model="joinSettings.message"
                        :disabled="busy"
                        rows="3"
                        placeholder="例: ようこそ {user} さん！"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white"></textarea>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                        使用可能な変数: {user} (ユーザー名), {mention} (ユーザーメンション)
                    </p>
                </div>

                <!-- 保存ボタン -->
                <div class="flex justify-end">
                    <button 
                        @click="updateJoinSettings()"
                        :disabled="busy"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 text-white font-medium rounded-md shadow-sm transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        <svg x-show="busy" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span x-text="busy ? '保存中...' : '参加時設定を保存'"></span>
                    </button>
                </div>
            </div>
        </div>

        <!-- 退出時の設定 -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">退出時のメッセージ</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">メンバーが退出した際に送信されるメッセージを設定します</p>
                </div>
                <div class="flex items-center">
                    <input 
                        type="checkbox" 
                        x-model="leaveSettings.isEnabled"
                        :disabled="busy"
                        class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                    <label class="ml-2 text-sm text-gray-700 dark:text-gray-300">有効にする</label>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        チャンネル
                    </label>
                    <select
                        x-model="leaveSettings.channelId"
                        :disabled="busy"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white">
                        <option value="" disabled>チャンネルを選択してください</option>
                        <template x-for="ch in channels" :key="ch.id">
                            <option :value="ch.id" x-text="'#' + ch.name"></option>
                        </template>
                    </select>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                        メッセージを送信するテキストチャンネルを選択してください
                    </p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        メッセージ内容
                    </label>
                    <textarea 
                        x-model="leaveSettings.message"
                        :disabled="busy"
                        rows="3"
                        placeholder="例: {user} さんが退出しました。また来てね！"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white"></textarea>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                        使用可能な変数: {user} (ユーザー名)
                    </p>
                </div>

                <!-- 保存ボタン -->
                <div class="flex justify-end">
                    <button 
                        @click="updateLeaveSettings()"
                        :disabled="busy"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 text-white font-medium rounded-md shadow-sm transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        <svg x-show="busy" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span x-text="busy ? '保存中...' : '退出時設定を保存'"></span>
                    </button>
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
</div>

<script>
function welcomerSettings() {
    return {
        isLoading: true,
        busy: false,
        channels: [],
        joinSettings: {
            channelId: '',
            message: 'ようこそ {user} さん！',
            isEnabled: false
        },
        leaveSettings: {
            channelId: '',
            message: '{user} さんが退出しました。また来てね！',
            isEnabled: false
        },
        saveStatus: {
            show: false,
            type: 'success',
            message: ''
        },

        async init() {
            await this.loadAll();
        },

        async loadAll() {
            this.isLoading = true;
            try {
                const [channelsRes, settingsRes] = await Promise.all([
                    fetch(`/api/guild/${window.GUILD_ID}/settings/welcomer/channels`),
                    fetch(`/api/guild/${window.GUILD_ID}/settings/welcomer`)
                ]);
                if (channelsRes.ok) {
                    const ch = await channelsRes.json();
                    this.channels = ch.channels || [];
                }
                if (settingsRes.ok) {
                    const data = await settingsRes.json();
                    this.joinSettings = data.onJoin || this.joinSettings;
                    this.leaveSettings = data.onLeave || this.leaveSettings;
                }
            } catch (error) {
                console.error('設定の読み込みに失敗しました:', error);
                this.showSaveStatus('設定の読み込みに失敗しました。環境変数の設定を確認してください。', 'error');
            } finally {
                this.isLoading = false;
            }
        },

        async updateJoinSettings() {
            this.busy = true;
            try {
                const response = await fetch(`/api/guild/${window.GUILD_ID}/settings/welcomer/join`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(this.joinSettings)
                });

                if (response.ok) {
                    this.showSaveStatus('参加時の設定を更新しました', 'success');
                } else {
                    const errorData = await response.json();
                    this.showSaveStatus(`設定の更新に失敗しました: ${errorData.message || '不明なエラー'}`, 'error');
                }
            } catch (error) {
                console.error('設定の更新に失敗しました:', error);
                this.showSaveStatus('設定の更新に失敗しました。ネットワーク接続を確認してください。', 'error');
            } finally {
                this.busy = false;
            }
        },

        async updateLeaveSettings() {
            this.busy = true;
            try {
                const response = await fetch(`/api/guild/${window.GUILD_ID}/settings/welcomer/leave`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(this.leaveSettings)
                });

                if (response.ok) {
                    this.showSaveStatus('退出時の設定を更新しました', 'success');
                } else {
                    const errorData = await response.json();
                    this.showSaveStatus(`設定の更新に失敗しました: ${errorData.message || '不明なエラー'}`, 'error');
                }
            } catch (error) {
                console.error('設定の更新に失敗しました:', error);
                this.showSaveStatus('設定の更新に失敗しました。ネットワーク接続を確認してください。', 'error');
            } finally {
                this.busy = false;
            }
        },

        showSaveStatus(message, type) {
            this.saveStatus = {
                show: true,
                type: type,
                message: message
            };

            setTimeout(() => {
                this.saveStatus.show = false;
            }, 5000);
        }
    }
}
</script>
