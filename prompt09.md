# 【新しいチャットへの引き継ぎ】Subscru アプリケーション フロントエンド開発 (テストの安定化と管理者機能検討)
この指示書は、Subscru アプリケーションのフロントエンド開発を、以下の状態から引き継ぐためのものです。

## 実装が完了した主な機能:

- Laravel Fortify および Sanctum を使用した認証機能（ログイン、登録、ログアウト）の基本的なセットアップと連携。
- 認証保護されたバックエンドAPI（サービス一覧表示、登録、更新、削除、認証ユーザー情報・iCalフィードURL取得）の実装とフロントエンド（Alpine.js）からの連携。
- iCalフィード生成機能（通知タイミングを考慮したイベント日付設定）およびURL配信、表示、コピー機能。
- サービス一覧のページネーション機能（バックエンド・フロントエンド連携、URL反映）。
- サービス一覧のバックエンドソート機能（バックエンド・フロントエンド連携、URL反映、ソートアイコン表示）。
- サービス名での検索機能（バックエンド・フロントエンド連携、URL反映）。
- JavaScript ファイルの機能単位での分割と整理（API連携、フォーム、日付、ソート、通知、モーダル、iCal関連ロジックを個別のファイルに分離）。
- UI/UX の改善点（期日接近時の強調表示、トースト通知、モーダル表示、ヘッダー、サイドバー、FABボタン、PC版通知対象日縦並び、PC版ヘッダーロゴ左寄せ、スマホ版ヘッダーロゴ中央寄せ）。
- サービス削除後のリスト更新問題の解決。
- Webフォント（Adobe Fonts）の導入とCSSでの適用。
- サービス一覧の1ページあたりの表示件数のデフォルト値を10件に変更。
- 検索フィールドにキーワードクリア用の「×」ボタンを追加し、機能実装。
- 開発環境 (APP_ENV=development/local) でのみコンソールログが表示されるユーティリティ関数を導入・適用。

## 現在の懸念点:

-
iCalフィードが様々なカレンダーアプリで期待通りに（特に通知設定が）動作するかについて、主要ターゲットアプリ（Google、Apple、Microsoft）を含めた実機での動作確認が未実施。現在の実装はイベント日を通知タイミングで調整する方式であり、通知の最終的な挙動はアプリ側のデフォルト設定に依存する部分が大きい。CalDAVのような読み書き可能なカレンダー連携はMVP範囲外と判断。
- php artisan test を実行するとテストが失敗する状態。

## プロジェクトの技術スタック:

- フレームワーク: Laravel 12.x
- CSS フレームワーク: Tailwind CSS v4 (Vite ビルドプロセス使用)
- JavaScript ライブラリ: Alpine.js v3.x (@alpinejs/intersect プラグイン使用)
- アイコンライブラリ: Font Awesome (フリー版)
- ビルドツール: Vite (Tailwind/Alpine が設定済み)

## 主なプロジェクトファイル:

これまでの開発で修正・新規作成されたファイルは、GitHubリポジトリ https://github.com/yng13/subscru.jp.git
に含まれるものとします。特に以下のファイルが主な作業対象となりました。

- resources/views/index.blade.php
- resources/views/auth/login.blade.php
- resources/views/auth/register.blade.php
- resources/views/components/* (ヘッダー, ドロワー, サイドバー, iCal設定, サービス一覧, FABボタン, 各種モーダル,
  トースト通知)
- resources/css/app.css
- resources/js/app.js
- resources/js/api/serviceApi.js
- resources/js/forms/serviceForms.js
- resources/js/utils/datetime.js
- resources/js/utils/sorting.js
- resources/js/utils/notification.js
- resources/js/utils/modal.js
- resources/js/utils/ical.js
- resources/js/utils/debug.js
- app/Http/Controllers/ServiceController.php
- app/Http/Controllers/IcalFeedController.php
- app/Models/User.php
- app/Models/Service.php
- database/factories/ServiceFactory.php
- database/seeders/ServiceSeeder.php
- app/Providers/FortifyServiceProvider.php
- routes/web.php
- routes/api.php
- config/fortify.php
- config/sanctum.php
- .env
- database/migrations/*
- tests/Feature/ExampleTest.php (テストファイル)
- tests/Unit/ExampleTest.php (テストファイル)

## 次に解決したい課題（優先度順）:

1. テストの失敗原因特定と修正: php artisan test を実行した際に発生しているテストの失敗原因を特定し、テストがパスするように修正します。

2. 管理者機能の実装検討: テストが安定した後に、以下の管理者機能について設計および実装の検討を開始します。
    - 管理者用の画面の場所とアクセス制御。
    - 管理画面で最初に実装する機能（全ユーザー、全サービスの一覧表示など）。
    - （将来的な検討）サービスのプラン・テンプレート機能、正規化ツール実行操作機能。

## 次チャットでの協力体制:

この新しいチャットでは、まずテストの失敗原因の特定と修正に集中的に取り組み、テストがパスする状態を目指します。その後、ご希望に応じて管理者機能の実装検討に進みます。

## 今後の開発方針:

機能実装と並行してテストを記述・実行し、コード品質とアプリケーションの安定性の維持に努めます。
コードの作成、修正、理解のサポート、教育、明確な指示、詳細なドキュメント提供を引き続き行います。
