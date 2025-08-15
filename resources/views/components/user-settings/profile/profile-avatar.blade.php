<!-- アバター設定 -->
<div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">アバター画像</h3>
    
    <div class="flex items-center space-x-6">
        <!-- 現在のアバター -->
        <div class="flex-shrink-0">
            <div class="relative">
                <img id="current_avatar" 
                     src="{{ $profile->avatar_url ?? $user->avatar_url }}" 
                     alt="現在のアバター" 
                     data-default-avatar="{{ $user->avatar_url }}"
                     class="h-24 w-24 rounded-full border-4 border-gray-200 dark:border-gray-600">
                
                <!-- アバター削除ボタン -->
                @if($profile->avatar_url)
                <button type="button" 
                        id="remove_avatar"
                        class="absolute -top-2 -right-2 bg-red-500 hover:bg-red-600 text-white rounded-full p-1 shadow-lg">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
                @endif
            </div>
        </div>

        <!-- アップロードエリア -->
        <div class="flex-1">
            <div id="upload_area" 
                 class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-6 text-center hover:border-blue-400 dark:hover:border-blue-500 transition-colors cursor-pointer">
                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                <div class="mt-4">
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        <span class="font-medium text-blue-600 hover:text-blue-500">クリックしてファイルを選択</span>
                        またはドラッグ&ドロップ
                    </p>
                    <p class="text-xs text-gray-500 mt-1">
                        PNG, JPG, WebP, GIF 最大1MB
                    </p>
                </div>
            </div>
            
            <!-- アップロード進捗 -->
            <div id="upload_progress" class="hidden mt-4">
                <div class="bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                    <div id="progress_bar" class="bg-blue-600 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
                </div>
                <p id="progress_text" class="text-sm text-gray-600 dark:text-gray-400 mt-2">アップロード中...</p>
            </div>
        </div>
    </div>

    <!-- 隠しファイル入力 -->
    <input type="file" 
           id="avatar_file" 
           accept="image/jpeg,image/jpg,image/png,image/webp,image/gif" 
           class="hidden">
</div>

@include('components.toast')
@include('components.confirm-dialog')
