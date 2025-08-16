// プロフィール設定用JavaScript - モジュール版

// 必要なモジュールを読み込み
// 注意: これらのスクリプトは、HTMLで個別に読み込む必要があります
// <script src="/js/profile/utils/ProfileUtils.js"></script>
// <script src="/js/profile/managers/LinkManager.js"></script>
// <script src="/js/profile/managers/ProfileBasicInfoManager.js"></script>
// <script src="/js/profile/avatar/AvatarManager.js"></script>
// <script src="/js/profile/theme/ThemeManager.js"></script>
// <script src="/js/profile/ProfileSettings.js"></script>

// 後方互換性のため、既存のコードとの互換性を保持
document.addEventListener('DOMContentLoaded', function () {
    // プロフィール設定の初期化
    if (window.ProfileSettings) {
        // プロフィールが存在する場合のみ実行する処理
        if (document.querySelector('[data-profile-exists="true"]')) {
            window.ProfileSettings.initializeProfileFeatures();
        }
    }
});
