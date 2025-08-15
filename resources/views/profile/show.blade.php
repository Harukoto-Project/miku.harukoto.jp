<!DOCTYPE html>
<html lang="ja" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $profile->display_name ?: $user->discord_display_name }} - プロフィール</title>
    <meta name="description" content="{{ $profile->bio ?: 'ユーザープロフィール' }}">
    <meta property="og:title" content="{{ $profile->display_name ?: $user->discord_display_name }}">
    <meta property="og:description" content="{{ $profile->bio ?: 'ユーザープロフィール' }}">
    @if($profile->avatar_url)
    <meta property="og:image" content="{{ $profile->avatar_url }}">
    @endif
    <meta property="og:type" content="profile">
    <meta name="twitter:card" content="summary_large_image">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    animation: {
                        'fade-in': 'fadeIn 0.5s ease-in-out',
                        'slide-up': 'slideUp 0.5s ease-out',
                        'scale-in': 'scaleIn 0.3s ease-out',
                    },
                    keyframes: {
                        fadeIn: {
                            '0%': { opacity: '0' },
                            '100%': { opacity: '1' },
                        },
                        slideUp: {
                            '0%': { transform: 'translateY(20px)', opacity: '0' },
                            '100%': { transform: 'translateY(0)', opacity: '1' },
                        },
                        scaleIn: {
                            '0%': { transform: 'scale(0.9)', opacity: '0' },
                            '100%': { transform: 'scale(1)', opacity: '1' },
                        },
                    },
                    boxShadow: {
                        '3xl': '0 25px 50px -12px rgba(0, 0, 0, 0.25)',
                    }
                }
            }
        }
    </script>
    
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, {{ $profile->theme?->background_color_solid ?? '#6366f1' }}20, {{ $profile->theme?->background_color_solid ?? '#6366f1' }}10);
        }
        .profile-card {
            background: {{ $profile->theme?->background_color_solid ?? '#6366f1' }};
            box-shadow: 0 25px 50px -12px {{ $profile->theme?->background_color_solid ?? '#6366f1' }}40;
        }
        .display-name {
            color: {{ $profile->theme?->display_name_color_solid ?? '#ffffff' }};
            text-shadow: 0 2px 4px {{ $profile->theme?->display_name_color_solid ?? '#ffffff' }}30;
        }
        .pronouns {
            color: {{ $profile->theme?->pronouns_color_solid ?? '#e2e8f0' }};
        }
        .location {
            color: {{ $profile->theme?->location_color_solid ?? '#f1f5f9' }};
        }
        .bio-text {
            color: {{ $profile->theme?->bio_text_color_solid ?? '#ffffff' }};
        }
        .bio-bg {
            background: {{ $profile->theme?->bio_background_color_solid ?? 'rgba(255, 255, 255, 0.1)' }};
            border: 1px solid {{ $profile->theme?->bio_text_color_solid ?? '#ffffff' }}20;
            box-shadow: 0 4px 6px {{ $profile->theme?->bio_background_color_solid ?? 'rgba(255, 255, 255, 0.1)' }}20;
        }
        
        /* テーマが設定されていない場合のフォールバックスタイル */
        .profile-card:not([style*="background"]) {
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
        }
        
        .display-name:not([style*="color"]) {
            color: #ffffff;
        }
        
        .pronouns:not([style*="color"]) {
            color: #e2e8f0;
        }
        
        .location:not([style*="color"]) {
            color: #f1f5f9;
        }
        
        .bio-text:not([style*="color"]) {
            color: #ffffff;
        }
        
        .bio-bg:not([style*="background"]) {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
    </style>
</head>
<body class="h-full">
    <div class="min-h-screen gradient-bg flex items-center justify-center p-4 transition-colors duration-300">
        <div class="profile-card hover:shadow-3xl flex w-full max-w-[40rem] flex-col justify-center rounded-xl p-6 pt-8 text-center shadow-2xl backdrop-blur-sm transition-all duration-500">
            <div class="flex flex-col items-center justify-center">
                <!-- アバター -->
                <div class="relative h-32 w-32 overflow-hidden rounded-full border-4 border-white shadow-lg transition-transform duration-300 hover:scale-105">
                    @if($profile->avatar_url)
                        <img src="{{ $profile->avatar_url }}" 
                             alt="{{ $profile->display_name ?: $user->discord_display_name }}のアバター"
                             class="h-full w-full object-cover transition-transform duration-300 hover:scale-110">
                    @else
                        <div class="flex h-full w-full items-center justify-center bg-gray-300">
                            <i class="fas fa-user text-6xl text-gray-600"></i>
                        </div>
                    @endif
                </div>

                <!-- 表示名 -->
                @if($profile->display_name)
                    <h1 class="display-name pt-4 text-4xl font-bold transition-all duration-300 hover:scale-105">
                        {{ trim($profile->display_name) }}
                    </h1>
                @elseif($user->discord_display_name)
                    <h1 class="display-name pt-4 text-4xl font-bold transition-all duration-300 hover:scale-105">
                        {{ trim($user->discord_display_name) }}
                    </h1>
                @endif

                <!-- 代名詞 -->
                @if($profile->pronouns)
                    <h2 class="pronouns mt-2 text-xl font-medium transition-colors duration-300">
                        {{ trim($profile->pronouns) }}
                    </h2>
                @endif

                <!-- 場所 -->
                @if($profile->location)
                    <div class="location mt-2 flex items-center justify-center gap-1 text-lg transition-colors duration-300">
                        <i class="fas fa-map-marker-alt text-xl"></i>
                        <span>{{ trim($profile->location) }}</span>
                    </div>
                @endif

                <!-- 自己紹介 -->
                @if($profile->bio)
                    <div class="bio-bg mt-4 rounded-lg p-4 backdrop-blur-sm transition-all duration-300 hover:backdrop-blur-md">
                        <p class="bio-text text-center whitespace-pre-wrap text-sm leading-relaxed transition-colors duration-300">{{ trim($profile->bio) }}</p>
                    </div>
                @endif
            </div>

            <!-- リンク -->
            @if($profile->links && $profile->links->count() > 0)
                <div class="mt-6">
                    <div class="grid gap-3 sm:grid-cols-2">
                        @foreach($profile->links as $link)
                            @php
                                $buttonTypes = [
                                    'website' => ['label' => 'ウェブサイト', 'icon' => 'fas fa-globe', 'defaultColor' => '#2563eb'],
                                    'twitter' => ['label' => 'Twitter', 'icon' => 'fab fa-twitter', 'defaultColor' => '#0ea5e9'],
                                    'github' => ['label' => 'GitHub', 'icon' => 'fab fa-github', 'defaultColor' => '#1f2937'],
                                    'youtube' => ['label' => 'YouTube', 'icon' => 'fab fa-youtube', 'defaultColor' => '#dc2626'],
                                    'instagram' => ['label' => 'Instagram', 'icon' => 'fab fa-instagram', 'defaultColor' => '#db2777'],
                                    'discord' => ['label' => 'Discord', 'icon' => 'fab fa-discord', 'defaultColor' => '#4f46e5'],
                                    'twitch' => ['label' => 'Twitch', 'icon' => 'fab fa-twitch', 'defaultColor' => '#7c3aed'],
                                    'linkedin' => ['label' => 'LinkedIn', 'icon' => 'fab fa-linkedin', 'defaultColor' => '#1d4ed8'],
                                    'facebook' => ['label' => 'Facebook', 'icon' => 'fab fa-facebook', 'defaultColor' => '#1e40af'],
                                    'tiktok' => ['label' => 'TikTok', 'icon' => 'fab fa-tiktok', 'defaultColor' => '#000000'],
                                    'steam' => ['label' => 'Steam', 'icon' => 'fab fa-steam', 'defaultColor' => '#374151'],
                                    'spotify' => ['label' => 'Spotify', 'icon' => 'fab fa-spotify', 'defaultColor' => '#16a34a'],
                                    'paypal' => ['label' => 'PayPal', 'icon' => 'fab fa-paypal', 'defaultColor' => '#2563eb'],
                                    'amazon' => ['label' => 'Amazon', 'icon' => 'fas fa-shopping-cart', 'defaultColor' => '#f97316'],
                                    'email' => ['label' => 'メール', 'icon' => 'fas fa-envelope', 'defaultColor' => '#4b5563'],
                                ];
                                $buttonInfo = $buttonTypes[$link->button_id] ?? $buttonTypes['website'];
                                $buttonColor = $link->custom_color ?: $buttonInfo['defaultColor'];
                            @endphp
                            <button onclick="handleLinkClick('{{ $link->url }}')"
                                    class="group flex items-center gap-3 rounded-lg p-4 text-white transition-all duration-300 hover:scale-105 hover:shadow-lg active:scale-95"
                                    style="background-color: {{ $buttonColor }}; box-shadow: 0 4px 14px 0 {{ $buttonColor }}40;"
                                    onmouseenter="this.style.boxShadow='0 8px 25px 0 {{ $buttonColor }}60'"
                                    onmouseleave="this.style.boxShadow='0 4px 14px 0 {{ $buttonColor }}40'">
                                <i class="{{ $buttonInfo['icon'] }} text-2xl flex-shrink-0 transition-transform duration-300 group-hover:scale-110"></i>
                                <div class="flex-1 text-left">
                                    <div class="font-medium transition-transform duration-300 group-hover:translate-x-1">
                                        {{ $link->display_name }}
                                    </div>
                                    <div class="text-sm opacity-75">{{ $buttonInfo['label'] }}</div>
                                </div>
                                <i class="fas fa-external-link-alt text-lg opacity-75 transition-all duration-300 group-hover:translate-x-1 group-hover:opacity-100"></i>
                            </button>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- 空の状態 -->
            @if((!$profile->links || $profile->links->count() == 0) && !$profile->bio)
                <div class="py-8 text-center">
                    <i class="fas fa-info-circle mx-auto mb-4 text-5xl opacity-50 transition-opacity duration-300 hover:opacity-70 display-name"></i>
                    <p class="text-sm opacity-75 transition-opacity duration-300 display-name">
                        このユーザーはまだプロフィール情報を設定していません
                    </p>
                </div>
            @endif
        </div>
    </div>

    <script>
        // ボタン情報を取得（PHPで定義済み）
        function getButtonInfo(buttonId) {
            // PHPで定義されたボタン情報を使用
            return null; // この関数は使用されていないため、nullを返す
        }

        // リンククリック処理
        function handleLinkClick(url) {
            const fullUrl = url.startsWith('http') ? url : `https://${url}`;
            window.open(fullUrl, '_blank', 'noopener,noreferrer');
        }

        // ページ読み込み時のアニメーション
        document.addEventListener('DOMContentLoaded', function() {
            const profileCard = document.querySelector('.profile-card');
            if (profileCard) {
                profileCard.style.animation = 'fadeIn 0.5s ease-in-out';
            }
        });
    </script>
</body>
</html>
