{{-- resources/views/index.blade.php --}}
    <!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subscru - サービス一覧</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    {{-- Vite CSS Asset --}}
    @vite(['resources/css/app.css'])
</head>
{{-- x-data now calls the function registered with Alpine.data in app.js --}}
<body class="bg-gray-100 text-gray-700 font-sans leading-relaxed" x-data="serviceListPage()">

{{-- PCブレークポイント (md) 以上でflex表示、それ未満で非表示 --}}
<header class="fixed top-0 left-0 w-full bg-white shadow-md z-50 hidden md:flex">
    <div class="container mx-auto px-4 py-3 flex items-center justify-between">
        <div class="site-logo text-xl md:text-2xl font-bold text-blue-500">Subscru</div>
        <div class="user-info font-medium text-gray-700">
            〇〇さん
        </div>
    </div>
</header>

{{-- PCブレークポイント (md) 未満で表示、それ以上で非表示 --}}
<header class="fixed top-0 left-0 w-full bg-white shadow-md z-50 md:hidden">
    {{-- Flexbox を使ってアイテムを配置 --}}
    <div class="container mx-auto px-4 py-3 flex items-center justify-between">
        <div class="flex-1 flex items-center">
            {{-- @click でドロワーを開く --}}
            <button class="drawer-toggle block text-gray-700 text-2xl focus:outline-none" @click="isDrawerOpen = true"
                    aria-label="メニューを開く">
                <i class="fas fa-bars"></i>
            </button>
        </div>

        <div class="site-logo text-xl font-bold text-blue-500 flex-grow text-center">Subscru</div>

        <div class="flex-1 flex justify-end items-center">
            <div class="user-info font-medium text-gray-700">
                〇〇さん
            </div>
        </div>
    </div>
</header>


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
        {{-- !!!今回の修正点!!! ログアウトをボタンに変更 --}}
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

<aside class="left-sidebar hidden md:flex md:flex-col w-64 bg-white shadow-md fixed top-0 left-0 h-screen z-40">
    <div class="site-logo text-2xl font-bold text-blue-500 p-4 border-b border-gray-200">Subscru</div>
    <nav class="global-nav flex-grow flex flex-col p-4">
        <ul class="flex flex-col space-y-4 flex-grow">
            <li><a href="#" class="text-gray-700 hover:text-blue-500 font-medium flex items-center"><i
                        class="fas fa-home mr-2"></i>ホーム</a></li>
            <li class="mt-auto"><a href="#" class="text-gray-700 hover:text-blue-500 font-medium flex items-center"><i
                        class="fas fa-cog mr-2"></i>設定</a></li>
            {{-- !!!今回の修正点!!! ログアウトをボタンに変更 --}}
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


