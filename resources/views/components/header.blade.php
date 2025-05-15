{{-- resources/views/components/header.blade.php --}}
{{-- fixed top-0 left-0 w-full bg-white shadow-md z-50 はPC/スマホ共通 --}}
<header class="fixed top-0 left-0 w-full bg-white shadow-md z-50">
    <div class="container mx-auto px-4 py-3 flex items-center justify-between">

        {{-- 左側の要素 (スマホ版ドロワーボタン / PC版はロゴの左側) --}}
        {{-- スマホ版でのみ表示されるドロワー開閉ボタン --}}
        <div class="flex-1 flex items-center md:hidden"> {{-- md:hidden を追加 --}}
            <button class="drawer-toggle block text-gray-700 text-2xl focus:outline-none" @click="isDrawerOpen = true"
                    aria-label="メニューを開く">
                <i class="fas fa-bars"></i>
            </button>
        </div>
        {{-- PC版ではここは空になるか、必要に応じて要素を追加 --}}
        {{-- === ここを修正 - PC版で Flex アイテムとして振る舞い、ロゴの左側にスペースを確保 === --}}
        <div class="flex-1 flex items-center hidden md:flex"></div>
        {{-- ============================================================= --}}


        {{-- 中央の要素 (サイトロゴ) --}}
        {{-- スマホ版ではflex-grow text-center で中央寄せ、PC版ではデフォルトの配置 --}}
        {{-- === ここを修正 - PC版では中央寄せを解除し、flex-grow-0 を明示的に指定 === --}}
        <div
            class="site-logo text-xl md:text-2xl font-bold text-blue-500
            flex-grow text-center
            md:flex-grow-0 md:text-left"
        >
            Subscru
        </div>
        {{-- ======================================================= --}}


        {{-- 右側の要素 (ユーザー情報 / ログイン・登録リンク) --}}
        <div class="flex-1 flex justify-end items-center">
            <div class="user-info font-medium text-gray-700">
                @auth
                    {{-- アバターアイコン + ユーザー名 + ドロップダウンメニュー (PC/スマホ共通のドロップダウン本体) --}}
                    {{-- button 内の表示要素をレスポンシブで切り替える --}}
                    <div x-data="{ open: false }" class="relative">
                        {{-- クリッカブルな領域 (アバターアイコン + PC版ユーザー名) --}}
                        {{-- Flexbox でアイコンとユーザー名を横並びにし、中央揃え --}}
                        <button @click="open = !open"
                                class="flex items-center text-gray-700 hover:text-blue-500 font-medium focus:outline-none p-1 rounded-md hover:bg-gray-100">
                            {{-- アバターアイコン --}}
                            <i class="fas fa-user-circle text-2xl"></i>
                            {{-- ユーザー名 (PC版のみ表示) --}}
                            <span
                                class="ml-2 hidden md:inline">{{ Auth::user()->name }}さん</span> {{-- ml-2 でアイコンとの間にスペース --}}
                            {{-- ドロップダウンの開閉を示すアイコン (PC版のみ表示) --}}
                            <svg class="ml-1 h-4 w-4 transition-transform hidden md:inline"
                                 :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                 xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>

                        {{-- ドロップダウンメニュー本体 (PC/スマホ共通) --}}
                        <div x-show="open" @click.away="open = false"
                             class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50" x-cloak>
                            {{-- マイページリンク --}}
                            <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">マイページ</a>
                            {{-- 区切り線 --}}
                            <div class="border-t border-gray-100 my-1"></div>
                            {{-- ログアウトボタン --}}
                            <button @click="document.getElementById('logout-form').submit(); open = false;"
                                    class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 focus:outline-none">
                                ログアウト
                            </button>
                        </div>
                    </div>
                @endauth
                {{-- 未ログイン時のログイン/登録リンクはindex.bladeでは表示しないため削除 --}}
            </div>
        </div>

    </div>
</header>
