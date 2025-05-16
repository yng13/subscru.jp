<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Subscru - あなたの月額費用をスマートに管理</title>

    {{-- Adobe Fonts (プロジェクトに合わせて適宜変更・削除してください) --}}
    {{-- 既存のindex.blade.phpでTypekit (Adobe Fonts) を使用しているため、ここでもPlaceholderとして残します --}}
    <script>
        (function (d) {
            var config = {
                    kitId: 'tus1ovr', // ★ ご自身の Kit ID に置き換えてください ★
                    scriptTimeout: 3000,
                    async: true
                },
                h = d.documentElement, t = setTimeout(function () {
                    h.className = h.className.replace(/\bwf-loading\b/g, "") + " wf-inactive";
                }, config.scriptTimeout), tk = d.createElement("script"), f = false,
                s = d.getElementsByTagName("script")[0], a;
            h.className += " wf-loading";
            tk.src = 'https://use.typekit.net/' + config.kitId + '.js';
            tk.async = true;
            tk.onload = tk.onreadystatechange = function () {
                a = this.readyState;
                if (f || a && a != "complete" && a != "loaded") return;
                f = true;
                clearTimeout(t);
                try {
                    Typekit.load(config)
                } catch (e) {
                }
            };
            s.parentNode.insertBefore(tk, s)
        })(document);
    </script>

    {{-- Vite CSS Asset - Tailwind CSS を読み込みます --}}
    @vite(['resources/css/app.css'])

    {{-- カスタムCSSをここに追加することも可能です --}}
    <style>
        /* 必要に応じてランディングページ固有のスタイルを記述 */
        /* ヒーローセクションの背景にグラデーションなどを追加するとよりオシャレに */
        .hero {
            /* 例：下から上へ青のグラデーション */
            /* background: linear-gradient(to top, #3b82f6, #2563eb); */
            /* または、Tailwind configで定義したカスタムグラデーションクラスを使用 */
        }
    </style>
</head>
<body class="font-sans antialiased bg-gray-100 text-gray-800 leading-relaxed">

{{-- 未認証ユーザー向け簡易ヘッダー --}}
<header class="bg-white shadow-sm py-4">
    <div class="container mx-auto px-4 flex justify-between items-center">
        {{-- サービス名/ロゴ --}}
        <div class="site-logo text-xl md:text-2xl font-bold text-blue-500">Subscru</div>
        {{-- ログイン・登録リンク --}}
        <nav>
            @if (Route::has('login'))
                <div class="flex items-center space-x-4">
                    @auth
                        {{-- 認証済みの場合はサービス一覧へリダイレクトされる想定なので、ここは表示されない可能性が高いですが、念のため --}}
                        <a href="{{ url('/my') }}" class="text-gray-700 hover:text-blue-600">サービス一覧</a>
                    @else
                        <a href="{{ route('login') }}"
                           class="text-blue-500 hover:text-blue-600 font-medium">ログイン</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}"
                               class="bg-blue-500 text-white font-bold py-2 px-4 rounded-md hover:bg-blue-600 transition-colors">無料ではじめる</a>
                        @endif
                    @endauth
                </div>
            @endif
        </nav>
    </div>
</header>

