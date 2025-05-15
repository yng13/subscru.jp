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
        <button
            class="button-secondary bg-gray-300 text-gray-700 font-bold py-2 px-4 rounded hover:bg-gray-400 focus:outline-none w-full md:w-auto"
            @click="cancelDelete()">キャンセル
        </button>
        {{-- Delete button calls deleteService method --}}
        {{-- 削除処理は serviceToDelete のデータを使って行います --}}
        <button
            class="button-danger bg-red-500 text-white font-bold py-2 px-4 rounded hover:bg-red-600 focus:outline-none w-full md:w-auto"
            @click="deleteService()">削除する
        </button>
    </div>
</div> {{-- 削除ダイアログの閉じタグ --}}
