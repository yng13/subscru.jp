{{-- resources/views/components/service-list-section.blade.php --}}

<section class="bg-white p-6 rounded-lg shadow mb-8">
    <h1 class="text-2xl font-semibold text-gray-900 mb-6">サービス一覧</h1>

    {{-- Debug: Display service count --}}
    {{-- ロード中は非表示 --}}
    {{-- 総件数 (pagination.total) を表示するように変更 --}}
    <p x-text="'Services count: ' + pagination.total" class="mb-4 text-sm text-gray-600"
       x-show="!isLoading && pagination.total > 0"></p>

    {{-- === 検索入力フィールドを追加 === --}}
    {{-- x-model="searchTerm" で入力値を Alpine.js の searchTerm プロパティにバインド --}}
    {{-- @input="fetchServices(1, sortBy, sortDirection)" で入力があるたびにサービスを再取得 (必要に応じて調整) --}}
    <div class="mb-4">
        <label for="search" class="sr-only">サービスを検索</label> {{-- アクセシビリティのためにラベルを追加し、非表示にする --}}
        <input type="text" id="search" placeholder="サービス名で検索..."
               class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:border-blue-500 focus:ring focus:ring-blue-200"
               x-model="searchTerm"
               @input.debounce.500="fetchServices(1, sortBy, sortDirection)" {{-- 入力終了500ms後に検索実行 --}}
        >
    </div>
    {{-- ============================== --}}

    {{-- ロード中の表示 --}}
    <div x-show="isLoading" class="text-center text-blue-600 text-lg font-semibold py-8">
        <i class="fas fa-spinner fa-spin mr-2"></i> <span x-text="loadingMessage"></span>
    </div>
    {{-- ロード中とじタグ --}}

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
             @click="sortServices('notification_date')"
             :class="{ 'sorted-asc': sortBy === 'notification_date' && sortDirection === 'asc', 'sorted-desc': sortBy === 'notification_date' && sortDirection === 'desc' }">
            通知対象日 <i class="fas fa-sort ml-2 text-gray-400 text-sm"
                          x-show="sortBy !== 'notification_date'"></i>
            <i class="fas fa-sort-up ml-2 text-gray-600 text-sm"
               x-show="sortBy === 'notification_date' && sortDirection === 'asc'"></i>
            <i class="fas fa-sort-down ml-2 text-gray-600 text-sm"
               x-show="sortBy === 'notification_date' && sortDirection === 'desc'"></i>
        </div>
        {{-- Non-sortable cells - Added py-3 for consistent height --}}
        <div class="py-3">メモ</div>
    </div>


    {{-- Loop through services array --}}
    {{-- ロード中はリストを非表示 --}}
    <div class="service-list grid grid-cols-1 gap-4 md:gap-0 md:flex md:flex-col divide-y divide-gray-200"
         x-show="!isLoading">
        <template x-for="service in services" :key="service.id">
            {{-- Bind class for near deadline --}}
            {{-- Call openModal method on click, passing service object --}}
            <div
                class="service-item bg-white rounded-lg shadow-sm hover:shadow-md p-4 md:p-0 md:rounded-none md:shadow-none md:border-b md:border-gray-200 cursor-pointer flex flex-col md:flex-row items-start md:items-center"
                :class="{ 'near-deadline': getDaysRemaining(service.notification_date) <= 30 }"
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
                {{-- PC: Stack date and days remaining / スマホ: Horizontal --}}
                <div class="mb-2 md:mb-0 md:py-4 md:px-6 notification-text flex flex-row items-center md:flex-col md:items-start">
                    <span class="md:hidden font-semibold mr-2">通知対象日:</span>
                    {{-- 日付と残り日数を囲むspanにスペースを追加 --}}
                    <span class="space-x-2">
                        <span x-text="formatDate(service.notification_date)"></span>
                        <span x-text="'(あと ' + getDaysRemaining(service.notification_date) + ' 日)'"></span>
                    </span>
                </div>
                <div class="md:py-4 md:px-6 break-words md:flex-1 w-full">
                    <span class="md:hidden font-semibold mr-2">メモ:</span>
                    <span x-text="service.memo"></span>
                </div>
            </div>
        </template>
        {{-- Message when services list is empty --}}
        {{-- ロード中は非表示 --}}
        <div x-show="!isLoading && pagination.total === 0" class="p-4 text-center text-gray-500">
            サービスはまだ登録されていません。
        </div>
    </div>

    {{-- === ページネーションリンクを動的に生成 === --}}
    {{-- サービスが1ページに収まらない場合のみ表示 (pagination.last_page > 1) --}}
    <div class="pagination flex justify-center items-center mt-8" x-show="!isLoading && pagination.last_page > 1">
        <template x-for="(link, index) in pagination.links" :key="index">
            {{-- ページリンク --}}
            <a href="#"
               class="page-link px-4 py-2 mx-1 border rounded-md text-gray-700 hover:bg-gray-200"
               :class="{
                    'border-blue-500 bg-blue-500 text-white pointer-events-none hover:bg-blue-500': link.active, // アクティブなページ
                    'border-gray-300': !link.active, // 非アクティブなページ
                    'pointer-events-none opacity-50': !link.url // 前後ページでURLがない場合 (最初/最後のページ)
               }"
               x-text="link.label === 'pagination.previous' ? '前へ' : (link.label === 'pagination.next' ? '次へ' : link.label)" {{-- 前後ページは日本語ラベルに置き換え、それ以外は元のラベルを使用 --}}
               @click.prevent="goToPage(link.url)" {{-- クリックイベントで goToPage を呼び出し --}}
            ></a>
        </template>
    </div>
    {{-- ========================================= --}}

</section>