<main>
    {{-- 1. ヒーローセクション --}}
    <section class="hero bg-blue-500 text-white py-20 md:py-32 text-center relative overflow-hidden">
        {{-- イラストの代わりにシンプルな背景デザイン要素 --}}
        {{-- 例えば、円や波線などをCSSで追加する --}}
        <div class="absolute inset-0 z-0 opacity-20">
            {{-- 例: 背景に大きな円 --}}
            {{-- <div class="absolute top-1/4 left-1/4 w-96 h-96 bg-blue-400 rounded-full mix-blend-multiply filter blur-xl opacity-75 animate-blob"></div>
            <div class="absolute top-1/2 right-1/4 w-96 h-96 bg-emerald-400 rounded-full mix-blend-multiply filter blur-xl opacity-75 animate-blob animation-delay-2000"></div>
            <div class="absolute bottom-1/4 left-1/2 w-96 h-96 bg-blue-400 rounded-full mix-blend-multiply filter blur-xl opacity-75 animate-blob animation-delay-4000"></div> --}}
            {{-- シンプルな斜線パターンなど --}}
            <svg class="w-full h-full" xmlns="http://www.w3.org/2000/svg">
                <pattern id="pattern-checkerboard" x="0" y="0" width="20" height="20" patternUnits="userSpaceOnUse">
                    <rect x="0" y="0" width="10" height="10" fill="rgba(255,255,255,0.1)"/>
                    <rect x="10" y="10" width="10" height="10" fill="rgba(255,255,255,0.1)"/>
                </pattern>
                <rect x="0" y="0" width="100%" height="100%" fill="url(#pattern-checkerboard)"/>
            </svg>
        </div>


        <div class="container mx-auto px-4 relative z-10">
            {{-- サービス名の提示 --}}
            <p class="text-lg md:text-xl mb-3 opacity-90">Subscru（サブスクる）</p>
            {{-- キャッチーな大見出し --}}
            <h1 class="text-3xl md:text-5xl font-bold mb-6 leading-tight drop-shadow-md">
                あなたの月額費用、<br class="sm:hidden">まとめてカレンダーへ。
            </h1>
            {{-- サービス概要を補足する小見出し --}}
            <p class="text-lg md:text-xl mb-12 opacity-95">
                無料トライアルや契約期間の終了日をあなたのカレンダーにお知らせ。<br class="sm:hidden">
                無駄な支払いを自動で防ぎます。
            </p>

            {{-- 主要なCTAボタン --}}
            <a href="{{ route('register') }}"
               class="inline-block bg-emerald-500 text-white text-xl font-bold py-4 px-8 rounded-lg shadow-lg hover:bg-emerald-600 transition-colors transform hover:scale-105 mb-12">
                無料でSubscruをはじめる
            </a>

            {{-- 短いメリットリスト（アイコン付き） --}}
            <ul class="flex justify-center space-x-6 md:space-x-10 text-sm md:text-base font-medium">
                <li class="flex flex-col items-center">
                    <i class="fas fa-calendar-check text-2xl md:text-3xl mb-2"></i>
                    <span>忘れず通知</span>
                </li>
                <li class="flex flex-col items-center">
                    <i class="fas fa-eye text-2xl md:text-3xl mb-2"></i>
                    <span>見える化</span>
                </li>
                <li class="flex flex-col items-center">
                    <i class="fas fa-face-smile text-2xl md:text-3xl mb-2"></i>
                    <span>あんしん</span>
                </li>
            </ul>
        </div>
    </section>

    {{-- 2. 課題提起セクション --}}
    <section class="problem py-20 bg-white">
        <div class="container mx-auto px-4 text-center">
            {{-- セクションタイトル --}}
            <h2 class="text-2xl md:text-4xl font-bold text-gray-900 mb-12">
                あなたは、こんな経験ありませんか？
            </h2>
            {{-- 具体的なペインポイントの列挙 --}}
            <div class="grid md:grid-cols-2 gap-8 max-w-4xl mx-auto">
                <div class="p-6 bg-gray-50 rounded-lg shadow-sm text-left flex items-start border border-gray-200">
                    <i class="fas fa-money-bill-wave text-blue-500 text-2xl mr-4 mt-1 flex-shrink-0"></i>
                    <div>
                        <h3 class="font-semibold text-lg mb-2 text-gray-900">
                            無料トライアル、気づいたら有料になってた…</h3>
                        <p class="text-gray-700 text-sm">
                            「後で解約しよう」と思っていたのに、うっかり期間を過ぎてしまい、自動で課金されてしまった。</p>
                    </div>
                </div>
                <div class="p-6 bg-gray-50 rounded-lg shadow-sm text-left flex items-start border border-gray-200">
                    <i class="fas fa-link text-blue-500 text-2xl mr-4 mt-1 flex-shrink-0"></i>
                    <div>
                        <h3 class="font-semibold text-lg mb-2 text-gray-900">
                            このネット契約、いつまで『縛り』があるんだっけ？</h3>
                        <p class="text-gray-700 text-sm">
                            契約更新月を忘れてしまい、解約金がかかる期間に入ってしまった。サービスの詳細や契約内容をすぐに確認できない。</p>
                    </div>
                </div>
                <div class="p-6 bg-gray-50 rounded-lg shadow-sm text-left flex items-start border border-gray-200">
                    <i class="fas fa-wallet text-blue-500 text-2xl mr-4 mt-1 flex-shrink-0"></i>
                    <div>
                        <h3 class="font-semibold text-lg mb-2 text-gray-900">
                            毎月、一体いくらサブスクに払ってるんだろう？</h3>
                        <p class="text-gray-700 text-sm">
                            たくさんのサービスを契約しているが、全体の費用を把握できていない。無駄なサービスがあるかもしれないが、調べるのが面倒。</p>
                    </div>
                </div>
                <div class="p-6 bg-gray-50 rounded-lg shadow-sm text-left flex items-start border border-gray-200">
                    <i class="fas fa-search text-blue-500 text-2xl mr-4 mt-1 flex-shrink-0"></i>
                    <div>
                        <h3 class="font-semibold text-lg mb-2 text-gray-900">
                            サービスの管理画面や、解約方法がすぐ見つからない</h3>
                        <p class="text-gray-700 text-sm">
                            サービスごとに管理画面の場所や解約手順が異なり、いざという時にすぐに見つけられず困った経験がある。</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- 3. Subscruができること / 解決策セクション --}}
    <section class="solution py-20 bg-blue-600 text-white">
        <div class="container mx-auto px-4 text-center">
            {{-- セクションタイトル --}}
            <h2 class="text-2xl md:text-4xl font-bold mb-8">
                Subscru があなたの悩みを解決します
            </h2>
            <h3 class="text-xl md:text-2xl font-semibold mb-12 opacity-95">使い方はシンプル。</h3>

            {{-- 利用ステップの提示 --}}
            <div class="grid md:grid-cols-3 gap-8 max-w-5xl mx-auto">
                {{-- Step 1 --}}
                <div class="flex flex-col items-center p-6 bg-blue-700 rounded-lg shadow-lg">
                    <div
                        class="w-16 h-16 rounded-full bg-white text-blue-700 flex items-center justify-center text-3xl font-bold mb-4 shadow-md">
                        1
                    </div>
                    <h4 class="font-semibold text-lg mb-3">サービスを登録</h4>
                    <p class="text-sm mb-4 opacity-90">サービス名と終了日などを簡単入力。</p>
                    {{-- シンプルな図形やアイコンの組み合わせで代用 --}}
                    <div
                        class="w-full max-w-xs bg-blue-500 h-32 rounded-md shadow flex items-center justify-center text-white text-3xl">
                        <i class="fas fa-keyboard"></i>
                    </div>
                </div>
                {{-- Step 2 --}}
                <div class="flex flex-col items-center p-6 bg-blue-700 rounded-lg shadow-lg">
                    <div
                        class="w-16 h-16 rounded-full bg-white text-blue-700 flex items-center justify-center text-3xl font-bold mb-4 shadow-md">
                        2
                    </div>
                    <h4 class="font-semibold text-lg mb-3">カレンダーと連携</h4>
                    <p class="text-sm mb-4 opacity-90">あなたのカレンダーに専用カレンダーを追加。</p>
                    {{-- シンプルな図形やアイコンの組み合わせで代用 --}}
                    <div
                        class="w-full max-w-xs bg-blue-500 h-32 rounded-md shadow flex items-center justify-center text-white text-3xl">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                </div>
                {{-- Step 3 --}}
                <div class="flex flex-col items-center p-6 bg-blue-700 rounded-lg shadow-lg">
                    <div
                        class="w-16 h-16 rounded-full bg-white text-blue-700 flex items-center justify-center text-3xl font-bold mb-4 shadow-md">
                        3
                    </div>
                    <h4 class="font-semibold text-lg mb-3">通知を受け取る</h4>
                    <p class="text-sm mb-4 opacity-90">重要な期日が近づくと、カレンダーから通知。</p>
                    {{-- シンプルな図形やアイコンの組み合わせで代用 --}}
                    <div
                        class="w-full max-w-xs bg-blue-500 h-32 rounded-md shadow flex items-center justify-center text-white text-3xl">
                        <i class="fas fa-bell"></i>
                    </div>
                </div>
            </div>
            {{-- イラスト案の代わりにアイコン間の関連性を示すシンプルなデザイン --}}
            <div class="flex justify-center mt-8">
                <div class="w-8 h-8 bg-white rounded-full flex items-center justify-center text-blue-600 text-xl">
                    <i class="fas fa-arrow-down"></i>
                </div>
                <div class="w-8 h-8 bg-white rounded-full flex items-center justify-center text-blue-600 text-xl ml-4">
                    <i class="fas fa-arrow-down"></i>
                </div>
                <div class="w-8 h-8 bg-white rounded-full flex items-center justify-center text-blue-600 text-xl ml-4">
                    <i class="fas fa-arrow-down"></i>
                </div>
            </div>
        </div>
    </section>

    {{-- 4. Subscru の強み / なぜ選ばれるかセクション --}}
    <section class="features py-20 bg-white">
        <div class="container mx-auto px-4 text-center">
            {{-- セクションタイトル --}}
            <h2 class="text-2xl md:text-4xl font-bold text-gray-900 mb-12">
                Subscru が選ばれる理由
            </h2>
            <h3 class="text-xl md:text-2xl font-semibold text-gray-800 mb-12">もう、うっかりをなくそう。</h3>

            {{-- V1の主要機能を具体的にアピール --}}
            <div class="grid md:grid-cols-2 gap-8 max-w-4xl mx-auto text-left">
                <div class="flex items-start p-6 bg-gray-50 rounded-lg shadow-sm border border-gray-200">
                    <i class="fas fa-bell text-blue-500 text-2xl mr-4 mt-1 flex-shrink-0"></i>
                    <div>
                        <h4 class="font-semibold text-lg mb-2 text-gray-900">無料トライアル・契約期間 通知</h4>
                        <p class="text-gray-700 text-sm">
                            もう、気づいたら有料化はなし。解約のベストタイミングを逃しません。重要な期日をカレンダーで事前に確認できます。
                        </p>
                    </div>
                </div>
                <div class="flex items-start p-6 bg-gray-50 rounded-lg shadow-sm border border-gray-200">
                    <i class="fas fa-layer-group text-blue-500 text-2xl mr-4 mt-1 flex-shrink-0"></i>
                    <div>
                        <h4 class="font-semibold text-lg mb-2 text-gray-900">あらゆる月額費用に対応</h4>
                        <p class="text-gray-700 text-sm">
                            デジタルサービスから、通信費、光熱費まで。カレンダーに登録できるものなら、まとめて管理できます。
                        </p>
                    </div>
                </div>
                <div class="flex items-start p-6 bg-gray-50 rounded-lg shadow-sm border border-gray-200">
                    <i class="fas fa-calendar-alt text-blue-500 text-2xl mr-4 mt-1 flex-shrink-0"></i>
                    <div>
                        <h4 class="font-semibold text-lg mb-2 text-gray-900">普段お使いのカレンダー連携</h4>
                        <p class="text-gray-700 text-sm">
                            新しいアプリを覚える必要はありません。普段お使いのGoogleカレンダーやAppleカレンダーなどで通知を受け取れます。
                        </p>
                    </div>
                </div>
                <div class="flex items-start p-6 bg-gray-50 rounded-lg shadow-sm border border-gray-200">
                    <i class="fas fa-keyboard text-blue-500 text-2xl mr-4 mt-1 flex-shrink-0"></i>
                    <div>
                        <h4 class="font-semibold text-lg mb-2 text-gray-900">シンプルな手動入力</h4>
                        <p class="text-gray-700 text-sm">
                            項目を絞って、簡単登録。複雑なサービス連携は不要です。必要な情報を手軽に登録・管理できます。
                        </p>
                    </div>
                </div>
            </div>

            {{-- （もし入るなら）将来への期待 --}}
            <div class="mt-16 text-gray-700">
                <p class="text-xl font-semibold mb-4">将来の展望</p>
                <p class="text-sm">
                    今後は、みんなのデータで平均料金や解約方法なども分かるようにするなど、<br class="sm:hidden">
                    さらに便利な機能を提供予定です。
                </p>
            </div>
        </div>
    </section>

    {{-- 5. 行動喚起セクション --}}
    <section class="cta py-20 bg-blue-500 text-white text-center">
        <div class="container mx-auto px-4">
            {{-- もう一度、核となるメリットを強調 --}}
            <h2 class="text-2xl md:text-4xl font-bold mb-8">
                さあ、あなたもSubscruで<br class="sm:hidden">月額費用をスマートに管理しませんか？
            </h2>

            {{-- 主要なCTAボタンを再度配置 --}}
            <a href="{{ route('register') }}"
               class="inline-block bg-emerald-500 text-white text-xl font-bold py-4 px-8 rounded-lg shadow-lg hover:bg-emerald-600 transition-colors transform hover:scale-105 mb-8">
                無料でSubscruをはじめる
            </a>

            {{-- 信頼性を示す情報（あれば） --}}
            {{-- <p class="text-lg mb-4 opacity-90">〇〇人が利用中！</p> --}}
            {{-- <p class="text-sm mb-4 opacity-90">メディア掲載実績：XX新聞、YYサイト</p> --}}
            {{-- ユーザーの声（あれば） --}}
            {{-- <div class="mt-8 italic text-white text-opacity-80">
                <p>「Subscruのおかげで、無駄な支払いが激減しました！」 - ユーザーAさん</p>
            </div> --}}

            {{-- 安心感を与える一言 --}}
            <p class="text-lg font-semibold mt-8 opacity-95">
                <i class="fas fa-check-circle mr-2"></i>クレジットカード登録不要
            </p>
        </div>
    </section>

    {{-- 6. FAQセクション --}}
    <section class="faq py-20 bg-white">
        <div class="container mx-auto px-4">
            {{-- セクションタイトル --}}
            <h2 class="text-2xl md:text-4xl font-bold text-gray-900 text-center mb-12">
                よくあるご質問 (FAQ)
            </h2>
            {{-- よくある質問とその回答リスト --}}
            <div class="max-w-3xl mx-auto text-left divide-y divide-gray-200">
                <div class="py-6"> {{-- パディングを調整 --}}
                    <h3 class="font-semibold text-lg mb-2 text-gray-900">Q: 本当に無料ですか？</h3>
                    <p class="text-gray-700 text-sm">A: はい、Subscruは基本的な機能は完全無料でご利用いただけます。</p>
                </div>
                <div class="py-6"> {{-- パディングを調整 --}}
                    <h3 class="font-semibold text-lg mb-2 text-gray-900">Q: どんな情報を提供する必要がありますか？</h3>
                    <p class="text-gray-700 text-sm">A:
                        登録時に必要な情報は、サービス名、種別（契約中またはトライアル）、通知対象日、通知タイミング、および任意でメモです。個人情報やクレジットカード情報などの入力は不要です。</p>
                </div>
                <div class="py-6"> {{-- パディングを調整 --}}
                    <h3 class="font-semibold text-lg mb-2 text-gray-900">Q: カレンダー連携は安全ですか？</h3>
                    <p class="text-gray-700 text-sm">A:
                        はい、安全です。カレンダー連携には、ユーザーごとに発行されるユニークなURL（iCalフィード）を使用します。このURLを知っている人のみがカレンダー情報を購読できますが、個人を特定できる情報は含まれていません。パスワードなどの認証情報は不要です。</p>
                </div>
                <div class="py-6"> {{-- パディングを調整 --}}
                    <h3 class="font-semibold text-lg mb-2 text-gray-900">Q: スマートフォンでも使えますか？</h3>
                    <p class="text-gray-700 text-sm">A:
                        はい、ご利用いただけます。スマートフォンやタブレットなどの様々なデバイスのブラウザからアクセス可能です。レスポンシブデザインに対応しており、快適にご利用いただけます。</p>
                </div>
                {{-- 必要に応じて質問を追加 --}}
            </div>
        </div>
    </section>
</main>

{{-- 7. フッター --}}
<footer class="bg-gray-800 text-gray-300 py-8 text-sm">
    <div class="container mx-auto px-4 text-center">
        <p class="mb-4">&copy; {{ date('Y') }} Subscru. All rights reserved.</p>
        <div class="flex justify-center space-x-4">
            <a href="{{ route('privacy') }}" class="hover:text-white transition-colors">プライバシーポリシー</a>
            <a href="#" class="hover:text-white transition-colors">利用規約</a>
            <a href="#" class="hover:text-white transition-colors">お問い合わせ</a>
        </div>
    </div>
</footer>

{{-- Vite JS Asset - 静的なランディングページなら不要 --}}
{{-- @vite(['resources/js/app.js']) --}}

</body>
</html>
