{{-- resources/views/index.blade.php --}}
    <!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subscru - サービス一覧</title>

    {{-- CSRF Token for API requests --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script>
        (function (d) {
            var config = {
                    kitId: 'tus1ovr', // ご自身の Kit ID に置き換えてください
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
    {{-- Vite CSS Asset --}}
    @vite(['resources/css/app.css'])
</head>
{{-- x-data now calls the function registered with Alpine.data in app.js --}}
<body class="bg-gray-100 text-gray-700 font-sans leading-relaxed" x-data="serviceListPage()">

{{-- ヘッダー部分を単一のHTMLにまとめる --}}
@include('components.header')
{{-- ここまで --}}

@include('components.drawer')

@include('components.sidebar')

<main class="main-content py-8 md:py-10 md:ml-64 md:mt-16">
    <div class="container mx-auto px-4">

        @include('components.ical-settings')

        @include('components.service-list-section')

    </div>
</main>

@include('components.fab-button')

@include('components.modals')

{{-- ログアウトのための隠しフォーム --}}
<form id="logout-form" action="/logout" method="POST" style="display: none;">
    @csrf {{-- Laravel の CSRF 保護のためのトークン --}}
</form>

{{-- Vite JS Asset --}}
@vite(['resources/js/app.js'])

@include('components.toast-notification')
</body>
</html>
