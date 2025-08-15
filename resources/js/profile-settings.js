// プロフィール設定用JavaScript

// 認証状態確認
async function checkAuthStatus() {
    try {
        const response = await fetch('/api/auth/status', {
            method: 'GET',
            headers: {
                Accept: 'application/json',
            },
            credentials: 'same-origin',
        });

        const data = await response.json();
        return data;
    } catch (error) {
        return null;
    }
}

// リンク管理機能
class LinkManager {
    constructor() {
        this.linksContainer = document.getElementById('links_container');
        this.addLinkBtn = document.getElementById('add_link');
        this.saveLinksBtn = document.getElementById('save_links');
        this.resetLinksBtn = document.getElementById('reset_links');
        this.changesIndicator = document.getElementById('changes_indicator');
        this.currentLinksContainer = document.getElementById(
            'current_links_container',
        );
        this.links = [];
        this.originalLinks = [];
        this.nextId = 1;
        this.maxLinks = 10;

        this.initialize();
        this.initializeDragAndDrop();
    }

    initialize() {
        if (this.addLinkBtn) {
            this.addLinkBtn.addEventListener('click', () => this.addLink());
        }

        if (this.saveLinksBtn) {
            this.saveLinksBtn.addEventListener('click', () => this.saveLinks());
        }

        if (this.resetLinksBtn) {
            this.resetLinksBtn.addEventListener('click', () =>
                this.resetLinks(),
            );
        }

        // 既存のリンクを読み込み
        this.loadExistingLinks();
    }

    // ドラッグ&ドロップ機能の初期化
    initializeDragAndDrop() {
        if (!this.currentLinksContainer) return;

        // 既存のリンクアイテムにドラッグ&ドロップを設定
        this.setupDragAndDrop();
    }

    // ドラッグ&ドロップの設定
    setupDragAndDrop() {
        const linkItems =
            this.currentLinksContainer.querySelectorAll('.link-item');

        linkItems.forEach((item) => {
            const dragHandle = item.querySelector('.drag-handle');
            if (dragHandle) {
                item.draggable = true;

                item.addEventListener('dragstart', (e) => {
                    e.dataTransfer.setData('text/plain', item.dataset.linkId);
                    item.classList.add('opacity-50');
                });

                item.addEventListener('dragend', () => {
                    item.classList.remove('opacity-50');
                });
            }
        });

        this.currentLinksContainer.addEventListener('dragover', (e) => {
            e.preventDefault();
            const draggingItem = document.querySelector(
                '.link-item.opacity-50',
            );
            if (draggingItem) {
                const afterElement = this.getDragAfterElement(
                    this.currentLinksContainer,
                    e.clientY,
                );
                if (afterElement) {
                    this.currentLinksContainer.insertBefore(
                        draggingItem,
                        afterElement,
                    );
                } else {
                    this.currentLinksContainer.appendChild(draggingItem);
                }
            }
        });

        this.currentLinksContainer.addEventListener('drop', (e) => {
            e.preventDefault();
            this.updateSortOrder();
        });
    }

    // ドラッグ後の要素位置を取得
    getDragAfterElement(container, y) {
        const draggableElements = [
            ...container.querySelectorAll('.link-item:not(.opacity-50)'),
        ];

        return draggableElements.reduce(
            (closest, child) => {
                const box = child.getBoundingClientRect();
                const offset = y - box.top - box.height / 2;

                if (offset < 0 && offset > closest.offset) {
                    return { offset: offset, element: child };
                } else {
                    return closest;
                }
            },
            { offset: Number.NEGATIVE_INFINITY },
        ).element;
    }

    // ソート順序を更新
    updateSortOrder() {
        const linkItems =
            this.currentLinksContainer.querySelectorAll('.link-item');
        linkItems.forEach((item, index) => {
            item.dataset.sortOrder = index;
        });
        this.checkForChanges();
    }

    loadExistingLinks() {
        // 既存のリンクデータを保存
        if (this.currentLinksContainer) {
            const existingLinks =
                this.currentLinksContainer.querySelectorAll('.link-item');
            this.originalLinks = Array.from(existingLinks).map((item) => ({
                id: item.dataset.linkId,
                button_id: item
                    .querySelector('.text-xs')
                    .textContent.toLowerCase(),
                url: item.querySelector('.text-xs.text-gray-500').textContent,
                display_name: item.querySelector('.font-medium').textContent,
                custom_color:
                    item.querySelector('.w-8.h-8').style.backgroundColor,
                sort_order: parseInt(item.dataset.sortOrder) || 0,
            }));
        }
    }

    addLink() {
        if (this.links.length >= this.maxLinks) {
            if (window.toastManager) {
                window.toastManager.warning(
                    `リンクは最大${this.maxLinks}個まで追加できます`,
                );
            }
            return;
        }

        const linkData = {
            id: `temp_${this.nextId++}`,
            button_id: 'website',
            url: '',
            display_name: '',
            custom_color: '#6B7280',
            sort_order: this.links.length,
        };

        this.links.push(linkData);
        this.renderLinks();
        this.checkForChanges();
    }

    removeLink(linkId) {
        this.links = this.links.filter((link) => link.id !== linkId);
        this.renderLinks();
        this.checkForChanges();
    }

    updateLink(linkId, field, value) {
        const link = this.links.find((l) => l.id === linkId);
        if (link) {
            link[field] = value;
            this.checkForChanges();
        }
    }

    renderLinks() {
        if (!this.linksContainer) return;

        this.linksContainer.innerHTML = '';

        if (this.links.length === 0) {
            this.linksContainer.innerHTML = `
                <div class="rounded-lg border-2 border-dashed border-gray-300 p-8 text-center dark:border-gray-600">
                    <svg class="mx-auto h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">「追加」ボタンでリンクを追加できます</p>
                </div>
            `;
            return;
        }

        this.links.forEach((link, index) => {
            const linkElement = this.createLinkElement(link, index);
            this.linksContainer.appendChild(linkElement);
        });
    }

