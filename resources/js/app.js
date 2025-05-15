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
import {serviceFormLogic} from './forms/serviceForms';

// datetime.js から日付・ユーティリティ関数をインポート
import {getDaysRemaining, formatDate} from './utils/datetime';

// sorting.js からソートロジック関数をインポート
import {sortingLogic} from './utils/sorting';

// notification.js からトースト通知ロジック関数をインポート
import {notificationLogic} from './utils/notification';


// Alpine.js プラグインの登録
Alpine.plugin(intersect);


// x-data で使用するデータとメソッドを定義する関数
// x-data で使用するデータとメソッドを定義する関数
function serviceListPage() {
    console.log('serviceListPage function called, initializing data...');

    const forms = serviceFormLogic();
    const notification = notificationLogic();

    // fetchServices メソッドへの参照を作成
    // sortingLogic に渡すコールバックとして使用します
    // this のコンテキストを維持するためにアロー関数を使用
    const fetchServicesCallback = (page, sortBy, sortDirection) => this.fetchServices(page, sortBy, sortDirection);

    // sortingLogic に fetchServicesCallback を渡してソートロジックを取得
    const sorting = sortingLogic(fetchServicesCallback); // <<< sortingLogic にコールバック関数を渡す


    return {
        // === State properties ===
        ...forms,
        ...notification,
        ...sorting, // <<< sortingLogic から返されるプロパティとメソッドを展開して追加

        isDrawerOpen: false,

        showAddModal: false,
        showEditModal: false,
        showGuideModal: false,
        showDeleteConfirmModal: false,
        serviceToDelete: null,
        editingService: null,

        userIcalUrl: '',

        services: [],

        pagination: {
            current_page: 1, last_page: 1, total: 0, per_page: 15, links: [],
            first_page_url: null, last_page_url: null, prev_page_url: null, next_page_url: null,
        },

        // sortBy, sortDirection は sortingLogic に移動したので削除

        // === utils/datetime.js からインポートした関数をメソッドとして追加 ===
        getDaysRemaining: getDaysRemaining,
        formatDate: formatDate,
        // ==============================================================

        // データのロード状態
        isLoading: false,
        loadingMessage: 'データを読み込み中...',


        // === Methods ===

        // fetchServices メソッド内では、引数として受け取った sortBy/sortDirection を serviceApi.js に渡す
        async fetchServices(page = this.pagination.current_page, sortBy = this.sortBy, sortDirection = this.sortDirection) {
            if (this.isLoading) return;

            this.isLoading = true;
            this.loadingMessage = `サービスを読み込み中 (ページ ${page})...`;

            const url = new URL(window.location);
            url.searchParams.set('page', page);
            url.searchParams.set('sb', sortBy);
            url.searchParams.set('sd', sortDirection);

            history.pushState({}, '', url);

            try {
                const response = await fetchServicesApi(page, sortBy, sortDirection);

                this.services = response.data.map(service => {
                    return {
                        ...service,
                        notification_timing: parseInt(service.notification_timing, 10)
                    };
                });
                this.pagination = {
                    current_page: response.current_page,
                    last_page: response.last_page,
                    total: response.total,
                    per_page: response.per_page,
                    links: response.links,
                    first_page_url: response.first_page_url,
                    last_page_url: response.last_page_url,
                    next_page_url: response.next_page_url,
                    prev_page_url: response.prev_page_url,
                };

                // 成功時にソート状態を更新 (sortingLogic 側で状態を保持しているので不要になるはずだが、念のため)
                // sortingLogic の setInitialSort や sortServices メソッド内で状態が更新されることを想定
                // this.sortBy = sortBy; // << この行は不要になる可能性が高い
                // this.sortDirection = sortDirection; // << この行は不要になる可能性が高い

                console.log('サービス一覧とページネーション情報を正常に取得しました', this.services, this.pagination);

            } catch (error) {
                console.error('サービスの取得中にエラーが発生しました:', error);
                this.showToastNotification(error.message || 'サービスの取得中にエラーが発生しました。', 'error', 5000);
            } finally {
                this.isLoading = false;
                this.loadingMessage = 'データを読み込み中...';
            }
        },

        // ページを切り替えるメソッド
        goToPage(pageUrl) {
            if (!pageUrl || this.isLoading) {
                return;
            }
            const url = new URL(pageUrl);
            const page = url.searchParams.get('page');

            if (page) {
                console.log('ページ切り替え:', page);
                // ページ番号、現在のソート設定を渡して fetchServices を呼び出す
                // ソート状態は sortingLogic から取得
                this.fetchServices(parseInt(page, 10), this.sortBy, this.sortDirection); // this.sortBy, this.sortDirection は sortingLogic から来たプロパティ
            } else {
                console.warn('ページURLからページ番号を抽出できませんでした:', pageUrl);
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

        // ソート処理を行う関数 (定義は sorting.js にあります)
        // sortServices: function(...) { ... }, // ここに定義があったが、sortingLogic に移動した


        // iCal URLをクリップボードにコピーする関数
        copyIcalUrl() {
            const urlElement = document.getElementById('ical-url');
            if (urlElement && this.userIcalUrl && this.userIcalUrl !== 'ログインすると表示されます。' && this.userIcalUrl !== 'エラーにより取得できませんでした。') {
                navigator.clipboard.writeText(urlElement.innerText)
                    .then(() => {
                        this.showToastNotification('URLをコピーしました！', 'success', 3000);
                    })
                    .catch(err => {
                        console.error('URLのコピーに失敗しました: ', err);
                        this.showToastNotification('URLのコピーに失敗しました。手動でコピーしてください。', 'error', 5000);
                    });
            } else {
                console.warn('コピーするiCal URLがありません。');
                this.showToastNotification('コピーできるiCal URLがありません。', 'error', 3000);
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
                    this.resetAddForm();
                    this.showAddModal = true;
                    console.log('新規登録モーダルを開きます');
                } else if (modalId === '#edit-modal') {
                    if (service) {
                        this.editingService = {...service};
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
                        if (typeof this.editingService.notification_timing !== 'string') {
                            this.editingService.notification_timing = String(this.editingService.notification_timing);
                        }
                        this.resetEditFormErrors();
                        this.showEditModal = true;
                        console.log('編集モーダルを開きます', this.editingService);
                    } else {
                        console.error('Service data not passed to openModal for edit');
                        this.showToastNotification('サービスの編集に失敗しました。', 'error', 5000);
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
            console.log('削除確認モーdalを開きます', this.serviceToDelete);
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
            if (!this.validateAddForm()) {
                console.log('新規登録フォームにバリデーションエラーがあります。');
                this.showToastNotification('入力内容に不備があります。ご確認ください。', 'error', 5000);
                return;
            }

            console.log('登録処理を実行');
            this.isLoading = true;
            this.loadingMessage = 'サービスを登録中...';

            try {
                const newService = await addServiceApi(this.addModalForm);
                console.log('新しいサービスを正常に登録しました', newService);

                // 登録後、サービス一覧を再取得 (現在のページ・ソート設定で)
                // ソート状態は sortingLogic から取得
                await this.fetchServices(this.pagination.current_page, this.sortBy, this.sortDirection); // this.sortBy, this.sortDirection は sortingLogic から来たプロパティ

                this.closeModals();
                this.showToastNotification('新しいサービスを追加しました！', 'success', 3000);

            } catch (error) {
                console.error('サービスの登録中にエラーが発生しました:', error);
                this.showToastNotification(error.message || 'サービスの登録中にエラーが発生しました。', 'error', 5000);
            } finally {
                this.isLoading = false;
                this.loadingMessage = 'データを読み込み中...';
            }
        },

        // サービス保存/更新処理
        async saveService() {
            if (!this.editingService || !this.editingService.id) {
                console.error('編集対象サービスが指定されていません。');
                this.showToastNotification('編集対象サービスが見つかりません。', 'error', 5000);
                this.closeModals();
                return;
            }

            if (!this.validateEditForm(this.editingService)) {
                console.log('編集フォームにバリデーションエラーがあります。');
                this.showToastNotification('入力内容に不備があります。ご確認ください。', 'error', 5000);
                return;
            }

            console.log('保存処理を実行');
            this.isLoading = true;
            this.loadingMessage = 'サービスを保存中...';

            try {
                const updatedService = await saveServiceApi(this.editingService.id, this.editingService);
                console.log('サービスを正常に保存しました', updatedService);

                // 更新後、サービス一覧を再取得 (現在のページ・ソート設定で)
                // ソート状態は sortingLogic から取得
                await this.fetchServices(this.pagination.current_page, this.sortBy, this.sortDirection); // this.sortBy, this.sortDirection は sortingLogic から来たプロパティ

                this.closeModals();
                this.showToastNotification('サービスを保存しました！', 'success', 3000);

            } catch (error) {
                console.error('サービスの保存中にエラーが発生しました:', error);
                this.showToastNotification(error.message || 'サービスの保存中にエラーが発生しました。', 'error', 5000);
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
                this.showToastNotification('削除対象サービスが見つかりません。', 'error', 5000);
                this.showDeleteConfirmModal = false;
                this.serviceToDelete = null;
                return;
            }

            this.isLoading = true;
            this.loadingMessage = 'サービスを削除中...';

            try {
                await deleteServiceApi(this.serviceToDelete.id);
                console.log('サービスを正常に削除しました', this.serviceToDelete.id);

                // 削除後、サービス一覧を再取得 (現在のページ・ソート設定で)
                // ソート状態は sortingLogic から取得
                await this.fetchServices(this.pagination.current_page, this.sortBy, this.sortDirection); // this.sortBy, this.sortDirection は sortingLogic から来たプロパティ

                this.showDeleteConfirmModal = false;
                this.serviceToDelete = null;

                this.showToastNotification('サービスを削除しました！', 'success', 3000);

            } catch (error) {
                console.error('サービスの削除中にエラーが発生しました:', error);
                this.showToastNotification(error.message || 'サービスの削除中にエラーが発生しました。', 'error', 5000);
            } finally {
                this.isLoading = false;
                this.loadingMessage = 'データを読み込み中...';
            }
        },


        // ページの初期化処理 (URLパラメータから状態を読み込む)
        init() {
            console.log('Alpine component initialized');

            const urlParams = new URLSearchParams(window.location.search);
            const initialPage = parseInt(urlParams.get('page') || '1', 10);
            const initialSortBy = urlParams.get('sb') || 'notification_date';
            const initialSortDirection = urlParams.get('sd') || 'asc';

            console.log('Initial params from URL:', {
                page: initialPage,
                sortBy: initialSortBy,
                sortDirection: initialSortDirection
            });

            // ソートの状態を初期化
            this.setInitialSort(initialSortBy, initialSortDirection); // <<< sortingLogic から取得したメソッドを呼び出す

            const initialUrl = new URL(window.location.origin + window.location.pathname);
            initialUrl.searchParams.set('page', initialPage);
            initialUrl.searchParams.set('sb', initialSortBy);
            initialUrl.searchParams.set('sd', initialSortDirection);
            history.replaceState({}, '', initialUrl);

            // 初期読み込み時に fetchServices を呼び出す
            // ソート状態は sortingLogic から取得
            this.fetchServices(initialPage, initialSortBy, initialSortDirection);

            this.fetchAuthenticatedUser();

            window.addEventListener('popstate', () => {
                console.log('Popstate event triggered.');
                const currentUrlParams = new URLSearchParams(window.location.search);
                const currentPage = parseInt(currentUrlParams.get('page') || '1', 10);
                const currentSortBy = currentUrlParams.get('sb') || 'notification_date';
                const currentSortDirection = currentUrlParams.get('sd') || 'asc';

                // 現在のAlpineの状態とURLの状態が異なる場合のみfetchServicesを呼び出す
                // ソート状態は sortingLogic から取得
                if (this.pagination.current_page !== currentPage ||
                    this.sortBy !== currentSortBy || // this.sortBy は sortingLogic から来たプロパティ
                    this.sortDirection !== currentSortDirection) { // this.sortDirection は sortingLogic から来たプロパティ
                    console.log('State mismatch detected, refetching services.');
                    // ソート状態を更新 (sortingLogic から取得したメソッドを呼び出す)
                    this.setInitialSort(currentSortBy, currentSortDirection);
                    // ページ番号と新しいソート設定を渡して再取得
                    this.fetchServices(currentPage, currentSortBy, currentSortDirection);
                } else {
                    console.log('Popstate event, but state matches URL. No refetch needed.');
                }
            });
        },
    }
}

// serviceListPage 関数を 'serviceListPage' という名前で Alpine に登録
Alpine.data('serviceListPage', serviceListPage);

// Alpine を開始
Alpine.start();
