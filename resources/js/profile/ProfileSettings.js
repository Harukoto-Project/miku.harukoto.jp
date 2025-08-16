// プロフィール設定メインファイル

// グローバル変数
let linkManager;
let profileBasicInfoManager;
let avatarManager;
let themeManager;

// プロフィール機能の初期化
function initializeProfileFeatures() {
    // 文字数カウンター
    if (window.ProfileUtils) {
        window.ProfileUtils.initializeCharCounters();
        window.ProfileUtils.initializeCustomUrlCheck();
        window.ProfileUtils.initializeSaveButtons();
    }

    // アバターアップロード処理
    initializeAvatarUpload();

    // テーマ関連の機能
    initializeThemeEditor();

    // リンク管理機能の初期化
    initializeLinkManager();

    // 基本情報管理機能の初期化
    initializeProfileBasicInfoManager();
}

// アバターアップロード処理の初期化
function initializeAvatarUpload() {
    if (window.AvatarManager) {
        avatarManager = new window.AvatarManager();
        window.avatarManager = avatarManager;
    }
}

// テーマエディターの初期化
function initializeThemeEditor() {
    if (window.ThemeManager) {
        themeManager = new window.ThemeManager();
        window.themeManager = themeManager;
    }
}

// リンク管理機能の初期化
function initializeLinkManager() {
    if (window.LinkManager) {
        linkManager = new window.LinkManager();
        window.linkManager = linkManager;
    }
}

// 基本情報管理機能の初期化
function initializeProfileBasicInfoManager() {
    if (window.ProfileBasicInfoManager) {
        profileBasicInfoManager = new window.ProfileBasicInfoManager();
        window.profileBasicInfoManager = profileBasicInfoManager;
    }
}

// DOMContentLoadedイベントリスナー
document.addEventListener('DOMContentLoaded', function () {
    // セットアップ開始ボタンの設定
    const startSetupBtn = document.getElementById('start_setup');

    if (startSetupBtn) {
        startSetupBtn.addEventListener('click', function (e) {
            e.preventDefault();
            if (window.ProfileUtils) {
                window.ProfileUtils.startProfileSetup();
            }
        });
    }

    // プロフィールが存在する場合のみ実行する処理
    if (document.querySelector('[data-profile-exists="true"]')) {
        initializeProfileFeatures();
    }
});

// グローバルに公開
window.ProfileSettings = {
    initializeProfileFeatures,
    initializeAvatarUpload,
    initializeThemeEditor,
    initializeLinkManager,
    initializeProfileBasicInfoManager,
};
