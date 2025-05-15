{{-- Call openModal method --}}
{{-- スマホ版FABボタンの形状とサイズを修正 --}}
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
