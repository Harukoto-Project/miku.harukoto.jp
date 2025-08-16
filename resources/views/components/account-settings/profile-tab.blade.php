<div class="space-y-6" x-data="profileForm()">
    <div>
        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">プロフィール設定</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">あなたのポートフォリオページで表示される情報を設定できます</p>
    </div>

    <form @submit.prevent="updateProfile" class="space-y-6">
        <!-- 表示名 -->
        <div>
            <label for="display_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                表示名
            </label>
            <input 
                type="text" 
                id="display_name" 
                name="display_name" 
                x-model="form.display_name"
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-gray-100"
                placeholder="あなたの表示名を入力してください"
            >
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">この名前がポートフォリオページに表示されます</p>
        </div>

        <!-- 自己紹介 -->
        <div>
            <label for="bio" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                自己紹介
            </label>
            <textarea 
                id="bio" 
                name="bio" 
                rows="4"
                x-model="form.bio"
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-gray-100"
                placeholder="あなたについて簡単に紹介してください"
            ></textarea>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">最大1000文字まで入力できます</p>
        </div>

        <!-- 代名詞 -->
        <div>
            <label for="pronouns" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                代名詞
            </label>
            <input 
                type="text" 
                id="pronouns" 
                name="pronouns" 
                x-model="form.pronouns"
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-gray-100"
                placeholder="例: 彼/彼女/彼ら"
            >
        </div>

        <!-- 場所 -->
        <div>
            <label for="location" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                場所
            </label>
            <input 
                type="text" 
                id="location" 
                name="location" 
                x-model="form.location"
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-gray-100"
                placeholder="例: Osaka, Japan"
            >
        </div>

        <!-- テーマカラー -->
        <div>
            <label for="theme_color" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                テーマカラー
            </label>
            <div class="flex items-center space-x-3">
                <input 
                    type="color" 
                    id="theme_color" 
                    name="theme_color" 
                    x-model="form.theme_color"
                    class="h-10 w-20 border border-gray-300 dark:border-gray-600 rounded-md cursor-pointer"
                >
                <input 
                    type="text" 
                    x-model="form.theme_color"
                    class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-gray-100"
                    placeholder="#3B82F6"
                >
            </div>
        </div>

        <!-- テーマエディター -->
        <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
            <div class="mb-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">テーマエディター</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">プロフィールページの色をカスタマイズできます</p>
            </div>

            <!-- プリセットテーマ -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">プリセットテーマ</label>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                    <button 
                        type="button" 
                        @click="applyPresetTheme('default')"
                        class="group relative overflow-hidden rounded-lg p-3 text-left transition-all hover:scale-105"
                        style="background: linear-gradient(45deg, #3B82F6 0%, #1E40AF 100%)"
                    >
                        <div class="relative z-10">
                            <div class="text-xs font-medium text-white">デフォルト</div>
                            <div class="mt-1 flex gap-1">
                                <div class="h-3 w-3 rounded-full border border-white/30" style="background: #3B82F6"></div>
                                <div class="h-3 w-3 rounded-full border border-white/30" style="background: #FFFFFF"></div>
                                <div class="h-3 w-3 rounded-full border border-white/30" style="background: #E2E8F0"></div>
                            </div>
                        </div>
                    </button>
                    <button 
                        type="button" 
                        @click="applyPresetTheme('dark')"
                        class="group relative overflow-hidden rounded-lg p-3 text-left transition-all hover:scale-105"
                        style="background: linear-gradient(45deg, #1f2937 0%, #374151 100%)"
                    >
                        <div class="relative z-10">
                            <div class="text-xs font-medium text-white">ダーク</div>
                            <div class="mt-1 flex gap-1">
                                <div class="h-3 w-3 rounded-full border border-white/30" style="background: #1f2937"></div>
                                <div class="h-3 w-3 rounded-full border border-white/30" style="background: #f9fafb"></div>
                                <div class="h-3 w-3 rounded-full border border-white/30" style="background: #d1d5db"></div>
                            </div>
                        </div>
                    </button>
                    <button 
                        type="button" 
                        @click="applyPresetTheme('ocean')"
                        class="group relative overflow-hidden rounded-lg p-3 text-left transition-all hover:scale-105"
                        style="background: linear-gradient(135deg, #0ea5e9 0%, #06b6d4 100%)"
                    >
                        <div class="relative z-10">
                            <div class="text-xs font-medium text-white">オーシャン</div>
                            <div class="mt-1 flex gap-1">
                                <div class="h-3 w-3 rounded-full border border-white/30" style="background: #0ea5e9"></div>
                                <div class="h-3 w-3 rounded-full border border-white/30" style="background: #ffffff"></div>
                                <div class="h-3 w-3 rounded-full border border-white/30" style="background: #e0f2fe"></div>
                            </div>
                        </div>
                    </button>
                    <button 
                        type="button" 
                        @click="applyPresetTheme('sunset')"
                        class="group relative overflow-hidden rounded-lg p-3 text-left transition-all hover:scale-105"
                        style="background: linear-gradient(45deg, #f97316 0%, #dc2626 50%, #9333ea 100%)"
                    >
                        <div class="relative z-10">
                            <div class="text-xs font-medium text-white">サンセット</div>
                            <div class="mt-1 flex gap-1">
                                <div class="h-3 w-3 rounded-full border border-white/30" style="background: #f97316"></div>
                                <div class="h-3 w-3 rounded-full border border-white/30" style="background: #ffffff"></div>
                                <div class="h-3 w-3 rounded-full border border-white/30" style="background: #fed7aa"></div>
                            </div>
                        </div>
                    </button>
                </div>
            </div>

            <!-- カラー設定 -->
            <div class="space-y-6">
                <div class="grid gap-6 md:grid-cols-2">
                    <!-- カード背景色 -->
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">カード背景色</label>
                            <div class="flex items-center gap-2">
                                <span class="text-xs text-gray-500">グラデーション</span>
                                <button 
                                    type="button" 
                                    @click="toggleGradient('card')"
                                    :class="form.card_gradient_enabled ? 'bg-blue-600' : 'bg-gray-300 dark:bg-gray-600'"
                                    class="relative inline-flex h-5 w-9 items-center rounded-full transition-colors cursor-pointer"
                                >
                                    <span 
                                        :class="form.card_gradient_enabled ? 'translate-x-5' : 'translate-x-1'"
                                        class="inline-block h-3 w-3 transform rounded-full bg-white transition-transform"
                                    ></span>
                                </button>
                            </div>
                        </div>

                        <!-- プレビュー -->
                        <div 
                            class="h-8 w-full rounded border border-gray-300 dark:border-gray-600"
                            :style="getGradientStyle('card')"
                        ></div>

                        <!-- 単色設定 -->
                        <div x-show="!form.card_gradient_enabled" class="space-y-2">
                            <div class="flex items-center space-x-2">
                                <input type="color" x-model="form.card_background_color" class="h-8 w-16 border border-gray-300 dark:border-gray-600 rounded cursor-pointer">
                                <input type="text" x-model="form.card_background_color" class="flex-1 px-2 py-1 text-sm border border-gray-300 dark:border-gray-600 rounded focus:ring-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-gray-100" placeholder="#3B82F6">
                            </div>
                        </div>

                        <!-- グラデーション設定 -->
                        <div x-show="form.card_gradient_enabled" class="space-y-3">
                            <!-- 方向設定 -->
                            <div>
                                <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">
                                    方向: <span x-text="form.card_gradient_direction"></span>°
                                </label>
                                <input 
                                    type="range" 
                                    x-model="form.card_gradient_direction" 
                                    min="0" 
                                    max="360" 
                                    step="1"
                                    class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer dark:bg-gray-700"
                                >
                                <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    <span>0°</span>
                                    <span>90°</span>
                                    <span>180°</span>
                                    <span>270°</span>
                                    <span>360°</span>
                                </div>
                            </div>

                            <!-- カラーストップ -->
                            <div class="space-y-2">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs text-gray-600 dark:text-gray-400">
                                        カラーストップ (<span x-text="getGradientStops('card').length"></span>/5)
                                    </span>
                                    <button 
                                        x-show="getGradientStops('card').length < 5"
                                        @click="addGradientStop('card')"
                                        class="flex items-center gap-1 text-xs text-blue-600 hover:text-blue-700"
                                    >
                                        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                        </svg>
                                        追加
                                    </button>
                                </div>

                                <template x-for="(stop, index) in getGradientStops('card')" :key="stop.id">
                                    <div class="flex items-center gap-2">
                                        <svg class="h-3 w-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                                        </svg>
                                        <input 
                                            type="color" 
                                            x-model="stop.color" 
                                            class="h-6 w-12 cursor-pointer rounded border border-gray-300 dark:border-gray-600"
                                        >
                                        <input 
                                            type="number" 
                                            min="0" 
                                            max="100" 
                                            x-model="stop.position"
                                            class="w-16 rounded border border-gray-300 px-2 py-1 text-xs dark:border-gray-600 dark:bg-gray-700"
                                            placeholder="%"
                                        >
                                        <span class="text-xs text-gray-500">%</span>
                                        <button 
                                            x-show="getGradientStops('card').length > 2"
                                            @click="removeGradientStop('card', stop.id)"
                                            class="text-red-500 hover:text-red-700"
                                        >
                                            <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                    <!-- ページ背景色 -->
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">ページ背景色</label>
                            <div class="flex items-center gap-2">
                                <span class="text-xs text-gray-500">グラデーション</span>
                                <button 
                                    type="button" 
                                    @click="toggleGradient('page')"
                                    :class="form.page_gradient_enabled ? 'bg-blue-600' : 'bg-gray-300 dark:bg-gray-600'"
                                    class="relative inline-flex h-5 w-9 items-center rounded-full transition-colors cursor-pointer"
                                >
                                    <span 
                                        :class="form.page_gradient_enabled ? 'translate-x-5' : 'translate-x-1'"
                                        class="inline-block h-3 w-3 transform rounded-full bg-white transition-transform"
                                    ></span>
                                </button>
                            </div>
                        </div>

                        <!-- プレビュー -->
                        <div 
                            class="h-8 w-full rounded border border-gray-300 dark:border-gray-600"
                            :style="getGradientStyle('page')"
                        ></div>

                        <!-- 単色設定 -->
                        <div x-show="!form.page_gradient_enabled" class="space-y-2">
                            <div class="flex items-center space-x-2">
                                <input type="color" x-model="form.page_background_color" class="h-8 w-16 border border-gray-300 dark:border-gray-600 rounded cursor-pointer">
                                <input type="text" x-model="form.page_background_color" class="flex-1 px-2 py-1 text-sm border border-gray-300 dark:border-gray-600 rounded focus:ring-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-gray-100" placeholder="#F9FAFB">
                            </div>
                        </div>

                        <!-- グラデーション設定 -->
                        <div x-show="form.page_gradient_enabled" class="space-y-3">
                            <!-- 方向設定 -->
                            <div>
                                <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">
                                    方向: <span x-text="form.page_gradient_direction"></span>°
                                </label>
                                <input 
                                    type="range" 
                                    x-model="form.page_gradient_direction" 
                                    min="0" 
                                    max="360" 
                                    step="1"
                                    class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer dark:bg-gray-700"
                                >
                                <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    <span>0°</span>
                                    <span>90°</span>
                                    <span>180°</span>
                                    <span>270°</span>
                                    <span>360°</span>
                                </div>
                            </div>

                            <!-- カラーストップ -->
                            <div class="space-y-2">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs text-gray-600 dark:text-gray-400">
                                        カラーストップ (<span x-text="getGradientStops('page').length"></span>/5)
                                    </span>
                                    <button 
                                        x-show="getGradientStops('page').length < 5"
                                        @click="addGradientStop('page')"
                                        class="flex items-center gap-1 text-xs text-blue-600 hover:text-blue-700"
                                    >
                                        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                        </svg>
                                        追加
                                    </button>
                                </div>

                                <template x-for="(stop, index) in getGradientStops('page')" :key="stop.id">
                                    <div class="flex items-center gap-2">
                                        <svg class="h-3 w-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                                        </svg>
                                        <input 
                                            type="color" 
                                            x-model="stop.color" 
                                            class="h-6 w-12 cursor-pointer rounded border border-gray-300 dark:border-gray-600"
                                        >
                                        <input 
                                            type="number" 
                                            min="0" 
                                            max="100" 
                                            x-model="stop.position"
                                            class="w-16 rounded border border-gray-300 px-2 py-1 text-xs dark:border-gray-600 dark:bg-gray-700"
                                            placeholder="%"
                                        >
                                        <span class="text-xs text-gray-500">%</span>
                                        <button 
                                            x-show="getGradientStops('page').length > 2"
                                            @click="removeGradientStop('page', stop.id)"
                                            class="text-red-500 hover:text-red-700"
                                        >
                                            <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                    <!-- 自己紹介カード色 -->
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">自己紹介カード色</label>
                            <div class="flex items-center gap-2">
                                <span class="text-xs text-gray-500">グラデーション</span>
                                <button 
                                    type="button" 
                                    @click="toggleGradient('bio_card')"
                                    :class="form.bio_card_gradient_enabled ? 'bg-blue-600' : 'bg-gray-300 dark:bg-gray-600'"
                                    class="relative inline-flex h-5 w-9 items-center rounded-full transition-colors cursor-pointer"
                                >
                                    <span 
                                        :class="form.bio_card_gradient_enabled ? 'translate-x-5' : 'translate-x-1'"
                                        class="inline-block h-3 w-3 transform rounded-full bg-white transition-transform"
                                    ></span>
                                </button>
                            </div>
                        </div>

                        <!-- プレビュー -->
                        <div 
                            class="h-8 w-full rounded border border-gray-300 dark:border-gray-600"
                            :style="getGradientStyle('bio_card')"
                        ></div>

                        <!-- 単色設定 -->
                        <div x-show="!form.bio_card_gradient_enabled" class="space-y-2">
                            <div class="flex items-center space-x-2">
                                <input type="color" x-model="form.bio_card_color" name="bio_card_color" class="h-8 w-16 border border-gray-300 dark:border-gray-600 rounded cursor-pointer" @change="updateBioCardColor">
                                <input type="text" x-model="form.bio_card_color" class="flex-1 px-2 py-1 text-sm border border-gray-300 dark:border-gray-600 rounded focus:ring-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-gray-100" placeholder="#FFFFFF">
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="text-xs text-gray-500 dark:text-gray-400">プレビュー:</span>
                                <div class="w-16 h-8 rounded border border-gray-300 dark:border-gray-600" :style="'background: ' + getBioCardColorWithOpacity()"></div>
                                <span class="text-xs text-gray-500 dark:text-gray-400" x-text="form.bio_card_color + ' (透明度自動調整)'"></span>
                            </div>
                        </div>

                        <!-- グラデーション設定 -->
                        <div x-show="form.bio_card_gradient_enabled" class="space-y-3">
                            <!-- 方向設定 -->
                            <div>
                                <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">
                                    方向: <span x-text="form.bio_card_gradient_direction"></span>°
                                </label>
                                <input 
                                    type="range" 
                                    x-model="form.bio_card_gradient_direction" 
                                    min="0" 
                                    max="360" 
                                    step="1"
                                    class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer dark:bg-gray-700"
                                >
                                <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    <span>0°</span>
                                    <span>90°</span>
                                    <span>180°</span>
                                    <span>270°</span>
                                    <span>360°</span>
                                </div>
                            </div>

                            <!-- カラーストップ -->
                            <div class="space-y-2">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs text-gray-600 dark:text-gray-400">
                                        カラーストップ (<span x-text="getGradientStops('bio_card').length"></span>/5)
                                    </span>
                                    <button 
                                        x-show="getGradientStops('bio_card').length < 5"
                                        @click="addGradientStop('bio_card')"
                                        class="flex items-center gap-1 text-xs text-blue-600 hover:text-blue-700"
                                    >
                                        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                        </svg>
                                        追加
                                    </button>
                                </div>

                                <template x-for="(stop, index) in getGradientStops('bio_card')" :key="stop.id">
                                    <div class="flex items-center gap-2">
                                        <svg class="h-3 w-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                                        </svg>
                                        <input 
                                            type="color" 
                                            x-model="stop.color" 
                                            class="h-6 w-12 cursor-pointer rounded border border-gray-300 dark:border-gray-600"
                                        >
                                        <input 
                                            type="number" 
                                            min="0" 
                                            max="100" 
                                            x-model="stop.position"
                                            class="w-16 rounded border border-gray-300 px-2 py-1 text-xs dark:border-gray-600 dark:bg-gray-700"
                                            placeholder="%"
                                        >
                                        <span class="text-xs text-gray-500">%</span>
                                        <button 
                                            x-show="getGradientStops('bio_card').length > 2"
                                            @click="removeGradientStop('bio_card', stop.id)"
                                            class="text-red-500 hover:text-red-700"
                                        >
                                            <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                    <!-- その他の色設定 -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">プロフィール名色</label>
                        <div class="flex items-center space-x-2">
                            <input type="color" x-model="form.profile_name_color" class="h-8 w-16 border border-gray-300 dark:border-gray-600 rounded cursor-pointer">
                            <input type="text" x-model="form.profile_name_color" class="flex-1 px-2 py-1 text-sm border border-gray-300 dark:border-gray-600 rounded focus:ring-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-gray-100">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">代名詞色</label>
                        <div class="flex items-center space-x-2">
                            <input type="color" x-model="form.pronouns_color" class="h-8 w-16 border border-gray-300 dark:border-gray-600 rounded cursor-pointer">
                            <input type="text" x-model="form.pronouns_color" class="flex-1 px-2 py-1 text-sm border border-gray-300 dark:border-gray-600 rounded focus:ring-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-gray-100">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">ロケーション色</label>
                        <div class="flex items-center space-x-2">
                            <input type="color" x-model="form.location_color" class="h-8 w-16 border border-gray-300 dark:border-gray-600 rounded cursor-pointer">
                            <input type="text" x-model="form.location_color" class="flex-1 px-2 py-1 text-sm border border-gray-300 dark:border-gray-600 rounded focus:ring-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-gray-100">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Discord ID色</label>
                        <div class="flex items-center space-x-2">
                            <input type="color" x-model="form.discord_color" class="h-8 w-16 border border-gray-300 dark:border-gray-600 rounded cursor-pointer">
                            <input type="text" x-model="form.discord_color" class="flex-1 px-2 py-1 text-sm border border-gray-300 dark:border-gray-600 rounded focus:ring-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-gray-100">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">自己紹介文字色</label>
                        <div class="flex items-center space-x-2">
                            <input type="color" x-model="form.bio_text_color" class="h-8 w-16 border border-gray-300 dark:border-gray-600 rounded cursor-pointer">
                            <input type="text" x-model="form.bio_text_color" class="flex-1 px-2 py-1 text-sm border border-gray-300 dark:border-gray-600 rounded focus:ring-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-gray-100">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 公開設定 -->
        <div class="flex items-center">
            <input 
                type="checkbox" 
                id="is_public" 
                name="is_public" 
                x-model="form.is_public"
                class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
            >
            <label for="is_public" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                プロフィールを公開する
            </label>
        </div>

        <!-- 保存ボタン -->
        <div class="flex justify-end space-x-3">
            <button 
                type="submit" 
                :disabled="loading"
                class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-200"
            >
                <span x-show="!loading">保存</span>
                <span x-show="loading">保存中...</span>
            </button>
        </div>
    </form>

    <!-- 自分のプロフィールページを見るボタン -->
    <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">公開用プロフィールページ</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">設定した情報がどのように表示されるかを確認できます</p>
            </div>
            <div class="flex space-x-3">
                <a 
                    :href="'/profile/' + {{ $user->id }}" 
                    target="_blank"
                    class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors duration-200"
                >
                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                    </svg>
                    自分のページを見る
                </a>
                <a 
                    :href="'/profile/' + {{ $user->discord_id }}" 
                    target="_blank"
                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors duration-200"
                >
                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                    </svg>
                    Discord IDで見る
                </a>
            </div>
        </div>
        <div class="mt-3 p-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-md">
            <p class="text-sm text-blue-800 dark:text-blue-200">
                <strong>注意:</strong> プロフィールを公開する設定を有効にしないと、他のユーザーからアクセスできません。
            </p>
        </div>
    </div>

    <!-- 成功メッセージ -->
    <div x-show="success" x-transition class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-md p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm text-green-800 dark:text-green-200" x-text="successMessage"></p>
            </div>
        </div>
    </div>

    <!-- エラーメッセージ -->
    <div x-show="error" x-transition class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-md p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
    </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm text-red-800 dark:text-red-200" x-text="errorMessage"></p>
            </div>
        </div>
    </div>