    createLinkElement(link, index) {
        const linkElement = document.createElement('div');
        linkElement.className =
            'space-y-3 rounded-lg border border-gray-200 p-4 dark:border-gray-700';
        linkElement.dataset.linkId = link.id;

        const buttonOptions = [
            { value: 'website', label: 'ウェブサイト', color: 'bg-blue-600' },
            { value: 'twitter', label: 'Twitter', color: 'bg-sky-500' },
            { value: 'github', label: 'GitHub', color: 'bg-gray-800' },
            { value: 'youtube', label: 'YouTube', color: 'bg-red-600' },
            { value: 'instagram', label: 'Instagram', color: 'bg-pink-600' },
            { value: 'discord', label: 'Discord', color: 'bg-indigo-600' },
            { value: 'twitch', label: 'Twitch', color: 'bg-purple-600' },
            { value: 'linkedin', label: 'LinkedIn', color: 'bg-blue-700' },
            { value: 'facebook', label: 'Facebook', color: 'bg-blue-800' },
            { value: 'tiktok', label: 'TikTok', color: 'bg-black' },
            { value: 'steam', label: 'Steam', color: 'bg-gray-700' },
            { value: 'spotify', label: 'Spotify', color: 'bg-green-600' },
            { value: 'paypal', label: 'PayPal', color: 'bg-blue-600' },
            { value: 'amazon', label: 'Amazon', color: 'bg-orange-500' },
            { value: 'email', label: 'メール', color: 'bg-gray-600' },
        ];

        linkElement.innerHTML = `
            <div class="flex items-center justify-between">
                <h5 class="font-medium text-gray-900 dark:text-white">リンク #${index + 1}</h5>
                <button type="button" class="text-red-600 hover:text-red-700 dark:text-red-400 p-1 rounded hover:bg-red-50 dark:hover:bg-red-900/20" onclick="linkManager.removeLink('${link.id}')">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                </button>
            </div>
            
            <div class="grid grid-cols-1 gap-3 md:grid-cols-3">
                <div>
                    <label class="mb-1 block text-xs font-medium text-gray-700 dark:text-gray-300">ボタンタイプ</label>
                    <select name="button_id" class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm dark:border-gray-600 dark:bg-gray-800" onchange="linkManager.updateLink('${link.id}', 'button_id', this.value)">
                        ${buttonOptions
                            .map(
                                (option) =>
                                    `<option value="${option.value}" ${link.button_id === option.value ? 'selected' : ''}>${option.label}</option>`,
                            )
                            .join('')}
                    </select>
                </div>
                
                <div>
                    <label class="mb-1 block text-xs font-medium text-gray-700 dark:text-gray-300">表示名</label>
                    <input type="text" name="display_name" value="${link.display_name}" 
                           class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm dark:border-gray-600 dark:bg-gray-800" 
                           placeholder="My Website"
                           maxlength="50"
                           onchange="linkManager.updateLink('${link.id}', 'display_name', this.value)">
                    <p class="mt-1 text-xs text-gray-500">${link.display_name.length}/50</p>
                </div>
                
                <div>
                    <label class="mb-1 block text-xs font-medium text-gray-700 dark:text-gray-300">URL</label>
                    <input type="url" name="url" value="${link.url}" 
                           class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm dark:border-gray-600 dark:bg-gray-800" 
                           placeholder="https://example.com"
                           maxlength="500"
                           onchange="linkManager.updateLink('${link.id}', 'url', this.value)">
                    <p class="mt-1 text-xs text-gray-500">${link.url.length}/500</p>
                </div>
            </div>

            <!-- プレビュー -->
            ${
                link.display_name && link.url
                    ? `
                <div class="mt-3 flex items-center gap-3 rounded-lg bg-gray-50 p-3 dark:bg-gray-800/50">
                    <div class="rounded-full p-2 text-white" style="background-color: ${link.custom_color}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">${link.display_name}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">${link.url}</p>
                    </div>
                </div>
            `
                    : ''
            }
        `;

        return linkElement;
    }

    getLinksData() {
        return this.links.map((link) => ({
            button_id: link.button_id,
            url: link.url,
            display_name: link.display_name,
            custom_color: link.custom_color,
            sort_order: link.sort_order,
        }));
    }

    // 変更があるかチェック
    checkForChanges() {
        const hasChanges =
            this.links.length > 0 || this.hasExistingLinksChanges();

        if (this.changesIndicator) {
            this.changesIndicator.classList.toggle('hidden', !hasChanges);
        }

        if (this.saveLinksBtn) {
            this.saveLinksBtn.disabled = !hasChanges;
        }

        if (this.resetLinksBtn) {
            this.resetLinksBtn.disabled = !hasChanges;
        }
    }

    // 既存リンクの変更をチェック
    hasExistingLinksChanges() {
        if (!this.currentLinksContainer) return false;

        const currentItems =
            this.currentLinksContainer.querySelectorAll('.link-item');
        const currentOrder = Array.from(currentItems).map(
            (item) => item.dataset.linkId,
        );
        const originalOrder = this.originalLinks.map((link) => link.id);

        return JSON.stringify(currentOrder) !== JSON.stringify(originalOrder);
    }

    validateLinks() {
        const errors = [];

        this.links.forEach((link, index) => {
            if (!link.button_id) {
                errors.push(
                    `リンク ${index + 1}: ボタンタイプを選択してください`,
                );
            }
            if (!link.url.trim()) {
                errors.push(`リンク ${index + 1}: URLを入力してください`);
            } else if (!this.isValidUrl(link.url)) {
                errors.push(`リンク ${index + 1}: 有効なURLを入力してください`);
            }
            if (!link.display_name.trim()) {
                errors.push(`リンク ${index + 1}: 表示名を入力してください`);
            }
        });

        return errors;
    }

    isValidUrl(string) {
        try {
            new URL(string);
            return true;
        } catch (_) {
            return false;
        }
    }

    // リセット機能
    resetLinks() {
        this.links = [];
        this.renderLinks();

        // 既存リンクの順序も元に戻す
        if (this.currentLinksContainer && this.originalLinks.length > 0) {
            this.originalLinks.forEach((link) => {
                const item = this.currentLinksContainer.querySelector(
                    `[data-link-id="${link.id}"]`,
                );
                if (item) {
                    item.dataset.sortOrder = link.sort_order;
                }
            });

            // 順序を元に戻す
            const sortedItems = Array.from(
                this.currentLinksContainer.children,
            ).sort((a, b) => {
                return (
                    parseInt(a.dataset.sortOrder) -
                    parseInt(b.dataset.sortOrder)
                );
            });

            sortedItems.forEach((item) => {
                this.currentLinksContainer.appendChild(item);
            });
        }

        this.checkForChanges();
    }

    async saveLinks() {
        const saveBtn = this.saveLinksBtn;
        const originalText = saveBtn.innerHTML;

        saveBtn.disabled = true;
        saveBtn.innerHTML =
            '<svg class="animate-spin w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>保存中...';

        try {
            // リンクのバリデーション
            const linkErrors = this.validateLinks();
            if (linkErrors.length > 0) {
                throw new Error(
                    'リンクの設定に問題があります:\n' + linkErrors.join('\n'),
                );
            }

            const linksData = this.getLinksData();

            // 既存リンクの順序変更も含める
            if (this.currentLinksContainer) {
                const currentItems =
                    this.currentLinksContainer.querySelectorAll('.link-item');
                currentItems.forEach((item, index) => {
                    const linkId = item.dataset.linkId;
                    const existingLink = this.originalLinks.find(
                        (link) => link.id === linkId,
                    );
                    if (existingLink) {
                        linksData.push({
                            ...existingLink,
                            sort_order: index,
                        });
                    }
                });
            }

            const response = await fetch('/api/profile/links', {
                method: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': document
                        .querySelector('meta[name="csrf-token"]')
                        .getAttribute('content'),
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                },
                credentials: 'same-origin',
                body: JSON.stringify({ links: linksData }),
            });

            if (response.ok) {
                if (window.toastManager) {
                    window.toastManager.success('リンクが保存されました！');
                }
                // ページをリロードして既存リンクを更新
                window.location.reload();
            } else {
                const data = await response.json();
                throw new Error(data.error || 'リンクの保存に失敗しました。');
            }
        } catch (error) {
            console.error('リンク保存エラー:', error);
            if (window.toastManager) {
                window.toastManager.error(
                    'リンクの保存に失敗しました: ' + error.message,
                );
            }
        } finally {
            saveBtn.disabled = false;
            saveBtn.innerHTML = originalText;
        }
    }
}

// 基本情報管理機能
class ProfileBasicInfoManager {
    constructor() {
        this.displayNameInput = document.getElementById('display_name');
        this.pronounsInput = document.getElementById('pronouns');
        this.locationInput = document.getElementById('location');
        this.customUrlInput = document.getElementById('custom_url');
        this.bioInput = document.getElementById('bio');
        this.changesIndicator = document.getElementById('changes_indicator');
        this.previewButton = document.getElementById('preview_button');
        this.resetButton = document.getElementById('reset_button');
        this.saveButton = document.getElementById('save_profile');
        this.previewSection = document.getElementById('preview_section');

        this.originalValues = {};
        this.isCheckingUrl = false;
        this.urlCheckTimeout = null;

        this.initialize();
    }

    initialize() {
        // 元の値を保存
        this.saveOriginalValues();

        // イベントリスナーを設定
        this.setupEventListeners();

        // 初期状態をチェック
        this.checkForChanges();
    }

