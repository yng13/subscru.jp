{{-- トースト通知メッセージの表示エリア (中央上部に表示, 色分け対応) --}}
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
