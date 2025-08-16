// テーマ管理機能
class ThemeManager {
    constructor() {
        this.themeContainer = document.getElementById('theme_editor');
        this.previewContainer = document.getElementById('theme_preview');
        this.saveBtn = document.getElementById('save_theme');
        this.resetBtn = document.getElementById('reset_theme');
        this.revertBtn = document.getElementById('revert_theme');

        this.currentTheme = {};
        this.originalTheme = {};

        this.initialize();
    }

    initialize() {
        this.loadCurrentTheme();
        this.setupEventListeners();
        this.setupColorEditors();
    }

    // 現在のテーマを読み込み
    loadCurrentTheme() {
        // データ属性からテーマ情報を取得
        const themeData = this.themeContainer?.dataset.theme;
        if (themeData) {
            try {
                this.currentTheme = JSON.parse(themeData);
                this.originalTheme = JSON.parse(themeData);
            } catch (e) {
                this.currentTheme = this.getDefaultTheme();
                this.originalTheme = this.getDefaultTheme();
            }
        } else {
            this.currentTheme = this.getDefaultTheme();
            this.originalTheme = this.getDefaultTheme();
        }
    }

    // デフォルトテーマを取得
    getDefaultTheme() {
        return {
            backgroundColor: '#8686f0',
            displayNameColor: '#ffffff',
            pronounsColor: '#e5e7eb',
            locationColor: '#d1d5db',
            bioTextColor: '#ffffff',
            bioBackgroundColor: 'rgba(255, 255, 255, 0.1)',
        };
    }

    // イベントリスナーを設定
    setupEventListeners() {
        if (this.saveBtn) {
            this.saveBtn.addEventListener('click', () => this.saveTheme());
        }

        if (this.resetBtn) {
            this.resetBtn.addEventListener('click', () => this.resetTheme());
        }

        if (this.revertBtn) {
            this.revertBtn.addEventListener('click', () => this.revertTheme());
        }
    }

    // カラーエディターを設定
    setupColorEditors() {
        this.setupColorEditor('backgroundColor', '背景色');
        this.setupColorEditor('displayNameColor', '表示名の色');
        this.setupColorEditor('pronounsColor', '代名詞の色');
        this.setupColorEditor('locationColor', '場所の色');
        this.setupColorEditor('bioTextColor', '自己紹介の文字色');
        this.setupColorEditor('bioBackgroundColor', '自己紹介の背景色');
    }

    // 個別のカラーエディターを設定
    setupColorEditor(colorType, label) {
        const editor = this.themeContainer?.querySelector(`[data-color-type="${colorType}"]`);
        if (!editor) return;

        const colorInput = editor.querySelector('input[type="color"]');
        const gradientToggle = editor.querySelector('.gradient-toggle');
        const directionSlider = editor.querySelector('.direction-slider');

        if (colorInput) {
            colorInput.addEventListener('input', (e) => {
                this.currentTheme[colorType] = e.target.value;
                this.updatePreview();
            });
        }

        if (gradientToggle) {
            gradientToggle.addEventListener('click', () => {
                this.toggleGradient(colorType, editor);
            });
        }

        if (directionSlider) {
            directionSlider.addEventListener('input', (e) => {
                this.updatePreview();
            });
        }

        // グラデーションストップの設定
        this.setupGradientStops(editor, colorType);
    }

    // グラデーションストップを設定
    setupGradientStops(editor, colorType) {
        const addStopBtn = editor.querySelector('.add-stop');
        const removeStopBtn = editor.querySelector('.remove-stop');

        if (addStopBtn) {
            addStopBtn.addEventListener('click', () => {
                this.addGradientStop(editor, colorType);
            });
        }

        if (removeStopBtn) {
            removeStopBtn.addEventListener('click', () => {
                this.removeGradientStop(editor, colorType);
            });
        }

        // 既存のストップにイベントリスナーを設定
        const stops = editor.querySelectorAll('.gradient-stop input[type="color"]');
        stops.forEach((stop) => {
            stop.addEventListener('input', () => {
                this.updatePreview();
            });
        });
    }

    // グラデーションストップを追加
    addGradientStop(editor, colorType) {
        const stopsContainer = editor.querySelector('.gradient-stops');
        if (!stopsContainer) return;

        const stopDiv = document.createElement('div');
        stopDiv.className = 'gradient-stop flex items-center space-x-2 mb-2';
        stopDiv.innerHTML = `
            <input type="color" value="#ffffff" class="w-8 h-8 border rounded">
            <input type="range" min="0" max="100" value="50" class="flex-1">
            <span class="text-sm text-gray-600 w-8 text-center">50%</span>
        `;

        const colorInput = stopDiv.querySelector('input[type="color"]');
        const rangeInput = stopDiv.querySelector('input[type="range"]');
        const percentSpan = stopDiv.querySelector('span');

        colorInput.addEventListener('input', () => this.updatePreview());
        rangeInput.addEventListener('input', (e) => {
            percentSpan.textContent = e.target.value + '%';
            this.updatePreview();
        });

        stopsContainer.appendChild(stopDiv);
    }

    // グラデーションストップを削除
    removeGradientStop(editor, colorType) {
        const stopsContainer = editor.querySelector('.gradient-stops');
        if (!stopsContainer) return;

        const stops = stopsContainer.querySelectorAll('.gradient-stop');
        if (stops.length > 2) {
            stops[stops.length - 1].remove();
            this.updatePreview();
        }
    }