    saveOriginalValues() {
        this.originalValues = {
            display_name: this.displayNameInput?.value || '',
            pronouns: this.pronounsInput?.value || '',
            location: this.locationInput?.value || '',
            custom_url: this.customUrlInput?.value || '',
            bio: this.bioInput?.value || '',
        };
    }

    setupEventListeners() {
        // 入力フィールドの変更監視
        [
            this.displayNameInput,
            this.pronounsInput,
            this.locationInput,
            this.bioInput,
        ].forEach((input) => {
            if (input) {
                input.addEventListener('input', () => {
                    this.updateCharCount(input);
                    this.checkForChanges();
                });
            }
        });

        // カスタムURLの変更監視
        if (this.customUrlInput) {
            this.customUrlInput.addEventListener('input', () => {
                this.updateCharCount(this.customUrlInput);
                this.checkForChanges();
                this.checkCustomUrlAvailability();
            });
        }

        // プレビューボタン
        if (this.previewButton) {
            this.previewButton.addEventListener('click', () =>
                this.togglePreview(),
            );
        }

        // リセットボタン
        if (this.resetButton) {
            this.resetButton.addEventListener('click', () =>
                this.resetToOriginal(),
            );
        }

        // 保存ボタン
        if (this.saveButton) {
            this.saveButton.addEventListener('click', () => this.saveProfile());
        }
    }

    updateCharCount(input) {
        const counterId = input.id + '_count';
        const counter = document.getElementById(counterId);
        if (counter) {
            counter.textContent = input.value.length;

            // 文字数警告の色を変更
            const maxLength = parseInt(input.maxLength);
            if (input.value.length > maxLength * 0.8) {
                counter.classList.add('text-red-500');
            } else {
                counter.classList.remove('text-red-500');
            }
        }
    }

    checkForChanges() {
        const currentValues = {
            display_name: this.displayNameInput?.value || '',
            pronouns: this.pronounsInput?.value || '',
            location: this.locationInput?.value || '',
            custom_url: this.customUrlInput?.value || '',
            bio: this.bioInput?.value || '',
        };

        const hasChanges = Object.keys(currentValues).some(
            (key) => currentValues[key] !== this.originalValues[key],
        );

        // 変更インジケーターの表示/非表示
        if (this.changesIndicator) {
            this.changesIndicator.classList.toggle('hidden', !hasChanges);
        }

        // リセットボタンの表示/非表示
        if (this.resetButton) {
            this.resetButton.classList.toggle('hidden', !hasChanges);
        }

        // 保存ボタンの有効/無効
        if (this.saveButton) {
            this.saveButton.disabled = !hasChanges;
        }

        return hasChanges;
    }

    async checkCustomUrlAvailability() {
        const url = this.customUrlInput?.value.trim() || '';

        // 入力値の検証
        if (!url || url.length < 3) {
            this.updateUrlStatus('', '');
            return;
        }

        if (!/^[a-zA-Z0-9._]{3,30}$/.test(url)) {
            this.updateUrlStatus(
                'error',
                '英数字、ピリオド、アンダーバーのみで3文字以上30文字以下で入力してください',
            );
            return;
        }

        // 元の値と同じ場合はチェック不要
        if (url === this.originalValues.custom_url) {
            this.updateUrlStatus('', '');
            return;
        }

        // デバウンス処理
        clearTimeout(this.urlCheckTimeout);
        this.urlCheckTimeout = setTimeout(async () => {
            try {
                this.isCheckingUrl = true;
                this.updateUrlStatus('checking', '確認中...');

                const response = await fetch(
                    `/api/profile/custom-url/check?url=${encodeURIComponent(url)}`,
                );
                if (response.ok) {
                    const data = await response.json();
                    if (data.available) {
                        this.updateUrlStatus('success', '✓ 利用可能です');
                    } else {
                        this.updateUrlStatus(
                            'error',
                            '✗ このURLは既に使用されています',
                        );
                    }
                } else {
                    this.updateUrlStatus(
                        'error',
                        '確認中にエラーが発生しました',
                    );
                }
            } catch (error) {
                console.error('URL可用性チェックエラー:', error);
                this.updateUrlStatus('error', '確認中にエラーが発生しました');
            } finally {
                this.isCheckingUrl = false;
            }
        }, 500);
    }

    updateUrlStatus(type, message) {
        const statusElement = document.getElementById('custom_url_status');
        if (!statusElement) return;

        statusElement.textContent = message;
        statusElement.className = 'mt-2 text-sm';

        switch (type) {
            case 'success':
                statusElement.classList.add(
                    'text-green-600',
                    'dark:text-green-400',
                );
                break;
            case 'error':
                statusElement.classList.add(
                    'text-red-600',
                    'dark:text-red-400',
                );
                break;
            case 'checking':
                statusElement.classList.add(
                    'text-gray-600',
                    'dark:text-gray-400',
                );
                break;
            default:
                statusElement.className = 'mt-2 text-sm';
        }
    }

    togglePreview() {
        if (!this.previewSection) return;

        const isHidden = this.previewSection.classList.contains('hidden');

        if (isHidden) {
            this.showPreview();
            this.previewButton.innerHTML = `
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                プレビューを隠す
            `;
        } else {
            this.hidePreview();
            this.previewButton.innerHTML = `
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                </svg>
                プレビュー
            `;
        }
    }

    showPreview() {
        if (!this.previewSection) return;

        this.previewSection.classList.remove('hidden');

        // プレビュー内容を更新
        const displayName = this.displayNameInput?.value || '未設定';
        const pronouns = this.pronounsInput?.value || '未設定';
        const location = this.locationInput?.value || '未設定';
        const bio = this.bioInput?.value || '未設定';
        const customUrl = this.customUrlInput?.value || '未設定';

        const previewDisplayName = document.getElementById(
            'preview_display_name',
        );
        const previewPronouns = document.getElementById('preview_pronouns');
        const previewLocation = document.getElementById('preview_location');
        const previewBio = document.getElementById('preview_bio');
        const previewCustomUrl = document.getElementById('preview_custom_url');

        if (previewDisplayName) previewDisplayName.textContent = displayName;
        if (previewPronouns) previewPronouns.textContent = pronouns;
        if (previewLocation) previewLocation.textContent = location;
        if (previewBio) previewBio.textContent = bio;
        if (previewCustomUrl) {
            if (customUrl !== '未設定') {
                previewCustomUrl.textContent = `${window.location.origin}/p/${customUrl}`;
            } else {
                previewCustomUrl.textContent = 'カスタムURL未設定';
            }
        }
    }

    hidePreview() {
        if (this.previewSection) {
            this.previewSection.classList.add('hidden');
        }
    }

    resetToOriginal() {
        if (this.displayNameInput)
            this.displayNameInput.value = this.originalValues.display_name;
        if (this.pronounsInput)
            this.pronounsInput.value = this.originalValues.pronouns;
        if (this.locationInput)
            this.locationInput.value = this.originalValues.location;
        if (this.customUrlInput)
            this.customUrlInput.value = this.originalValues.custom_url;
        if (this.bioInput) this.bioInput.value = this.originalValues.bio;

        // 文字数カウンターを更新
        [
            this.displayNameInput,
            this.pronounsInput,
            this.locationInput,
            this.customUrlInput,
            this.bioInput,
        ].forEach((input) => {
            if (input) this.updateCharCount(input);
        });

        // カスタムURLステータスをリセット
        this.updateUrlStatus('', '');

        // 変更チェック
        this.checkForChanges();

        // プレビューを隠す
        this.hidePreview();
        if (this.previewButton) {
            this.previewButton.innerHTML = `
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                </svg>
                プレビュー
            `;
        }
    }

