// resources/js/app.js

// Alpine.js のインポート
import Alpine from 'alpinejs';
import intersect from '@alpinejs/intersect'; // 例: Intersect プラグイン

// Alpine.js プラグインの登録
Alpine.plugin(intersect);

// x-data で使用するデータとメソッドを定義する関数
function serviceListPage() {
    // Debug: この関数が呼び出されているか確認
    console.log('serviceListPage function called, initializing data...');

    // バリデーションエラーメッセージの定義
    const validationMessages = {
        required: 'この項目は必須です。',
        invalidDate: '有効な日付を入力してください。'
    };

    return {
        // === State properties ===
        isDrawerOpen: false,

        showAddModal: false,
        showEditModal: false,
        showGuideModal: false,
        // !!! 今回の追加 !!! 削除確認モーダルの状態と削除対象サービス
        showDeleteConfirmModal: false,
        serviceToDelete: null, // 削除対象のサービスデータを保持
        editingService: null, // 編集対象のサービスデータを保持

        // トースト通知の状態とメッセージ (メインの x-data で管理)
        showToast: false,
        toastMessage: '',
        toastType: null, // 'success', 'error', null

        // サービスデータの例 (実際にはAPIから取得)
        services: [
            // Netflixの通知対象日を翌日に設定（例: 2025/05/12）
            { id: 1, name: 'Netflix', type: 'contract', notificationDate: '2025-05-12', memo: '年払い契約、次回更新時に解約を検討...', categoryIcon: 'fas fa-music', notificationTiming: '7' }, // notificationTimingを追加
            { id: 2, name: 'Google Drive', type: 'trial', notificationDate: '2026-01-15', memo: 'トライアル終了前に容量を確認...', categoryIcon: 'fas fa-cloud', notificationTiming: '3' },
            { id: 3, name: 'Spotify', type: 'contract', notificationDate: '2025-11-20', memo: 'ファミリープランを契約中...', categoryIcon: 'fas fa-music', notificationTiming: '0' },
            { id: 4, name: 'AWS S3', type: 'contract', notificationDate: '2026-03-01', memo: 'バックアップ用ストレージ...', categoryIcon: 'fas fa-database', notificationTiming: '30' },
            { id: 5, name: 'Adobe Creative Cloud', type: 'contract', notificationDate: '2025-10-10', memo: '年間プラン、期限が近い...', categoryIcon: 'fas fa-paint-brush', notificationTiming: '1' },
        ],

        // ソートの状態
        sortBy: 'notificationDate', // 初期ソートキー
        sortDirection: 'asc', // 'asc' or 'desc'

        // サービス登録モーダルのフォーム状態とバリデーションエラー
        addModalForm: {
            name: '',
            type: '', // ラジオボタンは初期値 null または '' が良い
            notificationDate: '',
            notificationTiming: '0', // select の初期値
            memo: '',
            errors: {
                name: '',
                type: '',
                notificationDate: ''
            },
            isValid: false // フォーム全体の有効性
        },

        // サービス編集モーダルのフォーム状態とバリデーションエラー
        // 編集モーダルは editingService とデータを共有するため、エラー状態のみを別途管理
        editModalFormErrors: {
            name: '',
            type: '',
            notificationDate: ''
        },

        // === Methods ===

        // 特定のフィールドのバリデーションを行う関数
        // field: フィールド名 ('name', 'type', 'notificationDate')
        // value: フィールドの値
        // formType: 'add' or 'edit'
        validateField(field, value, formType) {
            let errorMessage = '';
            let formErrors;

            if (formType === 'add') {
                formErrors = this.addModalForm.errors;
            } else if (formType === 'edit') {
                formErrors = this.editModalFormErrors;
            } else {
                return; // 不正な formType
            }

            // 必須チェック
            if (!value || (typeof value === 'string' && value.trim() === '')) {
                errorMessage = validationMessages.required;
            } else {
                // 日付フィールドの追加バリデーション
                if (field === 'notificationDate') {
                    const date = new Date(value);
                    if (isNaN(date.getTime())) {
                        errorMessage = validationMessages.invalidDate;
                    }
                }
            }

            // エラーメッセージを更新
            formErrors[field] = errorMessage;

            // フォーム全体の有効性を更新 (今回は必須チェックのみなのでシンプル)
            if (formType === 'add') {
                this.addModalForm.isValid = this.addModalForm.errors.name === '' &&
                    this.addModalForm.errors.type === '' &&
                    this.addModalForm.errors.notificationDate === '';
            }
            // 編集モーダルのisValidは今回は使用しないが、必要に応じて同様に定義
        },

        // サービス登録フォーム全体のバリデーションを行う関数
        validateAddForm() {
            this.validateField('name', this.addModalForm.name, 'add');
            this.validateField('type', this.addModalForm.type, 'add');
            this.validateField('notificationDate', this.addModalForm.notificationDate, 'add');

            // フォーム全体の有効性を返す
            return this.addModalForm.isValid;
        },

        // サービス編集フォーム全体のバリデーションを行う関数
        validateEditForm() {
            // editingService の値を使ってバリデーション
            if (!this.editingService) return false; // editingService がなければ無効

            this.validateField('name', this.editingService.name, 'edit');
            this.validateField('type', this.editingService.type, 'edit');
            this.validateField('notificationDate', this.editingService.notificationDate, 'edit');

            // フォーム全体の有効性を計算して返す (プロパティとしては持たないが、関数で計算)
            return this.editModalFormErrors.name === '' &&
                this.editModalFormErrors.type === '' &&
                this.editModalFormErrors.notificationDate === '';
        },

        // フォームの状態をリセットする関数 (新規登録用)
        resetAddForm() {
            console.log('新規登録フォームをリセット');
            this.addModalForm.name = '';
            this.addModalForm.type = '';
            this.addModalForm.notificationDate = '';
            this.addModalForm.notificationTiming = '0';
            this.addModalForm.memo = '';
            // エラーメッセージもクリア
            this.addModalForm.errors.name = '';
            this.addModalForm.errors.type = '';
            this.addModalForm.errors.notificationDate = '';
            this.addModalForm.isValid = false;
        },

        // 編集モーダルを開く際にエラー状態をリセットする関数
        resetEditFormErrors() {
            console.log('編集フォームのエラーをリセット');
            this.editModalFormErrors.name = '';
            this.editModalFormErrors.type = '';
            this.editModalFormErrors.notificationDate = '';
        },

        // 通知対象日の残り日数を計算する関数
        getDaysRemaining(dateString) {
            // null または undefined の場合のハンドリングを追加
            if (!dateString) {
                //console.warn('getDaysRemaining called with null or undefined dateString');
                return Infinity; // 日付がない場合は遠い未来として扱う
            }
            // Dateオブジェクトの生成が失敗した場合のハンドリング
            const notificationDate = new Date(dateString);
            if (isNaN(notificationDate.getTime())) {
                console.error('Invalid date string provided to getDaysRemaining:', dateString);
                return Infinity; // 不正な日付文字列の場合は遠い未来として扱う
            }

            const today = new Date();
            // 時刻情報をリセットして日付のみで比較
            notificationDate.setHours(0, 0, 0, 0);
            today.setHours(0, 0, 0, 0);

            const timeDiff = notificationDate.getTime() - today.getTime();
            const daysDiff = Math.ceil(timeDiff / (1000 * 3600 * 24));

            // Debugログ (確認後削除可能)
            const isNear = daysDiff <= 30;
            //console.log(`[getDaysRemaining] Date: ${dateString}, Today: ${today.toISOString().split('T')[0]}, Days Diff: ${daysDiff}, Is Near Deadline: ${isNear}`);

            return daysDiff;
        },

        // 日付をYYYY/MM/DD 形式にフォーマットする関数
        formatDate(dateString) {
            // null または undefined の場合のハンドリングを追加
            if (!dateString) {
                return 'N/A';
            }
            const date = new Date(dateString);
            if (isNaN(date.getTime())) {
                return 'Invalid Date'; // 不正な日付文字列の場合
            }

            const year = date.getFullYear();
            const month = (date.getMonth() + 1).toString().padStart(2, '0');
            const day = date.getDate().toString().padStart(2, '0');
            return `${year}/${month}/${day}`;
        },

        // ソート処理を行う関数
        sortServices(key) {
            if (this.sortBy === key) {
                this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
            } else {
                this.sortBy = key;
                this.sortDirection = 'asc';
            }

            // サービスデータをソート
            this.services.sort((a, b) => {
                let comparison = 0;
                let valueA = a[this.sortBy];
                let valueB = b[this.sortBy];

                // 通知対象日の場合は日付として比較
                if (this.sortBy === 'notificationDate') {
                    // 不正な日付の場合の比較を考慮
                    const dateA = new Date(valueA).getTime();
                    const dateB = new Date(valueB).getTime();

                    if (isNaN(dateA) && isNaN(dateB)) comparison = 0; // 両方不正なら同じ扱い
                    else if (isNaN(dateA)) comparison = 1; // Aが不正ならBの方が大きい
                    else if (isNaN(dateB)) comparison = -1; // Bが不正ならAの方が大きい
                    else if (dateA > dateB) comparison = 1;
                    else if (dateA < dateB) comparison = -1;

                } else {
                    // 文字列の場合はロケールを考慮して比較
                    valueA = String(valueA).toLowerCase();
                    valueB = String(valueB).toLowerCase();


                    if (valueA > valueB) {
                        comparison = 1;
                    } else if (valueA < valueB) {
                        comparison = -1;
                    }
                }

                // 降順の場合は比較結果を反転
                return this.sortDirection === 'desc' ? comparison * -1 : comparison;
            });
        },

        // iCal URLをクリップボードにコピーする関数
        copyIcalUrl() {
            const urlElement = document.getElementById('ical-url');
            if (urlElement) {
                navigator.clipboard.writeText(urlElement.innerText)
                    .then(() => {
                        // alert の代わりにトーストを表示 (成功)
                        this.toastMessage = 'URLをコピーしました！'; // 成功メッセージをセット
                        this.toastType = 'success'; // タイプを success に
                        this.showToast = true; // トーストを表示状態に

                        // 3秒後にトーストを非表示にする
                        setTimeout(() => {
                            this.showToast = false;
                            this.toastMessage = ''; // メッセージもクリア
                            this.toastType = null; // タイプもクリア
                        }, 3000); // 3000ミリ秒 = 3秒
                    })
                    .catch(err => {
                        console.error('URLのコピーに失敗しました: ', err);
                        // alert の代わりにトーストを表示 (失敗)
                        this.toastMessage = 'URLのコピーに失敗しました。手動でコピーしてください。'; // 失敗メッセージをセット
                        this.toastType = 'error'; // タイプを error に
                        this.showToast = true; // トーストを表示状態に

                        // 5秒後にトーストを非表示にする (失敗は少し長く表示)
                        setTimeout(() => {
                            this.showToast = false;
                            this.toastMessage = ''; // メッセージもクリア
                            this.toastType = null; // タイプもクリア
                        }, 5000); // 5000ミリ秒 = 5秒
                    });
            }
        },

        // モーダルを開く関数
        // service オブジェクトを受け取り、編集モーダル用に編集対象として保持
        openModal(modalId, service = null) {
            if (modalId === '#add-modal') {
                this.resetAddForm(); // 新規登録モーダルを開く前にフォームをリセット
                this.showAddModal = true;
            } else if (modalId === '#edit-modal') {
                if (service) {
                    this.editingService = {...service}; // ディープコピーが必要な場合は修正
                    this.resetEditFormErrors(); // 編集モーダルを開く前にエラーをリセット
                    this.showEditModal = true;
                    console.log('編集モーダルを開きます', this.editingService);
                } else {
                    console.error('Service data not passed to openModal for edit');
                    // エラー処理など
                    this.toastMessage = 'サービスの編集に失敗しました。';
                    this.toastType = 'error';
                    this.showToast = true;
                    setTimeout(() => {
                        this.showToast = false;
                        this.toastMessage = '';
                        this.toastType = null;
                    }, 5000);
                }
            } else if (modalId === '#guide-modal') {
                this.showGuideModal = true;
            }
            // 削除確認モーダルは openDeleteConfirmModal で開くので、ここでは閉じます
            this.showDeleteConfirmModal = false;
            this.serviceToDelete = null;
        },

        // モーダルを閉じる関数 (削除確認モーダルの状態は変更しない)
        closeModals() {
            this.showAddModal = false;
            this.showEditModal = false;
            this.showGuideModal = false;
            // 編集中のサービスデータをクリア
            this.editingService = null; // これは編集モーダルを閉じる際に必要なので維持
            // showDeleteConfirmModal はこのメソッドでは変更しない
            // TODO: モーダルを閉じた後にフォームをリセットする処理などもここに追加
        },

        // !!! 今回の追加 !!! 削除確認モーダルを開く関数
        openDeleteConfirmModal(service) {
            // 編集モーダルを閉じる（重要：編集モーダルが閉じないと、その下の要素へのクリックが伝播してしまいます）
            this.closeModals();
            // 削除対象サービスをセット
            this.serviceToDelete = service;
            // DEBUG: 確認モーダルを開く直前
            console.log('削除確認モーダルを開く直前:', this.serviceToDelete);

            // モーダルが完全に閉じ、DOMが安定するのを少し待つ
            // タイミングの問題を回避するために少し遅延させる
            setTimeout(() => {
                this.showDeleteConfirmModal = true; // 削除確認モーダルを表示
                console.log('削除確認モーダルを開きます', this.serviceToDelete); // デバッグ用ログ
            }, 50); // 50ミリ秒の遅延（調整可能）
        },

        // !!! 今回の追加 !!! 削除確認をキャンセルする関数
        cancelDelete() {
            // 削除確認モーダルを閉じる
            this.showDeleteConfirmModal = false;
            // 削除対象サービスをクリア
            this.serviceToDelete = null;
            console.log('削除をキャンセルしました'); // デバッグ用ログ
            // 必要であれば編集モーダルを再度開く処理などをここに追加できますが、今回は閉じっぱなしにします。
        },

        // サービス削除処理 (モック)
        deleteService() {
            // 削除確認モーダルで「削除する」がクリックされた時に serviceToDelete を使用
            console.log('削除処理を実行', this.serviceToDelete);
            if (this.serviceToDelete) {
                // TODO: API連携してサービスを削除 (this.serviceToDelete.id を使用)
                // 例: this.services = this.services.filter(s => s.id !== this.serviceToDelete.id);

                // モックとしてservices配列から削除
                this.services = this.services.filter(s => s.id !== this.serviceToDelete.id);


                // 処理完了後に削除確認モーダルを閉じる
                this.showDeleteConfirmModal = false;
                this.serviceToDelete = null; // 削除対象をクリア

                // !!! 今回の追加 !!! 削除成功トースト表示
                this.toastMessage = 'サービスを削除しました！';
                this.toastType = 'success'; // 削除成功は success で良いでしょう
                this.showToast = true;
                setTimeout(() => {
                    this.showToast = false;
                    this.toastMessage = '';
                    this.toastType = null;
                }, 3000); // 3秒表示
            } else {
                console.error('削除対象サービスが指定されていません。'); // デバッグ用ログ
                // エラーメッセージを表示するなどの処理
                this.toastMessage = 'サービスの削除に失敗しました。';
                this.toastType = 'error';
                this.showToast = true;
                setTimeout(() => {
                    this.showToast = false;
                    this.toastMessage = '';
                    this.toastType = null;
                }, 5000); // 5秒表示
                this.showDeleteConfirmModal = false; // エラーでもモーダルは閉じる
            }
        },

        // サービス保存/更新処理 (モック)
        saveService() {
            // 編集フォームのバリデーションを実行
            if (!this.validateEditForm()) {
                console.log('編集フォームにバリデーションエラーがあります。');
                this.toastMessage = '入力内容に不備があります。ご確認ください。';
                this.toastType = 'error';
                this.showToast = true;
                setTimeout(() => {
                    this.showToast = false;
                    this.toastMessage = '';
                    this.toastType = null;
                }, 5000);
                return; // バリデーション失敗時は処理を中断
            }

            console.log('保存処理を実行', this.editingService);
            // TODO: API連携してデータを保存/更新
            // TODO: services 配列内の該当サービスを更新

            this.closeModals(); // 処理完了後に閉じる

            // 保存成功トースト表示
            this.toastMessage = 'サービスを保存しました！';
            this.toastType = 'success';
            this.showToast = true;
            setTimeout(() => {
                this.showToast = false;
                this.toastMessage = '';
                this.toastType = null;
            }, 3000); // 3秒表示
        },

        // サービス新規登録処理 (モック)
        addService() {
            // 新規登録フォームのバリデーションを実行
            if (!this.validateAddForm()) {
                console.log('新規登録フォームにバリデーションエラーがあります。');
                this.toastMessage = '入力内容に不備があります。ご確認ください。';
                this.toastType = 'error';
                this.showToast = true;
                setTimeout(() => {
                    this.showToast = false;
                    this.toastMessage = '';
                    this.toastType = null;
                }, 5000);
                return; // バリデーション失敗時は処理を中断
            }

            console.log('登録処理を実行');
            // TODO: フォームからデータを取得
            // TODO: API連携して新規サービスを登録
            // TODO: services 配列に新しいサービスを追加

            this.closeModals(); // 処理完了後に閉じる

            // 登録成功トースト表示
            this.toastMessage = '新しいサービスを追加しました！';
            this.toastType = 'success';
            this.showToast = true;
            setTimeout(() => {
                this.showToast = false;
                this.toastMessage = '';
                this.toastType = null;
            }, 3000); // 3秒表示
        },

    }
}

// serviceListPage 関数を 'serviceListPage' という名前で Alpine に登録
Alpine.data('serviceListPage', serviceListPage);

// Alpine を開始
// 通常は bootstrap.js またはこのファイルで一度だけ呼び出す
// プロジェクト設定に合わせてどちらか適切な方を使用してください
Alpine.start();