    // グラデーションを切り替え
    toggleGradient(colorType, editor) {
        const toggle = editor.querySelector('.gradient-toggle');
        const isGradient = toggle.classList.contains('bg-blue-600');

        if (isGradient) {
            toggle.classList.remove('bg-blue-600');
            toggle.classList.add('bg-gray-400');
            toggle.textContent = '単色';
        } else {
            toggle.classList.remove('bg-gray-400');
            toggle.classList.add('bg-blue-600');
            toggle.textContent = 'グラデーション';
        }

        this.updatePreview();
    }

    // プレビューを更新
    updatePreview() {
        if (!this.previewContainer) return;

        const preview = this.previewContainer.querySelector('.profile-preview');
        if (!preview) return;

        // 背景色を更新
        const backgroundColor = this.getCurrentBackgroundColor();
        if (preview) {
            preview.style.background = backgroundColor;
        }

        // テキスト色を更新
        const nameElement = preview.querySelector('h1');
        if (nameElement) {
            nameElement.style.color = this.currentTheme.displayNameColor;
        }

        const pronounsElement = preview.querySelector('h2');
        if (pronounsElement) {
            pronounsElement.style.color = this.currentTheme.pronounsColor;
        }

        const locationElement = preview.querySelector('.text-gray-100');
        if (locationElement) {
            locationElement.style.color = this.currentTheme.locationColor;
        }

        const bioElement = preview.querySelector('p');
        if (bioElement) {
            bioElement.style.color = this.currentTheme.bioTextColor;
        }

        const bioBackgroundElement = preview.querySelector('.mt-4.rounded-lg');
        if (bioBackgroundElement) {
            bioBackgroundElement.style.background = this.currentTheme.bioBackgroundColor;
        }
    }

    // 現在の背景色を取得
    getCurrentBackgroundColor() {
        const editor = this.themeContainer?.querySelector(
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
    getCurrentColor(colorType) {
        const editor = this.themeContainer?.querySelector(`[data-color-type="${colorType}"]`);
        if (!editor) return '#000000';

        const colorInput = editor.querySelector('input[type="color"]');
        return colorInput ? colorInput.value : '#000000';
    }

    // テーマを保存
    async saveTheme() {
        try {
            const themeData = {
                backgroundColor: this.getCurrentBackgroundColor(),
                displayNameColor: this.currentTheme.displayNameColor,
                pronounsColor: this.currentTheme.pronounsColor,
                locationColor: this.currentTheme.locationColor,
                bioTextColor: this.currentTheme.bioTextColor,
                bioBackgroundColor: this.currentTheme.bioBackgroundColor,
            };

            const response = await fetch('/api/profile/theme', {
                method: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': document
                        .querySelector('meta[name="csrf-token"]')
                        .getAttribute('content'),
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                },
                credentials: 'same-origin',
                body: JSON.stringify(themeData),
            });

            if (response.ok) {
                this.originalTheme = { ...themeData };
                this.currentTheme = { ...themeData };

                if (window.toastManager) {
                    window.toastManager.success('テーマが保存されました！');
                }
            } else {
                const data = await response.json();
                throw new Error(data.error || 'テーマの保存に失敗しました。');
            }
        } catch (error) {
            if (window.toastManager) {
                window.toastManager.error(
                    'テーマの保存に失敗しました: ' + error.message,
                );
            }
        }
    }

    // テーマをリセット
    resetTheme() {
        this.applyPresetTheme('default');
    }

    // プリセットテーマを適用
    applyPresetTheme(presetName) {
        const presets = {
            default: {
                backgroundColor: '#8686f0',
                displayNameColor: '#ffffff',
                pronounsColor: '#e5e7eb',
                locationColor: '#d1d5db',
                bioTextColor: '#ffffff',
                bioBackgroundColor: 'rgba(255, 255, 255, 0.1)',
            },
            dark: {
                backgroundColor: '#1f2937',
                displayNameColor: '#ffffff',
                pronounsColor: '#d1d5db',
                locationColor: '#9ca3af',
                bioTextColor: '#e5e7eb',
                bioBackgroundColor: 'rgba(255, 255, 255, 0.05)',
            },
            light: {
                backgroundColor: '#f3f4f6',
                displayNameColor: '#1f2937',
                pronounsColor: '#4b5563',
                locationColor: '#6b7280',
                bioTextColor: '#374151',
                bioBackgroundColor: 'rgba(0, 0, 0, 0.05)',
            },
        };

        const preset = presets[presetName] || presets.default;
        this.currentTheme = { ...preset };
        this.updateColorInputs();
        this.updatePreview();
    }

    // カラー入力を更新
    updateColorInputs() {
        Object.keys(this.currentTheme).forEach((colorType) => {
            const editor = this.themeContainer?.querySelector(`[data-color-type="${colorType}"]`);
            if (!editor) return;

            const colorInput = editor.querySelector('input[type="color"]');
            if (colorInput && colorType !== 'backgroundColor') {
                colorInput.value = this.currentTheme[colorType];
            }
        });
    }

    // テーマの変更を破棄
    revertTheme() {
        this.currentTheme = { ...this.originalTheme };
        this.updateColorInputs();
        this.updatePreview();
    }
}

// グローバルに公開
window.ThemeManager = ThemeManager;
