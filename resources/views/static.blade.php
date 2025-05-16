<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Subscru - @yield('title')</title> {{-- ★ ページのタイトルを各ページで変更 ★ --}}

    {{-- Adobe Fonts (プロジェクトに合わせて適宜変更・削除してください) --}}
    {{-- 既存のindex.blade.phpやwelcome.blade.phpに合わせます --}}
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

    {{-- 各ページ固有のスタイルをここに追加することも可能です --}}
    <style>
    </style>
</head>
{{-- 既存のコードの body クラスに合わせる --}}
<body class="font-sans antialiased bg-gray-100 text-gray-800 leading-relaxed">

{{-- ヘッダー部分 --}}
{{-- ランディングページで使用した簡易ヘッダーをベースにします --}}
<header class="bg-white shadow-sm py-4">
    <div class="container mx-auto px-4 flex justify-between items-center">
        {{-- サービス名/ロゴ - クリックでランディングページに戻る --}}
        <div class="site-logo text-xl md:text-2xl font-bold text-blue-500">
            <a href="{{ route('welcome') }}" class="hover:text-blue-600 transition-colors">Subscru</a>
        </div>
        {{-- ナビゲーションリンク --}}
        <nav>
            <ul class="flex items-center space-x-4 text-sm md:text-base">
                {{-- 必要に応じて他のページへのリンクを追加 --}}
                <li><a href="{{ route('welcome') }}" class="text-gray-700 hover:text-blue-600">ホーム</a></li>
                {{--<li><a href="{{ route('my.index') }}" class="text-gray-700 hover:text-blue-600">サービス一覧 (Myページ)</a></li> {{-- 認証済みの場合のみアクセス可 --}}
                {{-- 各ページへのリンクをここに追加 --}}
                <li><a href="{{ route('privacy') }}" class="text-gray-700 hover:text-blue-600">プライバシーポリシー</a>
                </li>
                <li><a href="{{ route('terms') }}" class="text-gray-700 hover:text-blue-600">利用規約</a></li>
                <li><a href="#" class="text-gray-700 hover:text-blue-600">お問い合わせ</a></li>
            </ul>
        </nav>
    </div>
</header>

{{-- メインコンテンツエリア --}}
<main class="py-12 md:py-20"> {{-- 上下に十分な余白を持たせる --}}
    <div class="container mx-auto px-4 max-w-3xl"> {{-- コンテンツ幅を制限し中央寄せ --}}

        @yield('content')

        {{-- ★ ここに各ページ固有のコンテンツを記述 ★ --}}

        {{-- 例: プライバシーポリシーページのコンテンツ構造 --}}
        {{--
        <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mb-8">プライバシーポリシー</h1>

        <section class="mb-8">
            <h2 class="text-xl md:text-2xl font-semibold text-gray-800 mb-4">はじめに</h2>
            <p class="text-gray-700 leading-relaxed mb-4">
                このプライバシーポリシーは、[サービス名]（以下「本サービス」といいます）における、ユーザーの個人情報を含む利用者情報の取扱いについて定めるものです。
            </p>
            <p class="text-gray-700 leading-relaxed">
                本サービスをご利用になる前に、本プライバシーポリシーをよくお読みください。
            </p>
        </section>

        <section class="mb-8">
            <h2 class="text-xl md:text-2xl font-semibold text-gray-800 mb-4">取得する情報と取得方法</h2>
            <h3 class="text-lg font-semibold text-gray-800 mb-2">1. ユーザーにご提供いただく情報</h3>
            <ul class="list-disc list-inside text-gray-700 leading-relaxed mb-4">
                <li>氏名またはニックネーム</li>
                <li>メールアドレス</li>
                <li>パスワード</li>
                <li>その他、本サービスにおいて入力いただく情報</li>
            </ul>
             <h3 class="text-lg font-semibold text-gray-800 mb-2">2. 本サービスのご利用にあたって当社が取得する情報</h3>
            <ul class="list-disc list-inside text-gray-700 leading-relaxed">
                <li>端末情報</li>
                <li>ログ情報（IPアドレス、ブラウザ種類、OS種類など）</li>
                <li>Cookie</li>
                <li>その他、本サービスの利用状況に関する情報</li>
            </ul>
        </section>

        <section>
             <h2 class="text-xl md:text-2xl font-semibold text-gray-800 mb-4">情報の利用目的</h2>
             <p class="text-gray-700 leading-relaxed mb-4">
                 取得した利用者情報は、以下の目的のために利用します。
             </p>
             <ul class="list-disc list-inside text-gray-700 leading-relaxed">
                 <li>本サービスの提供・運営のため</li>
                 <li>ユーザーからのお問い合わせに対応するため</li>
                 <li>本サービスの改善、新サービスの開発のため</li>
                 <li>本サービスに関する情報提供のため</li>
                 <li>利用規約に違反したユーザーへの対応のため</li>
                 <li>その他、上記利用目的に付随する目的のため</li>
             </ul>
        </section>
         --}}

        {{-- 例: お問い合わせフォームのコンテンツ構造 --}}
        {{--
        <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mb-8">お問い合わせ</h1>

        <p class="text-gray-700 leading-relaxed mb-8">
            本サービスに関するご意見、ご要望、不具合報告など、以下のフォームよりお気軽にお問い合わせください。
        </p>

        <form action="[お問い合わせ送信先のURL]" method="POST">
            @csrf
        <div class="mb-4">
            <label for="name" class="block font-medium text-gray-900 mb-1">お名前</label>
            <input type="text" id="name" name="name" class="w-full p-2 border rounded-md focus:outline-none focus:border-blue-500 focus:ring focus:ring-blue-200" required>
        </div>
        <div class="mb-4">
            <label for="email" class="block font-medium text-gray-900 mb-1">メールアドレス</label>
            <input type="email" id="email" name="email" class="w-full p-2 border rounded-md focus:outline-none focus:border-blue-500 focus:ring focus:ring-blue-200" required>
        </div>
        <div class="mb-6">
            <label for="message" class="block font-medium text-gray-900 mb-1">お問い合わせ内容</label>
            <textarea id="message" name="message" rows="6" class="w-full p-2 border rounded-md focus:outline-none focus:border-blue-500 focus:ring focus:ring-blue-200" required></textarea>
        </div>
        <div>
            <button type="submit" class="bg-blue-500 text-white font-bold py-2 px-4 rounded-md hover:bg-blue-600 transition-colors">送信する</button>
        </div>
        </form>
        --}}

    </div>
</main>

{{-- フッター部分 --}}
{{-- ランディングページで使用したフッターをベースにします --}}
<footer class="bg-gray-800 text-gray-300 py-8 text-sm">
    <div class="container mx-auto px-4 text-center">
        <p class="mb-4">&copy; {{ date('Y') }} Subscru. All rights reserved.</p>
        <div class="flex justify-center space-x-4">
            <a href="{{ route('privacy') }}" class="hover:text-white transition-colors">プライバシーポリシー</a>
            <a href="{{ route('terms') }}" class="hover:text-white transition-colors">利用規約</a>
            <a href="#" class="hover:text-white transition-colors">お問い合わせ</a>
        </div>
    </div>
</footer>

{{-- Vite JS Asset - これらのページでAlpine.jsなどが必要なければ不要です --}}
{{-- @vite(['resources/js/app.js']) --}}

</body>
</html>
