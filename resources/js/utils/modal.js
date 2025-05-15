// resources/js/utils/modal.js

// モーダル表示/非表示と関連ロジックをまとめたオブジェクトを返す関数
// このロジックは Alpine.js の data オブジェクトに展開して使用されます。
export function modalLogic() {
    return {
        // モーダルの表示/非表示状態
        showAddModal: false,
        showEditModal: false,
        showGuideModal: false,
        showDeleteConfirmModal: false,

        // 編集対象または削除対象のサービスデータ
        editingService: null,
        serviceToDelete: null,

        // モーダルを開くメソッド
        // modalId: 開くモーダルのID (例: '#add-modal')
        // service: 編集/削除モーダルの場合に渡されるサービスオブジェクト
        // formLogic: フォームのリセットに使用するため、app.js から serviceFormLogic オブジェクトを渡す必要があります
        // notificationLogic: トースト通知に使用するため、app.js から notificationLogic オブジェクトを渡す必要があります
        openModal(modalId, service = null) {
            console.log('modalLogic: openModal called with:', modalId, service);

            // 既に削除確認モーダルが開いている場合は閉じる
            if (this.showDeleteConfirmModal) {
                this.showDeleteConfirmModal = false;
                this.serviceToDelete = null;
            }

            // 他のモーダルを開く前に少し遅延を入れる（削除確認モーダルが閉じるアニメーションのため）
            setTimeout(() => {
                if (modalId === '#add-modal') {
                    // 新規登録フォームの状態をリセット
                    // app.js のコンテキストにある resetAddForm メソッドを呼び出す想定
                    // this.$data を経由して呼び出す必要がありますが、ここでは依存性を減らすためコメントアウト
                    // 呼び出し元 (app.js) でフォームリセット処理を行う必要があります
                    // または、modalLogic に formLogic を引数として渡す設計も考えられます
                    if (typeof this.resetAddForm === 'function') { // resetAddForm が存在するか確認
                        this.resetAddForm();
                    } else {
                        console.warn('modalLogic: resetAddForm method not found on this context.');
                    }

                    this.showAddModal = true;
                    console.log('modalLogic: 新規登録モーダルを開きます');

                } else if (modalId === '#edit-modal') {
                    if (service) {
                        // 編集対象サービスを設定
                        this.editingService = {...service};

                        // 日付形式を datepicker 向けに YYYY-MM-DD に変換
                        if (this.editingService.notification_date) {
                            const date = new Date(this.editingService.notification_date);
                            if (!isNaN(date.getTime())) {
                                const year = date.getFullYear();
                                const month = (date.getMonth() + 1).toString().padStart(2, '0');
                                const day = date.getDate().toString().padStart(2, '0');
                                this.editingService.notification_date = `${year}-${month}-${day}`;
                            } else {
                                this.editingService.notification_date = '';
                            }
                        } else {
                            this.editingService.notification_date = '';
                        }

                        // notification_timing の型を文字列に変換（select要素のvalueは文字列のため）
                        if (typeof this.editingService.notification_timing !== 'string') {
                            this.editingService.notification_timing = String(this.editingService.notification_timing);
                        }

                        // 編集フォームのエラー状態をリセット
                        // app.js のコンテキストにある resetEditFormErrors メソッドを呼び出す想定
                        if (typeof this.resetEditFormErrors === 'function') { // resetEditFormErrors が存在するか確認
                            this.resetEditFormErrors();
                        } else {
                            console.warn('modalLogic: resetEditFormErrors method not found on this context.');
                        }


                        this.showEditModal = true;
                        console.log('modalLogic: 編集モーダルを開きます', this.editingService);
                    } else {
                        console.error('modalLogic: Service data not passed to openModal for edit');
                        if (typeof this.showToastNotification === 'function') { // showToastNotification が存在するか確認
                            this.showToastNotification('サービスの編集に失敗しました。', 'error', 5000);
                        }
                    }

                } else if (modalId === '#guide-modal') {
                    this.showGuideModal = true;
                    console.log('modalLogic: 設定ガイドモーダルを開きます');

                } else if (modalId === '#delete-confirm-modal') {
                    // 削除確認モーダルは openDeleteConfirmModal で開くため、ここでは直接開かない
                    console.warn('modalLogic: Attempted to open delete confirm modal directly with openModal.');
                }
            }, this.showDeleteConfirmModal ? 50 : 0); // 削除確認モーダルが閉じている間に遅延
        },

        // 全てのモーダルを閉じるメソッド (削除確認モーダルは含まない)
        closeModals() {
            this.showAddModal = false;
            this.showEditModal = false;
            this.showGuideModal = false;
            // 編集中のサービスデータもクリア
            this.editingService = null;
            console.log('modalLogic: 全てのモーダルを閉じました (削除確認除く)');
        },

        // 削除確認モーダルを開くメソッド
        // service: 削除対象のサービスオブジェクト
        openDeleteConfirmModal(service) {
            console.log('modalLogic: openDeleteConfirmModal called with service:', service);

            // 他のモーダルが開いている場合は閉じる
            this.closeModals(); // closeModals は serviceToDelete を null にしないため、ここでは editingService のみがクリアされる

            this.serviceToDelete = service; // 削除対象サービスを設定

            // 削除確認モーダルを開くのに少し遅延を入れる
            setTimeout(() => {
                this.showDeleteConfirmModal = true;
                console.log('modalLogic: showDeleteConfirmModal is now true');
            }, 50); // 短い遅延
        },

        // 削除確認をキャンセルするメソッド
        cancelDelete() {
            this.showDeleteConfirmModal = false;
            this.serviceToDelete = null; // 削除対象サービスをクリア
            console.log('modalLogic: 削除をキャンセルしました');
        },

        // サービス新規登録成功後にモーダルを閉じる
        closeAddModalOnSuccess() {
            this.showAddModal = false;
            console.log('modalLogic: 新規登録モーダルを閉じました');
        },

        // サービス保存成功後にモーダルを閉じる
        closeEditModalOnSuccess() {
            this.showEditModal = false;
            this.editingService = null; // 編集中のサービスデータもクリア
            console.log('modalLogic: 編集モーダルを閉じました');
        },

        // サービス削除成功後に削除確認モーダルを閉じる
        closeDeleteConfirmModalOnSuccess() {
            this.showDeleteConfirmModal = false;
            this.serviceToDelete = null; // 削除対象サービスをクリア
            console.log('modalLogic: 削除確認モーダルを閉じました');
        }
    };
}
