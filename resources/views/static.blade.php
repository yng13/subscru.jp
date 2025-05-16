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

@include('partials.header')

{{-- メインコンテンツエリア --}}
<main class="py-12 md:py-20"> {{-- 上下に十分な余白を持たせる --}}
    <div class="container mx-auto px-4 max-w-3xl"> {{-- コンテンツ幅を制限し中央寄せ --}}

        @yield('content')

    </div>
</main>

@include('partials.footer')

{{-- Vite JS Asset - これらのページでAlpine.jsなどが必要なければ不要です --}}
{{-- @vite(['resources/js/app.js']) --}}

</body>
</html>
