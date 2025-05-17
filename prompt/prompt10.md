# 【新しいチャットへの引き継ぎ】Subscru アプリケーション フロントエンド開発 (管理者機能実装 - Gate/Policy 入門)

この指示書は、Subscru アプリケーションのフロントエンド開発（認証、API連携、iCal、ページネーション、ソート、検索、UI/UX改善、PHP/JavaScript
テスト環境構築と安定化を含む）が完了し、次に管理者機能の実装に着手するためのものです。

## プロジェクトの技術スタック:

- フレームワーク: Laravel 12.x
- CSS フレームワーク: Tailwind CSS v4 (Vite ビルドプロセス使用)
- JavaScript ライブラリ: Alpine.js v3.x, Vitest, jsdom
- アイコンライブラリ: Font Awesome (フリー版)
- ビルドツール: Vite
- テストツール: PHPUnit (Feature/Unit), Vitest (Unit)
- CI: GitHub Actions

## 現在のプロジェクトの状態:

- 主要なユーザー向け機能（サービスの CRUD、一覧表示、ページネーション、ソート、検索、iCal フィード）が実装済み。
- 認証機能（ログイン、登録、ログアウト）が Laravel Fortify/Sanctum を使用して実装済み。
- API 連携が実装され、認証保護されている。
- UI/UX が概ね実装され、レスポンシブ対応している。
- PHPUnit および Vitest によるテスト環境が構築され、GitHub Actions 上での自動テストもすべてパスしている。
- GitHub Actions で Vite ビルドと JavaScript テスト（カバレッジ収集含む）が正常に実行されている。
- プロジェクトのソースコードは https://github.com/yng13/subscru.jp.git のリポジトリで共有しています。

## 次に解決したい課題（優先度順）:

- 管理者機能の設計と実装開始:
    - 管理者専用の画面を /admin という URL で設ける。
    - User モデルに is_admin カラムを追加し、管理者を識別する仕組みを導入する。
    - 管理者専用画面へのアクセス制御を実装する（Laravel の Gate/Policy を活用）。

## 次チャットでの協力体制:

この新しいチャットでは、上記「次に解決したい課題」にある管理者機能の実装に集中的に取り組みます。特に、Laravel の Gate と
Policy を使用したアクセス制御について、その概念から具体的な実装方法まで詳しく解説し、あなたの知見を深めるサポートを行います。

コードの作成、修正、理解のサポート、教育、明確な指示、詳細なドキュメント提供を引き続き行います。