    async saveProfile() {
        if (!this.checkForChanges()) return;

        const saveBtn = this.saveButton;
        const originalText = saveBtn.innerHTML;

        saveBtn.disabled = true;
        saveBtn.innerHTML = `
            <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            保存中...
        `;

        try {
            const profileData = {
                display_name: this.displayNameInput?.value.trim() || '',
                bio: this.bioInput?.value.trim() || '',
                pronouns: this.pronounsInput?.value.trim() || '',
                location: this.locationInput?.value.trim() || '',
                custom_url: this.customUrlInput?.value.trim() || '',
            };

            const response = await fetch('/api/profile', {
                method: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': document
                        .querySelector('meta[name="csrf-token"]')
                        .getAttribute('content'),
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                },
                credentials: 'same-origin',
                body: JSON.stringify(profileData),
            });

            if (response.ok) {
                // 元の値を更新
                this.saveOriginalValues();

                // 変更チェック
                this.checkForChanges();

                // プレビューを隠す
                this.hidePreview();
                if (this.previewButton) {
                    this.previewButton.innerHTML = `
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        プレビュー
                    `;
                }

                if (window.toastManager) {
                    window.toastManager.success(
                        'プロフィールが保存されました！',
                    );
                }
            } else {
                const data = await response.json();
                throw new Error(
                    data.error || 'プロフィールの保存に失敗しました。',
                );
            }
        } catch (error) {
            console.error('プロフィール保存エラー:', error);
            if (window.toastManager) {
                window.toastManager.error(
                    'プロフィールの保存に失敗しました: ' + error.message,
                );
            }
        } finally {
            saveBtn.disabled = false;
            saveBtn.innerHTML = originalText;
        }
    }
}

// グローバル変数としてリンクマネージャーを初期化
let linkManager;

// グローバル変数として基本情報マネージャーを初期化
let profileBasicInfoManager;

// プロフィールセットアップ開始
async function startProfileSetup() {
    try {
        // 認証状態を確認
        const authStatus = await checkAuthStatus();
        if (!authStatus || !authStatus.authenticated) {
            throw new Error('ユーザーが認証されていません。');
        }

        const csrfToken = document
            .querySelector('meta[name="csrf-token"]')
            ?.getAttribute('content');

        const requestData = {
            display_name:
                document.querySelector('[data-discord-display-name]')?.dataset
                    .discordDisplayName || 'ユーザー',
            bio: '自己紹介を入力してください...',
        };

        const response = await fetch('/api/profile', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json',
                Accept: 'application/json',
            },
            credentials: 'same-origin',
            body: JSON.stringify(requestData),
        });

        if (response.ok) {
            // ページをリロードしてプロフィール設定画面を表示
            window.location.reload();
        } else {
            const data = await response.json();
            throw new Error(data.error || 'プロフィールの作成に失敗しました。');
        }
    } catch (error) {
        console.error('プロフィールセットアップエラー:', error);
        if (window.toastManager) {
            window.toastManager.error(
                'プロフィールの作成に失敗しました: ' + error.message,
            );
        }
    }
}

// カスタムURL可用性チェック
async function checkCustomUrlAvailability(url) {
    try {
        const response = await fetch(
            `/api/profile/custom-url/check?url=${encodeURIComponent(url)}`,
            {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': document
                        .querySelector('meta[name="csrf-token"]')
                        .getAttribute('content'),
                    Accept: 'application/json',
                },
                credentials: 'same-origin',
            },
        );

        const data = await response.json();
        const statusElement = document.getElementById('custom_url_status');

        if (data.available) {
            statusElement.textContent = '✓ 利用可能';
            statusElement.className = 'ml-2 text-green-600';
        } else {
            statusElement.textContent = '✗ 既に使用されています';
            statusElement.className = 'ml-2 text-red-600';
        }
    } catch (error) {
        console.error('URL可用性チェックエラー:', error);
        if (window.toastManager) {
            window.toastManager.error('URL可用性チェックに失敗しました');
        }
    }
}

// ファイル選択処理
async function handleFileSelect(file) {
    // ファイル検証
    if (!file.type.startsWith('image/')) {
        if (window.toastManager) {
            window.toastManager.warning('画像ファイルを選択してください。');
        }
        return;
    }

    if (file.size > 1024 * 1024) {
        // 1MB
        if (window.toastManager) {
            window.toastManager.warning(
                'ファイルサイズは1MB以下にしてください。',
            );
        }
        return;
    }

    try {
        // アップロード準備
        const response = await fetch('/api/profile/avatar/upload', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document
                    .querySelector('meta[name="csrf-token"]')
                    .getAttribute('content'),
                'Content-Type': 'application/json',
                Accept: 'application/json',
            },
            credentials: 'same-origin',
            body: JSON.stringify({
                fileName: file.name,
                fileType: file.type,
                fileSize: file.size,
            }),
        });

        const data = await response.json();

        if (response.ok) {
            // ファイルをアップロード
            await uploadFileToMinio(file, data.signedUrl);

            // プロフィールを更新
            await applyAvatar(
                data.avatarUrl,
                data.avatarHash,
                data.oldAvatarUrl,
            );
        } else {
            throw new Error(data.error || 'アップロード準備に失敗しました。');
        }
    } catch (error) {
        console.error('アップロードエラー:', error);
        if (window.toastManager) {
            window.toastManager.error(
                'アップロードに失敗しました: ' + error.message,
            );
        }
    }
}

// MinIOへのファイルアップロード
async function uploadFileToMinio(file, signedUrl) {
    const progressBar = document.getElementById('progress_bar');
    const progressText = document.getElementById('progress_text');
    const uploadProgress = document.getElementById('upload_progress');

    uploadProgress.classList.remove('hidden');

    try {
        const xhr = new XMLHttpRequest();

        xhr.upload.addEventListener('progress', (e) => {
            if (e.lengthComputable) {
                const percentComplete = (e.loaded / e.total) * 100;
                progressBar.style.width = percentComplete + '%';
                progressText.textContent = `アップロード中... ${Math.round(percentComplete)}%`;
            }
        });

        xhr.addEventListener('load', () => {
            if (xhr.status === 200) {
                progressText.textContent = 'アップロード完了！';
                setTimeout(() => {
                    uploadProgress.classList.add('hidden');
                }, 2000);
            }
        });

        xhr.addEventListener('error', () => {
            progressText.textContent = 'アップロードエラー';
            setTimeout(() => {
                uploadProgress.classList.add('hidden');
            }, 3000);
        });

        xhr.open('PUT', signedUrl);
        xhr.setRequestHeader('Content-Type', file.type);
        xhr.send(file);
    } catch (error) {
        throw new Error('ファイルアップロードに失敗しました。');
    }
}

// アバター適用
async function applyAvatar(avatarUrl, avatarHash, oldAvatarUrl) {
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
            // デバッグ用ログ
            console.log('Avatar update successful');
            console.log('Avatar URL:', avatarUrl);
            console.log(
                'Current avatar element:',
                document.getElementById('current_avatar'),
            );

            // アバター画像を更新
            const avatarElement = document.getElementById('current_avatar');
            if (avatarElement) {
                avatarElement.src = avatarUrl;
                console.log('Avatar src updated to:', avatarElement.src);
            } else {
                console.error('Current avatar element not found');
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
                    removeButton.addEventListener('click', removeAvatar);
                    avatarContainer.appendChild(removeButton);
                    console.log('Remove button added');
                } else {
                    console.error('Avatar container not found');
                }
            } else {
                console.log('Remove button already exists');
            }

            if (window.toastManager) {
                window.toastManager.success('アバターが更新されました！');
            }
        } else {
            throw new Error(data.error || 'アバターの適用に失敗しました。');
        }
    } catch (error) {
        console.error('アバター適用エラー:', error);
        if (window.toastManager) {
            window.toastManager.error(
                'アバターの適用に失敗しました: ' + error.message,
            );
        }
    }
}

