// プロフィール基本情報管理機能
class ProfileBasicInfoManager {
    constructor() {
        this.displayNameInput = document.getElementById('display_name');
        this.pronounsInput = document.getElementById('pronouns');
        this.locationInput = document.getElementById('location');
        this.customUrlInput = document.getElementById('custom_url');
        this.bioInput = document.getElementById('bio');
        this.saveBtn = document.getElementById('save_profile');
        this.resetBtn = document.getElementById('reset_profile');
        this.changesIndicator = document.getElementById('profile_changes_indicator');

        this.originalData = {};
        this.currentData = {};

        this.initialize();
    }

    initialize() {
        this.loadOriginalData();
        this.setupEventListeners();
        this.checkForChanges();
    }

    // 元のデータを読み込み
    loadOriginalData() {
        this.originalData = {
            display_name: this.displayNameInput?.value || '',
            pronouns: this.pronounsInput?.value || '',
            location: this.locationInput?.value || '',
            custom_url: this.customUrlInput?.value || '',
            bio: this.bioInput?.value || '',
        };

        this.currentData = { ...this.originalData };
    }

    // イベントリスナーを設定
    setupEventListeners() {
        if (this.displayNameInput) {
            this.displayNameInput.addEventListener('input', () => this.handleInputChange('display_name'));
        }

        if (this.pronounsInput) {
            this.pronounsInput.addEventListener('input', () => this.handleInputChange('pronouns'));
        }

        if (this.locationInput) {
            this.locationInput.addEventListener('input', () => this.handleInputChange('location'));
        }

        if (this.customUrlInput) {
            this.customUrlInput.addEventListener('input', () => this.handleInputChange('custom_url'));
        }

        if (this.bioInput) {
            this.bioInput.addEventListener('input', () => this.handleInputChange('bio'));
        }

        if (this.saveBtn) {
            this.saveBtn.addEventListener('click', () => this.saveProfile());
        }

        if (this.resetBtn) {
            this.resetBtn.addEventListener('click', () => this.resetProfile());
        }
    }

    // 入力変更を処理
    handleInputChange(field) {
        if (this[field + 'Input']) {
            this.currentData[field] = this[field + 'Input'].value;
            this.checkForChanges();
        }
    }

    // 変更があるかチェック
    checkForChanges() {
        if (!this.changesIndicator) return;

        const hasChanges = Object.keys(this.originalData).some(
            (key) => this.originalData[key] !== this.currentData[key]
        );

        if (hasChanges) {
            this.changesIndicator.classList.remove('hidden');
        } else {
            this.changesIndicator.classList.add('hidden');
        }
    }

    // プロフィールを保存
    async saveProfile() {
        try {
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
                body: JSON.stringify(this.currentData),
            });

            if (response.ok) {
                this.originalData = { ...this.currentData };
                this.checkForChanges();

                if (window.toastManager) {
                    window.toastManager.success('プロフィールが保存されました！');
                }
            } else {
                const data = await response.json();
                throw new Error(data.error || 'プロフィールの保存に失敗しました。');
            }
        } catch (error) {
            if (window.toastManager) {
                window.toastManager.error(
                    'プロフィールの保存に失敗しました: ' + error.message,
                );
            }
        }
    }

    // プロフィールをリセット
    resetProfile() {
        this.currentData = { ...this.originalData };
        
        if (this.displayNameInput) this.displayNameInput.value = this.currentData.display_name;
        if (this.pronounsInput) this.pronounsInput.value = this.currentData.pronouns;
        if (this.locationInput) this.locationInput.value = this.currentData.location;
        if (this.customUrlInput) this.customUrlInput.value = this.currentData.custom_url;
        if (this.bioInput) this.bioInput.value = this.currentData.bio;

        this.checkForChanges();
    }

    // データを取得
    getData() {
        return this.currentData;
    }

    // データを設定
    setData(data) {
        this.currentData = { ...data };
        this.originalData = { ...data };
        this.updateInputs();
        this.checkForChanges();
    }

    // 入力フィールドを更新
    updateInputs() {
        if (this.displayNameInput) this.displayNameInput.value = this.currentData.display_name || '';
        if (this.pronounsInput) this.pronounsInput.value = this.currentData.pronouns || '';
        if (this.locationInput) this.locationInput.value = this.currentData.location || '';
        if (this.customUrlInput) this.customUrlInput.value = this.currentData.custom_url || '';
        if (this.bioInput) this.bioInput.value = this.currentData.bio || '';
    }
}

// グローバルに公開
window.ProfileBasicInfoManager = ProfileBasicInfoManager;
