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
