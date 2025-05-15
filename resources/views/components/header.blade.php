{{-- resources/views/components/header.blade.php --}}
{{-- fixed top-0 left-0 w-full bg-white shadow-md z-50 はPC/スマホ共通 --}}
<header class="fixed top-0 left-0 w-full bg-white shadow-md z-50">
    {{-- container mx-auto の使用をやめ、w-full px-4 を使用 --}}
    <div class="w-full px-4 py-3 flex items-center justify-between">

        {{-- 左側の要素 (スマホ版ドロワーボタン) --}}
        {{-- スマホ版で中央寄せを維持するために flex-1 を再度追加し、PC版では非表示 --}}
        <div class="flex-1 flex items-center md:hidden"> {{-- flex-1 を再度追加 --}}
            <button class="drawer-toggle block text-gray-700 text-2xl focus:outline-none" @click="isDrawerOpen = true"
                    aria-label="メニューを開く">
                <i class="fas fa-bars"></i>
            </button>
        </div>
        {{-- PC版では、左側にサイドバーがあるためヘッダー内に左寄せ用のFlexアイテムは不要 --}}


        {{-- 中央の要素 (サイトロゴ) --}}
        {{-- スマホ版ではflex-grow text-center で中央寄せ、PC版ではflex-grow-0 md:text-left で左寄せ --}}
        {{-- flex-grow を持たせることで、スマホ版で左と右の要素に挟まれた中央に配置されやすくなります --}}
        <div
            class="site-logo text-xl md:text-2xl font-bold text-blue-500
            flex-grow text-center
            md:flex-grow-0 md:text-left"
        >
            Subscru
        </div>


        {{-- 右側の要素 (ユーザー情報 / ログイン・登録リンク) --}}
        {{-- スマホ版ではflex-1 justify-end、PC版でもflex-1 justify-end で右寄せ --}}
        <div class="flex-1 flex justify-end items-center"> {{-- flex-1 はそのまま --}}
            <div class="user-info font-medium text-gray-700">
                @auth
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open"
                                class="flex items-center text-gray-700 hover:text-blue-500 font-medium focus:outline-none p-1 rounded-md hover:bg-gray-100">
                            <i class="fas fa-user-circle text-2xl"></i>
                            <span
                                class="ml-2 hidden md:inline">{{ Auth::user()->name }}さん</span>
                            <svg class="ml-1 h-4 w-4 transition-transform hidden md:inline"
                                 :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                 xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>

                        <div x-show="open" @click.away="open = false"
                             class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50" x-cloak>
                            <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">マイページ</a>
                            <div class="border-t border-gray-100 my-1"></div>
                            <button @click="document.getElementById('logout-form').submit(); open = false;"
                                    class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 focus:outline-none">
                                ログアウト
                            </button>
                        </div>
                    </div>
                @endauth
            </div>
        </div>

    </div>
</header>
