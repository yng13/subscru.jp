# 【新しいチャットへの引き継ぎ】Subscru アプリケーション フロントエンド開発 (認証機能の実装)

この指示書は、Subscru アプリケーションのフロントエンド開発を、バックエンドAPI連携（サービス一覧表示、登録、更新、削除）の基礎実装が完了した状態から引き継ぐためのものです。これまでのチャットで実施した開発内容と現状を以下にまとめます。

## プロジェクトの技術スタック:

- フレームワーク: Laravel 10.x 以降 (Blade テンプレート)
- CSS フレームワーク: Tailwind CSS v4 (Vite ビルドプロセス使用)
- JavaScript ライブラリ: Alpine.js v3.x
- アイコンライブラリ: Font Awesome (フリー版)
- ビルドツール: Vite (Tailwind/Alpine が設定済み)

## プロジェクトファイル:

- resources/views/index.blade.php (メインの Blade テンプレート)
- resources/css/app.css (Tailwind CSS とカスタムスタイルのエントリーポイント)
- resources/js/app.js (Alpine.js の初期化とデータ・メソッド定義)
- routes/api.php (バックエンドAPIルート定義)
- app/Http/Controllers/ServiceController.php (サービス関連バックエンドコントローラー)
- database/migrations/YYYY_MM_DD_create_services_table.php (services テーブル マイグレーション)
- database/seeders/ServiceSeeder.php (services テーブル用Seeder)
- app/Models/Service.php (Service Eloquent モデル - $fillable, $casts 設定済み)
- tailwind.config.js (Tailwind CSS 設定ファイル - 存在するものとして扱う)
- vite.config.js (Vite 設定ファイル - Tailwind/Alpine が設定されているものとして扱う)

## これまでに実装・確認が完了した主な機能:

- シンプルでモダンなデザインスタイル（指定配色基調）。
- PC版・スマホ版のレスポンシブレイアウト（ヘッダー、サイドバー/ドロワー）。
- 各種UI要素の配置とスタイル（カレンダー連携セクション、サービス一覧、FABボタンなど）。
- iCal URL コピー機能（トースト通知付き）。
- サービス一覧のソート機能（サービス名、通知対象日）。
- 期日接近時の視覚強調。
- サービス件数表示およびサービスが空の場合のメッセージ表示。
- サービス登録、詳細/編集、設定ガイド、削除確認のモーダルダイアログ（共通オーバーレイ、スクロール対応）。
- 削除確認ダイアログの安定表示（@click.stop および Alpine.js 状態管理による修正）。
- サービス登録・編集モーダルにおける必須項目のクライアント側リアルタイムバリデーション（エラー表示付き）。
- ログアウト機能（見た目を維持しつつPOST送信）。
- バックエンドAPI連携の基礎実装完了:
    - `/api/services` (GET): サービス一覧取得
    - `/api/services` (POST): 新規サービス登録
    - `/api/services/{id}` (PUT): サービス編集・更新
    - `/api/services/{id}` (DELETE): サービス削除
- Service モデルに $fillable プロパティを設定し、Mass Assignment エラーを解消。
- 編集モーダルで notification_date が正しく表示されるよう、フロントエンドでの日付フォーマット処理を修正。

## 現在の課題および次のステップ:

-   **認証機能の未実装:** 現在、サービス関連のAPIルート（`/api/services` および `/api/services/{id}`）は認証ミドルウェアの保護下にありません。これにより、未認証ユーザーでもデータにアクセス・操作できる状態です。
-   **次のステップ:** 認証機能（ログイン、ログアウト、APIリクエスト認証）を実装し、サービス関連APIを認証保護下に置きます。

## 次チャットでの協力体制:

次回のチャットでは、上記「現在の課題および次のステップ」にある認証機能の実装に集中的に取り組みます。具体的には、Laravel Sanctum などを使用した API 認証の実装と、フロントエンド（Alpine.js）からのログイン処理、API リクエストへの認証トークン付加、ログアウト処理の実装をサポートします。

コードの作成、修正、理解のサポート、教育、明確な指示、詳細なドキュメント提供を引き続き行います。
