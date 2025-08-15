<!-- テーマ設定 -->
<div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
    <!-- ヘッダー -->
    <div class="mb-6 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <svg class="h-6 w-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zM21 5a2 2 0 00-2-2h-4a2 2 0 00-2 2v12a4 4 0 004 4h4a2 2 0 002-2V5z"></path>
            </svg>
            <h3 class="text-xl font-bold text-gray-900 dark:text-white">テーマ設定</h3>
        </div>
        <div class="flex items-center gap-2">
            <button type="button" id="preview_theme" class="flex items-center gap-2 rounded-lg bg-blue-100 px-3 py-2 text-sm font-medium text-blue-700 transition-colors hover:bg-blue-200 dark:bg-blue-900 dark:text-blue-300 dark:hover:bg-blue-800">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                </svg>
                プレビュー
            </button>
            <button type="button" id="toggle_theme_expanded" class="rounded-lg bg-gray-100 p-2 text-gray-600 transition-colors hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-400 dark:hover:bg-gray-600">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zM21 5a2 2 0 00-2-2h-4a2 2 0 00-2 2v12a4 4 0 004 4h4a2 2 0 002-2V5z"></path>
                </svg>
            </button>
        </div>
    </div>

    <!-- プリセットテーマ -->
    <div class="mb-6">
        <h4 class="mb-3 text-sm font-medium text-gray-700 dark:text-gray-300">プリセットテーマ</h4>
        <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
            <button type="button" data-theme="default" class="theme-preset group relative overflow-hidden rounded-lg p-3 text-left transition-all hover:scale-105" style="background: #8686f0;">
                <div class="relative z-10">
                    <div class="text-xs font-medium text-white">デフォルト</div>
                    <div class="mt-1 flex gap-1">
                        <div class="h-3 w-3 rounded-full border border-white/30" style="background: #ffffff;"></div>
                        <div class="h-3 w-3 rounded-full border border-white/30" style="background: #e2e8f0;"></div>
                        <div class="h-3 w-3 rounded-full border border-white/30" style="background: #f1f5f9;"></div>
                    </div>
                </div>
            </button>
            <button type="button" data-theme="dark" class="theme-preset group relative overflow-hidden rounded-lg p-3 text-left transition-all hover:scale-105" style="background: #1f2937;">
                <div class="relative z-10">
                    <div class="text-xs font-medium text-white">ダーク</div>
                    <div class="mt-1 flex gap-1">
                        <div class="h-3 w-3 rounded-full border border-white/30" style="background: #f9fafb;"></div>
                        <div class="h-3 w-3 rounded-full border border-white/30" style="background: #d1d5db;"></div>
                        <div class="h-3 w-3 rounded-full border border-white/30" style="background: #9ca3af;"></div>
                    </div>
                </div>
            </button>
            <button type="button" data-theme="ocean" class="theme-preset group relative overflow-hidden rounded-lg p-3 text-left transition-all hover:scale-105" style="background: linear-gradient(135deg, #0ea5e9 0%, #06b6d4 100%);">
                <div class="relative z-10">
                    <div class="text-xs font-medium text-white">オーシャン</div>
                    <div class="mt-1 flex gap-1">
                        <div class="h-3 w-3 rounded-full border border-white/30" style="background: #ffffff;"></div>
                        <div class="h-3 w-3 rounded-full border border-white/30" style="background: #e0f2fe;"></div>
                        <div class="h-3 w-3 rounded-full border border-white/30" style="background: #bae6fd;"></div>
                    </div>
                </div>
            </button>
            <button type="button" data-theme="sunset" class="theme-preset group relative overflow-hidden rounded-lg p-3 text-left transition-all hover:scale-105" style="background: linear-gradient(45deg, #f97316 0%, #dc2626 50%, #9333ea 100%);">
                <div class="relative z-10">
                    <div class="text-xs font-medium text-white">サンセット</div>
                    <div class="mt-1 flex gap-1">
                        <div class="h-3 w-3 rounded-full border border-white/30" style="background: #ffffff;"></div>
                        <div class="h-3 w-3 rounded-full border border-white/30" style="background: #fed7aa;"></div>
                        <div class="h-3 w-3 rounded-full border border-white/30" style="background: #fdba74;"></div>
                    </div>
                </div>
            </button>
        </div>
    </div>

    <!-- カラー設定 -->
    <div id="theme_color_settings" class="space-y-6">
        <div class="grid gap-6 md:grid-cols-2">
            <!-- 背景色 -->
            <div class="color-editor" data-color-type="backgroundColor">
                <div class="flex items-center justify-between mb-3">
                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">背景色</label>
                    <div class="flex items-center gap-2">
                        <span class="text-xs text-gray-500">グラデーション</span>
                        <button type="button" class="gradient-toggle relative inline-flex h-5 w-9 items-center rounded-full transition-colors bg-gray-300 dark:bg-gray-600">
                            <span class="inline-block h-3 w-3 transform rounded-full bg-white transition-transform translate-x-1"></span>
                        </button>
                    </div>
                </div>

                <!-- プレビュー -->
                <div class="color-preview h-8 w-full rounded border border-gray-300 dark:border-gray-600 mb-3" style="background: #8686f0;"></div>
                
                <!-- 単色設定 -->
                <div class="solid-color-input">
                    <input type="color" value="#8686f0" class="h-10 w-full cursor-pointer rounded-lg border border-gray-300 dark:border-gray-600">
                </div>
                
                <!-- グラデーション設定 -->
                <div class="gradient-settings hidden">
                    <div class="mb-3">
                        <label class="mb-1 block text-xs text-gray-600 dark:text-gray-400">方向: <span class="direction-value">135</span>°</label>
                        <input type="range" min="0" max="360" value="135" class="direction-slider w-full">
                    </div>
                    
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-gray-600 dark:text-gray-400">カラーストップ (<span class="stops-count">2</span>/5)</span>
                            <button type="button" class="add-stop-btn flex items-center gap-1 text-xs text-blue-600 hover:text-blue-700">
                                <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                追加
                            </button>
                        </div>
                        
                        <div class="gradient-stops space-y-2">
                            <div class="gradient-stop flex items-center gap-2" data-stop-index="0">
                                <svg class="h-3 w-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4"></path>
                                </svg>
                                <input type="color" value="#8686f0" class="h-8 w-12 cursor-pointer rounded border border-gray-300">
                                <input type="number" min="0" max="100" value="0" class="w-16 rounded border border-gray-300 px-2 py-1 text-xs dark:border-gray-600 dark:bg-gray-700"> <span class="text-xs text-gray-500">%</span>
                            </div>
                            <div class="gradient-stop flex items-center gap-2" data-stop-index="1">
                                <svg class="h-3 w-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4"></path>
                                </svg>
                                <input type="color" value="#6366f1" class="h-8 w-12 cursor-pointer rounded border border-gray-300">
                                <input type="number" min="0" max="100" value="100" class="w-16 rounded border border-gray-300 px-2 py-1 text-xs dark:border-gray-600 dark:bg-gray-700"> <span class="text-xs text-gray-500">%</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 表示名の色 -->
            <div class="color-editor" data-color-type="displayNameColor">
                <div class="flex items-center justify-between mb-3">
                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">表示名の色</label>
                    <div class="flex items-center gap-2">
                        <span class="text-xs text-gray-500">グラデーション</span>
                        <button type="button" class="gradient-toggle relative inline-flex h-5 w-9 items-center rounded-full transition-colors bg-gray-300 dark:bg-gray-600">
                            <span class="inline-block h-3 w-3 transform rounded-full bg-white transition-transform translate-x-1"></span>
                        </button>
                    </div>
                </div>
                
                <!-- プレビュー -->
                <div class="color-preview h-8 w-full rounded border border-gray-300 dark:border-gray-600 mb-3" style="background: #ffffff;"></div>
                
                <!-- 単色設定 -->
                <div class="solid-color-input">
                    <input type="color" value="#ffffff" class="h-10 w-full cursor-pointer rounded-lg border border-gray-300 dark:border-gray-600">
                </div>
                
                <!-- グラデーション設定 -->
                <div class="gradient-settings hidden">
                    <!-- グラデーション設定の内容（背景色と同様） -->
                </div>
            </div>

            <!-- 代名詞の色 -->
            <div class="color-editor" data-color-type="pronounsColor">
                <div class="flex items-center justify-between mb-3">
                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">代名詞の色</label>
                    <div class="flex items-center gap-2">
                        <span class="text-xs text-gray-500">グラデーション</span>
                        <button type="button" class="gradient-toggle relative inline-flex h-5 w-9 items-center rounded-full transition-colors bg-gray-300 dark:bg-gray-600">
                            <span class="inline-block h-3 w-3 transform rounded-full bg-white transition-transform translate-x-1"></span>
                        </button>
                    </div>
                </div>
                
                <!-- プレビュー -->
                <div class="color-preview h-8 w-full rounded border border-gray-300 dark:border-gray-600 mb-3" style="background: #e2e8f0;"></div>
                
                <!-- 単色設定 -->
                <div class="solid-color-input">
                    <input type="color" value="#e2e8f0" class="h-10 w-full cursor-pointer rounded-lg border border-gray-300 dark:border-gray-600">
                </div>
            </div>

            <!-- 場所の色 -->
            <div class="color-editor" data-color-type="locationColor">
                <div class="flex items-center justify-between mb-3">
                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">場所の色</label>
                    <div class="flex items-center gap-2">
                        <span class="text-xs text-gray-500">グラデーション</span>
                        <button type="button" class="gradient-toggle relative inline-flex h-5 w-9 items-center rounded-full transition-colors bg-gray-300 dark:bg-gray-600">
                            <span class="inline-block h-3 w-3 transform rounded-full bg-white transition-transform translate-x-1"></span>
                        </button>
                    </div>
                </div>
                
                <!-- プレビュー -->
                <div class="color-preview h-8 w-full rounded border border-gray-300 dark:border-gray-600 mb-3" style="background: #f1f5f9;"></div>
                
                <!-- 単色設定 -->
                <div class="solid-color-input">
                    <input type="color" value="#f1f5f9" class="h-10 w-full cursor-pointer rounded-lg border border-gray-300 dark:border-gray-600">
                </div>
            </div>

            <!-- 自己紹介文の色 -->
            <div class="color-editor" data-color-type="bioTextColor">
                <div class="flex items-center justify-between mb-3">
                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">自己紹介文の色</label>
                    <div class="flex items-center gap-2">
                        <span class="text-xs text-gray-500">グラデーション</span>
                        <button type="button" class="gradient-toggle relative inline-flex h-5 w-9 items-center rounded-full transition-colors bg-gray-300 dark:bg-gray-600">
                            <span class="inline-block h-3 w-3 transform rounded-full bg-white transition-transform translate-x-1"></span>
                        </button>
                    </div>
                </div>
                
                <!-- プレビュー -->
                <div class="color-preview h-8 w-full rounded border border-gray-300 dark:border-gray-600 mb-3" style="background: #ffffff;"></div>
                
                <!-- 単色設定 -->
                <div class="solid-color-input">
                    <input type="color" value="#ffffff" class="h-10 w-full cursor-pointer rounded-lg border border-gray-300 dark:border-gray-600">
                </div>
            </div>

            <!-- 自己紹介背景色 -->
            <div class="color-editor" data-color-type="bioBackgroundColor">
                <div class="flex items-center justify-between mb-3">
                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">自己紹介背景色</label>
                    <div class="flex items-center gap-2">
                        <span class="text-xs text-gray-500">グラデーション</span>
                        <button type="button" class="gradient-toggle relative inline-flex h-5 w-9 items-center rounded-full transition-colors bg-gray-300 dark:bg-gray-600">
                            <span class="inline-block h-3 w-3 transform rounded-full bg-white transition-transform translate-x-1"></span>
                        </button>
                    </div>
                </div>
                
                <!-- プレビュー -->
                <div class="color-preview h-8 w-full rounded border border-gray-300 dark:border-gray-600 mb-3" style="background: rgba(255, 255, 255, 0.1);"></div>
                
                <!-- 単色設定 -->
                <div class="solid-color-input">
                    <input type="color" value="#ffffff" class="h-10 w-full cursor-pointer rounded-lg border border-gray-300 dark:border-gray-600">
                </div>
            </div>
        </div>
    </div>

    <!-- プレビュー -->
    <div id="theme_preview" class="mt-6 hidden">
        <h4 class="mb-3 text-sm font-medium text-gray-700 dark:text-gray-300">プレビュー</h4>
        <div class="rounded-lg p-6 text-center" style="background: #8686f0;">
            <div class="mx-auto h-16 w-16 rounded-full bg-gray-300 dark:bg-gray-600"></div>
            <h1 class="mt-3 text-2xl font-bold text-white">プレビュー名</h1>
            <h2 class="mt-1 text-lg text-gray-200">They/Them</h2>
            <div class="mt-2 flex items-center justify-center gap-1 text-gray-100">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                <span>Tokyo, Japan</span>
            </div>
            <div class="mt-4 rounded-lg p-3" style="background: rgba(255, 255, 255, 0.1);">
                <p class="text-sm text-white">これは自己紹介文のプレビューです。テーマの色がどのように表示されるかを確認できます。</p>
            </div>
        </div>
    </div>

    <!-- アクションボタン -->
    <div class="mt-6 flex flex-wrap items-center justify-between gap-3 border-t border-gray-200 pt-4 dark:border-gray-600">
        <div class="flex gap-2">
            <button type="button" id="reset_theme" class="flex items-center gap-2 rounded-lg bg-gray-100 px-3 py-2 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-200 disabled:opacity-50 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path>
                </svg>
                デフォルトに戻す
            </button>
            <button type="button" id="revert_theme" class="hidden flex items-center gap-2 rounded-lg bg-yellow-100 px-3 py-2 text-sm font-medium text-yellow-700 transition-colors hover:bg-yellow-200 disabled:opacity-50 dark:bg-yellow-900 dark:text-yellow-300 dark:hover:bg-yellow-800">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6-6l6-6"></path>
                </svg>
                変更を破棄
            </button>
        </div>

        <div class="flex items-center gap-3">
            <span id="save_message" class="hidden text-sm"></span>
            <button type="button" id="save_theme" class="flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-50">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                保存する
            </button>
        </div>
    </div>
</div>

@include('components.toast')
