<!-- Toast通知コンポーネント -->
<div id="toast-container" class="fixed z-50 space-y-2" style="bottom: 1rem; right: 1rem; max-height: calc(100vh - 2rem); overflow-y: auto;">
    <!-- Toast通知がここに動的に追加されます -->
</div>

<script>
// Toast通知システム
class ToastManager {
    constructor() {
        this.container = document.getElementById('toast-container');
        this.toasts = [];
        this.maxToasts = 5; // 最大表示数
        this.updatePosition();
        this.setupResizeListener();
    }

    // 画面サイズに応じて位置を調整
    updatePosition() {
        if (!this.container) return;
        
        const viewportHeight = window.innerHeight;
        const viewportWidth = window.innerWidth;
        const containerHeight = this.container.scrollHeight;
        const containerWidth = this.container.scrollWidth;
        
        // 右端の位置を調整（コンテナが画面幅を超えないように）
        const rightPosition = Math.max(1, Math.min(16, viewportWidth - containerWidth - 16));
        
        // 下端の位置を調整（コンテナが画面高さを超えないように）
        let bottomPosition = 60;
        if (containerHeight > viewportHeight - 32) {
            bottomPosition = Math.max(1, viewportHeight - containerHeight - 16);
        }
        
        this.container.style.right = `${rightPosition}px`;
        this.container.style.bottom = `${bottomPosition}px`;
        
        // スクロールが必要な場合のスタイル調整
        if (containerHeight > viewportHeight - 32) {
            this.container.style.maxHeight = `${viewportHeight - 32}px`;
            this.container.style.overflowY = 'auto';
        } else {
            this.container.style.maxHeight = 'none';
            this.container.style.overflowY = 'visible';
        }
    }

    // リサイズリスナーの設定
    setupResizeListener() {
        let resizeTimeout;
        window.addEventListener('resize', () => {
            clearTimeout(resizeTimeout);
            resizeTimeout = setTimeout(() => {
                this.updatePosition();
            }, 100);
        });
    }

    // 成功メッセージ
    success(message, duration = 5000) {
        this.show(message, 'success', duration);
    }

    // エラーメッセージ
    error(message, duration = 7000) {
        this.show(message, 'error', duration);
    }

    // 警告メッセージ
    warning(message, duration = 5000) {
        this.show(message, 'warning', duration);
    }

    // 情報メッセージ
    info(message, duration = 5000) {
        this.show(message, 'info', duration);
    }

    // Toast表示
    show(message, type = 'info', duration = 5000) {
        // 最大表示数を超えた場合、古いtoastを削除
        if (this.toasts.length >= this.maxToasts) {
            this.removeToast(this.toasts[0]);
        }

        const toast = this.createToast(message, type);
        this.container.appendChild(toast);
        this.toasts.push(toast);

        // アニメーション開始
        setTimeout(() => {
            toast.classList.add('translate-y-0', 'opacity-100');
        }, 100);

        // 位置を再計算
        this.updatePosition();

        // 自動削除
        if (duration > 0) {
            setTimeout(() => {
                this.removeToast(toast);
            }, duration);
        }
    }

    // Toast要素作成
    createToast(message, type) {
        const toast = document.createElement('div');
        
        const typeConfig = {
            success: {
                bgColor: 'bg-green-500',
                borderColor: 'border-green-600',
                iconColor: 'text-green-600',
                icon: `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>`
            },
            error: {
                bgColor: 'bg-red-500',
                borderColor: 'border-red-600',
                iconColor: 'text-red-600',
                icon: `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>`
            },
            warning: {
                bgColor: 'bg-yellow-500',
                borderColor: 'border-yellow-600',
                iconColor: 'text-yellow-600',
                icon: `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>`
            },
            info: {
                bgColor: 'bg-blue-500',
                borderColor: 'border-blue-600',
                iconColor: 'text-blue-600',
                icon: `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>`
            }
        };

        const config = typeConfig[type] || typeConfig.info;

        toast.className = `
            transform translate-y-full opacity-0 transition-all duration-300 ease-in-out
            max-w-sm w-full ${config.bgColor} border-l-4 ${config.borderColor}
            text-white shadow-lg rounded-lg p-4
        `;

        toast.innerHTML = `
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <div class="w-6 h-6 ${config.iconColor} bg-white rounded-full flex items-center justify-center">
                        ${config.icon}
                    </div>
                </div>
                <div class="ml-3 flex-1">
                    <p class="text-sm font-medium">${message}</p>
                </div>
                <div class="ml-4 flex-shrink-0">
                    <button class="text-white hover:text-gray-200 focus:outline-none" onclick="window.toastManager.removeToast(this.closest('[id^=toast-]'))">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
        `;

        // 一意のIDを設定
        toast.id = `toast-${Date.now()}-${Math.random().toString(36).substr(2, 9)}`;

        return toast;
    }

    // Toast削除
    removeToast(toast) {
        if (!toast) return;
        
        toast.classList.remove('translate-y-0', 'opacity-100');
        toast.classList.add('translate-y-full', 'opacity-0');
        
        setTimeout(() => {
            if (toast.parentNode) {
                toast.parentNode.removeChild(toast);
            }
            this.toasts = this.toasts.filter(t => t !== toast);
            
            // 位置を再計算
            this.updatePosition();
        }, 300);
    }

    // 全てのToast削除
    clearAll() {
        this.toasts.forEach(toast => this.removeToast(toast));
    }
}

// グローバルインスタンス作成
window.toastManager = new ToastManager();

// ページ読み込み完了時にtoastコンテナを確認
document.addEventListener('DOMContentLoaded', function() {
    if (!document.getElementById('toast-container')) {
        const container = document.createElement('div');
        container.id = 'toast-container';
        container.className = 'fixed z-50 space-y-2';
        container.style.cssText = 'bottom: 1rem; right: 1rem; max-height: calc(100vh - 2rem); overflow-y: auto;';
        document.body.appendChild(container);
        window.toastManager.container = container;
        window.toastManager.updatePosition();
    }
});
</script>