</div>

<script>
function profileForm() {
    return {
        loading: false,
        success: false,
        error: false,
        successMessage: '',
        errorMessage: '',
        form: {
            display_name: '{{ $user->profile->display_name ?? "" }}',
            bio: '{{ $user->profile->bio ?? "" }}',
            pronouns: '{{ $user->profile->pronouns ?? "" }}',
            location: '{{ $user->profile->location ?? "" }}',
            theme_color: '{{ $user->profile->theme_color ?? "#3B82F6" }}',
            card_background_color: '{{ $user->profile->card_background_color ?? "#3B82F6" }}',
            page_background_color: '{{ $user->profile->page_background_color ?? "#F9FAFB" }}',
            profile_name_color: '{{ $user->profile->profile_name_color ?? "#FFFFFF" }}',
            pronouns_color: '{{ $user->profile->pronouns_color ?? "#E2E8F0" }}',
            location_color: '{{ $user->profile->location_color ?? "#F1F5F9" }}',
            discord_color: '{{ $user->profile->discord_color ?? "#F1F5F9" }}',
            bio_card_color: '{{ $user->profile->bio_card_color ?? "#FFFFFF" }}',
            bio_text_color: '{{ $user->profile->bio_text_color ?? "#FFFFFF" }}',
            is_public: {{ $user->profile->is_public ?? false ? 'true' : 'false' }},
            card_gradient_enabled: {{ $user->profile->card_gradient_enabled ?? false ? 'true' : 'false' }},
            card_gradient_direction: '{{ $user->profile->card_gradient_direction ?? "0deg" }}',
            card_gradient_stops: @json($user->profile->card_gradient_stops ?? []),
            page_gradient_enabled: {{ $user->profile->page_gradient_enabled ?? false ? 'true' : 'false' }},
            page_gradient_direction: '{{ $user->profile->page_gradient_direction ?? "0deg" }}',
            page_gradient_stops: @json($user->profile->page_gradient_stops ?? []),
            bio_card_gradient_enabled: {{ $user->profile->bio_card_gradient_enabled ?? false ? 'true' : 'false' }},
            bio_card_gradient_direction: '{{ $user->profile->bio_card_gradient_direction ?? "0deg" }}',
            bio_card_gradient_stops: @json($user->profile->bio_card_gradient_stops ?? []),
        },
        
        init() {
            
            // グラデーション設定の初期化（文字列の場合はJSON.parseで変換）
            if (typeof this.form.card_gradient_stops === 'string') {
                try {
                    this.form.card_gradient_stops = JSON.parse(this.form.card_gradient_stops);
                } catch (e) {
                    console.error('Error parsing card_gradient_stops:', e);
                    this.form.card_gradient_stops = [];
                }
            }
            if (typeof this.form.page_gradient_stops === 'string') {
                try {
                    this.form.page_gradient_stops = JSON.parse(this.form.page_gradient_stops);
                } catch (e) {
                    console.error('Error parsing page_gradient_stops:', e);
                    this.form.page_gradient_stops = [];
                }
            }
            if (typeof this.form.bio_card_gradient_stops === 'string') {
                try {
                    this.form.bio_card_gradient_stops = JSON.parse(this.form.bio_card_gradient_stops);
                } catch (e) {
                    console.error('Error parsing bio_card_gradient_stops:', e);
                    this.form.bio_card_gradient_stops = [];
                }
            }
            
            // 配列が空または存在しない場合はデフォルト値を設定
            if (!this.form.card_gradient_stops || this.form.card_gradient_stops.length === 0) {
                this.form.card_gradient_stops = [
                    { id: 1, color: '#3B82F6', position: 0 },
                    { id: 2, color: '#1E40AF', position: 100 }
                ];
            }
            
            if (!this.form.page_gradient_stops || this.form.page_gradient_stops.length === 0) {
                this.form.page_gradient_stops = [
                    { id: 1, color: '#F9FAFB', position: 0 },
                    { id: 2, color: '#E5E7EB', position: 100 }
                ];
            }
            
            if (!this.form.bio_card_gradient_stops || this.form.bio_card_gradient_stops.length === 0) {
                this.form.bio_card_gradient_stops = [
                    { id: 1, color: '#FFFFFF', position: 0 },
                    { id: 2, color: '#F3F4F6', position: 100 }
                ];
            }
            
            // グラデーション方向の初期化
            if (!this.form.card_gradient_direction) {
                this.form.card_gradient_direction = '0deg';
            }
            if (!this.form.page_gradient_direction) {
                this.form.page_gradient_direction = '0deg';
            }
            if (!this.form.bio_card_gradient_direction) {
                this.form.bio_card_gradient_direction = '0deg';
            }
            

        },
        
        // テーマテンプレートを適用
        applyPresetTheme(themeName) {
            const themes = {
                default: {
                    card_background_color: '#3B82F6',
                    page_background_color: '#F9FAFB',
                    profile_name_color: '#FFFFFF',
                    pronouns_color: '#E2E8F0',
                    location_color: '#F1F5F9',
                    discord_color: '#F1F5F9',
                    bio_card_color: '#FFFFFF',
                    bio_text_color: '#FFFFFF'
                },
                dark: {
                    card_background_color: '#1f2937',
                    page_background_color: '#1f2937',
                    profile_name_color: '#FFFFFF',
                    pronouns_color: '#d1d5db',
                    location_color: '#374151',
                    discord_color: '#374151',
                    bio_card_color: '#FFFFFF',
                    bio_text_color: '#FFFFFF'
                },
                ocean: {
                    card_background_color: '#0ea5e9',
                    page_background_color: '#e0f2fe',
                    profile_name_color: '#FFFFFF',
                    pronouns_color: '#93c5fd',
                    location_color: '#bae6fd',
                    discord_color: '#bae6fd',
                    bio_card_color: '#FFFFFF',
                    bio_text_color: '#FFFFFF'
                },
                sunset: {
                    card_background_color: '#f97316',
                    page_background_color: '#fef3c7',
                    profile_name_color: '#FFFFFF',
                    pronouns_color: '#fed7aa',
                    location_color: '#fef3c7',
                    discord_color: '#fef3c7',
                    bio_card_color: '#FFFFFF',
                    bio_text_color: '#FFFFFF'
                }
            };
            
            if (themes[themeName]) {
                Object.assign(this.form, themes[themeName]);
                // カラーピッカーの値を同期
                this.$nextTick(() => {
                    this.syncColorPickers();
                });
            }
        },
        
        async updateProfile() {
            this.loading = true;
            this.success = false;
            this.error = false;
            
            // フォームデータを準備
            const formData = {
                ...this.form,
                // グラデーション設定をJSON文字列に変換
                card_gradient_stops: JSON.stringify(this.form.card_gradient_stops || []),
                page_gradient_stops: JSON.stringify(this.form.page_gradient_stops || []),
                bio_card_gradient_stops: JSON.stringify(this.form.bio_card_gradient_stops || [])
            };
            

            
            try {
                const response = await fetch('/profile/update', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(formData)
                });
                

                
                if (response.redirected) {
                    // リダイレクトが発生した場合はページをリロード
                    window.location.reload();
                    return;
                }
                
                const data = await response.json();
                
                if (response.ok) {
                    this.success = true;
                    this.successMessage = data.message;
                    
                    // プロフィール情報を更新（グラデーション設定は復元しない）
                    if (data.profile) {
                        this.form.display_name = data.profile.display_name || '';
                        this.form.bio = data.profile.bio || '';
                        this.form.pronouns = data.profile.pronouns || '';
                        this.form.location = data.profile.location || '';
                        this.form.theme_color = data.profile.theme_color || '#3B82F6';
                        this.form.card_background_color = data.profile.card_background_color || '#3B82F6';
                        this.form.page_background_color = data.profile.page_background_color || '#F9FAFB';
                        this.form.profile_name_color = data.profile.profile_name_color || '#FFFFFF';
                        this.form.pronouns_color = data.profile.pronouns_color || '#E2E8F0';
                        this.form.location_color = data.profile.location_color || '#F1F5F9';
                        this.form.discord_color = data.profile.discord_color || '#F1F5F9';
                        this.form.bio_card_color = data.profile.bio_card_color || '#FFFFFF';
                        this.form.bio_text_color = data.profile.bio_text_color || '#FFFFFF';
                        this.form.is_public = data.profile.is_public || false;
                        this.form.card_gradient_enabled = data.profile.card_gradient_enabled || false;
                        this.form.card_gradient_direction = data.profile.card_gradient_direction || '0deg';
                        this.form.page_gradient_enabled = data.profile.page_gradient_enabled || false;
                        this.form.page_gradient_direction = data.profile.page_gradient_direction || '0deg';
                        this.form.bio_card_gradient_enabled = data.profile.bio_card_gradient_enabled || false;
                        this.form.bio_card_gradient_direction = data.profile.bio_card_gradient_direction || '0deg';
                        

                    }
                    
                    setTimeout(() => this.success = false, 5000);
                } else {
                    this.error = true;
                    this.errorMessage = data.message || 'エラーが発生しました';
                }
            } catch (err) {
                console.error('Error in updateProfile:', err);
                this.error = true;
                this.errorMessage = 'ネットワークエラーが発生しました: ' + err.message;
            } finally {
                this.loading = false;
            }
        },

        updateBioCardColor() {
            // カラーピッカーの値をRGB形式に設定
            const colorPicker = this.$el.querySelector('input[name="bio_card_color"]');
            if (colorPicker) {
                this.form.bio_card_color = colorPicker.value;
            }
        },

        // カラーピッカーの値を同期
        syncColorPickers() {
            // 自己紹介カード色のカラーピッカーを同期
            const bioCardColorPicker = this.$el.querySelector('input[name="bio_card_color"]');
            if (bioCardColorPicker && this.form.bio_card_color.startsWith('rgba')) {
                // rgba形式からhex形式に変換
                const rgbaMatch = this.form.bio_card_color.match(/rgba?\((\d+),\s*(\d+),\s*(\d+)/);
                if (rgbaMatch) {
                    const r = parseInt(rgbaMatch[1]);
                    const g = parseInt(rgbaMatch[2]);
                    const b = parseInt(rgbaMatch[3]);
                    const hexColor = '#' + ((1 << 24) + (r << 16) + (g << 8) + b).toString(16).slice(1);
                    bioCardColorPicker.value = hexColor;
                }
            }
        },

        getBioCardColorWithOpacity() {
            const color = this.form.bio_card_color;
            if (color.startsWith('#')) {
                // hex形式からRGBに変換
                const r = parseInt(color.substr(1, 2), 16);
                const g = parseInt(color.substr(3, 2), 16);
                const b = parseInt(color.substr(5, 2), 16);
                
                // 背景色の明度に応じて透明度を自動調整
                const brightness = (r * 299 + g * 587 + b * 114) / 1000;
                let opacity = 0.1; // デフォルト透明度
                
                if (brightness > 200) {
                    // 明るい色の場合は透明度を上げる
                    opacity = 0.2;
                } else if (brightness < 100) {
                    // 暗い色の場合は透明度を下げる
                    opacity = 0.05;
                }
                
                return `rgba(${r}, ${g}, ${b}, ${opacity})`;
            } else if (color.startsWith('rgb')) {
                // rgb形式の場合
                const rgbMatch = color.match(/rgb?\((\d+),\s*(\d+),\s*(\d+)\)/);
                if (rgbMatch) {
                    const r = parseInt(rgbMatch[1]);
                    const g = parseInt(rgbMatch[2]);
                    const b = parseInt(rgbMatch[3]);
                    
                    // 背景色の明度に応じて透明度を自動調整
                    const brightness = (r * 299 + g * 587 + b * 114) / 1000;
                    let opacity = 0.1;
                    
                    if (brightness > 200) {
                        opacity = 0.2;
                    } else if (brightness < 100) {
                        opacity = 0.05;
                    }
                    
                    return `rgba(${r}, ${g}, ${b}, ${opacity})`;
                }
            }
            return color; // エラー時はそのまま返す
        },

        getGradientStops(type) {
            let stops = [];
            if (type === 'card') {
                stops = this.form.card_gradient_stops || [];
            } else if (type === 'page') {
                stops = this.form.page_gradient_stops || [];
            } else if (type === 'bio_card') {
                stops = this.form.bio_card_gradient_stops || [];
            }
            
            // 文字列の場合はJSON.parseで配列に変換
            if (typeof stops === 'string') {
                try {
                    stops = JSON.parse(stops);
                } catch (e) {
                    console.error(`Error parsing gradient stops for ${type}:`, e);
                    stops = [];
                }
            }
            
            // 配列でない場合は空配列を返す
            if (!Array.isArray(stops)) {
                console.warn(`Gradient stops for ${type} is not an array:`, stops);
                stops = [];
            }
            
            return stops;
        },

        addGradientStop(type) {
            // 配列が初期化されていない場合は初期化
            if (!this.form[`${type}_gradient_stops`]) {
                this.form[`${type}_gradient_stops`] = [];
            }
            
            const newId = Math.max(...this.getGradientStops(type).map(s => s.id), 0) + 1;
            const newStop = { 
                id: newId, 
                color: '#FFFFFF', 
                position: Math.min(100, this.getGradientStops(type).length * 25) 
            };
            
            // 配列に新しいストップを追加
            this.form[`${type}_gradient_stops`].push(newStop);
        },

        removeGradientStop(type, id) {
            // 配列が初期化されていない場合は何もしない
            if (!this.form[`${type}_gradient_stops`]) {
                console.warn(`Gradient stops array for ${type} is not initialized`);
                return;
            }
            
            const index = this.getGradientStops(type).findIndex(stop => stop.id === id);
            if (index > -1) {
                this.form[`${type}_gradient_stops`].splice(index, 1);
            }
        },

        toggleGradient(type) {
            this.form[`${type}_gradient_enabled`] = !this.form[`${type}_gradient_enabled`];
            if (!this.form[`${type}_gradient_enabled`]) {
                this.form[`${type}_background_color`] = '#FFFFFF'; // グラデーション無効時は単色
            }
        },

        getGradientStyle(type) {
            if (!this.form[`${type}_gradient_enabled`]) {
                // グラデーション無効時は単色を表示
                return `background-color: ${this.form[`${type}_background_color`] || '#FFFFFF'}`;
            }

            const stops = this.getGradientStops(type);
            if (stops.length === 0) {
                // カラーストップがない場合は単色を表示
                return `background-color: ${this.form[`${type}_background_color`] || '#FFFFFF'}`;
            }

            // 方向の単位を確認し、degが付いていない場合は追加
            let direction = this.form[`${type}_gradient_direction`] || '0deg';
            if (!direction.includes('deg')) {
                direction = direction + 'deg';
            }

            const gradient = `linear-gradient(${direction}, ${stops.map(stop => `${stop.color} ${stop.position}%`).join(', ')})`;
            return `background: ${gradient}`;
        }
    }
}
</script>
