<section class="bg-white p-6 rounded-lg shadow mb-8">
    <h2 class="text-xl font-semibold text-gray-900 mb-4">カレンダー連携設定</h2>
    {{-- Call copyIcalUrl method --}}
    <div class="mb-4">
        <label class="font-medium block mb-2 text-gray-900">あなたのiCalフィードURL: </label>
        <div
            class="ical-url-display flex flex-wrap items-center bg-gray-100 border border-gray-300 rounded-md p-4 break-all">
            {{-- スタティックなURLを、動的に生成されるユーザーごとのURLに変更 --}}
            {{-- ユーザーがログインしている場合のみURLを表示 --}}
            @auth
                {{-- userIcalUrl は Alpine.js の data プロパティから取得 --}}
                {{-- ここをspanからaタグに変更し、hrefをバインド --}}
                <a id="ical-url" class="flex-grow mr-4 text-blue-600 hover:underline"
                   x-bind:href="userIcalUrl"
                   x-text="userIcalUrl">webcal://subscru.example.com/feed/abcdef1234567890</a>
            @else
                {{-- 未ログインの場合はメッセージを表示 --}}
                <span class="flex-grow mr-4 text-gray-500">ログインすると表示されます。</span>
            @endauth
            <button
                class="copy-button bg-gray-300 text-gray-700 font-bold py-2 px-4 rounded hover:bg-gray-400 focus:outline-none whitespace-nowrap mt-2 md:mt-0"
                {{-- ログイン済みの場合のみボタンを有効化 --}}
                @auth
                    @click="copyIcalUrl()"
                @else
                    disabled {{-- 未ログインの場合は無効化 --}}
                @endauth
            >
                <i class="fas fa-copy mr-2"></i>URLをコピー
            </button>
        </div>
    </div>
    {{-- Call openModal method --}}
    <button
        class="guide-button text-blue-500 border border-blue-500 font-bold py-2 px-4 rounded hover:bg-blue-500 hover:text-white focus:outline-none mt-2"
        @click="openModal('#guide-modal')">設定ガイド
    </button>
</section>
