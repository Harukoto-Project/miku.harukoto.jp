// プロフィール関連ユーティリティ関数

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

// カスタムURL可用性チェック
async function checkCustomUrlAvailability(url) {
    try {
        const response = await fetch(`/api/profile/check-url/${url}`, {
            method: 'GET',
            headers: {
                Accept: 'application/json',
            },
            credentials: 'same-origin',
        });

        const data = await response.json();
        return data;
    } catch (error) {
        return { available: false, error: 'ネットワークエラーが発生しました。' };
    }
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
                customUrlStatus.innerHTML = '';
            }
        });
    }
}

// 保存ボタンの初期化
function initializeSaveButtons() {
    const saveButtons = document.querySelectorAll('[data-save-action]');
    
    saveButtons.forEach(button => {
        button.addEventListener('click', async function() {
            const action = this.dataset.saveAction;
            const targetId = this.dataset.targetId;
            
            if (action === 'profile') {
                await saveProfile();
            } else if (action === 'theme') {
                await saveTheme();
            } else if (action === 'links') {
                await saveLinks();
            }
        });
    });
}

// プロフィールセットアップ開始
async function startProfileSetup() {
    try {
        const response = await fetch('/api/profile/setup', {
            method: 'POST',
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
            window.location.href = '/user-settings?tab=profile';
        } else {
            const data = await response.json();
            throw new Error(data.error || 'プロフィールセットアップに失敗しました。');
        }
    } catch (error) {
        if (window.toastManager) {
            window.toastManager.error(
                'プロフィールセットアップに失敗しました: ' + error.message,
            );
        }
    }
}

// プロフィール保存
async function saveProfile() {
    try {
        const profileData = {
            display_name: document.getElementById('display_name')?.value || '',
            pronouns: document.getElementById('pronouns')?.value || '',
            location: document.getElementById('location')?.value || '',
            custom_url: document.getElementById('custom_url')?.value || '',
            bio: document.getElementById('bio')?.value || '',
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

// テーマ保存
async function saveTheme() {
    try {
        const themeData = {
            backgroundColor: getCurrentBackgroundColor(),
            displayNameColor: getCurrentColor('displayNameColor'),
            pronounsColor: getCurrentColor('pronounsColor'),
            locationColor: getCurrentColor('locationColor'),
            bioTextColor: getCurrentColor('bioTextColor'),
            bioBackgroundColor: getCurrentColor('bioBackgroundColor'),
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

// リンク保存
async function saveLinks() {
    try {
        const links = Array.from(document.querySelectorAll('.link-item')).map(item => ({
            id: item.dataset.linkId,
            title: item.querySelector('.link-title')?.textContent || '',
            url: item.querySelector('.link-url')?.href || '',
            icon: item.querySelector('.link-icon i')?.className || '',
            color: item.querySelector('.link-color')?.style.backgroundColor || '#000000',
            type: item.dataset.linkType || 'custom',
        }));

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
            body: JSON.stringify({ links }),
        });

        if (response.ok) {
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

// 現在の背景色を取得
function getCurrentBackgroundColor() {
    const editor = document.querySelector('[data-color-type="backgroundColor"]');
    if (!editor) return '#8686f0';

    const toggle = editor.querySelector('.gradient-toggle');
    const isGradient = toggle && toggle.classList.contains('bg-blue-600');

    if (isGradient) {
        const stops = editor.querySelectorAll('.gradient-stop input[type="color"]');
        const direction = editor.querySelector('.direction-slider')?.value || 135;

        if (stops.length >= 2) {
            const colors = Array.from(stops).map(stop => stop.value);
            const positions = Array.from(stops).map((stop, index) =>
                Math.round((index / (stops.length - 1)) * 100)
            );

            return `linear-gradient(${direction}deg, ${colors
                .map((color, index) => `${color} ${positions[index]}%`)
                .join(', ')})`;
        }
    }

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

// グローバルに公開
window.ProfileUtils = {
    checkAuthStatus,
    checkCustomUrlAvailability,
    initializeCharCounters,
    initializeCustomUrlCheck,
    initializeSaveButtons,
    startProfileSetup,
    saveProfile,
    saveTheme,
    saveLinks,
    getCurrentBackgroundColor,
    getCurrentColor,
};