// アバター削除
async function removeAvatar() {
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

        const data = await response.json();

        if (response.ok) {
            // デフォルトアバターに戻す
            const currentAvatar = document.getElementById('current_avatar');
            const defaultAvatarUrl = currentAvatar.dataset.defaultAvatar;
            currentAvatar.src = defaultAvatarUrl;

            // 削除ボタンを削除
            const removeBtn = document.getElementById('remove_avatar');
            if (removeBtn) {
                removeBtn.remove();
            }

            if (window.toastManager) {
                window.toastManager.success('アバターが削除されました。');
            }
        } else {
            throw new Error(data.error || 'アバターの削除に失敗しました。');
        }
    } catch (error) {
        console.error('アバター削除エラー:', error);
        if (window.toastManager) {
            window.toastManager.error(
                'アバターの削除に失敗しました: ' + error.message,
            );
        }
    }
}

// プロフィール保存
async function saveProfile() {
    // 基本情報マネージャーが存在する場合は、そちらで処理
    if (window.profileBasicInfoManager) {
        await window.profileBasicInfoManager.saveProfile();
        return;
    }

    // フォールバック処理（既存の実装）
    const saveBtn = document.getElementById('save_profile');
    saveBtn.disabled = true;
    saveBtn.innerHTML =
        '<svg class="animate-spin w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>保存中...';

    try {
        // リンクのバリデーション
        if (linkManager) {
            const linkErrors = linkManager.validateLinks();
            if (linkErrors.length > 0) {
                throw new Error(
                    'リンクの設定に問題があります:\n' + linkErrors.join('\n'),
                );
            }
        }

        const profileData = {
            display_name:
                document.getElementById('display_name')?.value?.trim() || '',
            bio: document.getElementById('bio')?.value?.trim() || '',
            pronouns: document.getElementById('pronouns')?.value?.trim() || '',
            location: document.getElementById('location')?.value?.trim() || '',
            custom_url:
                document.getElementById('custom_url')?.value?.trim() || '',
        };

        // リンクデータを追加
        if (linkManager) {
            profileData.links = linkManager.getLinksData();
        }

        const response = await fetch('/api/profile', {
            method: 'PUT',
            headers: {
                'X-CSRF-TOKEN': document
                    .querySelector('meta[name="csrf-token"]')
                    .getAttribute('content'),
                'Content-Type': 'application/json',
                Accept: 'application/json',
            },
            credentials: 'same-origin',
            body: JSON.stringify(profileData),
        });

        if (response.ok) {
            if (window.toastManager) {
                window.toastManager.success('プロフィールが保存されました！');
            }
        } else {
            const data = await response.json();
            throw new Error(data.error || 'プロフィールの保存に失敗しました。');
        }
    } catch (error) {
        console.error('プロフィール保存エラー:', error);
        if (window.toastManager) {
            window.toastManager.error(
                'プロフィールの保存に失敗しました: ' + error.message,
            );
        }
    } finally {
        saveBtn.disabled = false;
        saveBtn.innerHTML =
            '<svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>保存';
    }
}

// テーマ保存
// テーマ保存
async function saveTheme() {
    const saveBtn = document.getElementById('save_theme');
    saveBtn.disabled = true;
    saveBtn.innerHTML =
        '<svg class="animate-spin w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3.938l3-2.647z"></path></svg>保存中...';

    try {
        // カラーエディターから現在の値を取得
        const themeData = {};

        // 各カラーエディターの値を適切な形式で設定
        [
            'backgroundColor',
            'displayNameColor',
            'pronounsColor',
            'locationColor',
            'bioTextColor',
            'bioBackgroundColor',
        ].forEach((colorType) => {
            const editor = document.querySelector(
                `[data-color-type="${colorType}"]`,
            );
            if (!editor) return;

            const toggle = editor.querySelector('.gradient-toggle');
            const isGradient =
                toggle && toggle.classList.contains('bg-blue-600');

            const fieldName = colorType
                .replace(/([A-Z])/g, '_$1')
                .toLowerCase();

            if (isGradient) {
                // グラデーションモードの場合
                themeData[`${fieldName}_type`] = 'gradient';
                themeData[`${fieldName}_gradient_direction`] = parseInt(
                    editor.querySelector('.direction-slider')?.value || 135,
                );

                const stops = editor.querySelectorAll('.gradient-stop');
                const gradientStops = [];
                stops.forEach((stop, index) => {
                    const colorInput = stop.querySelector(
                        'input[type="color"]',
                    );
                    const positionInput = stop.querySelector(
                        'input[type="number"]',
                    );
                    if (colorInput && positionInput) {
                        gradientStops.push({
                            color: colorInput.value,
                            position: parseInt(positionInput.value),
                        });
                    }
                });
                themeData[`${fieldName}_gradient_stops`] = gradientStops;
            } else {
                // 単色モードの場合
                themeData[`${fieldName}_type`] = 'solid';
                const colorInput = editor.querySelector('input[type="color"]');
                if (colorInput) {
                    themeData[`${fieldName}_solid`] = colorInput.value;
                }
            }
        });

        console.log('保存するテーマデータ:', themeData);

        // テーマデータを適切な形式で送信
        const requestData = {
            theme: themeData,
        };

        const response = await fetch('/api/profile', {
            method: 'PUT',
            headers: {
                'X-CSRF-TOKEN': document
                    .querySelector('meta[name="csrf-token"]')
                    .getAttribute('content'),
                'Content-Type': 'application/json',
                Accept: 'application/json',
            },
            credentials: 'same-origin',
            body: JSON.stringify(requestData),
        });

        if (response.ok) {
            if (window.toastManager) {
                window.toastManager.success('テーマが保存されました！');
            }
            // 変更インジケーターを非表示
            hideThemeChangesIndicator();
        } else {
            const data = await response.json();
            console.error('テーマ保存エラーレスポンス:', data);

            // バリデーションエラーの詳細を表示
            if (data.errors) {
                const errorMessages = Object.values(data.errors)
                    .flat()
                    .join('\n');
                throw new Error(`バリデーションエラー:\n${errorMessages}`);
            } else if (data.message) {
                throw new Error(data.message);
            } else {
                throw new Error(data.error || 'テーマの保存に失敗しました。');
            }
        }
    } catch (error) {
        console.error('テーマ保存エラー:', error);
        if (window.toastManager) {
            window.toastManager.error(
                'テーマの保存に失敗しました: ' + error.message,
            );
        }
    } finally {
        saveBtn.disabled = false;
        saveBtn.innerHTML =
            '<svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>保存';
    }
}

// カラーエディターから値を取得するヘルパー関数
function getColorValue(colorType) {
    const editor = document.querySelector(`[data-color-type="${colorType}"]`);
    if (!editor) return null;

    const toggle = editor.querySelector('.gradient-toggle');
    const isGradient = toggle && toggle.classList.contains('bg-blue-600');

    if (isGradient) {
        // グラデーションモードの場合
        const stops = editor.querySelectorAll(
            '.gradient-stop input[type="color"]',
        );
        const direction =
            editor.querySelector('.direction-slider')?.value || 135;

        if (stops.length >= 2) {
            const colors = Array.from(stops).map((stop) => stop.value);
            const positions = Array.from(stops).map((stop, index) => {
                const positionInput = stop.querySelector(
                    'input[type="number"]',
                );
                return positionInput
                    ? parseInt(positionInput.value)
                    : Math.round((index / (stops.length - 1)) * 100);
            });

            return `linear-gradient(${direction}deg, ${colors
                .map((color, index) => `${color} ${positions[index]}%`)
                .join(', ')})`;
        }
    }

    // 単色モードの場合
    const colorInput = editor.querySelector('input[type="color"]');
    return colorInput ? colorInput.value : null;
}