<main class="main-content py-8 md:py-10 md:ml-64 md:mt-16">
    <div class="container mx-auto px-4">

        <section class="bg-white p-6 rounded-lg shadow mb-8">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">カレンダー連携設定</h2>
            {{-- Call copyIcalUrl method --}}
            <div class="mb-4">
                <label class="font-medium block mb-2 text-gray-900">あなたのiCalフィードURL: </label>
                <div
                    class="ical-url-display flex flex-wrap items-center bg-gray-100 border border-gray-300 rounded-md p-4 break-all">
                    <span id="ical-url" class="flex-grow mr-4">webcal://subscru.example.com/feed/abcdef1234567890</span>
                    <button
                        class="copy-button bg-gray-300 text-gray-700 font-bold py-2 px-4 rounded hover:bg-gray-400 focus:outline-none whitespace-nowrap mt-2 md:mt-0"
                        @click="copyIcalUrl()">
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

        <section class="bg-white p-6 rounded-lg shadow mb-8">
            <h1 class="text-2xl font-semibold text-gray-900 mb-6">サービス一覧</h1>

            {{-- Debug: Display service count --}}
            <p x-text="'Services count: ' + services.length" class="mb-4 text-sm text-gray-600"></p>

            <div
                class="service-list-header hidden md:flex bg-gray-100 text-xs font-medium text-gray-500 uppercase tracking-wider border-b border-gray-200">
                {{-- Sortable header cells - Added py-3 and text-sm for icon --}}
                <div class="cursor-pointer hover:bg-gray-200 flex items-center py-3" @click="sortServices('name')"
                     :class="{ 'sorted-asc': sortBy === 'name' && sortDirection === 'asc', 'sorted-desc': sortBy === 'name' && sortDirection === 'desc' }">
                    サービス名 <i class="fas fa-sort ml-2 text-gray-400 text-sm" x-show="sortBy !== 'name'"></i>
                    <i class="fas fa-sort-up ml-2 text-gray-600 text-sm"
                       x-show="sortBy === 'name' && sortDirection === 'asc'"></i>
                    <i class="fas fa-sort-down ml-2 text-gray-600 text-sm"
                       x-show="sortBy === 'name' && sortDirection === 'desc'"></i>
                </div>
                {{-- Non-sortable cells - Added py-3 for consistent height --}}
                <div class="py-3">種別</div>
                {{-- Sortable header cells - Added py-3 and text-sm for icon --}}
                <div class="cursor-pointer hover:bg-gray-200 flex items-center py-3"
                     @click="sortServices('notificationDate')"
                     :class="{ 'sorted-asc': sortBy === 'notificationDate' && sortDirection === 'asc', 'sorted-desc': sortBy === 'notificationDate' && sortDirection === 'desc' }">
                    通知対象日 <i class="fas fa-sort ml-2 text-gray-400 text-sm"
                                  x-show="sortBy !== 'notificationDate'"></i>
                    <i class="fas fa-sort-up ml-2 text-gray-600 text-sm"
                       x-show="sortBy === 'notificationDate' && sortDirection === 'asc'"></i>
                    <i class="fas fa-sort-down ml-2 text-gray-600 text-sm"
                       x-show="sortBy === 'notificationDate' && sortDirection === 'desc'"></i>
                </div>
                {{-- Non-sortable cells - Added py-3 for consistent height --}}
                <div class="py-3">メモ</div>
            </div>


            {{-- Loop through services array --}}
            <div class="service-list grid grid-cols-1 gap-4 md:gap-0 md:flex md:flex-col divide-y divide-gray-200">
                <template x-for="service in services" :key="service.id">
                    {{-- Bind class for near deadline --}}
                    {{-- Call openModal method on click, passing service object --}}
                    <div
                        class="service-item bg-white rounded-lg shadow-sm hover:shadow-md p-4 md:p-0 md:rounded-none md:shadow-none md:border-b md:border-gray-200 cursor-pointer flex flex-col md:flex-row items-start md:items-center"
                        :class="{ 'near-deadline': getDaysRemaining(service.notificationDate) <= 30 }"
                        @click="openModal('#edit-modal', service)"
                    >
                        <div class="mb-2 md:mb-0 md:py-4 md:px-6 whitespace-nowrap flex items-center">
                            <i :class="service.categoryIcon" class="mr-2 text-gray-600 md:text-base"></i>
                            <span class="md:hidden font-semibold mr-2">サービス名:</span>
                            <span x-text="service.name"></span>
                        </div>
                        <div class="mb-2 md:mb-0 md:py-4 md:px-6 whitespace-nowrap">
                            <span class="md:hidden font-semibold mr-2">種別:</span>
                            {{-- Bind class for service type color --}}
                            <span class="service-type text-white text-xs font-bold px-2.5 py-0.5 rounded-full"
                                  :class="{ 'bg-blue-500': service.type === 'contract', 'bg-emerald-500': service.type === 'trial' }"
                                  x-text="service.type === 'contract' ? '契約中' : 'トライアル中'"></span>
                        </div>
                        {{-- PC: Stack date and days remaining --}}
                        <div class="mb-2 md:mb-0 md:py-4 md:px-6 notification-text flex flex-col items-start">
                            <span class="md:hidden font-semibold mr-2">通知対象日:</span>
                            <span x-text="formatDate(service.notificationDate)"></span>
                            <span x-text="'(あと ' + getDaysRemaining(service.notificationDate) + ' 日)'"></span>
                        </div>
                        <div class="md:py-4 md:px-6 break-words md:flex-1 w-full">
                            <span class="md:hidden font-semibold mr-2">メモ:</span>
                            <span x-text="service.memo"></span>
                        </div>
                    </div>
                </template>
                {{-- Message when services list is empty --}}
                <div x-show="services.length === 0" class="p-4 text-center text-gray-500">
                    サービスはまだ登録されていません。
                </div>
            </div>

            {{-- Static pagination links for now --}}
            <div class="pagination flex justify-center items-center mt-8">
                <a href="#"
                   class="page-link px-4 py-2 mx-1 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-200">&laquo;
                    前へ</a>
                <a href="#"
                   class="page-link px-4 py-2 mx-1 border border-blue-500 rounded-md bg-blue-500 text-white pointer-events-none">1</a>
                <a href="#"
                   class="page-link px-4 py-2 mx-1 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-200">2</a>
                <a href="#"
                   class="page-link px-4 py-2 mx-1 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-200">3</a>
                <a href="#"
                   class="page-link px-4 py-2 mx-1 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-200">次へ
                    &raquo;</a>
            </div>

        </section>

    </div>
