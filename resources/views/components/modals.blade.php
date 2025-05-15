{{-- Show/hide overlay based on modal states --}}
{{-- Close modals on overlay click --}}
{{-- Top alignment and horizontal center --}}
<div class="modal-overlay fixed inset-0 bg-black bg-opacity-60 backdrop-blur-sm z-40 items-start justify-center"
     x-show="showAddModal || showEditModal || showGuideModal || showDeleteConfirmModal"
     {{-- ここに showDeleteConfirmModal が含まれているか確認 --}}
     @click.self="closeModals()"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-300"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     style="display: none;"
>
    {{-- Include individual modal components --}}
    @include('components.modals.add-service-modal')
    @include('components.modals.edit-service-modal')
    @include('components.modals.delete-confirm-modal')
    @include('components.modals.guide-modal')
</div>