// テーマ変更インジケーターの表示
function showThemeChangesIndicator() {
    const revertBtn = document.getElementById('revert_theme');
    if (revertBtn) {
        revertBtn.classList.remove('hidden');
    }

    const saveMessage = document.getElementById('save_message');
    if (saveMessage) {
        saveMessage.classList.remove('hidden');
        saveMessage.textContent = 'テーマに変更があります';
        saveMessage.className = 'text-sm text-orange-600 dark:text-orange-400';
    }
}

// テーマ変更インジケーターの非表示
function hideThemeChangesIndicator() {
    const revertBtn = document.getElementById('revert_theme');
    if (revertBtn) {
        revertBtn.classList.add('hidden');
    }

    const saveMessage = document.getElementById('save_message');
    if (saveMessage) {
        saveMessage.classList.add('hidden');
    }
}

// ポートフォリオ削除
async function deletePortfolio() {
    if (window.confirmDialog) {
        const confirmed = await window.confirmDialog.confirm(
            'ポートフォリオ削除の確認',
            '本当にポートフォリオを削除しますか？この操作は取り消せません。',
            '削除',
            'キャンセル',
        );
        if (!confirmed) {
            return;
        }
    } else {
        // フォールバック
        if (
            !confirm(
                '本当にポートフォリオを削除しますか？この操作は取り消せません。',
            )
        ) {
            return;
        }
    }

    try {
        const response = await fetch('/api/profile', {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document
                    .querySelector('meta[name="csrf-token"]')
                    ?.getAttribute('content'),
                'Content-Type': 'application/json',
                Accept: 'application/json',
            },
            credentials: 'same-origin',
        });

        if (response.ok) {
            if (window.toastManager) {
                window.toastManager.success('ポートフォリオが削除されました。');
            }
            window.location.reload();
        } else {
            const data = await response.json();
            throw new Error(data.error || 'ポートフォリオの削除に失敗しました');
        }
    } catch (error) {
        console.error('ポートフォリオ削除エラー:', error);
        if (window.toastManager) {
            window.toastManager.error(
                'ポートフォリオの削除に失敗しました: ' + error.message,
            );
        }
    }
}

// 既存リンクの削除機能
async function removeExistingLink(linkId, displayName) {
    if (window.confirmDialog) {
        const confirmed = await window.confirmDialog.confirm(
            'リンク削除の確認',
            `「${displayName}」のリンクを削除しますか？`,
            '削除',
            'キャンセル',
        );
        if (!confirmed) {
            return;
        }
    } else {
        // フォールバック
        if (!confirm(`「${displayName}」のリンクを削除しますか？`)) {
            return;
        }
    }

    try {
        const response = await fetch(`/api/profile/links/${linkId}`, {
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
            if (window.toastManager) {
                window.toastManager.success('リンクが削除されました。');
            }

            // 削除されたリンクをUIからも削除
            const linkItem = document.querySelector(
                `[data-link-id="${linkId}"]`,
            );
            if (linkItem) {
                linkItem.remove();

                // 変更検知を更新
                if (window.linkManager) {
                    window.linkManager.checkForChanges();
                }

                // リンク数を更新
                const currentLinksContainer = document.getElementById(
                    'current_links_container',
                );
                if (currentLinksContainer) {
                    const linkCount =
                        currentLinksContainer.querySelectorAll(
                            '.link-item',
                        ).length;
                    const countElement =
                        currentLinksContainer.previousElementSibling;
                    if (countElement && countElement.tagName === 'H4') {
                        countElement.textContent = `現在のリンク (${linkCount}/10)`;
                    }
                }
            } else {
                // ページをリロードしてリンク一覧を更新
                window.location.reload();
            }
        } else {
            const data = await response.json();
            throw new Error(data.error || 'リンクの削除に失敗しました。');
        }
    } catch (error) {
        console.error('リンク削除エラー:', error);
        if (window.toastManager) {
            window.toastManager.error(
                'リンクの削除に失敗しました: ' + error.message,
            );
        }
    }
}

// DOMContentLoadedイベントリスナー
document.addEventListener('DOMContentLoaded', function () {
    console.log('DOMContentLoadedイベントが発火しました');

    // セットアップ開始ボタンの設定
    const startSetupBtn = document.getElementById('start_setup');
    console.log('セットアップ開始ボタンの検索結果:', startSetupBtn);

    if (startSetupBtn) {
        console.log('セットアップ開始ボタンにイベントリスナーを設定します');
        startSetupBtn.addEventListener('click', function (e) {
            console.log('セットアップ開始ボタンがクリックされました');
            e.preventDefault();
            startProfileSetup();
        });
    } else {
        console.error('セットアップ開始ボタンが見つかりません');
    }

    // プロフィールが存在する場合のみ実行する処理
    if (document.querySelector('[data-profile-exists="true"]')) {
        initializeProfileFeatures();
    }
});

// プロフィール機能の初期化
function initializeProfileFeatures() {
    // 文字数カウンター
    initializeCharCounters();

    // カスタムURL可用性チェック
    initializeCustomUrlCheck();

    // アバターアップロード処理
    initializeAvatarUpload();

    // 保存ボタン
    initializeSaveButtons();

    // テーマ関連の機能
    initializeThemeEditor();

    // リンク管理機能の初期化
    initializeLinkManager();

    // 基本情報管理機能の初期化
    initializeProfileBasicInfoManager();
}

// 文字数カウンターの初期化
function initializeCharCounters() {
    function updateCharCount(inputId, counterId, maxLength) {
        const input = document.getElementById(inputId);
        const counter = document.getElementById(counterId);

        if (input && counter) {
            input.addEventListener('input', function () {
                const count = this.value.length;
                counter.textContent = count;

                if (count > maxLength * 0.8) {
                    counter.classList.add('text-red-500');
                } else {
                    counter.classList.remove('text-red-500');
                }
            });
        }
    }

    // 各フィールドの文字数カウンターを初期化
    updateCharCount('display_name', 'display_name_count', 50);
    updateCharCount('pronouns', 'pronouns_count', 32);
    updateCharCount('location', 'location_count', 32);
    updateCharCount('custom_url', 'custom_url_count', 30);
    updateCharCount('bio', 'bio_count', 500);
}

// カスタムURL可用性チェックの初期化
function initializeCustomUrlCheck() {
    const customUrlInput = document.getElementById('custom_url');
    const customUrlStatus = document.getElementById('custom_url_status');

    if (customUrlInput && customUrlStatus) {
        let checkTimeout;

        customUrlInput.addEventListener('input', function () {
            clearTimeout(checkTimeout);
            const url = this.value.trim();

            if (url.length >= 3) {
                checkTimeout = setTimeout(() => {
                    checkCustomUrlAvailability(url);
                }, 500);
            } else {
                customUrlStatus.textContent = '';
            }
        });
    }
}

// アバターアップロード処理の初期化
function initializeAvatarUpload() {
    const uploadArea = document.getElementById('upload_area');
    const fileInput = document.getElementById('avatar_file');

    if (uploadArea && fileInput) {
        // クリックでファイル選択
        uploadArea.addEventListener('click', () => fileInput.click());

        // ドラッグ&ドロップ
        uploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            uploadArea.classList.add('border-blue-400', 'bg-blue-50');
        });

        uploadArea.addEventListener('dragleave', () => {
            uploadArea.classList.remove('border-blue-400', 'bg-blue-50');
        });

        uploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            uploadArea.classList.remove('border-blue-400', 'bg-blue-50');

            const files = e.dataTransfer.files;
            if (files.length > 0) {
                handleFileSelect(files[0]);
            }
        });

        // ファイル選択
        fileInput.addEventListener('change', (e) => {
            if (e.target.files.length > 0) {
                handleFileSelect(e.target.files[0]);
            }
        });
    }
}