</main>

{{-- Call openModal method --}}
{{-- スマホ版FABボタンの形状とサイズを修正 --}}
{{-- FAB: Floating Action Button. 画面右下に固定される主要アクションボタンのデザインパターンです。 --}}
{{-- デフォルト (スマホ): w-14 h-14 で正円サイズ (例: 56px), p-0 でパディング解除、Flexbox でアイコンを中央寄せ --}}
{{-- PC (md:): w-auto h-auto で内容に応じたサイズに、md:p-4 でパディングを追加、PCは正円ではなく丸角長方形 --}}
<button id="add-service-fab"
        class="fab fixed bottom-8 right-8 bg-blue-500 text-white shadow-lg hover:bg-blue-600 focus:outline-none z-30
                   flex items-center justify-center
                   rounded-full
                   w-14 h-14 p-0
                   md:w-auto md:h-auto md:p-4
                  "
        @click="openModal('#add-modal')"
        aria-label="新しいサービスを追加"
>
    {{-- アイコンサイズはそのまま text-lg --}}
    <i class="fas fa-plus text-lg"></i>
    {{-- PC版のテキストは md:inline で表示 --}}
    <span class="fab-text ml-2 font-bold hidden md:inline">新しいサービスを追加</span>
</button>


{{-- Show/hide overlay based on modal states --}}
{{-- Close modals on overlay click --}}
{{-- Top alignment and horizontal center --}}
<div class="modal-overlay fixed inset-0 bg-black bg-opacity-60 backdrop-blur-sm z-40 items-start justify-center"
     x-show="showAddModal || showEditModal || showGuideModal || showDeleteConfirmModal" {{-- ここに showDeleteConfirmModal が含まれているか確認 --}}
     @click.self="closeModals()"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-300"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     style="display: none;"
