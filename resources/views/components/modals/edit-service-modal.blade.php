<!-- サービス詳細/編集モーダル -->
{{-- Show/hide based on state --}}
{{-- Top margin and horizontal auto margin --}}
{{-- max-h-screen を max-h-[90vh] に変更 --}}
<div id="edit-modal"
     class="modal bg-white rounded-lg shadow-xl w-11/12 md:max-w-md flex flex-col max-h-[90vh] mt-16 mx-auto"
     x-show="showEditModal" @click.stop>
    <div class="modal-header flex justify-between items-center p-4 border-b border-gray-200">
        {{-- Modal title bound to service name --}}
        {{-- editingService が null の場合は「サービス名」と表示 --}}
        <h2 id="edit-modal-title" class="text-lg font-semibold text-gray-900"
            x-text="editingService ? editingService.name : 'サービス名'"></h2>
        {{-- Close modal button --}}
        <button class="modal-close text-gray-500 text-xl hover:text-gray-700 focus:outline-none"
                @click="closeModals()"><i class="fas fa-times"></i></button>
    </div>
    {{-- editingService が null でない場合のみモーダルボディとフッターを表示 --}}
    {{-- template x-if は単一の要素しか囲めないため、wrapper div を追加 --}}
    <template x-if="editingService">
        <div> {{-- x-if で条件付きレンダリングする wrapper div --}}
            {{-- overflow-y-auto を追加して内容をスクロール可能に --}}
            {{-- @submit.prevent を削除。ボタンの @click でフォームを送信する。 --}}
            <div class="modal-body p-6 flex-grow overflow-y-auto">
                <form> {{-- Removed @submit.prevent --}}
                    <div class="mb-4">
                        <label for="edit-service-name"
                               class="block font-medium text-gray-900 mb-1">サービス名</label>
                        {{-- x-model で editingService.name をバインド --}}
                        {{-- @input と @blur でバリデーションメソッドを呼び出し --}}
                        <input type="text" id="edit-service-name"
                               class="w-full p-2 border rounded-md focus:outline-none focus:border-blue-500 focus:ring focus:ring-blue-200"
                               :class="{ 'border-red-500': editModalFormErrors.name }" {{-- エラーがある場合は赤枠 --}}
                               x-model="editingService.name"
                               @input="validateField('name', editingService.name, 'edit')"
                               {{-- editingService.name を渡す --}}
                               @blur="validateField('name', editingService.name, 'edit')"
                               {{-- editingService.name を渡す --}}
                               required
                        >
                        {{-- エラーメッセージを表示 --}}
                        <p class="text-red-500 text-sm mt-1" x-text="editModalFormErrors.name"
                           x-show="editModalFormErrors.name"></p>
                    </div>
                    <div class="mb-4">
                        <label class="block font-medium text-gray-900 mb-1">種別</label>
                        <div
                            @change="validateField('type', editingService.type, 'edit')"> {{-- 親要素で @change をリッスン --}}
                            {{-- x-model で editingService.type にバインド --}}
                            <input type="radio" id="edit-type-trial" name="edit-service-type" value="trial"
                                   class="mr-1" x-model="editingService.type">
                            <label for="edit-type-trial" class="mr-4 text-gray-700">トライアル中</label>
                            <input type="radio" id="edit-type-contract" name="edit-service-type" value="contract"
                                   class="mr-1" x-model="editingService.type">
                            <label for="edit-type-contract" class="text-gray-700">契約中</label>
                        </div>
                        {{-- エラーメッセージを表示 --}}
                        <p class="text-red-500 text-sm mt-1" x-text="editModalFormErrors.type"
                           x-show="editModalFormErrors.type"></p>
                    </div>
                    <div class="mb-4">
                        <label for="edit-notification-date"
                               class="block font-medium text-gray-900 mb-1">通知対象日</label>
                        {{-- x-model で editingService.notification_date をバインド --}}
                        {{-- @input と @blur でバリデーションメソッドを呼び出し --}}
                        <input type="date" id="edit-notification-date"
                               class="w-full p-2 border rounded-md focus:outline-none focus:border-blue-500 focus:ring focus:ring-blue-200"
                               :class="{ 'border-red-500': editModalFormErrors.notification_date }"
                               {{-- エラーがある場合は赤枠 --}}
                               x-model="editingService.notification_date"
                               @input="validateField('notification_date', editingService.notification_date, 'edit')"
                               {{-- editingService.notification_date を渡す --}}
                               @blur="validateField('notification_date', editingService.notification_date, 'edit')"
                               {{-- editingService.notification_date を渡す --}}
                               required
                        >
                        {{-- エラーメッセージを表示 --}}
                        <p class="text-red-500 text-sm mt-1" x-text="editModalFormErrors.notification_date"
                           x-show="editModalFormErrors.notification_date"></p>
                    </div>
                    <div class="mb-4">
                        <label for="edit-notification-timing"
                               class="block font-medium text-gray-900 mb-1">通知タイミング</label>
                        {{-- Bind select value to editingService data --}}
                        <select id="edit-notification-timing"
                                class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:border-blue-500 focus:ring focus:ring-blue-200"
                                x-model="editingService.notification_timing">
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
                        <textarea id="edit-memo" rows="4"
                                  class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:border-blue-500 focus:ring focus:ring-blue-200"
                                  x-model="editingService.memo"
                                  placeholder="例: 年払い契約、次回更新時に解約を検討..."></textarea>
                    </div>
                </form>
            </div>
            <div
                class="modal-footer flex flex-col-reverse md:flex-row md:justify-end gap-3 p-4 border-t border-gray-200">
                {{-- Delete button calls deleteService method --}}
                <button
                    class="button-danger bg-red-500 text-white font-bold py-2 px-4 rounded hover:bg-red-600 focus:outline-none order-last md:order-first w-full md:w-auto"
                    @click.stop="openDeleteConfirmModal(editingService)">削除する
                </button>
                {{-- Cancel button calls closeModals method --}}
                <button
                    class="button-secondary bg-gray-300 text-gray-700 font-bold py-2 px-4 rounded hover:bg-gray-400 focus:outline-none modal-close w-full md:w-auto"
                    @click="closeModals()">キャンセル
                </button>
                {{-- type="button" に変更し、クリックでフォームを送信してsaveServiceを呼び出す --}}
                <button
                    class="button-primary bg-blue-500 text-white font-bold py-2 px-4 rounded hover:bg-blue-600 focus:outline-none w-full md:w-auto"
                    type="button" @click="saveService()">保存する
                </button> {{-- Changed type and added @click --}}
            </div>
        </div> {{-- wrapper div の閉じタグ --}}
    </template> {{-- template x-if の閉じタグ --}}

</div> {{-- id="edit-modal" div の閉じタグ --}}
