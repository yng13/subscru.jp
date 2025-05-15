{{-- resources/views/components/service-list-section.blade.php --}}

<section class="bg-white p-6 rounded-lg shadow mb-8">
    <h1 class="text-2xl font-semibold text-gray-900 mb-6">サービス一覧</h1>

    {{-- Debug: Display service count --}}
    {{-- ロード中は非表示 --}}
    {{-- 総件数 (pagination.total) を表示するように変更 --}}
    <p x-text="'Services count: ' + pagination.total" class="mb-4 text-sm text-gray-600"
       x-show="!isLoading && pagination.total > 0"></p>

    {{-- === 検索入力フィールドを追加 === --}}
    <div class="mb-4">
        <label for="search" class="sr-only">サービスを検索</label>
        <input type="text" id="search" placeholder="サービス名で検索..."
               class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:border-blue-500 focus:ring focus:ring-blue-200"
               x-model="searchTerm"
               @input.debounce.500="fetchServices(1, sortBy, sortDirection, $event.target.value)"
        >
    </div>
    {{-- ============================== --}}

    {{-- === ローディング中またはサービスがない場合のメッセージエリア === --}}
    {{-- このコンテナに固定高さを設定し、中の要素を表示/非表示する --}}
    {{-- x-show で、ロード中 OR (ロードが完了していてかつサービスが0件) の場合に表示 --}}
    <div class="text-center text-blue-600 text-lg font-semibold py-8 h-20 flex items-center justify-center"
         x-show="isLoading || (!isLoading && pagination.total === 0)">
        {{-- ロード中の表示 --}}
        <div x-show="isLoading">
            <i class="fas fa-spinner fa-spin mr-2"></i> <span x-text="loadingMessage"></span>
        </div>
        {{-- サービスが空の場合のメッセージ --}}
        <div x-show="!isLoading && pagination.total === 0" class="p-4 text-center text-gray-500">
            サービスはまだ登録されていません。
        </div>
    </div>
    {{-- =============================================================== --}}


    {{-- サービスリストヘッダー (PC版のみ表示) --}}
    {{-- ロード中に関わらず常に表示されるように x-show="!isLoading" は削除済み --}}
    <div
        class="service-list-header hidden md:flex bg-gray-100 text-xs font-medium text-gray-500 uppercase tracking-wider border-b border-gray-200">
        <div class="cursor-pointer hover:bg-gray-200 flex items-center py-3" @click="sortServices('name')"
             :class="{ 'sorted': sortBy === 'name' }">
            サービス名
            <i class="ml-2 text-sm"
               :class="{
                   'fas fa-sort text-gray-400': sortBy !== 'name',
                   'fas fa-sort-up text-blue-600': sortBy === 'name' && sortDirection === 'asc',
                   'fas fa-sort-down text-blue-600': sortBy === 'name' && sortDirection === 'desc'
               }"></i>
        </div>
        <div class="py-3">種別</div>
        <div class="cursor-pointer hover:bg-gray-200 flex items-center py-3"
             @click="sortServices('notification_date')"
             :class="{ 'sorted': sortBy === 'notification_date' }">
            通知対象日
            <i class="ml-2 text-sm"
               :class="{
                   'fas fa-sort text-gray-400': sortBy !== 'notification_date',
                   'fas fa-sort-up text-blue-600': sortBy === 'notification_date' && sortDirection === 'asc',
                   'fas fa-sort-down text-blue-600': sortBy === 'notification_date' && sortDirection === 'desc'
               }"></i>
        </div>
        <div class="py-3">メモ</div>
    </div>


    {{-- === サービスリスト本体 === --}}
    {{-- ロードが完了していて、かつサービスが1件以上ある場合にのみ表示 --}}
    <div class="service-list grid grid-cols-1 gap-4 md:gap-0 md:flex md:flex-col divide-y divide-gray-200"
         x-show="!isLoading && pagination.total > 0">
        <template x-for="service in services" :key="service.id">
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
                    <span class="service-type text-white text-xs font-bold px-2.5 py-0.5 rounded-full"
                          :class="{ 'bg-blue-500': service.type === 'contract', 'bg-emerald-500': service.type === 'trial' }"
                          x-text="service.type === 'contract' ? '契約中' : 'トライアル中'"></span>
                </div>
                <div
                    class="mb-2 md:mb-0 md:py-4 md:px-6 notification-text flex flex-row items-center md:flex-col md:items-start">
                    <span class="md:hidden font-semibold mr-2">通知対象日:</span>
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
    </div>
    {{-- ====================== --}}


    {{-- ページネーションリンクを動的に生成 --}}
    {{-- サービスが1ページに収まらない場合のみ表示 (pagination.last_page > 1) --}}
    <div class="pagination flex justify-center items-center mt-8" x-show="!isLoading && pagination.last_page > 1">
        <template x-for="(link, index) in pagination.links" :key="index">
            <a href="#"
               class="page-link px-4 py-2 mx-1 border rounded-md text-gray-700 hover:bg-gray-200"
               :class="{
                    'border-blue-500 bg-blue-500 text-white pointer-events-none hover:bg-blue-500': link.active,
                    'border-gray-300': !link.active,
                    'pointer-events-none opacity-50': !link.url
               }"
               x-text="link.label === 'pagination.previous' ? '前へ' : (link.label === 'pagination.next' ? '次へ' : link.label)"
               @click.prevent="goToPage(link.url)"
            ></a>
        </template>
    </div>

</section>