// 保存ボタンの初期化
function initializeSaveButtons() {
    const saveProfileBtn = document.getElementById('save_profile');
    if (saveProfileBtn) {
        saveProfileBtn.addEventListener('click', saveProfile);
    }

    const saveThemeBtn = document.getElementById('save_theme');
    if (saveThemeBtn) {
        saveThemeBtn.addEventListener('click', saveTheme);
    }

    const deletePortfolioBtn = document.getElementById('delete_portfolio');
    if (deletePortfolioBtn) {
        deletePortfolioBtn.addEventListener('click', deletePortfolio);
    }

    const removeAvatarBtn = document.getElementById('remove_avatar');
    if (removeAvatarBtn) {
        removeAvatarBtn.addEventListener('click', removeAvatar);
    }
}

// テーマエディターの初期化
function initializeThemeEditor() {
    // プリセットテーマの選択
    document.querySelectorAll('.theme-preset').forEach((button) => {
        button.addEventListener('click', function () {
            const themeName = this.dataset.theme;
            applyPresetTheme(themeName);
        });
    });

    // プレビューボタン
    const previewBtn = document.getElementById('preview_theme');
    if (previewBtn) {
        previewBtn.addEventListener('click', togglePreview);
    }

    // 展開/折りたたみボタン
    const expandBtn = document.getElementById('toggle_theme_expanded');
    if (expandBtn) {
        expandBtn.addEventListener('click', toggleExpanded);
    }

    // グラデーショントグル
    document.querySelectorAll('.gradient-toggle').forEach((toggle) => {
        toggle.addEventListener('click', function () {
            toggleGradientMode(this);
        });
    });

    // カラーピッカー
    document
        .querySelectorAll('.color-editor input[type="color"]')
        .forEach((input) => {
            input.addEventListener('change', function () {
                updateColorPreview(this);
                showThemeChangesIndicator();
            });
        });

    // 方向スライダー
    document.querySelectorAll('.direction-slider').forEach((slider) => {
        slider.addEventListener('input', function () {
            updateDirectionValue(this);
            updateGradientPreview(this.closest('.color-editor'));
            showThemeChangesIndicator();
        });
    });

    // カラーストップ追加
    document.querySelectorAll('.add-stop-btn').forEach((btn) => {
        btn.addEventListener('click', function () {
            addGradientStop(this);
        });
    });

    // カラーストップ削除
    document.querySelectorAll('.gradient-stop').forEach((stop) => {
        const removeBtn = stop.querySelector('.remove-stop-btn');
        if (removeBtn) {
            removeBtn.addEventListener('click', function () {
                removeGradientStop(this);
            });
        }

        // 既存のグラデーションストップにもイベントリスナーを追加
        const colorInput = stop.querySelector('input[type="color"]');
        if (colorInput) {
            colorInput.addEventListener('change', function () {
                updateColorPreview(this);
                showThemeChangesIndicator();
            });
        }

        const positionInput = stop.querySelector('input[type="number"]');
        if (positionInput) {
            positionInput.addEventListener('input', function () {
                updateGradientPreview(stop.closest('.color-editor'));
                showThemeChangesIndicator();
            });
        }
    });

    // アクションボタン
    const resetBtn = document.getElementById('reset_theme');
    if (resetBtn) {
        resetBtn.addEventListener('click', resetTheme);
    }

    const revertBtn = document.getElementById('revert_theme');
    if (revertBtn) {
        revertBtn.addEventListener('click', revertTheme);
    }
}

// プリセットテーマの適用
function applyPresetTheme(themeName) {
    const themes = {
        default: {
            backgroundColor: '#8686f0',
            displayNameColor: '#ffffff',
            pronounsColor: '#e2e8f0',
            locationColor: '#f1f5f9',
            bioTextColor: '#ffffff',
            bioBackgroundColor: 'rgba(255, 255, 255, 0.1)',
        },
        dark: {
            backgroundColor: '#1f2937',
            displayNameColor: '#f9fafb',
            pronounsColor: '#d1d5db',
            locationColor: '#9ca3af',
            bioTextColor: '#f3f4f6',
            bioBackgroundColor: 'rgba(0, 0, 0, 0.3)',
        },
        ocean: {
            backgroundColor:
                'linear-gradient(135deg, #0ea5e9 0%, #06b6d4 100%)',
            displayNameColor: '#ffffff',
            pronounsColor: '#e0f2fe',
            locationColor: '#bae6fd',
            bioTextColor: '#ffffff',
            bioBackgroundColor: 'rgba(255, 255, 255, 0.2)',
        },
        sunset: {
            backgroundColor:
                'linear-gradient(45deg, #f97316 0%, #dc2626 50%, #9333ea 100%)',
            displayNameColor: '#ffffff',
            pronounsColor: '#fed7aa',
            locationColor: '#fdba74',
            bioTextColor: '#ffffff',
            bioBackgroundColor: 'rgba(255, 255, 255, 0.2)',
        },
    };

    const theme = themes[themeName];
    if (theme) {
        Object.keys(theme).forEach((key) => {
            const editor = document.querySelector(`[data-color-type="${key}"]`);
            if (editor) {
                const colorInput = editor.querySelector('input[type="color"]');
                const preview = editor.querySelector('.color-preview');

                if (colorInput && preview) {
                    if (theme[key].startsWith('linear-gradient')) {
                        // グラデーションの場合
                        preview.style.background = theme[key];
                        // グラデーションモードに切り替え
                        const toggle = editor.querySelector('.gradient-toggle');
                        if (toggle) {
                            toggleGradientMode(toggle);
                        }
                    } else {
                        // 単色の場合
                        colorInput.value = theme[key];
                        preview.style.background = theme[key];
                    }
                }
            }
        });

        // プレビューを更新
        updateThemePreview();

        // 変更インジケーターを表示
        showThemeChangesIndicator();
    }
}

// プレビューの表示/非表示
function togglePreview() {
    const preview = document.getElementById('theme_preview');
    if (preview) {
        preview.classList.toggle('hidden');
        updateThemePreview();
    }
}

// 展開/折りたたみ
function toggleExpanded() {
    const settings = document.getElementById('theme_color_settings');
    const icon = document.querySelector('#toggle_theme_expanded svg');

    if (settings && icon) {
        settings.classList.toggle('hidden');

        if (settings.classList.contains('hidden')) {
            icon.innerHTML =
                '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zM21 5a2 2 0 00-2-2h-4a2 2 0 00-2 2v12a4 4 0 004 4h4a2 2 0 002-2V5z"></path>';
        } else {
            icon.innerHTML =
                '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>';
        }
    }
}

// グラデーションモードの切り替え
function toggleGradientMode(toggle) {
    const editor = toggle.closest('.color-editor');
    const solidInput = editor.querySelector('.solid-color-input');
    const gradientSettings = editor.querySelector('.gradient-settings');

    if (solidInput && gradientSettings) {
        const isGradient = toggle.classList.contains('bg-blue-600');

        if (isGradient) {
            // 単色モードに切り替え
            toggle.classList.remove('bg-blue-600');
            toggle.classList.add('bg-gray-300', 'dark:bg-gray-600');
            toggle.querySelector('span').classList.remove('translate-x-5');
            toggle.querySelector('span').classList.add('translate-x-1');

            solidInput.classList.remove('hidden');
            gradientSettings.classList.add('hidden');

            // 単色プレビューを更新
            const colorInput = editor.querySelector('input[type="color"]');
            if (colorInput) {
                updateColorPreview(colorInput);
            }
        } else {
            // グラデーションモードに切り替え
            toggle.classList.remove('bg-gray-300', 'dark:bg-gray-600');
            toggle.classList.add('bg-blue-600');
            toggle.querySelector('span').classList.remove('translate-x-1');
            toggle.querySelector('span').classList.add('translate-x-5');

            solidInput.classList.add('hidden');
            gradientSettings.classList.remove('hidden');

            // グラデーションプレビューを更新
            updateGradientPreview(editor);
        }

        // 変更インジケーターを表示
        showThemeChangesIndicator();
    }
}

