<!-- プロフィール基本情報 -->
<div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
    <div class="flex items-center justify-between mb-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">基本情報設定</h3>
        <div class="flex items-center gap-3">
            <!-- 自分のページを見るボタン -->
            @if($profile->custom_url)
                <a href="{{ config('app.url') }}/p/{{ $profile->custom_url }}" 
                   target="_blank"
                   class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-blue-600 bg-blue-50 border border-blue-200 rounded-lg hover:bg-blue-100 dark:text-blue-400 dark:bg-blue-900/20 dark:border-blue-800 dark:hover:bg-blue-900/30 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                    </svg>
                    自分のページを見る
                </a>
            @else
                <span class="inline-flex items-center gap-2 px-4 py-2 text-sm text-gray-500 bg-gray-100 border border-gray-200 rounded-lg dark:text-gray-400 dark:bg-gray-700 dark:border-gray-600 cursor-not-allowed">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    カスタムURLを設定してください
                </span>
            @endif
            
            <div id="changes_indicator" class="hidden">
                <div class="flex items-center gap-1 text-sm text-orange-600 dark:text-orange-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                    未保存の変更
                </div>
            </div>
        </div>
    </div>

    <!-- 現在の設定表示 -->
    <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 mb-6 dark:border-gray-700 dark:bg-gray-800/50">
        <h4 class="mb-3 text-sm font-medium text-gray-700 dark:text-gray-300">現在の設定</h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="rounded-md bg-white p-3 dark:bg-gray-700">
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">表示名</p>
                <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                    {{ $profile->display_name ?: '未設定' }}
                </p>
            </div>
            <div class="rounded-md bg-white p-3 dark:bg-gray-700">
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">代名詞</p>
                <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                    {{ $profile->pronouns ?: '未設定' }}
                </p>
            </div>
            <div class="rounded-md bg-white p-3 dark:bg-gray-700">
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">場所</p>
                <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                    {{ $profile->location ?: '未設定' }}
                </p>
            </div>
            <div class="rounded-md bg-white p-3 dark:bg-gray-700">
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">カスタムURL</p>
                <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                    {{ $profile->custom_url ? config('app.url') . '/p/' . $profile->custom_url : '未設定' }}
                </p>
            </div>
        </div>
        <div class="mt-4 rounded-md bg-white p-3 dark:bg-gray-700">
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">自己紹介</p>
            <p class="text-sm text-gray-900 dark:text-gray-100 whitespace-pre-wrap">{{ $profile->bio ?: '未設定' }}</p>
        </div>
    </div>

    <!-- 入力フォーム -->
    <div class="space-y-6">
        <!-- 表示名 -->
        <div>
            @php
                $displayNameValue = trim($profile->display_name ?? '');
            @endphp
            <div class="mb-2 flex items-center justify-between">
                <label for="display_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    表示名
                </label>
                <span class="text-xs text-gray-500">
                    <span id="display_name_count">{{ strlen($displayNameValue) }}</span>/50
                </span>
            </div>
            <input type="text" id="display_name" name="display_name" maxlength="50" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition-all" placeholder="表示名を入力してください" value="{{ $displayNameValue }}">
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                他のユーザーに表示される名前です
            </p>
        </div>
        
        <!-- 代名詞 -->
        <div>
            @php
                $pronounsValue = trim($profile->pronouns ?? '');
            @endphp
            <div class="mb-2 flex items-center justify-between">
                <label for="pronouns" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    代名詞
                </label>
                <span class="text-xs text-gray-500">
                    <span id="pronouns_count">{{ strlen($pronounsValue) }}</span>/32
                </span>
            </div>
            <input type="text" id="pronouns" name="pronouns" maxlength="32" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition-all" placeholder="例: he/him, she/her, they/them" value="{{ $pronounsValue }}">
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                あなたの代名詞を設定できます
            </p>
        </div>
        
        <!-- 場所 -->
        <div>
            @php
                $locationValue = trim($profile->location ?? '');
            @endphp
            <div class="mb-2 flex items-center justify-between">
                <label for="location" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    場所
                </label>
                <span class="text-xs text-gray-500">
                    <span id="location_count">{{ strlen($locationValue) }}</span>/32
                </span>
            </div>
            <input type="text" id="location" name="location" maxlength="32" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition-all" placeholder="例: 東京, Japan" value="{{ $locationValue }}">
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                あなたの所在地や出身地を設定できます
            </p>
        </div>

        <!-- カスタムURL -->
        <div>
            @php
                $customUrlValue = trim($profile->custom_url ?? '');
            @endphp
            <div class="mb-2 flex items-center justify-between">
                <label for="custom_url" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    カスタムURL
                </label>
                <span class="text-xs text-gray-500">
                    <span id="custom_url_count">{{ strlen($customUrlValue) }}</span>/30
                </span>
            </div>
            <div class="flex items-center gap-2">
                <span class="whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                    {{ config('app.url') }}/p/
                </span>
                <div class="relative flex-1">
                    <input type="text" id="custom_url" name="custom_url" minlength="3" maxlength="30" pattern="[a-zA-Z0-9._]+" class="w-full px-4 py-3 pr-16 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition-all" placeholder="your-custom-url" value="{{ $customUrlValue }}">
                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-gray-500">
                        {{ strlen($customUrlValue) }}/30
                    </span>
                </div>
            </div>
            <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                英数字、ピリオド、アンダーバーのみで3文字以上30文字以下で設定してください
            </div>
            <div id="custom_url_status" class="mt-2"></div>
        </div>

        <!-- 自己紹介 -->
        <div>
            @php
                $bioValue = trim($profile->bio ?? '');
            @endphp
            <div class="mb-2 flex items-center justify-between">
                <label for="bio" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    自己紹介
                </label>
                <span class="text-xs text-gray-500">
                    <span id="bio_count">{{ strlen($bioValue) }}</span>/500
                </span>
            </div>
            <textarea id="bio" name="bio" rows="6" maxlength="500" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition-all resize-none" placeholder="自己紹介を入力してください...">{{ $bioValue }}</textarea>
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                あなたについて詳しく紹介してください
            </p>
        </div>

        <!-- プレビュー -->
        <div id="preview_section" class="hidden rounded-lg bg-gray-50 p-4 dark:bg-gray-800/50">
            <h5 class="mb-3 text-sm font-medium text-gray-800 dark:text-gray-200">プレビュー</h5>
            <div class="space-y-3">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-blue-600 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                    <div>
                        <h6 id="preview_display_name" class="text-sm font-medium text-gray-900 dark:text-white"></h6>
                        <p id="preview_pronouns" class="text-xs text-gray-500 dark:text-gray-400"></p>
                        <p id="preview_location" class="text-xs text-gray-500 dark:text-gray-400"></p>
                    </div>
                </div>
                <div id="preview_bio" class="text-sm text-gray-700 dark:text-gray-300"></div>
                <div id="preview_custom_url" class="text-xs text-blue-600 dark:text-blue-400"></div>
            </div>
        </div>

        <!-- 情報メッセージ -->
        <div class="rounded-lg bg-blue-50 p-4 dark:bg-blue-900/20">
            <h5 class="mb-2 text-sm font-medium text-blue-800 dark:text-blue-200">基本情報について</h5>
            <ul class="space-y-1 text-sm text-blue-700 dark:text-blue-300">
                <li>• 表示名は他のユーザーに公開されます</li>
                <li>• 代名詞と場所は任意で設定できます</li>
                <li>• カスタムURLは一意である必要があります</li>
                <li>• 自己紹介は改行も含まれて表示されます</li>
                <li>• 後からいつでも変更できます</li>
            </ul>
        </div>

        <!-- アクションボタン -->
        <div class="flex gap-3">
            <button type="button" 
                    id="preview_button"
                    class="flex items-center justify-center gap-2 rounded-lg border border-gray-300 px-4 py-3 text-sm font-medium transition-all dark:border-gray-600 bg-white text-gray-700 hover:border-gray-400 hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                </svg>
                プレビュー
            </button>

            <button type="button" 
                    id="reset_button"
                    class="hidden flex items-center justify-center gap-2 rounded-lg border border-gray-300 px-4 py-3 text-sm font-medium transition-all dark:border-gray-600 bg-white text-gray-700 hover:border-gray-400 hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                リセット
            </button>

            <button type="button" 
                    id="save_profile"
                    class="flex flex-1 items-center justify-center gap-2 rounded-lg px-6 py-3 text-sm font-medium text-white transition-all bg-blue-600 shadow-lg hover:bg-blue-700 hover:shadow-xl disabled:opacity-50 disabled:cursor-not-allowed">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                保存
            </button>
        </div>
    </div>
</div>

@include('components.toast')
