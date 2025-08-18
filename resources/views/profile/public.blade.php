<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $profile->display_name ?? $profile->user->discord_display_name ?? $profile->user->name }} - プロフィール</title>

    @include('components.og-tags', [
        'title' => '{{ $profile->display_name ?? $profile->user->discord_display_name ?? $profile->user->name }} - プロフィール',
        'description' => '{{ $profile->bio ?? "Discordユーザーのプロフィールページです。" }}',
        'image' => $profile->avatar_path ? Storage::disk('minio')->url($profile->avatar_path) : ($profile->custom_avatar ?? $profile->user->avatar_url ?? '/images/default-avatar.svg')
        'type' => 'profile',
        'siteName' => 'Miku'
    ])

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        theme: '{{ $profile->theme_color ?? "#3B82F6" }}'
                    }
                }
            }
        }
    </script>

    @php
        // カードグラデーションの処理
        $cardGradient = '';
        
        // グラデーションが有効で、カラーストップが存在する場合は必ずグラデーションを適用
        if ($profile->card_gradient_enabled && $profile->card_gradient_stops) {
            $stops = is_string($profile->card_gradient_stops) ? json_decode($profile->card_gradient_stops, true) : $profile->card_gradient_stops;
            
                         if (is_array($stops) && count($stops) > 0) {
                 $direction = $profile->card_gradient_direction ?? '0deg';
                 // 方向にdegが付いていない場合は追加
                 if (!str_contains($direction, 'deg')) {
                     $direction = $direction . 'deg';
                 }
                 $cardGradient = 'linear-gradient(' . $direction . ', ' . implode(', ', array_map(function($stop) {
                     return $stop['color'] . ' ' . $stop['position'] . '%';
                 }, $stops)) . ')';
             }
        }
        
        // グラデーションが設定されていない場合のみ単色のグラデーション
        if (!$cardGradient) {
            $cardGradient = 'linear-gradient(45deg, ' . ($profile->card_background_color ?? $profile->theme_color ?? "#3B82F6") . ' 0%, ' . ($profile->card_background_color ?? $profile->theme_color ?? "#3B82F6") . '80 100%)';
        }
        
        // ページ背景の処理
        $pageGradient = '';
        
        if ($profile->page_gradient_enabled && $profile->page_gradient_stops) {
            $stops = is_string($profile->page_gradient_stops) ? json_decode($profile->page_gradient_stops, true) : $profile->page_gradient_stops;
            
                         if (is_array($stops) && count($stops) > 0) {
                 $direction = $profile->page_gradient_direction ?? '0deg';
                 // 方向にdegが付いていない場合は追加
                 if (!str_contains($direction, 'deg')) {
                     $direction = $direction . 'deg';
                 }
                 $pageGradient = 'linear-gradient(' . $direction . ', ' . implode(', ', array_map(function($stop) {
                     return $stop['color'] . ' ' . $stop['position'] . '%';
                 }, $stops)) . ')';
             }
        }
        
        if (!$pageGradient) {
            $pageGradient = $profile->page_background_color ?? "#F9FAFB";
        }
        
        // 自己紹介カードの処理
        $bioCardGradient = '';
        
        if ($profile->bio_card_gradient_enabled && $profile->bio_card_gradient_stops) {
            $stops = is_string($profile->bio_card_gradient_stops) ? json_decode($profile->bio_card_gradient_stops, true) : $profile->bio_card_gradient_stops;
            
                         if (is_array($stops) && count($stops) > 0) {
                 $direction = $profile->bio_card_gradient_direction ?? '0deg';
                 // 方向にdegが付いていない場合は追加
                 if (!str_contains($direction, 'deg')) {
                     $direction = $direction . 'deg';
                 }
                 $bioCardGradient = 'linear-gradient(' . $direction . ', ' . implode(', ', array_map(function($stop) {
                     return $stop['color'] . ' ' . $stop['position'] . '%';
                 }, $stops)) . ')';
             }
        }
        
        if (!$bioCardGradient) {
            $bioCardGradient = $profile->bio_card_color ? 'rgba(' . implode(', ', array_map('intval', sscanf($profile->bio_card_color, '#%02x%02x%02x'))) . ', 0.1)' : 'rgba(255, 255, 255, 0.1)';
        }
        
        if (config('app.debug')) {
            echo "<!-- Final card gradient: " . $cardGradient . " -->";
            echo "<!-- Final page background: " . $pageGradient . " -->";
            echo "<!-- Final bio card background: " . $bioCardGradient . " -->";
        }
         @endphp
    
    <style>
        .theme-gradient {
            background: {{ $cardGradient }};
        }
        .theme-border {
            border-color: {{ $profile->card_background_color ?? $profile->theme_color ?? "#3B82F6" }};
        }
        .theme-text {
            color: {{ $profile->card_background_color ?? $profile->theme_color ?? "#3B82F6" }};
        }
        .page-background {
            background: {{ $pageGradient }};
        }
        .profile-name-color {
            color: {{ $profile->profile_name_color ?? "#FFFFFF" }};
        }
        .pronouns-color {
            color: {{ $profile->pronouns_color ?? "#E2E8F0" }};
        }
        .location-color {
            color: {{ $profile->location_color ?? "#F1F5F9" }};
        }
        .discord-color {
            color: {{ $profile->discord_color ?? "#F1F5F9" }};
        }
        .bio-card-color {
            background: {{ $bioCardGradient }};
        }
        .bio-text-color {
            color: {{ $profile->bio_text_color ?? "#FFFFFF" }};
        }
        .hover-shadow-3xl {
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }
        .shadow-2xl {
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }
        .backdrop-blur-sm {
            backdrop-filter: blur(4px);
        }
        .backdrop-blur-md {
            backdrop-filter: blur(12px);
        }
    </style>
