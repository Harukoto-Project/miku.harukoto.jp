@props(['avatarUrl'])

<header class="fixed z-[10] h-20 w-full bg-white dark:bg-dark-primary">
  <div class="top-0 z-[100] h-20 w-full justify-center bg-white py-4 dark:bg-dark-primary flex">
    <div class="grid grid-cols-3 items-center">
      <div class="flex items-center px-16">
        <a href="/" class="cursor-pointer"><span
            class="bg-gradient-to-r from-blue-800 via-indigo-500 to-sky-400 bg-clip-text text-right font-bold text-transparent">Miku</span></a>
      </div>
      <div class="flex w-fit items-center justify-center space-x-8 text-sm font-bold">
        <a class="cursor-pointer hover:underline" href="/dashboard">
          ダッシュボード
        </a>
        <a class="cursor-pointer hover:underline" href="/features">
          機能一覧
        </a>
        <a class="cursor-pointer hover:underline" href="/commands">
          コマンド一覧
        </a>
        <a class="cursor-pointer hover:underline" href="/terms">
          利用規約
        </a>
        <a class="cursor-pointer hover:underline" href="/privacy-policy">
          プライバシーポリシー
        </a>
      </div>
      <div class="flex items-center justify-between px-16">
        <div class="justify-start">
          <!-- テーマ切り替えToggleスイッチ -->
          <div x-data="{
                    theme: localStorage.getItem('theme') || 'light',
                    toggleTheme() {
                        this.theme = this.theme === 'light' ? 'dark' : 'light';
                        localStorage.setItem('theme', this.theme);
                        this.applyTheme();
                    },
                    applyTheme() {
                        if (this.theme === 'dark') {
                            document.documentElement.classList.add('dark');
                        } else {
                            document.documentElement.classList.remove('dark');
                        }
                    }
                }"
               x-init="applyTheme()"
               class="relative">
            <button @click="toggleTheme()"
                    class="relative flex h-8 w-20 items-center justify-between rounded-full bg-gray-300 dark:bg-gray-600 px-2 py-1 transition-colors duration-300">

              <!-- 背景のテキスト -->
              <span x-show="theme === 'light'"
                    x-transition:enter="transition-opacity duration-300"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="transition-opacity duration-300"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="absolute right-4 text-xs font-bold text-gray-700 dark:text-gray-300">
                                Light
                            </span>
              <span x-show="theme === 'dark'"
                    x-transition:enter="transition-opacity duration-300"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="transition-opacity duration-300"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="absolute left-4 text-xs font-bold text-gray-700 dark:text-gray-300">
                                Dark
                            </span>

              <!-- 動く丸 -->
              <div class="absolute h-6 w-6 rounded-full bg-white shadow-md transition-transform duration-300 ease-in-out"
                   :class="theme === 'dark' ? 'translate-x-10' : 'translate-x-0'">
                <!-- アイコン -->
                <div class="flex h-full w-full items-center justify-center">
                  <svg x-show="theme === 'light'"
                       x-transition:enter="transition-opacity duration-200"
                       x-transition:enter-start="opacity-0"
                       x-transition:enter-end="opacity-100"
                       x-transition:leave="transition-opacity duration-200"
                       x-transition:leave-start="opacity-100"
                       x-transition:leave-end="opacity-0"
                       class="h-4 w-4 text-yellow-500"
                       fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2.25a.75.75 0 01.75.75v2.25a.75.75 0 01-1.5 0V3a.75.75 0 01.75-.75zM7.5 12a4.5 4.5 0 119 0 4.5 4.5 0 01-9 0zM18.894 6.166a.75.75 0 00-1.06-1.06l-1.591 1.59a.75.75 0 101.06 1.061l1.591-1.59zM21.75 12a.75.75 0 01-.75.75h-2.25a.75.75 0 010-1.5H21a.75.75 0 01.75.75zM17.834 18.894a.75.75 0 001.06-1.06l-1.59-1.591a.75.75 0 10-1.061 1.06l1.59 1.591zM12 18a.75.75 0 01.75.75V21a.75.75 0 01-1.5 0v-2.25A.75.75 0 0112 18zM7.758 17.303a.75.75 0 00-1.061-1.06l-1.591 1.59a.75.75 0 001.06 1.061l1.591-1.59zM6 12a.75.75 0 01-.75.75H3a.75.75 0 010-1.5h2.25A.75.75 0 016 12zM6.697 7.757a.75.75 0 001.06-1.06l-1.59-1.591a.75.75 0 00-1.061 1.06l1.59 1.591z"/>
                  </svg>
                  <svg x-show="theme === 'dark'"
                       x-transition:enter="transition-opacity duration-200"
                       x-transition:enter-start="opacity-0"
                       x-transition:enter-end="opacity-100"
                       x-transition:leave="transition-opacity duration-200"
                       x-transition:leave-start="opacity-100"
                       x-transition:leave-end="opacity-0"
                       class="h-4 w-4 text-gray-700"
                       fill="currentColor" viewBox="0 0 24 24">
                    <path d="M9.528 1.718a.75.75 0 01.162.819A8.97 8.97 0 009 6a9 9 0 009 9 8.97 8.97 0 003.463-.69.75.75 0 01.981.98 10.503 10.503 0 01-9.694 6.46c-5.799 0-10.5-4.701-10.5-10.5 0-4.368 2.667-8.112 6.46-9.694a.75.75 0 01.818.162z"/>
                  </svg>
                </div>
              </div>
            </button>
          </div>
        </div>
        <div class="justify-end text-end">
          <div class="flex w-full cursor-pointer">
            <div class="chakra-menu__trigger css-0 flex items-center" data-scope="menu" data-part="trigger"
                 type="button" dir="ltr" id="menu:«r0»:trigger" data-uid="«r0»" aria-haspopup="menu"
                 data-state="closed">
              <img alt="アバター" loading="lazy" class="rounded-full h-10 w-10"
                   src="{{ $avatarUrl }}">
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</header>
