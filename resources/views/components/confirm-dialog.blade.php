<!-- 確認ダイアログコンポーネント -->
<div id="confirm-dialog-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div id="confirm-dialog" class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full transform transition-all">
            <div class="p-6">
                <div class="flex items-center mb-4">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-yellow-100 dark:bg-yellow-900 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-3">
                        <h3 id="confirm-dialog-title" class="text-lg font-medium text-gray-900 dark:text-gray-100">確認</h3>
                    </div>
                </div>
                
                <div class="mb-6">
                    <p id="confirm-dialog-message" class="text-sm text-gray-500 dark:text-gray-400">
                        この操作を実行しますか？
                    </p>
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button type="button" 
                            id="confirm-dialog-cancel"
                            class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-md hover:bg-gray-200 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors">
                        キャンセル
                    </button>
                    <button type="button" 
                            id="confirm-dialog-confirm"
                            class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors">
                        実行
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// 確認ダイアログシステム
class ConfirmDialog {
    constructor() {
        this.overlay = document.getElementById('confirm-dialog-overlay');
        this.dialog = document.getElementById('confirm-dialog');
        this.title = document.getElementById('confirm-dialog-title');
        this.message = document.getElementById('confirm-dialog-message');
        this.confirmBtn = document.getElementById('confirm-dialog-confirm');
        this.cancelBtn = document.getElementById('confirm-dialog-cancel');
        
        this.setupEventListeners();
    }

    setupEventListeners() {
        // キャンセルボタン
        this.cancelBtn.addEventListener('click', () => {
            this.hide();
        });

        // オーバーレイクリック
        this.overlay.addEventListener('click', (e) => {
            if (e.target === this.overlay) {
                this.hide();
            }
        });

        // ESCキー
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.isVisible()) {
                this.hide();
            }
        });
    }

    show(title, message, confirmText = '実行', cancelText = 'キャンセル') {
        this.title.textContent = title;
        this.message.textContent = message;
        this.confirmBtn.textContent = confirmText;
        this.cancelBtn.textContent = cancelText;
        
        this.overlay.classList.remove('hidden');
        this.overlay.classList.add('flex');
        
        // フォーカスを確認ボタンに設定
        setTimeout(() => {
            this.confirmBtn.focus();
        }, 100);
    }

    hide() {
        this.overlay.classList.add('hidden');
        this.overlay.classList.remove('flex');
    }

    isVisible() {
        return !this.overlay.classList.contains('hidden');
    }

    // Promiseベースの確認
    confirm(title, message, confirmText = '実行', cancelText = 'キャンセル') {
        return new Promise((resolve) => {
            this.confirmBtn.onclick = () => {
                this.hide();
                resolve(true);
            };
            
            this.cancelBtn.onclick = () => {
                this.hide();
                resolve(false);
            };
            
            this.show(title, message, confirmText, cancelText);
        });
    }
}

// グローバルインスタンス作成
window.confirmDialog = new ConfirmDialog();

// ページ読み込み完了時に確認ダイアログコンテナを確認
document.addEventListener('DOMContentLoaded', function() {
    if (!document.getElementById('confirm-dialog-overlay')) {
        const container = document.createElement('div');
        container.innerHTML = document.querySelector('#confirm-dialog-overlay').outerHTML;
        document.body.appendChild(container);
        window.confirmDialog = new ConfirmDialog();
    }
});
</script>