</head>
<body class="page-background transition-colors duration-300">
    <div class="flex min-h-screen items-center justify-center p-4 transition-colors duration-300">
        <div class="hover:shadow-3xl flex w-full max-w-[40rem] flex-col justify-center rounded-xl p-6 pt-8 text-center shadow-2xl backdrop-blur-sm transition-all duration-500 theme-gradient">
            <div class="flex flex-col items-center justify-center">
                <!-- アバター -->
                <div class="relative h-32 w-32 overflow-hidden rounded-full border-4 border-white shadow-lg transition-transform duration-300 hover:scale-105" x-data="{}">
                    <img 
                        src="{{ $profile->avatar_path ? Storage::disk('minio')->url($profile->avatar_path) : ($profile->custom_avatar ?? $profile->user->avatar_url ?? '/images/default-avatar.svg') }}" 
                        alt="{{ $profile->display_name ?? $profile->user->discord_display_name ?? $profile->user->name }} Avatar" 
                        class="object-cover transition-transform duration-300 hover:scale-110"
                        style="position: absolute; height: 100%; width: 100%; inset: 0px;"
                        x-on:error="this.src='{{ $profile->user->avatar_url ?? "/images/default-avatar.svg" }}'"
                    >
                </div>

                <!-- 名前 -->
                <h1 class="pt-4 text-4xl font-bold transition-all duration-300 hover:scale-105 profile-name-color" style="text-shadow: rgba(255, 255, 255, 0.19) 0px 2px 4px;">
                    {{ $profile->display_name ?? $profile->user->discord_display_name ?? $profile->user->name }}
                </h1>

                <!-- 代名詞 -->
                @if($profile->pronouns)
                    <h2 class="mt-2 text-xl font-medium transition-colors duration-300 pronouns-color">
                        {{ $profile->pronouns }}
                    </h2>
                @endif

                <!-- 場所 -->
                @if($profile->location)
                    <div class="mt-2 flex items-center justify-center gap-1 text-lg transition-colors duration-300 location-color">
                        <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-5 w-5">
                            <path fill="none" d="M0 0h24v24H0z"></path>
                            <path d="M12 2C8.13 2 5 5.13 5 9c0 1.74.5 3.37 1.41 4.84.95 1.54 2.2 2.86 3.16 4.4.47.75.81 1.45 1.17 2.26.26.55.47 1.5 1.26 1.5s1-.95 1.25-1.5c.37-.81.7-1.51 1.17-2.26.96-1.53 2.21-2.85 3.16-4.4C18.5 12.37 19 10.74 19 9c0-3.87-3.13-7-7-7zm0 9.75a2.5 2.5 0 0 1 0-5 2.5 2.5 0 0 1 0 5z"></path>
                        </svg>
                        <span>{{ $profile->location }}</span>
                    </div>
                @endif

                <!-- Discord情報 -->
                <div class="mt-2 flex items-center justify-center gap-1 text-lg transition-colors duration-300 discord-color">
                    <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 640 512" class="h-5 w-5">
                        <path d="M524.531,69.836a1.5,1.5,0,0,0-.764-.7A485.065,485.065,0,0,0,404.081,32.03a1.816,1.816,0,0,0-1.923.91,337.461,337.461,0,0,0-14.9,30.6,447.848,447.848,0,0,0-134.426,0,309.541,309.541,0,0,0-15.135-30.6,1.89,1.89,0,0,0-1.924-.91A483.689,483.689,0,0,0,116.085,69.137a1.712,1.712,0,0,0-.788.676C39.068,183.651,18.186,294.69,28.43,404.354a2.016,2.016,0,0,0,.765,1.375A487.666,487.666,0,0,0,176.02,479.918a1.9,1.9,0,0,0,2.063-.676A348.2,348.2,0,0,0,208.12,430.4a1.86,1.86,0,0,0-1.019-2.588,321.173,321.173,0,0,1-45.868-21.853,1.885,1.885,0,0,1-.185-3.126c3.082-2.309,6.166-4.711,9.109-7.137a1.819,1.819,0,0,1,1.9-.256c96.229,43.917,200.41,43.917,295.5,0a1.812,1.812,0,0,1,1.924.233c2.944,2.426,6.027,4.851,9.132,7.16a1.884,1.884,0,0,1-.162,3.126,301.407,301.407,0,0,1-45.89,21.83,1.875,1.875,0,0,0-1,2.611,391.055,391.055,0,0,0,30.014,48.815,1.864,1.864,0,0,0,2.063.7A486.048,486.048,0,0,0,610.7,405.729a1.882,1.882,0,0,0,.765-1.352C623.729,277.594,590.933,167.465,524.531,69.836ZM222.491,337.58c-28.972,0-52.844-26.587-52.844-59.239S193.056,219.1,222.491,219.1c29.665,0,53.306,26.82,52.843,59.239C275.334,310.993,251.924,337.58,222.491,337.58Zm195.38,0c-28.971,0-52.843-26.587-52.843-59.239S388.437,219.1,417.871,219.1c29.667,0,53.307,26.82,52.844,59.239C470.715,310.993,447.538,337.58,417.871,337.58Z"></path>
                    </svg>
                    <span>{{ $profile->user->discord_username ?? 'Discord User' }}</span>
                </div>

                <!-- 自己紹介 -->
                @if($profile->bio)
                    <div class="mt-4 rounded-lg p-4 backdrop-blur-sm transition-all duration-300 hover:backdrop-blur-md bio-card-color" style="border: 1px solid rgba(255, 255, 255, 0.125);">
                        <p class="whitespace-pre-wrap text-sm leading-relaxed transition-colors duration-300 bio-text-color">{!! $profile->bio !!}</p>
                    </div>
                @endif
            </div>

            <!-- フッター -->
            <div class="mt-6 text-center">
                <p class="text-sm text-white opacity-75">
                    Powered by <a href="https://miku.harukoto.jp" class="underline hover:opacity-100 transition-opacity duration-200">Miku Bot</a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>
