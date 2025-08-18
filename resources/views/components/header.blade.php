@props(['avatarUrl'])

<script src="https://cdn.jsdelivr.net/npm/@tailwindplus/elements@1" type="module"></script>
<header class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 transition-colors duration-150">
    <nav aria-label="Global" class="mx-auto flex max-w-7xl items-center justify-between p-6 lg:px-8">
        <div class="flex lg:flex-1">
            <a href="/" class="-m-1.5 p-1.5">
                <span
                    class="bg-gradient-to-r from-blue-800 via-indigo-500 to-sky-400 bg-clip-text text-right font-bold text-transparent">Miku</span>
            </a>
        </div>


        <div class="flex lg:hidden items-center space-x-2">

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
                }" x-init="applyTheme()">
                <button @click="toggleTheme()"
                    class="relative inline-flex items-center justify-center rounded-md p-2.5 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-all duration-150 focus:outline-none focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800">
                    <span class="sr-only">テーマ切り替え</span>

                    <svg x-show="theme === 'light'" x-transition:enter="transition-opacity duration-200"
                        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                        x-transition:leave="transition-opacity duration-200" x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0" class="h-6 w-6 text-amber-500" fill="currentColor"
                        viewBox="0 0 24 24">
                        <path
                            d="M12 2.25a.75.75 0 01.75.75v2.25a.75.75 0 01-1.5 0V3a.75.75 0 01.75-.75zM7.5 12a4.5 4.5 0 119 0 4.5 4.5 0 01-9 0zM18.894 6.166a.75.75 0 00-1.06-1.06l-1.591 1.59a.75.75 0 101.06 1.061l1.591-1.59zM21.75 12a.75.75 0 01-.75.75h-2.25a.75.75 0 010-1.5H21a.75.75 0 01.75.75zM17.834 18.894a.75.75 0 001.06-1.06l-1.59-1.591a.75.75 0 10-1.061 1.06l1.59 1.591zM12 18a.75.75 0 01.75.75V21a.75.75 0 01-1.5 0v-2.25A.75.75 0 0112 18zM7.758 17.303a.75.75 0 00-1.061-1.06l-1.591 1.59a.75.75 0 001.06 1.061l1.591-1.59zM6 12a.75.75 0 01-.75.75H3a.75.75 0 010-1.5h2.25A.75.75 0 016 12zM6.697 7.757a.75.75 0 001.06-1.06l-1.59-1.591a.75.75 0 00-1.061 1.06l1.59 1.591z" />
                    </svg>

                    <svg x-show="theme === 'dark'" x-transition:enter="transition-opacity duration-200"
                        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                        x-transition:leave="transition-opacity duration-200" x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0" class="h-6 w-6 text-slate-400" fill="currentColor"
                        viewBox="0 0 24 24">
                        <path
                            d="M9.528 1.718a.75.75 0 01.162.819A8.97 8.97 0 009 6a9 9 0 009 9 8.97 8.97 0 003.463-.69.75.75 0 01.981.98 10.503 10.503 0 01-9.694 6.46c-5.799 0-10.5-4.701-10.5-10.5 0-4.368 2.667-8.112 6.46-9.694a.75.75 0 01.818.162z" />
                    </svg>
                </button>
            </div>


            <button type="button" command="show-modal" commandfor="mobile-menu"
                class="-m-2.5 inline-flex items-center justify-center rounded-md p-2.5 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-300">
                <span class="sr-only">Open main menu</span>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" data-slot="icon"
                    aria-hidden="true" class="size-6">
                    <path d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" stroke-linecap="round"
                        stroke-linejoin="round" />
                </svg>
            </button>
        </div>

        <el-popover-group class="hidden lg:flex lg:gap-x-12">
            <a href="/dashboard"
                class="text-sm/6 font-semibold text-gray-900 dark:text-gray-100 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors duration-300">ダッシュボード</a>
            <a href="/commands"
                class="text-sm/6 font-semibold text-gray-900 dark:text-gray-100 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors duration-300">コマンド一覧</a>
            <a href="/terms"
                class="text-sm/6 font-semibold text-gray-900 dark:text-gray-100 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors duration-300">利用規約</a>
            <a href="/privacy-policy"
                class="text-sm/6 font-semibold text-gray-900 dark:text-gray-100 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors duration-300">プライバシーポリシー</a>
        </el-popover-group>


        <div class="hidden lg:flex lg:flex-1 lg:justify-end items-center space-x-8">

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
                }" x-init="applyTheme()" class="relative">
                <button @click="toggleTheme()"
                    class="relative flex h-8 w-20 items-center justify-between rounded-full bg-gray-200 dark:bg-gray-700 px-2 py-1 transition-all duration-150 hover:bg-gray-300 dark:hover:bg-gray-600 focus:outline-none focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800">


                    <span x-show="theme === 'light'" x-transition:enter="transition-opacity duration-300"
                        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                        x-transition:leave="transition-opacity duration-300" x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0"
                        class="absolute right-4 text-xs font-medium text-gray-600 dark:text-gray-400 select-none pointer-events-none">
                        Light
                    </span>
                    <span x-show="theme === 'dark'" x-transition:enter="transition-opacity duration-300"
                        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                        x-transition:leave="transition-opacity duration-300" x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0"
                        class="absolute left-4 text-xs font-medium text-gray-600 dark:text-gray-400 select-none pointer-events-none">
                        Dark
                    </span>


                    <div class="absolute h-6 w-6 rounded-full bg-white dark:bg-gray-200 shadow-lg transition-all duration-150 ease-in-out border border-gray-300 dark:border-gray-500"
                        :class="theme === 'dark' ? 'translate-x-10' : 'translate-x-0'">

                        <div class="flex h-full w-full items-center justify-center">
                            <svg x-show="theme === 'light'" x-transition:enter="transition-opacity duration-200"
                                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                                x-transition:leave="transition-opacity duration-200"
                                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                                class="h-4 w-4 text-amber-500" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M12 2.25a.75.75 0 01.75.75v2.25a.75.75 0 01-1.5 0V3a.75.75 0 01.75-.75zM7.5 12a4.5 4.5 0 119 0 4.5 4.5 0 01-9 0zM18.894 6.166a.75.75 0 00-1.06-1.06l-1.591 1.59a.75.75 0 101.06 1.061l1.591-1.59zM21.75 12a.75.75 0 01-.75.75h-2.25a.75.75 0 010-1.5H21a.75.75 0 01.75.75zM17.834 18.894a.75.75 0 001.06-1.06l-1.59-1.591a.75.75 0 10-1.061 1.06l1.59 1.591zM12 18a.75.75 0 01.75.75V21a.75.75 0 01-1.5 0v-2.25A.75.75 0 0112 18zM7.758 17.303a.75.75 0 00-1.061-1.06l-1.591 1.59a.75.75 0 001.06 1.061l1.591-1.59zM6 12a.75.75 0 01-.75.75H3a.75.75 0 010-1.5h2.25A.75.75 0 016 12zM6.697 7.757a.75.75 0 001.06-1.06l-1.59-1.591a.75.75 0 00-1.061 1.06l1.59 1.591z" />
                            </svg>
                            <svg x-show="theme === 'dark'" x-transition:enter="transition-opacity duration-200"
                                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                                x-transition:leave="transition-opacity duration-200"
                                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                                class="h-4 w-4 text-slate-600 dark:text-slate-700" fill="currentColor"
                                viewBox="0 0 24 24">
                                <path
                                    d="M9.528 1.718a.75.75 0 01.162.819A8.97 8.97 0 009 6a9 9 0 009 9 8.97 8.97 0 003.463-.69.75.75 0 01.981.98 10.503 10.503 0 01-9.694 6.46c-5.799 0-10.5-4.701-10.5-10.5 0-4.368 2.667-8.112 6.46-9.694a.75.75 0 01.818.162z" />
                            </svg>
                        </div>
                    </div>
                </button>
            </div>


            @auth
                <div class="flex items-center space-x-4">

                    <div class="flex items-center space-x-2">
                        <img src="{{ auth()->user()->avatar_url }}" alt="Avatar"
                            class="h-8 w-8 rounded-full border border-gray-300 dark:border-gray-600">
                        <span
                            class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ auth()->user()->discord_display_name ?? auth()->user()->name }}</span>
                    </div>


                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit"
                            class="text-sm/6 font-semibold text-gray-900 dark:text-gray-100 hover:text-red-600 dark:hover:text-red-400 transition-colors duration-300">
                            ログアウト
                        </button>
                    </form>
                </div>
            @else
                <a href="{{ route('discord.login') }}"
                    class="inline-flex items-center space-x-2 text-sm/6 font-semibold text-gray-900 dark:text-gray-100 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors duration-300">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                        <path
                            d="M20.317 4.3698a19.7913 19.7913 0 00-4.8851-1.5152.0741.0741 0 00-.0785.0371c-.211.3753-.4447.8648-.6083 1.2495-1.8447-.2762-3.68-.2762-5.4868 0-.1636-.3933-.4058-.8742-.6177-1.2495a.077.077 0 00-.0785-.037 19.7363 19.7363 0 00-4.8852 1.515.0699.0699 0 00-.0321.0277C.5334 9.0458-.319 13.5799.0992 18.0578a.0824.0824 0 00.0312.0561c2.0528 1.5076 4.0413 2.4228 5.9929 3.0294a.0777.0777 0 00.0842-.0276c.4616-.6304.8731-1.2952 1.226-1.9942a.076.076 0 00-.0416-.1057c-.6528-.2476-1.2743-.5495-1.8722-.8923a.077.077 0 01-.0076-.1277c.1258-.0943.2517-.1923.3718-.2914a.0743.0743 0 01.0776-.0105c3.9278 1.7933 8.18 1.7933 12.0614 0a.0739.0739 0 01.0785.0095c.1202.099.246.1981.3728.2924a.077.077 0 01-.0066.1276 12.2986 12.2986 0 01-1.873.8914.0766.0766 0 00-.0407.1067c.3604.698.7719 1.3628 1.225 1.9932a.076.076 0 00.0842.0286c1.961-.6067 3.9495-1.5219 6.0023-3.0294a.077.077 0 00.0313-.0552c.5004-5.177-.8382-9.6739-3.5485-13.6604a.061.061 0 00-.0312-.0286zM8.02 15.3312c-1.1825 0-2.1569-1.0857-2.1569-2.419 0-1.3332.9555-2.4189 2.157-2.4189 1.2108 0 2.1757 1.0952 2.1568 2.419-.0002 1.3332-.9555 2.4189-2.1569 2.4189zm7.9748 0c-1.1825 0-2.1569-1.0857-2.1569-2.419 0-1.3332.9554-2.4189 2.1569-2.4189 1.2108 0 2.1757 1.0952 2.1568 2.419 0 1.3332-.9555 2.4189-2.1568 2.4189Z" />
                    </svg>
                    <span>Discordでログイン</span>
                    <span aria-hidden="true">&rarr;</span>
                </a>
            @endauth
        </div>
    </nav>

    <el-dialog>
        <dialog id="mobile-menu" class="backdrop:bg-transparent lg:hidden">
            <div tabindex="0" class="fixed inset-0 focus:outline-none">
                <el-dialog-panel
                    class="fixed inset-y-0 right-0 z-50 w-full overflow-y-auto bg-white dark:bg-gray-800 p-6 sm:max-w-sm sm:ring-1 sm:ring-gray-900/10 dark:sm:ring-gray-100/10 transition-colors duration-300">
                    <div class="flex items-center justify-between">
                        <a href="/" class="-m-1.5 p-1.5">
                            <span
                                class="bg-gradient-to-r from-blue-800 via-indigo-500 to-sky-400 bg-clip-text text-right font-bold text-transparent">Miku</span>
                        </a>
                        <button type="button" command="close" commandfor="mobile-menu"
                            class="-m-2.5 rounded-md p-2.5 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-300">
                            <span class="sr-only">Close menu</span>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"
                                data-slot="icon" aria-hidden="true" class="size-6">
                                <path d="M6 18 18 6M6 6l12 12" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </button>
                    </div>
                    <div class="mt-6 flow-root">
                        <div class="-my-6 divide-y divide-gray-500/10 dark:divide-gray-400/10">
                            <div class="space-y-2 py-6">
                                <a href="/dashboard"
                                    class="-mx-3 block rounded-lg px-3 py-2 text-base/7 font-semibold text-gray-900 dark:text-gray-100 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-300">ダッシュボード</a>
                                <a href="/commands"
                                    class="-mx-3 block rounded-lg px-3 py-2 text-base/7 font-semibold text-gray-900 dark:text-gray-100 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-300">コマンド一覧</a>
                                <a href="/terms"
                                    class="-mx-3 block rounded-lg px-3 py-2 text-base/7 font-semibold text-gray-900 dark:text-gray-100 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-300">利用規約</a>
                                <a href="/privacy-policy"
                                    class="-mx-3 block rounded-lg px-3 py-2 text-base/7 font-semibold text-gray-900 dark:text-gray-100 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-300">プライバシーポリシー</a>
                            </div>
                            <div class="py-6">
                                @auth
                                    <div class="space-y-2">
                                        <div class="flex items-center space-x-2 px-3 py-2">
                                            <img src="{{ auth()->user()->avatar_url }}" alt="Avatar"
                                                class="h-8 w-8 rounded-full border border-gray-300 dark:border-gray-600">
                                            <span
                                                class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ auth()->user()->discord_display_name ?? auth()->user()->name }}</span>
                                        </div>
                                        <form method="POST" action="{{ route('logout') }}" class="inline-block w-full">
                                            @csrf
                                            <button type="submit"
                                                class="-mx-3 block rounded-lg px-3 py-2.5 text-base/7 font-semibold text-gray-900 dark:text-gray-100 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-300 w-full text-left">
                                                ログアウト
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    <a href="{{ route('discord.login') }}"
                                        class="-mx-3 flex items-center space-x-2 rounded-lg px-3 py-2.5 text-base/7 font-semibold text-gray-900 dark:text-gray-100 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-300">
                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                                            <path
                                                d="M20.317 4.3698a19.7913 19.7913 0 00-4.8851-1.5152.0741.0741 0 00-.0785.0371c-.211.3753-.4447.8648-.6083 1.2495-1.8447-.2762-3.68-.2762-5.4868 0-.1636-.3933-.4058-.8742-.6177-1.2495a.077.077 0 00-.0785-.037 19.7363 19.7363 0 00-4.8852 1.515.0699.0699 0 00-.0321.0277C.5334 9.0458-.319 13.5799.0992 18.0578a.0824.0824 0 00.0312.0561c2.0528 1.5076 4.0413 2.4228 5.9929 3.0294a.0777.0777 0 00.0842-.0276c.4616-.6304.8731-1.2952 1.226-1.9942a.076.076 0 00-.0416-.1057c-.6528-.2476-1.2743-.5495-1.8722-.8923a.077.077 0 01-.0076-.1277c.1258-.0943.2517-.1923.3718-.2914a.0743.0743 0 01.0776-.0105c3.9278 1.7933 8.18 1.7933 12.0614 0a.0739.0739 0 01.0785.0095c.1202.099.246.1981.3728.2924a.077.077 0 01-.0066.1276 12.2986 12.2986 0 01-1.873.8914.0766.0766 0 00-.0407.1067c.3604.698.7719 1.3628 1.225 1.9932a.076.076 0 00.0842.0286c1.961-.6067 3.9495-1.5219 6.0023-3.0294a.077.077 0 00.0313-.0552c.5004-5.177-.8382-9.6739-3.5485-13.6604a.061.061 0 00-.0312-.0286zM8.02 15.3312c-1.1825 0-2.1569-1.0857-2.1569-2.419 0-1.3332.9555-2.4189 2.157-2.4189 1.2108 0 2.1757 1.0952 2.1568 2.419-.0002 1.3332-.9555 2.4189-2.1569 2.4189zm7.9748 0c-1.1825 0-2.1569-1.0857-2.1569-2.419 0-1.3332.9554-2.4189 2.1569-2.4189 1.2108 0 2.1757 1.0952 2.1568 2.419 0 1.3332-.9555 2.4189-2.1568 2.4189Z" />
                                        </svg>
                                        <span>Discordでログイン</span>
                                    </a>
                                @endauth
                            </div>
                        </div>
                    </div>
                </el-dialog-panel>
            </div>
        </dialog>
    </el-dialog>
</header>