<!DOCTYPE html>
<html lang="ja" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ページが見つかりません - 404</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="h-full">
    <div class="flex min-h-screen items-center justify-center bg-gradient-to-br from-blue-50 to-indigo-100 dark:from-gray-900 dark:to-gray-800">
        <div class="rounded-xl bg-white/80 p-8 text-center shadow-lg backdrop-blur-md dark:bg-gray-900/80">
            <i class="fas fa-exclamation-triangle mx-auto mb-4 text-6xl text-red-500"></i>
            <h1 class="mb-2 text-2xl font-bold text-gray-900 dark:text-white">
                ページが見つかりません
            </h1>
            <p class="mb-6 text-gray-600 dark:text-gray-400">
                お探しのページは存在しないか、移動された可能性があります。
            </p>
            <div class="space-y-3">
                <a href="{{ route('home') }}" 
                   class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-6 py-3 text-sm font-medium text-white transition-all hover:bg-blue-700 hover:shadow-lg">
                    <i class="fas fa-home"></i>
                    ホームに戻る
                </a>
                <div class="text-sm text-gray-500 dark:text-gray-400">
                    <p>もしくは、以下のURLを確認してください：</p>
                    <p class="mt-1 font-mono text-xs">{{ request()->url() }}</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
