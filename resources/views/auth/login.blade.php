<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ログイン - Subscru</title> {{-- ここはブラウザタブのタイトルなのでこのままで良いでしょう --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    {{-- Vite CSS Asset --}}
    @vite(['resources/css/app.css'])
</head>
<body class="bg-gray-100 text-gray-700 font-sans leading-relaxed flex items-center justify-center min-h-screen">

<div class="container mx-auto px-4">
    <div class="max-w-md mx-auto bg-white p-8 rounded-lg shadow-lg">
        <div class="text-center mb-6">
            {{-- index.blade.php のロゴスタイルを適用 --}}
            <div class="site-logo text-xl md:text-2xl font-bold text-blue-500 inline-block">Subscru</div>
            {{-- 改行と「ログイン」の文言 --}}
            <h1 class="text-2xl font-semibold text-gray-900 mt-2">ログイン</h1>
        </div>

        {{-- ... フォームの内容はそのまま ... --}}
        <form method="POST" action="{{ route('login') }}">
            @csrf

            {{-- メールアドレス入力フィールド --}}
            <div class="mb-4">
                <label for="email" class="block font-medium text-gray-900 mb-1">メールアドレス</label>
                <input type="email" id="email" name="email"
                       class="w-full p-2 border rounded-md focus:outline-none focus:border-blue-500 focus:ring focus:ring-blue-200 @error('email') border-red-500 @enderror"
                       value="{{ old('email') }}" required autofocus>
                @error('email')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- パスワード入力フィールド --}}
            <div class="mb-6">
                <label for="password" class="block font-medium text-gray-900 mb-1">パスワード</label>
                <input type="password" id="password" name="password"
                       class="w-full p-2 border rounded-md focus:outline-none focus:border-blue-500 focus:ring focus:ring-blue-200 @error('password') border-red-500 @enderror"
                       required autocomplete="current-password">
                @error('password')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- ログイン状態を維持するチェックボックス (必要であれば) --}}
            {{--
            <div class="mb-4">
                <input type="checkbox" name="remember" id="remember" class="mr-1">
                <label for="remember" class="text-gray-700">ログイン状態を維持する</label>
            </div>
            --}}

            <div class="flex items-center justify-between">
                <button type="submit"
                        class="bg-blue-500 text-white font-bold py-2 px-4 rounded hover:bg-blue-600 focus:outline-none focus:shadow-outline">
                    ログイン
                </button>

                {{-- パスワードリセットリンク (必要であれば) --}}
                {{--
                @if (Route::has('password.request'))
                    <a class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800" href="{{ route('password.request') }}">
                        パスワードを忘れましたか？
                    </a>
                @endif
                --}}
            </div>
        </form>

        {{-- 登録画面へのリンク --}}
        @if (Route::has('register'))
            <div class="text-center mt-6">
                <p class="text-gray-700 text-sm">
                    アカウントをお持ちでない場合は、
                    <a href="{{ route('register') }}" class="text-blue-500 hover:text-blue-800 font-bold">
                        こちらから登録
                    </a>
                    してください。
                </p>
            </div>
        @endif

    </div>
</div>

{{-- Vite JS Asset --}}
@vite(['resources/js/app.js'])
</body>
</html>
