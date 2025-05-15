{{-- Show/hide based on state --}}
{{-- Top margin and horizontal auto margin --}}
{{-- max-h-screen を max-h-[90vh] に変更 --}}
<!-- サービス登録モーダル -->
{{-- Show/hide based on state --}}
{{-- Top margin and horizontal auto margin --}}
{{-- max-h-screen を max-h-[90vh] に変更 --}}
<div id="add-modal"
     class="modal bg-white rounded-lg shadow-xl w-11/12 md:max-w-md flex flex-col max-h-[90vh] mt-16 mx-auto"
     x-show="showAddModal" @click.stop>
    <div class="modal-header flex justify-between items-center p-4 border-b border-gray-200">
        <h2 class="text-lg font-semibold text-gray-900">新しいサービスを登録</h2>
        {{-- Close modal button --}}
        <button class="modal-close text-gray-500 text-xl hover:text-gray-700 focus:outline-none"
                @click="closeModals()"><i class="fas fa-times"></i></button>
    </div>
    {{-- overflow-y-auto を追加して内容をスクロール可能に --}}
    <div class="modal-body p-6 flex-grow overflow-y-auto">
        {{-- @submit.prevent を削除。ボタンの @click でフォームを送信する。 --}}
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
                <p class="text-red-500 text-sm mt-1" x-text="addModalForm.errors.name"
                   x-show="addModalForm.errors.name"></p>
            </div>
            <div class="mb-4">
                <label class="block font-medium text-gray-900 mb-1">種別</label>
                <div @change="validateField('type', addModalForm.type, 'add')"> {{-- 親要素で @change をリッスン --}}
                    {{-- x-model で addModalForm.type にバインド --}}
                    <input type="radio" id="type-trial" name="service-type" value="trial" class="mr-1"
                           x-model="addModalForm.type" required>
                    <label for="type-trial" class="mr-4 text-gray-700">トライアル中</label>
                    <input type="radio" id="type-contract" name="service-type" value="contract" class="mr-1"
                           x-model="addModalForm.type" required>
                    <label for="type-contract" class="text-gray-700">契約中</label>
                </div>
                {{-- エラーメッセージを表示 --}}
                <p class="text-red-500 text-sm mt-1" x-text="addModalForm.errors.type"
                   x-show="addModalForm.errors.type"></p>
            </div>
            <div class="mb-4">
                <label for="notification-date" class="block font-medium text-gray-900 mb-1">通知対象日</label>
                {{-- x-model でデータをバインド --}}
                {{-- @input と @blur でバリデーションメソッドを呼び出し --}}
                <input type="date" id="notification-date"
                       class="w-full p-2 border rounded-md focus:outline-none focus:border-blue-500 focus:ring focus:ring-blue-200"
                       :class="{ 'border-red-500': addModalForm.errors.notification_date }" {{-- エラーがある場合は赤枠 --}}
                       required
                       x-model="addModalForm.notification_date"
                       @input="validateField('notification_date', $event.target.value, 'add')"
                       @blur="validateField('notification_date', $event.target.value, 'add')"
                >
                {{-- エラーメッセージを表示 --}}
                <p class="text-red-500 text-sm mt-1" x-text="addModalForm.errors.notification_date"
                   x-show="addModalForm.errors.notification_date"></p>
            </div>
            <div class="mb-4">
                <label for="notification-timing" class="block font-medium text-gray-900 mb-1">通知タイミング</label>
                <select id="notification-timing"
                        class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:border-blue-500 focus:ring focus:ring-blue-200">
                    <option value="0">当日</option>
                    <option value="1">1日前</option>
                    <option value="3">3日前</option>
                    <option value="7">7日前</option>
                    <option value="30">30日前</option>
                </select>
            </div>
            <div class="mb-4">
                <label for="memo" class="block font-medium text-gray-900 mb-1">メモ</label>
                <textarea id="memo" rows="4"
                          class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:border-blue-500 focus:ring focus:ring-blue-200"
                          placeholder="例: 年払い契約、次回更新時に解約を検討..."></textarea>
            </div>
        </form>
    </div>
    <div class="modal-footer flex flex-col-reverse md:flex-row md:justify-end gap-3 p-4 border-t border-gray-200">
        {{-- Cancel button calls closeModals method --}}
        <button
            class="button-secondary bg-gray-300 text-gray-700 font-bold py-2 px-4 rounded hover:bg-gray-400 focus:outline-none modal-close w-full md:w-auto"
            @click="closeModals()">キャンセル
        </button>
        {{-- type="button" に変更し、クリックでフォームを送信してaddServiceを呼び出す --}}
        <button
            class="button-primary bg-blue-500 text-white font-bold py-2 px-4 rounded hover:bg-blue-600 focus:outline-none w-full md:w-auto"
            type="button" @click="addService()">登録する
        </button> {{-- Changed type and added @click --}}
    </div>
</div>
