<aside class="left-sidebar hidden md:flex md:flex-col w-64 bg-white shadow-md fixed top-0 left-0 h-screen z-40">
    <div class="site-logo text-2xl font-bold text-blue-500 p-4 border-b border-gray-200">Subscru</div>
    <nav class="global-nav flex-grow flex flex-col p-4">
        <ul class="flex flex-col space-y-4 flex-grow">
            <li><a href="#" class="text-gray-700 hover:text-blue-500 font-medium flex items-center"><i
                        class="fas fa-home mr-2"></i>ホーム</a></li>
            <li class="mt-auto"><a href="#" class="text-gray-700 hover:text-blue-500 font-medium flex items-center"><i
                        class="fas fa-cog mr-2"></i>設定</a></li>
            {{-- ログアウトをボタンに変更 --}}
            <li>
                {{-- ボタンとして実装し、クリックで隠しフォームを送信 --}}
                <button type="button"
                        class="text-gray-700 hover:text-blue-500 font-medium flex items-center focus:outline-none"
                        @click="document.getElementById('logout-form').submit()">
                    <i class="fas fa-sign-out-alt mr-2"></i>ログアウト
                </button>
            </li>
        </ul>
    </nav>
</aside>
