{{-- 未認証ユーザー向け簡易ヘッダー (項目絞り込み版 - ホームリンク削除) --}}
<header class="bg-white shadow-sm py-4">
    <div class="container mx-auto px-4 flex justify-between items-center">

        {{-- サービス名/ロゴ - クリックでランディングページに戻る --}}
        {{-- スマホ版では中央寄せにしたいので flex-grow と text-center を使用 --}}
        {{-- PC版では md:flex-grow-0 md:text-left で Flex の効果をなくし左寄せ --}}
        <div class="site-logo text-xl md:text-2xl font-bold text-blue-500
                        flex-grow text-center md:flex-grow-0 md:text-left">
            <a href="{{ route('welcome') }}" class="hover:text-blue-600 transition-colors">Subscru</a>
        </div>

        {{-- PC版ナビゲーションリンク --}}
        {{-- md:block でPC版では表示 --}}
        <nav class="hidden md:block">
            <ul class="flex items-center space-x-4 text-sm md:text-base">
                {{-- PC版にはホーム以外の主要リンクを表示 --}}
                {{-- <li><a href="{{ route('welcome') }}" class="text-gray-700 hover:text-blue-600 transition-colors">ホーム</a></li> --}} {{-- ホームリンクを削除 --}}

                {{-- 認証済みの場合のみ表示するリンク --}}
                @auth
                    <li><a href="{{ route('my.index') }}" class="text-gray-700 hover:text-blue-600 transition-colors">サービス一覧
                            (Myページ)</a></li>
                @endauth

                {{-- ログイン・無料ではじめるボタン (未認証の場合のみPC版ヘッダー右端に表示) --}}
                @guest
                    <li><a href="{{ route('login') }}"
                           class="text-blue-500 hover:text-blue-600 font-medium transition-colors">ログイン</a></li>
                    @if (Route::has('register'))
                        <li><a href="{{ route('register') }}"
                               class="bg-blue-500 text-white font-bold py-2 px-4 rounded-md hover:bg-blue-600 transition-colors">無料ではじめる</a>
                        </li>
                    @endif
                @endguest
            </ul>
        </nav>

        {{-- スマホ版ナビゲーションリンク --}}
        {{-- md:hidden でスマホ版のみ表示 --}}
        <nav class="md:hidden">
            <ul class="flex items-center space-x-3 text-sm"> {{-- スマホ向けにスペースを少し狭く --}}
                {{-- 未認証の場合のみログイン・無料ではじめるを表示 --}}
                @guest
                    <li><a href="{{ route('login') }}"
                           class="text-blue-500 hover:text-blue-600 font-medium transition-colors whitespace-nowrap">ログイン</a>
                    </li> {{-- whitespace-nowrap で折り返しを防ぐ --}}
                    @if (Route::has('register'))
                        <li><a href="{{ route('register') }}"
                               class="bg-blue-500 text-white font-bold py-1.5 px-3 rounded-md hover:bg-blue-600 transition-colors whitespace-nowrap">無料ではじめる</a>
                        </li> {{-- ボタンのパディングを調整、whitespace-nowrap --}}
                    @endif
                @endguest
                {{-- 認証済みの場合はMyページへのリンクなどを検討できますが、今回は未認証向けに絞ります --}}
            </ul>
        </nav>

    </div>
</header>