>
    {{-- Show/hide based on state --}}
    {{-- Top margin and horizontal auto margin --}}
    {{-- max-h-screen を max-h-[90vh] に変更 --}}
    <!-- サービス登録モーダル -->
    {{-- Show/hide based on state --}}
    {{-- Top margin and horizontal auto margin --}}
    {{-- max-h-screen を max-h-[90vh] に変更 --}}
    <div id="add-modal" class="modal bg-white rounded-lg shadow-xl w-11/12 md:max-w-md flex flex-col max-h-[90vh] mt-16 mx-auto" x-show="showAddModal" @click.stop>
        <div class="modal-header flex justify-between items-center p-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">新しいサービスを登録</h2>
            {{-- Close modal button --}}
            <button class="modal-close text-gray-500 text-xl hover:text-gray-700 focus:outline-none" @click="closeModals()"><i class="fas fa-times"></i></button>
        </div>
        {{-- overflow-y-auto を追加して内容をスクロール可能に --}}
        <div class="modal-body p-6 flex-grow overflow-y-auto">
            {{-- !!!今回の修正点!!! @submit.prevent を削除。ボタンの @click でフォームを送信する。 --}}
            <form> {{-- Removed @submit.prevent --}}
                <div class="mb-4">
                    <label for="service-name" class="block font-medium text-gray-900 mb-1">サービス名</label>
                    {{-- x-model でデータをバインド --}}
                    {{-- @input と @blur でバリデーションメソッドを呼び出し --}}
                    <input type="text" id="service-name"
                           class="w-full p-2 border rounded-md focus:outline-none focus:border-blue-500 focus:ring focus:ring-blue-200"
                           :class="{ 'border-red-500': addModalForm.errors.name }" {{-- エラーがある場合は赤枠 --}}
                           placeholder="例: Netflix" required
                           x-model="addModalForm.name"
                           @input="validateField('name', $event.target.value, 'add')"
                           @blur="validateField('name', $event.target.value, 'add')"
                    >
                    {{-- エラーメッセージを表示 --}}
                    <p class="text-red-500 text-sm mt-1" x-text="addModalForm.errors.name" x-show="addModalForm.errors.name"></p>
                </div>
                <div class="mb-4">
                    <label class="block font-medium text-gray-900 mb-1">種別</label>
                    <div @change="validateField('type', addModalForm.type, 'add')"> {{-- 親要素で @change をリッスン --}}
                        {{-- x-model で addModalForm.type にバインド --}}
                        <input type="radio" id="type-trial" name="service-type" value="trial" class="mr-1" x-model="addModalForm.type" required>
                        <label for="type-trial" class="mr-4 text-gray-700">トライアル中</label>
                        <input type="radio" id="type-contract" name="service-type" value="contract" class="mr-1" x-model="addModalForm.type" required>
                        <label for="type-contract" class="text-gray-700">契約中</label>
                    </div>
                    {{-- エラーメッセージを表示 --}}
                    <p class="text-red-500 text-sm mt-1" x-text="addModalForm.errors.type" x-show="addModalForm.errors.type"></p>
                </div>
                <div class="mb-4">
                    <label for="notification-date" class="block font-medium text-gray-900 mb-1">通知対象日</label>
                    {{-- x-model でデータをバインド --}}
                    {{-- @input と @blur でバリデーションメソッドを呼び出し --}}
                    <input type="date" id="notification-date"
                           class="w-full p-2 border rounded-md focus:outline-none focus:border-blue-500 focus:ring focus:ring-blue-200"
                           :class="{ 'border-red-500': addModalForm.errors.notificationDate }" {{-- エラーがある場合は赤枠 --}}
                           required
                           x-model="addModalForm.notificationDate"
                           @input="validateField('notificationDate', $event.target.value, 'add')"
                           @blur="validateField('notificationDate', $event.target.value, 'add')"
                    >
                    {{-- エラーメッセージを表示 --}}
                    <p class="text-red-500 text-sm mt-1" x-text="addModalForm.errors.notificationDate" x-show="addModalForm.errors.notificationDate"></p>
                </div>
                <div class="mb-4">
                    <label for="notification-timing" class="block font-medium text-gray-900 mb-1">通知タイミング</label>
                    <select id="notification-timing" class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:border-blue-500 focus:ring focus:ring-blue-200">
                        <option value="0">当日</option>
                        <option value="1">1日前</option>
                        <option value="3">3日前</option>
                        <option value="7">7日前</option>
                        <option value="30">30日前</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label for="memo" class="block font-medium text-gray-900 mb-1">メモ</label>
                    <textarea id="memo" rows="4" class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:border-blue-500 focus:ring focus:ring-blue-200" placeholder="例: 年払い契約、次回更新時に解約を検討..."></textarea>
                </div>
            </form>
        </div>
        <div class="modal-footer flex flex-col-reverse md:flex-row md:justify-end gap-3 p-4 border-t border-gray-200">
            {{-- Cancel button calls closeModals method --}}
            <button class="button-secondary bg-gray-300 text-gray-700 font-bold py-2 px-4 rounded hover:bg-gray-400 focus:outline-none modal-close w-full md:w-auto" @click="closeModals()">キャンセル</button>
            {{-- !!!今回の修正点!!! type="button" に変更し、クリックでフォームを送信してaddServiceを呼び出す --}}
            <button class="button-primary bg-blue-500 text-white font-bold py-2 px-4 rounded hover:bg-blue-600 focus:outline-none w-full md:w-auto" type="button" @click="addService()">登録する</button> {{-- Changed type and added @click --}}
        </div>
    </div>

    <!-- サービス詳細/編集モーダル -->
    {{-- Show/hide based on state --}}
    {{-- Top margin and horizontal auto margin --}}
    {{-- max-h-screen を max-h-[90vh] に変更 --}}
    <div id="edit-modal" class="modal bg-white rounded-lg shadow-xl w-11/12 md:max-w-md flex flex-col max-h-[90vh] mt-16 mx-auto" x-show="showEditModal" @click.stop>
        <div class="modal-header flex justify-between items-center p-4 border-b border-gray-200">
            {{-- Modal title bound to service name --}}
            {{-- editingService が null の場合は「サービス名」と表示 --}}
            <h2 id="edit-modal-title" class="text-lg font-semibold text-gray-900" x-text="editingService ? editingService.name : 'サービス名'"></h2>
            {{-- Close modal button --}}
            <button class="modal-close text-gray-500 text-xl hover:text-gray-700 focus:outline-none" @click="closeModals()"><i class="fas fa-times"></i></button>
        </div>
        {{-- editingService が null でない場合のみモーダルボディとフッターを表示 --}}
        {{-- template x-if は単一の要素しか囲めないため、wrapper div を追加 --}}
        <template x-if="editingService">
            <div> {{-- x-if で条件付きレンダリングする wrapper div --}}
                {{-- overflow-y-auto を追加して内容をスクロール可能に --}}
                {{-- !!!今回の修正点!!! @submit.prevent を削除。ボタンの @click でフォームを送信する。 --}}
                <div class="modal-body p-6 flex-grow overflow-y-auto">
                    <form> {{-- Removed @submit.prevent --}}
                        <div class="mb-4">
                            <label for="edit-service-name" class="block font-medium text-gray-900 mb-1">サービス名</label>
                            {{-- x-model で editingService.name をバインド --}}
                            {{-- @input と @blur でバリデーションメソッドを呼び出し --}}
                            <input type="text" id="edit-service-name"
                                   class="w-full p-2 border rounded-md focus:outline-none focus:border-blue-500 focus:ring focus:ring-blue-200"
                                   :class="{ 'border-red-500': editModalFormErrors.name }" {{-- エラーがある場合は赤枠 --}}
                                   x-model="editingService.name"
                                   @input="validateField('name', editingService.name, 'edit')" {{-- editingService.name を渡す --}}
                                   @blur="validateField('name', editingService.name, 'edit')" {{-- editingService.name を渡す --}}
                                   required
                            >
                            {{-- エラーメッセージを表示 --}}
                            <p class="text-red-500 text-sm mt-1" x-text="editModalFormErrors.name" x-show="editModalFormErrors.name"></p>
                        </div>
                        <div class="mb-4">
                            <label class="block font-medium text-gray-900 mb-1">種別</label>
                            <div @change="validateField('type', editingService.type, 'edit')"> {{-- 親要素で @change をリッスン --}}
                                {{-- x-model で editingService.type にバインド --}}
                                <input type="radio" id="edit-type-trial" name="edit-service-type" value="trial" class="mr-1" x-model="editingService.type">
                                <label for="edit-type-trial" class="mr-4 text-gray-700">トライアル中</label>
                                <input type="radio" id="edit-type-contract" name="edit-service-type" value="contract" class="mr-1" x-model="editingService.type">
                                <label for="edit-type-contract" class="text-gray-700">契約中</label>
                            </div>
                            {{-- エラーメッセージを表示 --}}
                            <p class="text-red-500 text-sm mt-1" x-text="editModalFormErrors.type" x-show="editModalFormErrors.type"></p>
                        </div>
                        <div class="mb-4">
                            <label for="edit-notification-date" class="block font-medium text-gray-900 mb-1">通知対象日</label>
                            {{-- x-model で editingService.notificationDate をバインド --}}
                            {{-- @input と @blur でバリデーションメソッドを呼び出し --}}
                            <input type="date" id="edit-notification-date"
                                   class="w-full p-2 border rounded-md focus:outline-none focus:border-blue-500 focus:ring focus:ring-blue-200"
                                   :class="{ 'border-red-500': editModalFormErrors.notificationDate }" {{-- エラーがある場合は赤枠 --}}
                                   x-model="editingService.notificationDate"
                                   @input="validateField('notificationDate', editingService.notificationDate, 'edit')" {{-- editingService.notificationDate を渡す --}}
                                   @blur="validateField('notificationDate', editingService.notificationDate, 'edit')" {{-- editingService.notificationDate を渡す --}}
                                   required
                            >
                            {{-- エラーメッセージを表示 --}}
                            <p class="text-red-500 text-sm mt-1" x-text="editModalFormErrors.notificationDate" x-show="editModalFormErrors.notificationDate"></p>
                        </div>
                        <div class="mb-4">
                            <label for="edit-notification-timing" class="block font-medium text-gray-900 mb-1">通知タイミング</label>
                            {{-- Bind select value to editingService data --}}
                            <select id="edit-notification-timing" class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:border-blue-500 focus:ring focus:ring-blue-200" x-model="editingService.notificationTiming">
                                <option value="0">当日</option>
                                <option value="1">1日前</option>
                                <option value="3">3日前</option>
                                <option value="7">7日前</option>
                                <option value="30">30日前</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label for="edit-memo" class="block font-medium text-gray-900 mb-1">メモ</label>
                            {{-- Bind textarea value to editingService data --}}
                            <textarea id="edit-memo" rows="4" class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:border-blue-500 focus:ring focus:ring-blue-200" x-model="editingService.memo" placeholder="例: 年払い契約、次回更新時に解約を検討..."></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer flex flex-col-reverse md:flex-row md:justify-end gap-3 p-4 border-t border-gray-200">
                    {{-- Delete button calls deleteService method --}}
                    <button class="button-danger bg-red-500 text-white font-bold py-2 px-4 rounded hover:bg-red-600 focus:outline-none order-last md:order-first w-full md:w-auto" @click.stop="openDeleteConfirmModal(editingService)">削除する</button>
                    {{-- Cancel button calls closeModals method --}}
                    <button class="button-secondary bg-gray-300 text-gray-700 font-bold py-2 px-4 rounded hover:bg-gray-400 focus:outline-none modal-close w-full md:w-auto" @click="closeModals()">キャンセル</button>
                    {{-- !!!今回の修正点!!! type="button" に変更し、クリックでフォームを送信してsaveServiceを呼び出す --}}
                    <button class="button-primary bg-blue-500 text-white font-bold py-2 px-4 rounded hover:bg-blue-600 focus:outline-none w-full md:w-auto" type="button" @click="saveService()">保存する</button> {{-- Changed type and added @click --}}
                </div>
            </div> {{-- wrapper div の閉じタグ --}}
        </template> {{-- template x-if の閉じタグ --}}

    </div> {{-- id="edit-modal" div の閉じタグ --}}

    <div id="delete-confirm-modal"
         class="modal bg-white rounded-lg shadow-xl w-11/12 md:max-w-sm flex flex-col max-h-[90vh] mt-16 mx-auto"
         x-show="showDeleteConfirmModal"
         @click.stop {{-- ここでクリックイベントの伝播を止めます --}}
    >
        <div class="modal-header flex justify-between items-center p-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">サービスの削除</h2>
            {{-- Close modal button --}}
            {{-- 削除確認モーダルはキャンセルボタンで閉じる想定なので、ヘッダーの閉じるボタンは必須ではありませんが、あった方が親切かもしれません。今回は一旦なしで進めます。 --}}
            {{-- <button class="modal-close text-gray-500 text-xl hover:text-gray-700 focus:outline-none" @click="cancelDelete()"><i class="fas fa-times"></i></button> --}}
        </div>
        <div class="modal-body p-6 flex-grow overflow-y-auto">
            {{-- serviceToDelete が存在する場合のみメッセージを表示 --}}
            <template x-if="serviceToDelete">
                <p class="text-gray-700">
                    「<strong x-text="serviceToDelete.name"></strong>」
                    サービスを本当に削除してもよろしいですか？
                </p>
            </template>
            <p x-show="!serviceToDelete" class="text-red-600">
                削除対象のサービス情報が取得できませんでした。
            </p>
        </div>
        <div class="modal-footer flex flex-col-reverse md:flex-row md:justify-end gap-3 p-4 border-t border-gray-200">
            {{-- Cancel button calls cancelDelete method --}}
            <button class="button-secondary bg-gray-300 text-gray-700 font-bold py-2 px-4 rounded hover:bg-gray-400 focus:outline-none w-full md:w-auto" @click="cancelDelete()">キャンセル</button>
            {{-- Delete button calls deleteService method --}}
            {{-- 削除処理は serviceToDelete のデータを使って行います --}}
            <button class="button-danger bg-red-500 text-white font-bold py-2 px-4 rounded hover:bg-red-600 focus:outline-none w-full md:w-auto" @click="deleteService()">削除する</button>
        </div>
    </div> {{-- 削除ダイアログの閉じタグ --}}

    {{-- Show/hide based on state --}}
    {{-- Top margin and horizontal auto margin --}}
    {{-- max-h-screen を max-h-[90vh] に変更 --}}
    <div id="guide-modal"
         class="modal bg-white rounded-lg shadow-xl w-11/12 md:max-w-md flex flex-col max-h-[90vh] mt-16 mx-auto"
         x-show="showGuideModal" @click.stop>
        <div class="modal-header flex justify-between items-center p-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">カレンダー連携設定ガイド</h2>
            {{-- Close modal button --}}
            <button class="modal-close text-gray-500 text-xl hover:text-gray-700 focus:outline-none"
                    @click="closeModals()"><i class="fas fa-times"></i></button>
        </div>
        {{-- overflow-y-auto を追加して内容をスクロール可能に --}}
        <div class="modal-body p-6 flex-grow overflow-y-auto">
            <p class="mb-4 text-gray-700">
                以下のURLをカレンダーアプリに登録することで、Subscruの通知をカレンダーで受け取ることができます。</p>
            <h3 class="text-md font-semibold text-gray-900 mb-3">主要カレンダーアプリでの購読方法</h3>
            <div class="guide-steps">
                <div class="step mb-4 pb-4 border-b border-gray-200 last:border-b-0 last:pb-0">
                    <h4 class="text-blue-500 font-semibold mb-2">Appleカレンダー (macOS/iOS)</h4>
                    <p class="text-sm text-gray-700 mb-1">1. カレンダーアプリを開きます。</p>
                    <p class="text-sm text-gray-700 mb-1">2. 「ファイル」>「新規カレンダーを購読」を選択します。</p>
                    <p class="text-sm text-gray-700">3. 上記のURLを入力し、「購読」をクリックします。</p>
                </div>
                <div class="step mb-4 pb-4 border-b border-gray-200 last:border-b-0 last:pb-0">
                    <h4 class="text-blue-500 font-semibold mb-2">Googleカレンダー (Web)</h4>
                    <p class="text-sm text-gray-700 mb-1">1. Googleカレンダーを開きます。</p>
                    <p class="text-sm text-gray-700 mb-1">2.
                        「他のカレンダー」の横にある「＋」をクリックし、「URLで追加」を選択します。</p>
                    <p class="text-sm text-gray-700">3. 上記のURLを入力し、「カレンダーを追加」をクリックします。</p>
                </div>
                <div class="step mb-4 pb-4 border-b border-gray-200 last:border-b-0 last:pb-0">
                    <h4 class="text-blue-500 font-semibold mb-2">Outlookカレンダー (Web)</h4>
                    <p class="text-sm text-gray-700 mb-1">1. Outlookカレンダーを開きます。</p>
                    <p class="text-sm text-gray-700 mb-1">2. 「カレンダーを追加」>「Webから購読」を選択します。</p>
                    <p class="text-sm text-gray-700">3.
                        上記のURLを入力し、カレンダー名などを設定して「インポート」をクリックします。</p>
                </div>
            </div>
        </div>
        <div class="modal-footer flex justify-end p-4 border-t border-gray-200">
            {{-- Close button calls closeModals method --}}
            <button
                class="button-secondary bg-gray-300 text-gray-700 font-bold py-2 px-4 rounded hover:bg-gray-400 focus:outline-none modal-close"
                @click="closeModals()">閉じる
            </button>
        </div>
    </div>

