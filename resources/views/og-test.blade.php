<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>OGタグテスト - Miku</title>

    @include('components.og-tags', [
        'title' => 'OGタグテスト - Miku',
        'description' => 'DiscordとTwitter用のOGタグのテストページです。',
        'image' => asset('images/default-avatar.svg')
    ])

    
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">
            <h1 class="text-3xl font-bold text-gray-900 mb-6">OGタグテストページ</h1>
            
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">実装されたOGタグ</h2>
                <div class="space-y-2 text-sm text-gray-600">
                    <p><strong>og:type:</strong> website</p>
                    <p><strong>og:title:</strong> OGタグテスト - Miku</p>
                    <p><strong>og:description:</strong> DiscordとTwitter用のOGタグのテストページです。</p>
                    <p><strong>og:image:</strong> {{ asset('images/default-avatar.svg') }}</p>
                    <p><strong>twitter:card:</strong> summary_large_image</p>
                </div>
            </div>

            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <h3 class="text-lg font-medium text-blue-800 mb-2">テスト方法</h3>
                <ul class="text-blue-700 space-y-1 text-sm">
                    <li>• Facebookのシェアデバッガー: <a href="https://developers.facebook.com/tools/debug/" class="underline" target="_blank">https://developers.facebook.com/tools/debug/</a></li>
                    <li>• Twitter Card Validator: <a href="https://cards-dev.twitter.com/validator" class="underline" target="_blank">https://cards-dev.twitter.com/validator</a></li>
                    <li>• Discord: リンクを貼り付けてプレビューを確認</li>
                </ul>
            </div>

            <div class="mt-6">
                <a href="{{ route('home') }}" class="text-indigo-600 hover:text-indigo-800 underline">← ホームに戻る</a>
            </div>
        </div>
    </div>
</body>
</html>
