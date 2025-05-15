<div class="drawer-overlay fixed inset-0 bg-black bg-opacity-50 z-40"
     x-show="isDrawerOpen"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-300"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     @click="isDrawerOpen = false"
     style="display: none;"
></div>
<nav class="drawer-nav bg-white shadow-lg flex flex-col"
     x-bind:class="{ 'open': isDrawerOpen }"
>
    <div class="drawer-header flex justify-between items-center p-4 border-b border-gray-200">
        <div class="text-xl font-bold text-blue-500">Subscru</div>
        <button class="drawer-close text-gray-700 text-2xl focus:outline-none" @click="isDrawerOpen = false">
            <i class="fas fa-times"></i>
        </button>
    </div>
    <ul class="flex flex-col p-4 flex-grow">
        <li class="mb-4"><a href="#" class="text-gray-700 hover:text-blue-500 font-medium flex items-center"><i
                    class="fas fa-home mr-2"></i>ホーム</a></li>
        <li class="mt-auto mb-4"><a href="#" class="text-gray-700 hover:text-blue-500 font-medium flex items-center"><i
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
