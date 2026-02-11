<div x-data="levelingSettings()" x-init="init()" class="space-y-8">
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
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">レベル報酬設定</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        レベルに応じて自動的に付与されるロールを設定します
                    </p>
                </div>
                <button
                    @click="addReward()"
                    :disabled="busy || roles.length === 0"
                    class="inline-flex items-center px-3 py-2 bg-green-600 hover:bg-green-700 disabled:bg-green-400 text-white text-sm font-medium rounded-md shadow-sm transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2"
                >
                    <span>報酬を追加</span>
                </button>
            </div>

            <template x-if="rewards.length === 0">
                <div class="rounded-md border border-dashed border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-900 px-4 py-8 text-center">
                    <p class="text-gray-500 dark:text-gray-400 text-sm">
                        レベル報酬が設定されていません。<br>
                        「報酬を追加」ボタンから新しい報酬を追加してください。
                    </p>
                </div>
            </template>

            <template x-if="rewards.length > 0">
                <div class="space-y-3">
                    <template x-for="(reward, index) in rewards" :key="index">
                        <div class="flex flex-col md:flex-row md:items-center gap-3 rounded-md border bg-gray-50 dark:bg-gray-900 border-gray-200 dark:border-gray-700 px-4 py-3">
                            <div class="flex items-center gap-2">
                                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">レベル</label>
                                <input
                                    type="number"
                                    min="1"
                                    x-model.number="reward.level"
                                    @change="normalizeLevel(index)"
                                    :disabled="busy"
                                    class="w-24 px-2 py-1 border border-gray-300 dark:border-gray-600 rounded-md text-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500"
                                >
                            </div>

                            <div class="flex-1 flex items-center gap-2">
                                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">ロール</label>
                                <select
                                    x-model="reward.roleId"
                                    :disabled="busy || roles.length === 0"
                                    class="flex-1 px-2 py-1 border border-gray-300 dark:border-gray-600 rounded-md text-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500"
                                >
                                    <option value="">ロールを選択</option>
                                    <template x-for="role in roles" :key="role.id">
                                        <option :value="role.id" x-text="role.name"></option>
                                    </template>
                                </select>
                            </div>

                            <div class="flex justify-end md:justify-start">
                                <button
                                    @click="removeReward(index)"
                                    :disabled="busy"
                                    class="inline-flex items-center px-2 py-1 bg-red-600 hover:bg-red-700 disabled:bg-red-400 text-white text-xs font-medium rounded-md shadow-sm transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                                >
                                    削除
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
            </template>

            <div x-show="errorMessage" class="mt-4 rounded-md bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-700 px-4 py-2">
                <p class="text-xs text-red-700 dark:text-red-300" x-text="errorMessage"></p>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 border border-gray-200 dark:border-gray-700">
            <div class="font-semibold text-gray-900 dark:text-gray-100 mb-2">
                レベル報酬について
            </div>
            <ul class="mt-2 list-disc list-inside text-sm text-gray-600 dark:text-gray-400 space-y-1">
                <li>ユーザーが指定したレベルに到達した時に、設定したロールが自動的に付与されます。</li>
                <li>同じレベルに複数の報酬を設定することはできません。</li>
                <li>レベルは1以上の数値を設定してください。</li>
            </ul>
        </div>

        <div class="flex justify-end">
            <button
                @click="saveRewards()"
                :disabled="busy"
                class="inline-flex items-center px-6 py-2 bg-indigo-600 hover:bg-indigo-700 disabled:bg-indigo-400 text-white font-medium text-sm rounded-md shadow-sm transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 w-full md:w-auto justify-center"
            >
                <svg x-show="busy" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span x-text="busy ? '保存中...' : '保存'"></span>
            </button>
        </div>

        <div x-show="saveStatus.show" x-transition class="fixed bottom-4 right-4">
            <div :class="saveStatus.type === 'success' ? 'bg-green-500' : 'bg-red-500'"
                 class="text-white px-6 py-3 rounded-lg shadow-lg">
                <span x-text="saveStatus.message"></span>
            </div>
        </div>
    </div>
</div>

<script>
function levelingSettings() {
    return {
        isLoading: true,
        busy: false,
        roles: [],
        rewards: [],
        errorMessage: '',
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
            this.errorMessage = '';
            try {
                const [rolesRes, rewardsRes] = await Promise.all([
                    fetch(`/api/guild/${window.GUILD_ID}/settings/leveling/roles`),
                    fetch(`/api/guild/${window.GUILD_ID}/settings/leveling/rewards`)
                ]);

                if (rolesRes.ok) {
                    const data = await rolesRes.json();
                    this.roles = data.roles || [];
                }

                if (rewardsRes.ok) {
                    const data = await rewardsRes.json();
                    this.rewards = data.rewards || [];
                }
            } catch (error) {
                console.error('レベル報酬設定の読み込みに失敗しました:', error);
                this.showSaveStatus('レベル報酬設定の読み込みに失敗しました。環境変数や接続設定を確認してください。', 'error');
            } finally {
                this.isLoading = false;
            }
        },

        addReward() {
            const firstRole = this.roles[0];
            this.rewards.push({
                level: 1,
                roleId: firstRole ? firstRole.id : ''
            });
        },

        removeReward(index) {
            this.rewards.splice(index, 1);
        },

        normalizeLevel(index) {
            const reward = this.rewards[index];
            if (!reward) return;
            if (!reward.level || reward.level < 1) {
                reward.level = 1;
            }
        },

        async saveRewards() {
            this.busy = true;
            this.errorMessage = '';

            const invalid = this.rewards.filter(r => !r.level || !r.roleId || r.level < 1);
            if (invalid.length > 0) {
                this.errorMessage = 'レベルとロールを正しく設定してください。';
                this.busy = false;
                return;
            }

            const levels = this.rewards.map(r => r.level);
            if (levels.length !== new Set(levels).size) {
                this.errorMessage = '同じレベルに複数の報酬を設定することはできません。';
                this.busy = false;
                return;
            }

            try {
                const response = await fetch(`/api/guild/${window.GUILD_ID}/settings/leveling/rewards`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(this.rewards)
                });

                if (response.ok) {
                    this.showSaveStatus('レベル報酬の設定を更新しました', 'success');
                    this.errorMessage = '';
                } else {
                    let message = 'レベル報酬の設定の更新に失敗しました。';
                    try {
                        const errorData = await response.json();
                        if (errorData.message) {
                            message = errorData.message;
                        }
                    } catch (_) {}
                    this.showSaveStatus(message, 'error');
                }
            } catch (error) {
                console.error('レベル報酬の保存に失敗しました:', error);
                this.showSaveStatus('レベル報酬の保存に失敗しました。ネットワーク接続を確認してください。', 'error');
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
