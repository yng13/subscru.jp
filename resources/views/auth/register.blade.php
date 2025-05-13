<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ユーザー登録 - Subscru</title> {{-- ここはブラウザタブのタイトルなのでこのままで良いでしょう --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    {{-- Vite CSS Asset --}}
    @vite(['resources/css/app.css'])
</head>
<body class="bg-gray-100 text-gray-700 font-sans leading-relaxed flex items-center justify-center min-h-screen">

<div class="container mx-auto px-4">
    <div class="max-w-md mx-auto bg-white p-8 rounded-lg shadow-lg">
        {{-- タイトル部分を修正 --}}
        <div class="text-center mb-6">
            {{-- index.blade.php のロゴスタイルを適用 --}}
            <div class="site-logo text-xl md:text-2xl font-bold text-blue-500 inline-block">Subscru</div>
            {{-- 改行と「ユーザー登録」の文言 --}}
            <h1 class="text-2xl font-semibold text-gray-900 mt-2">ユーザー登録</h1>
        </div>

        {{-- ... フォームの内容はそのまま ... --}}
        <form method="POST" action="{{ route('register') }}">
            @csrf

            {{-- 名前入力フィールド --}}
            <div class="mb-4">
                <label for="name" class="block font-medium text-gray-900 mb-1">お名前</label>
                <input type="text" id="name" name="name"
                       class="w-full p-2 border rounded-md focus:outline-none focus:border-blue-500 focus:ring focus:ring-blue-200 @error('name') border-red-500 @enderror"
                       value="{{ old('name') }}" required autofocus autocomplete="name">
                @error('name')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- メールアドレス入力フィールド --}}
            <div class="mb-4">
                <label for="email" class="block font-medium text-gray-900 mb-1">メールアドレス</label>
                <input type="email" id="email" name="email"
                       class="w-full p-2 border rounded-md focus:outline-none focus:border-blue-500 focus:ring focus:ring-blue-200 @error('email') border-red-500 @enderror"
                       value="{{ old('email') }}" required autocomplete="email">
                @error('email')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- パスワード入力フィールド --}}
            <div class="mb-4">
                <label for="password" class="block font-medium text-gray-900 mb-1">パスワード</label>
                <input type="password" id="password" name="password"
                       class="w-full p-2 border rounded-md focus:outline-none focus:border-blue-500 focus:ring focus:ring-blue-200 @error('password') border-red-500 @enderror"
                       required autocomplete="new-password">
                @error('password')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- パスワード確認入力フィールド --}}
            <div class="mb-6">
                <label for="password_confirmation" class="block font-medium text-gray-900 mb-1">パスワード (確認用)</label>
                <input type="password" id="password_confirmation" name="password_confirmation"
                       class="w-full p-2 border rounded-md focus:outline-none focus:border-blue-500 focus:ring focus:ring-blue-200"
                       required autocomplete="new-password">
            </div>

            <div>
                <button type="submit"
                        class="w-full bg-blue-500 text-white font-bold py-2 px-4 rounded hover:bg-blue-600 focus:outline-none focus:shadow-outline">
                    登録する
                </button>
            </div>
        </form>

        {{-- ログイン画面へのリンク --}}
        <div class="text-center mt-6">
            <p class="text-gray-700 text-sm">
                既にアカウントをお持ちの場合は、
                <a href="{{ route('login') }}" class="text-blue-500 hover:text-blue-800 font-bold">
                    こちらからログイン
                </a>
                してください。
            </p>
        </div>

    </div>
</div>

{{-- Vite JS Asset --}}
@vite(['resources/js/app.js'])
</body>
</html>
