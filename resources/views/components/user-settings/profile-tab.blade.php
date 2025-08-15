@if(!$profile)
    @include('components.user-settings.profile.profile-setup')
@else
    <!-- ポートフォリオ設定済みの場合 -->
    <div class="space-y-6" data-profile-exists="true">
        <!-- プロフィール基本情報 -->
        @include('components.user-settings.profile.profile-basic-info')
        
        <!-- アバター設定 -->
        @include('components.user-settings.profile.profile-avatar')
        
        <!-- リンク設定 -->
        @include('components.user-settings.profile.profile-links')
        
        <!-- テーマ設定 -->
        @include('components.user-settings.profile.profile-theme')
        
        <!-- アクションボタン -->
        @include('components.user-settings.profile.profile-actions')
    </div>
@endif

<!-- JavaScript -->
@vite(['resources/js/profile-settings.js'])
