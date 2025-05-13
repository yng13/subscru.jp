<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Fortify Guard
    |--------------------------------------------------------------------------
    |
    | Here you may specify which authentication guard Fortify will use while
    | authenticating users. This value should correspond with one of your
    | guards that is already present in your "auth" configuration file.
    |
    */

    'guard' => 'web',

    /*
    |--------------------------------------------------------------------------
    | Fortify Password Broker
    |--------------------------------------------------------------------------
    |
    | Here you may specify which password broker Fortify can use when a user
    | is resetting their password. This configured value should match one
    | of your password brokers setup in your "auth" configuration file.
    |
    */

    'passwords' => 'users',

    /*
    |--------------------------------------------------------------------------
    | Username / Email
    |--------------------------------------------------------------------------
    |
    | This value defines which model attribute should be considered as your
    | application's "username" field. Typically, this might be the email
    | address of the users, but you are free to change this value here.
    |
    | Out of the box, Fortify expects forgot password and reset password
    | requests to have a field named 'email'. If the application uses
    | another name for the field you may define it below as needed.
    |
    */

    'username' => 'email',

    'email' => 'email',

    /*
    |--------------------------------------------------------------------------
    | Lowercase Usernames
    |--------------------------------------------------------------------------
    |
    | This value defines whether usernames should be lowercased before saving
    | them in the database, as some database system string fields are case
    | sensitive. You may disable this for your application if necessary.
    |
    */

    'lowercase_usernames' => true,

    /*
    |--------------------------------------------------------------------------
    | Home Path
    |--------------------------------------------------------------------------
    |
    | Here you may configure the path where users will get redirected during
    | authentication or password reset when the operations are successful
    | and the user is authenticated. You are free to change this value.
    |
    */

    'home' => '/home',

    /*
    |--------------------------------------------------------------------------
    | Fortify Routes Prefix / Subdomain
    |--------------------------------------------------------------------------
    |
    | Here you may specify which prefix Fortify will assign to all the routes
    | that it registers with the application. If necessary, you may change
    | subdomain under which all the Fortify routes will be available.
    |
    */

    'prefix' => '',

    'domain' => null,

    /*
    |--------------------------------------------------------------------------
    | Fortify Routes Middleware
    |--------------------------------------------------------------------------
    |
    | Here you may specify which middleware Fortify will assign to the routes
    | that it registers with the application. If necessary, you may change
    | these middleware but typically this provided default is preferred.
    |
    */

    'middleware' => ['web'],

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting
    |--------------------------------------------------------------------------
    |
    | By default, Fortify will throttle logins to five requests per minute for
    | every email and IP address combination. However, if you would like to
    | specify a custom rate limiter to call then you may specify it here.
    |
    */

    'limiters' => [
        'login' => 'login',
        'two-factor' => 'two-factor',
    ],

    /*
    |--------------------------------------------------------------------------
    | Register View Routes
    |--------------------------------------------------------------------------
    |
    | Here you may specify if the routes returning views should be disabled as
    | you may not need them when building your own application. This may be
    | especially true if you're writing a custom single-page application.
    |
    */
    'views' => [
        'login' => 'auth.login', // カスタムログインビュー
        'register' => 'auth.register', // カスタム登録ビュー
        'verify-email' => 'auth.verify-email',
        'two-factor-challenge' => 'auth.two-factor-challenge',
        'confirm-password' => 'auth.confirm-password',
        'request-password-reset' => 'auth.request-password-reset',
        'reset-password' => 'auth.reset-password',
    ],

    'redirects' => [
        'login' => '/login',
        'logout' => '/login', // ログアウト後のリダイレクト先
        'register' => '/login', // 登録後のリダイレクト先 (適宜変更)
        //'password-reset' => '/login',
        //'password-confirm' => '/dashboard',
        //'email-verification' => '/dashboard',
        //'two-factor-challenge' => '/two-factor-challenge',
    ],

    /*
    |--------------------------------------------------------------------------
    | Features
    |--------------------------------------------------------------------------
    |
    | Some of the Fortify features are optional. You may disable the features
    | by removing them from this array. You're free to only remove some of
    | these features, or you can even remove all of these if you need to.
    |
    */

    'features' => [
        // ここには Features::CONSTANT の形式ではなく、機能を示す文字列を記述します。

        // 'registration' を有効にすると、/register ルートと登録処理が有効になります。
        'registration',

        // 'reset-passwords' を有効にすると、パスワードリセット関連のルートが有効になります。
        // 'reset-passwords',

        // 'verify-emails' を有効にすると、メール確認関連のルートが有効になります。
        // 'verify-emails',

        // 'update-profile-information' を有効にすると、プロフィール更新処理が有効になります。
        // 'update-profile-information',

        // 'update-passwords' を有効にすると、パスワード更新処理が有効になります。
        // 'update-passwords',

        // 'two-factor-authentication' を有効にすると、2要素認証機能が有効になります。
        // 'two-factor-authentication',

        // 'login' を有効にすると、/login ルートとログイン処理が有効になります。
        'login', // Blade アプリケーションでのセッションログインに必要です。

        // 'logout' を有効にすると、/logout ルートとログアウト処理が有効になります。
        'logout', // Blade アプリケーションでのセッションログアウトに必要です。

        // 'redirect-to-dashboard' を有効にすると、認証後のリダイレクトが /dashboard になります。
        //'redirect-to-dashboard', // 必要に応じてコメント解除

        // 'views' を有効にすると、Fortify のデフォルトビューが使用されます。
        // 今回はカスタム Blade ビューを作成するため、この行はコメントアウトしたままか、削除します。
        // 'views',
    ],

];
