// アバター管理機能
class AvatarManager {
    constructor() {
        this.avatarInput = document.getElementById('avatar_input');
        this.avatarPreview = document.getElementById('avatar_preview');
        this.currentAvatar = document.getElementById('current_avatar');
        this.uploadBtn = document.getElementById('upload_avatar');
        this.applyBtn = document.getElementById('apply_avatar');
        this.removeBtn = document.getElementById('remove_avatar');

        this.initialize();
    }

    initialize() {
        this.setupEventListeners();
    }

    // イベントリスナーを設定
    setupEventListeners() {
        if (this.avatarInput) {
            this.avatarInput.addEventListener('change', (e) => this.handleFileSelect(e));
        }

        if (this.uploadBtn) {
            this.uploadBtn.addEventListener('click', () => this.uploadAvatar());
        }

        if (this.applyBtn) {
            this.applyBtn.addEventListener('click', () => this.applyAvatar());
        }

        if (this.removeBtn) {
            this.removeBtn.addEventListener('click', () => this.removeAvatar());
        }
    }

    // ファイル選択を処理
    handleFileSelect(event) {
        const file = event.target.files[0];
        if (!file) return;

        // ファイルサイズチェック（5MB以下）
        if (file.size > 5 * 1024 * 1024) {
            if (window.toastManager) {
                window.toastManager.error('ファイルサイズは5MB以下にしてください。');
            }
            return;
        }

        // ファイル形式チェック
        if (!file.type.startsWith('image/')) {
            if (window.toastManager) {
                window.toastManager.error('画像ファイルを選択してください。');
            }
            return;
        }

        // プレビュー表示
        this.showPreview(file);
    }

    // プレビューを表示
    showPreview(file) {
        if (!this.avatarPreview) return;

        const reader = new FileReader();
        reader.onload = (e) => {
            this.avatarPreview.src = e.target.result;
            this.avatarPreview.classList.remove('hidden');
        };
        reader.readAsDataURL(file);
    }

    // アバターをアップロード
    async uploadAvatar() {
        const file = this.avatarInput?.files[0];
        if (!file) {
            if (window.toastManager) {
                window.toastManager.error('ファイルを選択してください。');
            }
            return;
        }

        try {
            const formData = new FormData();
            formData.append('avatar', file);

            const response = await fetch('/api/profile/avatar/upload', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document
                        .querySelector('meta[name="csrf-token"]')
                        .getAttribute('content'),
                },
                credentials: 'same-origin',
                body: formData,
            });

            if (response.ok) {
                const data = await response.json();
                await this.applyAvatar(data.avatarUrl, data.avatarHash, this.currentAvatar?.src);
            } else {
                const errorData = await response.json();
                throw new Error(errorData.error || 'アップロードに失敗しました。');
            }
        } catch (error) {
            if (window.toastManager) {
                window.toastManager.error(
                    'アップロードに失敗しました: ' + error.message,
                );
            }
        }
    }

    // アバターを適用
    async applyAvatar(avatarUrl, avatarHash, oldAvatarUrl) {
        try {
            const response = await fetch('/api/profile/avatar/apply', {
                method: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': document
                        .querySelector('meta[name="csrf-token"]')
                        .getAttribute('content'),
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                },
                credentials: 'same-origin',
                body: JSON.stringify({
                    avatarUrl,
                    avatarHash,
                    oldAvatarUrl,
                }),
            });

            const data = await response.json();

            if (response.ok) {
                // アバター画像を更新
                const avatarElement = document.getElementById('current_avatar');
                if (avatarElement) {
                    avatarElement.src = avatarUrl;
                }

                // 削除ボタンを表示
                const removeBtn = document.getElementById('remove_avatar');
                if (!removeBtn) {
                    const avatarContainer =
                        document.getElementById('current_avatar').parentElement;
                    if (avatarContainer) {
                        const removeButton = document.createElement('button');
                        removeButton.id = 'remove_avatar';
                        removeButton.className =
                            'absolute -top-2 -right-2 bg-red-500 hover:bg-red-600 text-white rounded-full p-1 shadow-lg';
                        removeButton.innerHTML =
                            '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>';
                        removeButton.addEventListener('click', () => this.removeAvatar());
                        avatarContainer.appendChild(removeButton);
                    }
                }

                // プレビューをクリア
                if (this.avatarPreview) {
                    this.avatarPreview.classList.add('hidden');
                }

                // ファイル入力をクリア
                if (this.avatarInput) {
                    this.avatarInput.value = '';
                }

                if (window.toastManager) {
                    window.toastManager.success('アバターが更新されました！');
                }
            } else {
                throw new Error(data.error || 'アバターの適用に失敗しました。');
            }
        } catch (error) {
            if (window.toastManager) {
                window.toastManager.error(
                    'アバターの適用に失敗しました: ' + error.message,
                );
            }
        }
    }

    // アバターを削除
    async removeAvatar() {
        if (window.confirmDialog) {
            const confirmed = await window.confirmDialog.confirm(
                'アバター削除の確認',
                'アバターを削除しますか？',
                '削除',
                'キャンセル',
            );
            if (!confirmed) {
                return;
            }
        } else {
            // フォールバック
            if (!confirm('アバターを削除しますか？')) {
                return;
            }
        }

        try {
            const response = await fetch('/api/profile/avatar/remove', {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document
                        .querySelector('meta[name="csrf-token"]')
                        .getAttribute('content'),
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                },
                credentials: 'same-origin',
            });

            if (response.ok) {
                // アバター画像をデフォルトに戻す
                const avatarElement = document.getElementById('current_avatar');
                if (avatarElement) {
                    avatarElement.src = '/images/default-avatar.png';
                }

                // 削除ボタンを削除
                const removeBtn = document.getElementById('remove_avatar');
                if (removeBtn) {
                    removeBtn.remove();
                }

                if (window.toastManager) {
                    window.toastManager.success('アバターが削除されました！');
                }
            } else {
                const data = await response.json();
                throw new Error(data.error || 'アバターの削除に失敗しました。');
            }
        } catch (error) {
            if (window.toastManager) {
                window.toastManager.error(
                    'アバターの削除に失敗しました: ' + error.message,
                );
            }
        }
    }
}

// グローバルに公開
window.AvatarManager = AvatarManager;
