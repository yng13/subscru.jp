@extends('static')
@section('title')
    プライバシーポリシー
@endsection
@section('content')
    <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mb-8">プライバシーポリシー</h1>

    <section class="mb-12"> {{-- セクション間の余白を調整 --}}
        <h2 class="text-xl md:text-2xl font-semibold text-gray-800 mb-4">はじめに</h2>
        <p class="text-gray-700 leading-relaxed mb-4">
            このプライバシーポリシーは、Subscru（以下「本サービス」といいます）における、ユーザーの個人情報を含む利用者情報の取扱いについて定めるものです。
        </p>
        <p class="text-gray-700 leading-relaxed">
            本サービスをご利用になる前に、本プライバシーポリシーをよくお読みください。
        </p>
    </section>

    <section class="mb-12"> {{-- セクション間の余白を調整 --}}
        <h2 class="text-xl md:text-2xl font-semibold text-gray-800 mb-6">
            取得する情報と取得方法</h2> {{-- 見出し下の余白を調整 --}}
        <div class="mb-8"> {{-- 項目間の余白 --}}
            <h3 class="text-lg font-semibold text-gray-800 mb-3">1. お客様にご提供いただく情報</h3> {{-- 見出し下の余白を調整 --}}
            <p class="text-gray-700 leading-relaxed mb-4">
                本サービスの利用にあたり、お客様ご自身に直接ご提供いただく情報です。
            </p>
            <ul class="list-disc list-inside text-gray-700 leading-relaxed space-y-2"> {{-- リストアイテム間の余白 --}}
                <li>氏名またはニックネーム: サービスの利用時、表示名として使用します。</li>
                <li>メールアドレス: サービスへのログイン、お知らせ、お問い合わせ時の連絡などに使用します。</li>
                <li>パスワード:
                    サービスへのログイン時に使用します。パスワード自体は暗号化して保存され、サービス運営者であっても内容を知ることはできません。
                </li>
                <li>
                    サービス登録情報: お客様が本サービスに登録されるサービスに関する情報です。
                    <ul class="list-circle list-inside ml-4 text-gray-600 text-sm space-y-1"> {{-- ネストしたリストとスタイル --}}
                        <li>サービス名</li>
                        <li>種別（契約中／トライアル中）</li>
                        <li>通知対象日</li>
                        <li>通知タイミング（通知対象日の〇日前に通知するか）</li>
                        <li>メモ</li>
                        <li>カテゴリ<span class="italic">(任意)</span></li>
                    </ul>
                </li>
                <li>お問い合わせ内容:
                    お客様が本サービスに関するお問い合わせフォームやメールなどを利用された際に、ご提供いただく情報です。
                </li>
            </ul>
        </div>
        <div> {{-- 項目間の余白 --}}
            <h3 class="text-lg font-semibold text-gray-800 mb-3">2.
                お客様が本サービスをご利用にあたって自動的に取得する情報</h3> {{-- 見出し下の余白を調整 --}}
            <p class="text-gray-700 leading-relaxed mb-4">
                お客様が本サービスにアクセスされたり、ご利用されたりする際に、自動的に取得する情報です。
            </p>
            <ul class="list-disc list-inside text-gray-700 leading-relaxed space-y-2"> {{-- リストアイテム間の余白 --}}
                <li>端末情報:
                    ご利用の端末の種類、OSのバージョン、端末識別情報などを取得する場合があります。これにより、お客様の利用環境に合わせたサービス提供や、不正アクセスの防止に役立てます。
                </li>
                <li>ログ情報:
                    お客様が本サービスにアクセスした際のIPアドレス、アクセス日時、ページの閲覧履歴、ブラウザの種類、OSの種類などを自動的に記録します。これらはサービスの維持・改善、統計分析などに利用します。
                </li>
                <li>Cookie (クッキー):
                    本サービスでは、Cookieを使用する場合があります。Cookieは、お客様のブラウザに情報を一時的に保存する仕組みで、お客様が本サービスを再度訪問された際に、ログイン状態を維持したり、設定を記憶させたりするために使用します。Cookieを無効にすることも可能ですが、その場合、サービスの一部が正常に機能しないことがあります。
                </li>
                <li>iCalフィードアクセス情報:
                    お客様がカレンダー連携機能を利用する際、iCalフィードへのアクセス元（IPアドレス、ユーザーエージェントなど）に関する情報を記録する場合があります。これは、フィードの利用状況把握や不正利用の監視に利用します。
                </li>
            </ul>
        </div>
    </section>

    <section class="mb-12"> {{-- セクション間の余白を調整 --}}
        <h2 class="text-xl md:text-2xl font-semibold text-gray-800 mb-6">情報の利用目的</h2> {{-- 見出し下の余白を調整 --}}
        <p class="text-gray-700 leading-relaxed mb-4">
            取得したお客様の情報は、以下の目的のために利用いたします。
        </p>
        <ul class="list-disc list-inside text-gray-700 leading-relaxed space-y-2"> {{-- リストアイテム間の余白 --}}
            <li>
                本サービスの提供、運営、維持のため（サービス一覧の表示、通知対象日の管理、iCalフィードの生成・提供などを含みます）
            </li>
            <li>お客様が利用されるサービスに関する通知を提供するため</li>
            <li>お客様からのお問い合わせに対応するため</li>
            <li>本サービスの改善、新サービスの開発、およびサービス内容のカスタマイズのため</li>
            <li>本サービスに関する重要なお知らせや情報を提供するため</li>
            <li>利用規約に違反する行為を検出、防止、および対応するため</li>
            <li>本サービスの利用状況に関する統計データを作成し、分析するため（個人が特定できない形で利用します）</li>
            <li>セキュリティの確保、不正アクセスの防止のため</li>
            <li>その他、上記利用目的に付随する目的のため</li>
        </ul>
    </section>

    <section class="mb-12"> {{-- セクション間の余白を調整 --}}
        <h2 class="text-xl md:text-2xl font-semibold text-gray-800 mb-6">情報の安全管理</h2> {{-- 見出し下の余白を調整 --}}
        <p class="text-gray-700 leading-relaxed">
            本サービスは、取得したお客様の情報の漏洩、滅失、毀損などを防止するため、必要な安全管理措置を講じます。技術的および組織的な対策を実施し、お客様の情報を適切に管理します。
        </p>
    </section>

    <section class="mb-12"> {{-- セクション間の余白を調整 --}}
        <h2 class="text-xl md:text-2xl font-semibold text-gray-800 mb-6">第三者への提供</h2> {{-- 見出し下の余白を調整 --}}
        <p class="text-gray-700 leading-relaxed mb-4">
            本サービスは、以下の場合を除き、お客様の同意を得ずに個人情報を第三者（本サービスおよびその業務委託先を除く）に提供することはありません。
        </p>
        <ul class="list-disc list-inside text-gray-700 leading-relaxed space-y-2"> {{-- リストアイテム間の余白 --}}
            <li>法令に基づく場合</li>
            <li>人の生命、身体または財産の保護のために必要がある場合であって、本人の同意を得ることが困難である場合</li>
            <li>
                公衆衛生の向上または児童の健全な育成の推進のために特に必要がある場合であって、本人の同意を得ることが困難である場合
            </li>
            <li>
                国の機関もしくは地方公共団体またはその委託を受けた者が法令の定める事務を遂行することに対して協力する必要がある場合であって、本人の同意を得ることにより当該事務の遂行に支障を及ぼすおそれがある場合
            </li>
            <li>合併、会社分割、事業譲渡その他の事由により事業の承継が行われる場合</li>
        </ul>
    </section>

    <section class="mb-12"> {{-- セクション間の余白を調整 --}}
        <h2 class="text-xl md:text-2xl font-semibold text-gray-800 mb-6">業務委託</h2> {{-- 見出し下の余白を調整 --}}
        <p class="text-gray-700 leading-relaxed">
            本サービスは、利用目的の達成に必要な範囲において、個人情報の取り扱いに関する業務の全部または一部を外部の事業者に委託する場合があります。その場合、委託先に対して個人情報の安全管理が図られるよう、必要かつ適切な監督を行います。
        </p>
    </section>

    <section class="mb-12"> {{-- セクション間の余白を調整 --}}
        <h2 class="text-xl md:text-2xl font-semibold text-gray-800 mb-6">統計データの利用</h2> {{-- 見出し下の余白を調整 --}}
        <p class="text-gray-700 leading-relaxed">
            本サービスでは、お客様の利用状況や提供いただいた情報を基に、個人が特定できない形での統計データを作成することがあります。これらの統計データは、本サービスの改善やマーケティングのために利用・公開することがあります。
        </p>
    </section>

    <section class="mb-12"> {{-- セクション間の余白を調整 --}}
        <h2 class="text-xl md:text-2xl font-semibold text-gray-800 mb-6">
            個人情報の開示、訂正、利用停止など</h2> {{-- 見出し下の余白を調整 --}}
        <p class="text-gray-700 leading-relaxed">
            お客様ご自身の個人情報について、開示、訂正、追加、削除、利用停止、消去などのご希望がある場合は、本人確認を行った上で、法令に基づき適切に対応いたします。ご希望の際は、[お問い合わせ窓口]にご連絡ください。
        </p>
    </section>

    <section class="mb-12"> {{-- セクション間の余白を調整 --}}
        <h2 class="text-xl md:text-2xl font-semibold text-gray-800 mb-6">本ポリシーの変更</h2> {{-- 見出し下の余白を調整 --}}
        <p class="text-gray-700 leading-relaxed">
            本サービスは、必要に応じて、または法令の改正に伴い、本ポリシーの内容を変更することがあります。変更後の本ポリシーは、本Webサイトに掲載された時点から効力を生じるものとします。お客様は、本サービスをご利用になる前に、常に最新の本ポリシーをご確認ください。
        </p>
    </section>

    <section class="mb-12"> {{-- セクション間の余白を調整 --}}
        <h2 class="text-xl md:text-2xl font-semibold text-gray-800 mb-6">お問い合わせ窓口</h2> {{-- 見出し下の余白を調整 --}}
        <p class="text-gray-700 leading-relaxed">
            本ポリシーに関するご意見、ご質問、苦情のお申し出、その他個人情報の取り扱いに関するお問い合わせは、以下のお問い合わせフォームよりご連絡ください。
        </p>
        <p class="text-center mt-6"> {{-- リンクを中央寄せ --}}
            <a href="{{ route('contact') }}"
               class="inline-block bg-blue-500 text-white font-bold py-2 px-4 rounded-md hover:bg-blue-600 transition-colors">お問い合わせフォームへ</a> {{-- CTAボタン風に --}}
        </p>
    </section>

    <section>
        <h2 class="text-xl md:text-2xl font-semibold text-gray-800 mb-6">制定日・改定日</h2> {{-- 見出し下の余白を調整 --}}
        <p class="text-gray-700 leading-relaxed">
            制定日：[YYYY年MM月DD日]<br>
            改定日：[YYYY年MM月DD日] （改定がある場合のみ記載）
        </p>
    </section>
@endsection
