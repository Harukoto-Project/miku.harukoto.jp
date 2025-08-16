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
            this.updateLinksOrder();
        });
    }

    // ドラッグ後の要素を取得
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

    // リンクの順序を更新
    updateLinksOrder() {
        const linkItems =
            this.currentLinksContainer.querySelectorAll('.link-item');
        const newOrder = [];

        linkItems.forEach((item) => {
            const linkId = item.dataset.linkId;
            const link = this.links.find((l) => l.id === linkId);
            if (link) {
                newOrder.push(link);
            }
        });

        this.links = newOrder;
        this.checkForChanges();
    }

    // 既存のリンクを読み込み
    loadExistingLinks() {
        if (!this.currentLinksContainer) return;

        const linkItems =
            this.currentLinksContainer.querySelectorAll('.link-item');
        this.links = [];
        this.originalLinks = [];

        linkItems.forEach((item) => {
            const linkData = {
                id: item.dataset.linkId,
                title: item.querySelector('.link-title')?.textContent || '',
                url: item.querySelector('.link-url')?.href || '',
                icon: item.querySelector('.link-icon i')?.className || '',
                color: item.querySelector('.link-color')?.style.backgroundColor || '#000000',
                type: item.dataset.linkType || 'custom',
            };

            this.links.push(linkData);
            this.originalLinks.push({ ...linkData });
        });

        this.nextId = Math.max(...this.links.map((l) => l.id), 0) + 1;
    }

    // 新しいリンクを追加
    addLink() {
        if (this.links.length >= this.maxLinks) {
            if (window.toastManager) {
                window.toastManager.error(
                    `リンクは最大${this.maxLinks}個まで追加できます。`,
                );
            }
            return;
        }

        const newLink = {
            id: this.nextId++,
            title: '',
            url: '',
            icon: 'fas fa-link',
            color: '#000000',
            type: 'custom',
        };

        this.links.push(newLink);
        this.renderLink(newLink);
        this.checkForChanges();
    }

    // リンクを削除
    removeLink(linkId) {
        this.links = this.links.filter((link) => link.id !== linkId);
        const linkElement = document.querySelector(
            `[data-link-id="${linkId}"]`,
        );
        if (linkElement) {
            linkElement.remove();
        }
        this.checkForChanges();
    }

    // リンクを編集
    editLink(linkId) {
        const link = this.links.find((l) => l.id === linkId);
        if (!link) return;

        const linkElement = document.querySelector(
            `[data-link-id="${linkId}"]`,
        );
        if (!linkElement) return;

        // 編集モードに切り替え
        linkElement.classList.add('editing');
        this.showEditForm(linkElement, link);
    }

    // 編集フォームを表示
    showEditForm(linkElement, link) {
        const titleElement = linkElement.querySelector('.link-title');
        const urlElement = linkElement.querySelector('.link-url');

        if (titleElement && urlElement) {
            // 入力フィールドを作成
            const titleInput = document.createElement('input');
            titleInput.type = 'text';
            titleInput.value = link.title;
            titleInput.className = 'w-full px-2 py-1 border rounded';

            const urlInput = document.createElement('input');
            urlInput.type = 'url';
            urlInput.value = link.url;
            urlInput.className = 'w-full px-2 py-1 border rounded';

            // 元の要素を置き換え
            titleElement.replaceWith(titleInput);
            urlElement.replaceWith(urlInput);

            // 保存ボタンを追加
            const saveBtn = document.createElement('button');
            saveBtn.textContent = '保存';
            saveBtn.className = 'px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600';
            saveBtn.onclick = () => this.saveLinkEdit(linkId, titleInput.value, urlInput.value);

            const cancelBtn = document.createElement('button');
            cancelBtn.textContent = 'キャンセル';
            cancelBtn.className = 'px-3 py-1 bg-gray-500 text-white rounded hover:bg-gray-600 ml-2';
            cancelBtn.onclick = () => this.cancelLinkEdit(linkId, link);

            // ボタンコンテナを作成
            const buttonContainer = document.createElement('div');
            buttonContainer.className = 'mt-2';
            buttonContainer.appendChild(saveBtn);
            buttonContainer.appendChild(cancelBtn);

            linkElement.appendChild(buttonContainer);
        }
    }

    // リンク編集を保存
    saveLinkEdit(linkId, title, url) {
        const link = this.links.find((l) => l.id === linkId);
        if (link) {
            link.title = title;
            link.url = url;
            this.renderLink(link);
            this.checkForChanges();
        }
    }

    // リンク編集をキャンセル
    cancelLinkEdit(linkId, originalLink) {
        this.renderLink(originalLink);
    }

    // リンクをレンダリング
    renderLink(link) {
        let linkElement = document.querySelector(
            `[data-link-id="${link.id}"]`,
        );

        if (!linkElement) {
            linkElement = document.createElement('div');
            linkElement.className = 'link-item bg-white rounded-lg shadow-md p-4 mb-3 relative';
            linkElement.dataset.linkId = link.id;
            linkElement.dataset.linkType = link.type;
            this.currentLinksContainer.appendChild(linkElement);
        }

        linkElement.innerHTML = `
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3 flex-1">
                    <div class="drag-handle cursor-move text-gray-400 hover:text-gray-600">
                        <i class="fas fa-grip-vertical"></i>
                    </div>
                    <div class="link-icon w-8 h-8 rounded-full flex items-center justify-center text-white" style="background-color: ${link.color}">
                        <i class="${link.icon}"></i>
                    </div>
                    <div class="flex-1">
                        <div class="link-title font-medium text-gray-900">${link.title || '無題のリンク'}</div>
                        <div class="link-url text-sm text-gray-500">${link.url || 'URLが設定されていません'}</div>
                    </div>
                </div>
                <div class="flex items-center space-x-2">
                    <button onclick="linkManager.editLink('${link.id}')" class="text-blue-500 hover:text-blue-700">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button onclick="linkManager.removeLink('${link.id}')" class="text-red-500 hover:text-red-700">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        `;

        // ドラッグ&ドロップを再設定
        this.setupDragAndDrop();
    }

    // リンクを保存
    async saveLinks() {
        try {
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
                body: JSON.stringify({ links: this.links }),
            });

            if (response.ok) {
                this.originalLinks = this.links.map((link) => ({ ...link }));
                this.checkForChanges();

                if (window.toastManager) {
                    window.toastManager.success('リンクが保存されました！');
                }
            } else {
                const data = await response.json();
                throw new Error(data.error || 'リンクの保存に失敗しました。');
            }
        } catch (error) {
            if (window.toastManager) {
                window.toastManager.error(
                    'リンクの保存に失敗しました: ' + error.message,
                );
            }
        }
    }

    // リンクをリセット
    resetLinks() {
        this.links = this.originalLinks.map((link) => ({ ...link }));
        this.renderAllLinks();
        this.checkForChanges();
    }

    // 全てのリンクをレンダリング
    renderAllLinks() {
        if (this.currentLinksContainer) {
            this.currentLinksContainer.innerHTML = '';
            this.links.forEach((link) => this.renderLink(link));
        }
    }

    // 変更があるかチェック
    checkForChanges() {
        if (!this.changesIndicator) return;

        const hasChanges = this.links.some((link, index) => {
            const original = this.originalLinks[index];
            return !original || JSON.stringify(link) !== JSON.stringify(original);
        });

        if (hasChanges) {
            this.changesIndicator.classList.remove('hidden');
        } else {
            this.changesIndicator.classList.add('hidden');
        }
    }
}

// グローバルに公開
window.LinkManager = LinkManager;