</div>

{{-- !!!今回の追加要素!!! ログアウトのための隠しフォーム --}}
<form id="logout-form" action="/logout-dummy" method="POST" style="display: none;">
    @csrf {{-- Laravel の CSRF 保護のためのトークン --}}
</form>

{{-- Vite JS Asset --}}
@vite(['resources/js/app.js'])

{{-- !!!今回の修正要素!!! トースト通知メッセージの表示エリア (中央上部に表示, 色分け対応) --}}
{{-- fixed で画面に固定 --}}
{{-- inset-x-0 で水平方向いっぱいに広げ、mx-auto で中央寄せ --}}
{{-- top-4 で画面上端から4単位離す（位置は調整可能） --}}
{{-- max-w-md で最大幅を設定 (例: 384px)。px-4 py-2 でパディングを設定します。これによりサイズが決まります。 --}}
{{-- z-50 で最前面 --}}
{{-- x-show="showToast" で表示/非表示 --}}
{{-- x-transition でフェードアニメーション --}}
{{-- :class で背景色とテキスト色を toastType ('success', 'error', null) に応じて動的に変更 --}}
{{-- showToast, toastMessage, toastType はメインの x-data から参照されます --}}
<div class="fixed inset-x-0 top-4 mx-auto max-w-md z-50 px-4 py-2 rounded shadow-lg text-center"
     x-show="showToast"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 transform translate-y-2"
     x-transition:enter-end="opacity-100 transform translate-y-0"
     x-transition:leave="transition ease-in duration-300"
     x-transition:leave-start="opacity-100 transform translate-y-0"
     x-transition:leave-end="opacity-0 transform translate-y-2"
     :class="{ 'bg-green-500 text-white': toastType === 'success', 'bg-red-500 text-white': toastType === 'error', 'bg-gray-800 text-white': toastType === null }"
     style="display: none;"
>
    {{-- ここにトーストメッセージが表示されます --}}
    <span x-text="toastMessage"></span>
</div>

</body>
</html>