// カラープレビューの更新
function updateColorPreview(input) {
    const editor = input.closest('.color-editor');
    const preview = editor.querySelector('.color-preview');

    if (preview) {
        preview.style.background = input.value;
    }

    updateThemePreview();
}

// グラデーションプレビューの更新
function updateGradientPreview(editor) {
    const preview = editor.querySelector('.color-preview');
    const toggle = editor.querySelector('.gradient-toggle');
    const isGradient = toggle && toggle.classList.contains('bg-blue-600');

    if (preview && isGradient) {
        const stops = editor.querySelectorAll(
            '.gradient-stop input[type="color"]',
        );
        const direction =
            editor.querySelector('.direction-slider')?.value || 135;

        if (stops.length >= 2) {
            const colors = Array.from(stops).map((stop) => stop.value);
            const positions = Array.from(stops).map((stop, index) => {
                const positionInput = stop.querySelector(
                    'input[type="number"]',
                );
                return positionInput
                    ? parseInt(positionInput.value)
                    : Math.round((index / (stops.length - 1)) * 100);
            });

            const gradient = `linear-gradient(${direction}deg, ${colors
                .map((color, index) => `${color} ${positions[index]}%`)
                .join(', ')})`;

            preview.style.background = gradient;
            updateThemePreview();
        }
    }
}

// 方向値の更新
function updateDirectionValue(slider) {
    const editor = slider.closest('.color-editor');
    const directionValue = editor.querySelector('.direction-value');

    if (directionValue) {
        directionValue.textContent = slider.value;
    }
}

// グラデーションストップの追加
function addGradientStop(btn) {
    const editor = btn.closest('.color-editor');
    const stopsContainer = editor.querySelector('.gradient-stops');
    const stopsCount = editor.querySelector('.stops-count');

    if (stopsContainer && stopsCount) {
        const currentCount = stopsContainer.children.length;

        if (currentCount < 5) {
            const newStop = document.createElement('div');
            newStop.className = 'gradient-stop flex items-center gap-2';
            newStop.dataset.stopIndex = currentCount;

            newStop.innerHTML = `
                <svg class="h-3 w-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4"></path>
                </svg>
                <input type="color" value="#ffffff" class="h-8 w-12 cursor-pointer rounded border border-gray-300">
                <input type="number" min="0" max="100" value="50" class="w-16 rounded border border-gray-300 px-2 py-1 text-xs dark:border-gray-600 dark:bg-gray-700"> <span class="text-xs text-gray-500">%</span>
                <button type="button" class="remove-stop-btn text-red-500 hover:text-red-700">
                    <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                    </svg>
                </button>
            `;

            stopsContainer.appendChild(newStop);
            stopsCount.textContent = currentCount + 1;

            // 削除ボタンのイベントリスナーを追加
            const removeBtn = newStop.querySelector('.remove-stop-btn');
            if (removeBtn) {
                removeBtn.addEventListener('click', function () {
                    removeGradientStop(this);
                });
            }

            // カラーピッカーのイベントリスナーを追加
            const colorInput = newStop.querySelector('input[type="color"]');
            if (colorInput) {
                colorInput.addEventListener('change', function () {
                    updateColorPreview(this);
                    showThemeChangesIndicator();
                });
            }

            // 位置のイベントリスナーを追加
            const positionInput = newStop.querySelector('input[type="number"]');
            if (positionInput) {
                positionInput.addEventListener('input', function () {
                    updateGradientPreview(editor);
                    showThemeChangesIndicator();
                });
            }
        }
    }
}

// グラデーションストップの削除
function removeGradientStop(btn) {
    const stop = btn.closest('.gradient-stop');
    const editor = btn.closest('.color-editor');
    const stopsCount = editor.querySelector('.stops-count');

    if (stop && stopsCount) {
        const currentCount = editor.querySelectorAll('.gradient-stop').length;

        if (currentCount > 2) {
            stop.remove();
            stopsCount.textContent = currentCount - 1;

            // インデックスを再設定
            editor.querySelectorAll('.gradient-stop').forEach((stop, index) => {
                stop.dataset.stopIndex = index;
            });
        }
    }
}

// テーマプレビューの更新
function updateThemePreview() {
    const preview = document.getElementById('theme_preview');
    if (!preview) return;

    const backgroundColor = getCurrentBackgroundColor();
    const displayNameColor = getCurrentColor('displayNameColor');
    const pronounsColor = getCurrentColor('pronounsColor');
    const locationColor = getCurrentColor('locationColor');
    const bioTextColor = getCurrentColor('bioTextColor');
    const bioBackgroundColor = getCurrentColor('bioBackgroundColor');

    // 背景色を更新
    const previewContainer = preview.querySelector('.rounded-lg');
    if (previewContainer) {
        previewContainer.style.background = backgroundColor;
    }

    // テキスト色を更新
    const nameElement = preview.querySelector('h1');
    if (nameElement) {
        nameElement.style.color = displayNameColor;
    }

    const pronounsElement = preview.querySelector('h2');
    if (pronounsElement) {
        pronounsElement.style.color = pronounsColor;
    }

    const locationElement = preview.querySelector('.text-gray-100');
    if (locationElement) {
        locationElement.style.color = locationColor;
    }

    const bioElement = preview.querySelector('p');
    if (bioElement) {
        bioElement.style.color = bioTextColor;
    }

    const bioBackgroundElement = preview.querySelector('.mt-4.rounded-lg');
    if (bioBackgroundElement) {
        bioBackgroundElement.style.background = bioBackgroundColor;
    }
}

// 現在の背景色を取得
function getCurrentBackgroundColor() {
    const editor = document.querySelector(
        '[data-color-type="backgroundColor"]',
    );
    if (!editor) return '#8686f0';

    const toggle = editor.querySelector('.gradient-toggle');
    const isGradient = toggle && toggle.classList.contains('bg-blue-600');

    if (isGradient) {
        // グラデーションの場合
        const stops = editor.querySelectorAll(
            '.gradient-stop input[type="color"]',
        );
        const direction =
            editor.querySelector('.direction-slider')?.value || 135;

        if (stops.length >= 2) {
            const colors = Array.from(stops).map((stop) => stop.value);
            const positions = Array.from(stops).map((stop, index) =>
                Math.round((index / (stops.length - 1)) * 100),
            );

            return `linear-gradient(${direction}deg, ${colors
                .map((color, index) => `${color} ${positions[index]}%`)
                .join(', ')})`;
        }
    }

    // 単色の場合
    const colorInput = editor.querySelector('input[type="color"]');
    return colorInput ? colorInput.value : '#8686f0';
}

// 現在の色を取得
function getCurrentColor(colorType) {
    const editor = document.querySelector(`[data-color-type="${colorType}"]`);
    if (!editor) return '#000000';

    const colorInput = editor.querySelector('input[type="color"]');
    return colorInput ? colorInput.value : '#000000';
}

// テーマのリセット
function resetTheme() {
    applyPresetTheme('default');
}

// テーマの変更を破棄
function revertTheme() {
    // 元のテーマに戻す処理（必要に応じて実装）
    console.log('テーマの変更を破棄しました');
}

// リンク管理機能の初期化
function initializeLinkManager() {
    linkManager = new LinkManager();
    // グローバル変数としても設定
    window.linkManager = linkManager;
}

// 基本情報管理機能の初期化
function initializeProfileBasicInfoManager() {
    profileBasicInfoManager = new ProfileBasicInfoManager();
    // グローバル変数としても設定
    window.profileBasicInfoManager = profileBasicInfoManager;
}
