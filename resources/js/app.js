// resources/js/app.js

// Alpine.js のインポート
import Alpine from 'alpinejs';
import intersect from '@alpinejs/intersect'; // 例: Intersect プラグイン

// serviceApi.js から API 呼び出し関数をインポート
import {
    fetchServicesApi,
    fetchAuthenticatedUserApi,
    addServiceApi,
    saveServiceApi,
    deleteServiceApi
} from './api/serviceApi';

// serviceForms.js からフォームロジック関数をインポート
import { serviceFormLogic } from './forms/serviceForms';


// Alpine.js プラグインの登録
Alpine.plugin(intersect);


// x-data で使用するデータとメソッドを定義する関数
function serviceListPage() {
    // Debug: この関数が呼び出されているか確認
    console.log('serviceListPage function called, initializing data...');

    // serviceForms.js からフォームの状態とメソッドを取得
    const forms = serviceFormLogic();

    return {
        // === State properties ===
        // フォーム関連の状態は forms オブジェクトに含まれる
        ...forms, // <<< serviceFormLogic から返されるプロパティとメソッドを展開して追加

        isDrawerOpen: false,

        showAddModal: false,
        showEditModal: false,
        showGuideModal: false,
        // 削除確認モーダルの状態と削除対象サービス
        showDeleteConfirmModal: false,
        serviceToDelete: null, // 削除対象のサービスデータを保持
        // editingService は openModal で設定され、API呼び出しやバリデーションで使用するため app.js に残す
        editingService: null,

        // トースト通知の状態とメッセージ
        showToast: false,
        toastMessage: '',
        toastType: null,

        // iCalフィード関連の状態プロパティ
        userIcalUrl: '',

        services: [], // 初期状態は空にする

        // ソートの状態
        sortBy: 'notification_date',
        sortDirection: 'asc',

        // データのロード状態
        isLoading: false,
        loadingMessage: 'データを読み込み中...',


        // === Methods ===

        // サービスのデータをバックエンドから取得するメソッド
        async fetchServices() {
            this.isLoading = true;
            this.loadingMessage = 'サービスを読み込み中...';
            try {
                const fetchedServices = await fetchServicesApi();

                this.services = fetchedServices.map(service => {
                    return {
                        ...service,
                        notification_timing: parseInt(service.notification_timing, 10)
                    };
                });
                console.log('サービス一覧を正常に取得しました', this.services);

                // 取得後にフロントエンドでソートを適用
                this.sortServices(this.sortBy, false);

            } catch (error) {
                console.error('サービスの取得中にエラーが発生しました:', error);
                this.toastMessage = error.message || 'サービスの取得中にエラーが発生しました。';
                this.toastType = 'error';
                this.showToast = true;
                setTimeout(() => {
                    this.showToast = false;
                    this.toastMessage = '';
                    this.toastType = null;
                }, 5000);
            } finally {
                this.isLoading = false;
                this.loadingMessage = 'データを読み込み中...';
            }
        },

        // 認証済みユーザーの情報を取得するメソッド
        async fetchAuthenticatedUser() {
            try {
                const userData = await fetchAuthenticatedUserApi();

                if (userData && userData.icalFeedUrl) {
                    this.userIcalUrl = userData.icalFeedUrl;
                    console.log('iCal URL:', this.userIcalUrl);
                } else {
                    console.warn('Authenticated user or iCal feed URL not found.');
                    this.userIcalUrl = 'ログインすると表示されます。';
                }

            } catch (error) {
                console.error('認証ユーザーまたはiCalフィードURLの取得中にエラーが発生しました:', error);
                this.userIcalUrl = 'エラーにより取得できませんでした。';
            }
        },

        // 通知対象日の残り日数を計算する関数 (utils.js に移動することも検討可能)
        getDaysRemaining(dateString) {
            if (!dateString || dateString.trim() === '') {
                return Infinity;
            }
            const notificationDate = new Date(dateString);
            if (isNaN(notificationDate.getTime())) {
                return Infinity;
            }

            const today = new Date();
            notificationDate.setHours(0, 0, 0, 0);
            today.setHours(0, 0, 0, 0);

            const timeDiff = notificationDate.getTime() - today.getTime();
            const daysDiff = Math.ceil(timeDiff / (1000 * 3600 * 24));

            return daysDiff;
        },

        // 日付をYYYY/MM/DD 形式にフォーマットする関数 (utils.js に移動することも検討可能)
        formatDate(dateString) {
            if (!dateString || dateString.trim() === '') {
                return 'N/A';
            }
            const date = new Date(dateString);
            if (isNaN(date.getTime())) {
                return 'Invalid Date';
            }

            const year = date.getFullYear();
            const month = (date.getMonth() + 1).toString().padStart(2, '0');
            const day = date.getDate().toString().padStart(2, '0');
            return `${year}/${month}/${day}`;
        },

        // ソート処理を行う関数 (このロジックも services.js のようなファイルに移動することも検討可能)
        sortServices(key, toggleDirection = true) {
            if (this.sortBy === key) {
                if (toggleDirection) {
                    this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
                }
            } else {
                this.sortBy = key;
                this.sortDirection = 'asc';
            }

            this.services.sort((a, b) => {
                let comparison = 0;
                let valueA = a[this.sortBy];
                let valueB = b[this.sortBy];

                if (valueA == null && valueB == null) return 0;
                if (valueA == null) return this.sortDirection === 'asc' ? -1 : 1;
                if (valueB == null) return this.sortDirection === 'asc' ? 1 : -1;


                if (this.sortBy === 'notification_date') {
                    const dateA = new Date(valueA).getTime();
                    const dateB = new Date(valueB).getTime();

                    if (isNaN(dateA) && isNaN(dateB)) comparison = 0;
                    else if (isNaN(dateA)) comparison = this.sortDirection === 'asc' ? -1 : 1;
                    else if (isNaN(dateB)) comparison = this.sortDirection === 'asc' ? 1 : -1;
                    else if (dateA > dateB) comparison = 1;
                    else if (dateA < dateB) comparison = -1;

                } else if (this.sortBy === 'notification_timing') {
                    const timingA = parseInt(valueA, 10);
                    const timingB = parseInt(valueB, 10);

                    if (timingA > timingB) comparison = 1;
                    else if (timingA < timingB) comparison = -1;
                    else comparison = 0;

                } else {
                    valueA = String(valueA).toLowerCase();
                    valueB = String(valueB).toLowerCase();
                    if (valueA > valueB) {
                        comparison = 1;
                    } else if (valueA < valueB) {
                        comparison = -1;
                    } else {
                        comparison = 0;
                    }
                }

                return this.sortDirection === 'desc' ? comparison * -1 : comparison;
            });
        },

        // iCal URLをクリップボードにコピーする関数 (これも utils.js に移動することも検討可能)
        copyIcalUrl() {
            const urlElement = document.getElementById('ical-url');
            if (urlElement && this.userIcalUrl && this.userIcalUrl !== 'ログインすると表示されます。' && this.userIcalUrl !== 'エラーにより取得できませんでした。') {
                navigator.clipboard.writeText(urlElement.innerText)
                    .then(() => {
                        this.toastMessage = 'URLをコピーしました！';
                        this.toastType = 'success';
                        this.showToast = true;
                        setTimeout(() => {
                            this.showToast = false;
                            this.toastMessage = '';
                            this.toastType = null;
                        }, 3000);
                    })
                    .catch(err => {
                        console.error('URLのコピーに失敗しました: ', err);
                        this.toastMessage = 'URLのコピーに失敗しました。手動でコピーしてください。';
                        this.toastType = 'error';
                        this.showToast = true;
                        setTimeout(() => {
                            this.showToast = false;
                            this.toastMessage = '';
                            this.toastType = null;
                        }, 5000);
                    });
            } else {
                console.warn('コピーするiCal URLがありません。');
                this.toastMessage = 'コピーできるiCal URLがありません。';
                this.toastType = 'error';
                this.showToast = true;
                setTimeout(() => {
                    this.showToast = false;
                    this.toastMessage = '';
                    this.toastType = null;
                }, 3000);
            }
        },

        // モーダルを開く関数
        openModal(modalId, service = null) {
            console.log('openModal called with:', modalId, service);

            if (this.showDeleteConfirmModal) {
                this.showDeleteConfirmModal = false;
                this.serviceToDelete = null;
            }
            setTimeout(() => {
                if (modalId === '#add-modal') {
                    this.resetAddForm(); // serviceForms から取得したメソッド
                    this.showAddModal = true;
                    console.log('新規登録モーダルを開きます');
                } else if (modalId === '#edit-modal') {
                    if (service) {
                        this.editingService = {...service};
                        // notification_date をYYYY-MM-DD 形式に変換
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
                        // notification_timing が数値でない場合は文字列に変換
                        if (typeof this.editingService.notification_timing !== 'string') {
                            this.editingService.notification_timing = String(this.editingService.notification_timing);
                        }

                        this.resetEditFormErrors(); // serviceForms から取得したメソッド
                        this.showEditModal = true;
                        console.log('編集モーダルを開きます', this.editingService);
                    } else {
                        console.error('Service data not passed to openModal for edit');
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
                    console.log('設定ガイドモーダルを開きます');
                } else if (modalId === '#delete-confirm-modal') {
                    console.warn('Attempted to open delete confirm modal directly with openModal.');
                }
            }, this.showDeleteConfirmModal ? 50 : 0);
        },

        // モーダルを閉じる関数
        closeModals() {
            this.showAddModal = false;
            this.showEditModal = false;
            this.showGuideModal = false;
            this.editingService = null;
            console.log('全てのモーダルを閉じました (削除確認除く)');
        },


        // 削除確認モーダルを開く関数
        openDeleteConfirmModal(service) {
            console.log('openDeleteConfirmModal called with service:', service);

            if (this.showEditModal) {
                this.showEditModal = false;
            }
            if (this.showAddModal) {
                this.showAddModal = false;
            }
            if (this.showGuideModal) {
                this.showGuideModal = false;
            }

            this.serviceToDelete = service;
            console.log('削除確認モーダルを開きます', this.serviceToDelete);

            setTimeout(() => {
                this.showDeleteConfirmModal = true;
                console.log('showDeleteConfirmModal is now true');
            }, 50);
        },

        // 削除確認をキャンセルする関数
        cancelDelete() {
            this.showDeleteConfirmModal = false;
            this.serviceToDelete = null;
            console.log('削除をキャンセルしました');
        },


        // サービス新規登録処理
        async addService() {
            // serviceForms から取得したメソッドでバリデーションを実行
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
                return;
            }

            console.log('登録処理を実行');

            this.isLoading = true;
            this.loadingMessage = 'サービスを登録中...';

            try {
                // serviceApi.js からインポートした関数を呼び出す
                const newService = await addServiceApi(this.addModalForm);
                console.log('新しいサービスを正常に登録しました', newService);

                await this.fetchServices(); // サービス一覧を再取得

                this.closeModals(); // 新規登録モーダルを閉じる
                this.toastMessage = '新しいサービスを追加しました！';
                this.toastType = 'success';
                this.showToast = true;
                setTimeout(() => {
                    this.showToast = false;
                    this.toastMessage = '';
                    this.toastType = null;
                }, 3000);

            } catch (error) {
                console.error('サービスの登録中にエラーが発生しました:', error);
                this.toastMessage = error.message || 'サービスの登録中にエラーが発生しました。';
                this.toastType = 'error';
                this.showToast = true;
                setTimeout(() => {
                    this.showToast = false;
                    this.toastMessage = '';
                    this.toastType = null;
                }, 5000);
            } finally {
                this.isLoading = false;
                this.loadingMessage = 'データを読み込み中...';
            }
        },

        // サービス保存/更新処理
        async saveService() {
            if (!this.editingService || !this.editingService.id) {
                console.error('編集対象サービスが指定されていません。');
                this.toastMessage = '編集対象サービスが見つかりません。';
                this.toastType = 'error';
                this.showToast = true;
                setTimeout(() => {
                    this.showToast = false;
                    this.toastMessage = '';
                    this.toastType = null;
                }, 5000);
                this.closeModals();
                return;
            }

            // serviceForms から取得したメソッドでバリデーションを実行
            if (!this.validateEditForm(this.editingService)) { // editingService を引数として渡す
                console.log('編集フォームにバリデーションエラーがあります。');
                this.toastMessage = '入力内容に不備があります。ご確認ください。';
                this.toastType = 'error';
                this.showToast = true;
                setTimeout(() => {
                    this.showToast = false;
                    this.toastMessage = '';
                    this.toastType = null;
                }, 5000);
                return;
            }

            console.log('保存処理を実行');

            this.isLoading = true;
            this.loadingMessage = 'サービスを保存中...';

            try {
                // serviceApi.js からインポートした関数を呼び出す
                const updatedService = await saveServiceApi(this.editingService.id, this.editingService);
                console.log('サービスを正常に保存しました', updatedService);

                await this.fetchServices(); // サービス一覧を再取得

                this.closeModals(); // 編集モーダルを閉じる
                this.toastMessage = 'サービスを保存しました！';
                this.toastType = 'success';
                this.showToast = true;
                setTimeout(() => {
                    this.showToast = false;
                    this.toastMessage = '';
                    this.toastType = null;
                }, 3000);

            } catch (error) {
                console.error('サービスの保存中にエラーが発生しました:', error);
                this.toastMessage = error.message || 'サービスの保存中にエラーが発生しました。';
                this.toastType = 'error';
                this.showToast = true;
                setTimeout(() => {
                    this.showToast = false;
                    this.toastMessage = '';
                    this.toastType = null;
                }, 5000);
            } finally {
                this.isLoading = false;
                this.loadingMessage = 'データを読み込み中...';
            }
        },

        // サービス削除処理
        async deleteService() {
            console.log('削除処理を実行', this.serviceToDelete);
            if (!this.serviceToDelete || !this.serviceToDelete.id) {
                console.error('削除対象サービスが指定されていません。');
                this.toastMessage = '削除対象サービスが見つかりません。';
                this.toastType = 'error';
                this.showToast = true;
                setTimeout(() => {
                    this.showToast = false;
                    this.toastMessage = '';
                    this.toastType = null;
                }, 5000);
                this.showDeleteConfirmModal = false;
                this.serviceToDelete = null;
                return;
            }

            this.isLoading = true;
            this.loadingMessage = 'サービスを削除中...';

            try {
                // serviceApi.js からインポートした関数を呼び出す
                await deleteServiceApi(this.serviceToDelete.id);
                console.log('サービスを正常に削除しました', this.serviceToDelete.id);

                await this.fetchServices(); // サービス一覧を再取得

                this.showDeleteConfirmModal = false;
                this.serviceToDelete = null;

                this.toastMessage = 'サービスを削除しました！';
                this.toastType = 'success';
                this.showToast = true;
                setTimeout(() => {
                    this.showToast = false;
                    this.toastMessage = '';
                    this.toastType = null;
                }, 3000);

            } catch (error) {
                console.error('サービスの削除中にエラーが発生しました:', error);
                this.toastMessage = error.message || 'サービスの削除中にエラーが発生しました。';
                this.toastType = 'error';
                this.showToast = true;
                setTimeout(() => {
                    this.showToast = false;
                    this.toastMessage = '';
                    this.toastType = null;
                }, 5000);
                this.showDeleteConfirmModal = false;
                this.serviceToDelete = null;
            } finally {
                this.isLoading = false;
                this.loadingMessage = 'データを読み込み中...';
            }
        },


        // ページの初期化処理としてサービス取得を呼び出す
        init() {
            console.log('Alpine component initialized');
            this.fetchServices();
            this.fetchAuthenticatedUser();
        },
    }
}

// serviceListPage 関数を 'serviceListPage' という名前で Alpine に登録
Alpine.data('serviceListPage', serviceListPage);

// Alpine を開始
// 通常は bootstrap.js またはこのファイルで一度だけ呼び出す
Alpine.start();
