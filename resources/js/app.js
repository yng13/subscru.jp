// resources/js/app.js

// Alpine.js のインポート
import Alpine from 'alpinejs';
import intersect from '@alpinejs/intersect';

// API 呼び出し関数をインポート
import {
    fetchServicesApi,
    fetchAuthenticatedUserApi, // fetchAuthenticatedUserApi も使用するためインポートを維持
    addServiceApi,
    saveServiceApi,
    deleteServiceApi
} from './api/serviceApi';

// フォームロジック関数をインポート
import {serviceFormLogic} from './forms/serviceForms';

// 日付・ユーティリティ関数をインポート
import {getDaysRemaining, formatDate} from './utils/datetime';

// ソートロジック関数をインポート
import {sortingLogic} from './utils/sorting';

// トースト通知ロジック関数をインポート
import {notificationLogic} from './utils/notification';

// === 新しく作成した modalLogic と icalLogic をインポート ===
import {modalLogic} from './utils/modal';
import {icalLogic} from './utils/ical';
// ======================================================


// Alpine.js プラグインの登録
Alpine.plugin(intersect);


// x-data で使用するデータとメソッドを定義する関数
function serviceListPage() {
    console.log('serviceListPage function called, initializing data...');

    // 各ロジックオブジェクトを生成
    const forms = serviceFormLogic();
    const notification = notificationLogic();
    const sorting = sortingLogic(); // sortingLogic はもう fetchServicesCallback を受け取らない
    const modal = modalLogic(); // === modalLogic を生成 ===
    const ical = icalLogic(); // === icalLogic を生成 ===


    return {
        // === 各ロジックオブジェクトのプロパティとメソッドを展開して統合 ===
        ...forms,
        ...notification,
        ...sorting,
        ...modal, // modalLogic のプロパティとメソッドを追加
        ...ical, // icalLogic のプロパティとメソッドを追加
        // ===============================================================

        isDrawerOpen: false,

        // modalLogic に移動したので削除
        // showAddModal: false,
        // showEditModal: false,
        // showGuideModal: false,
        // showDeleteConfirmModal: false,
        // serviceToDelete: null,
        // editingService: null,

        // icalLogic に移動したので削除
        // userIcalUrl: '',

        services: [],

        pagination: {
            current_page: 1, last_page: 1, total: 0, per_page: 15, links: [],
            first_page_url: null, last_page_url: null, prev_page_url: null, next_page_url: null,
        },

        searchTerm: '',

        // utils/datetime.js からインポートした関数はそのままメソッドとして残す
        getDaysRemaining: getDaysRemaining,
        formatDate: formatDate,


        // === Methods ===

        // fetchServices メソッドは app.js に残し、他のロジックから呼び出される中心的な役割を持つ
        // searchTerm 引数を維持
        async fetchServices(page = this.pagination.current_page, sortBy = this.sortBy, sortDirection = this.sortDirection, searchTerm = this.searchTerm) {
            if (this.isLoading) return;

            this.isLoading = true;
            this.loadingMessage = `サービスを読み込み中 (ページ ${page})...`;

            const url = new URL(window.location);
            url.searchParams.set('page', page);
            url.searchParams.set('sb', sortBy);
            url.searchParams.set('sd', sortDirection);
            if (searchTerm) {
                url.searchParams.set('q', searchTerm);
            } else {
                url.searchParams.delete('q');
            }
            history.pushState({}, '', url);

            try {
                // API呼び出し時に searchTerm を渡す (serviceApi.js は既に修正済み)
                const response = await fetchServicesApi(page, sortBy, sortDirection, searchTerm);

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

                console.log('サービス一覧とページネーション情報を正常に取得しました', this.services, this.pagination);

            } catch (error) {
                console.error('サービスの取得中にエラーが発生しました:', error);
                // notificationLogic の showToastNotification を呼び出し
                this.showToastNotification(error.message || 'サービスの取得中にエラーが発生しました。', 'error', 5000);
            } finally {
                this.isLoading = false;
                this.loadingMessage = 'データを読み込み中...';
            }
        },

        // ページを切り替えるメソッド (searchTerm を維持)
        goToPage(pageUrl) {
            if (!pageUrl || this.isLoading) {
                return;
            }
            const url = new URL(pageUrl);
            const page = url.searchParams.get('page');

            if (page) {
                console.log('ページ切り替え:', page);
                // 現在のソート設定と検索キーワードを渡して fetchServices を呼び出す
                this.fetchServices(parseInt(page, 10), this.sortBy, this.sortDirection, this.searchTerm);
            } else {
                console.warn('ページURLからページ番号を抽出できませんでした:', pageUrl);
            }
        },

        // 認証済みユーザーの情報を取得するメソッド (app.js に維持)
        async fetchAuthenticatedUser() {
            console.log('app.js: fetchAuthenticatedUser called');
            try {
                const userData = await fetchAuthenticatedUserApi();
                if (userData && userData.icalFeedUrl) {
                    // icalLogic の setIcalUrl メソッドを呼び出して userIcalUrl を更新
                    this.setIcalUrl(userData.icalFeedUrl); // this は app.js の data オブジェクト全体を指す
                    console.log('app.js: iCal URL:', this.userIcalUrl); // 更新された値を確認
                } else {
                    console.warn('app.js: Authenticated user or iCal feed URL not found.');
                    this.setIcalUrl('ログインすると表示されます。');
                }
            } catch (error) {
                console.error('app.js: 認証ユーザーまたはiCalフィードURLの取得中にエラーが発生しました:', error);
                this.setIcalUrl('エラーにより取得できませんでした。');
                // notificationLogic の showToastNotification を呼び出し
                this.showToastNotification('カレンダーURLの取得中にエラーが発生しました。', 'error', 5000);
            }
        },


        // iCal URLをクリップボードにコピーする関数は icalLogic に移動したので削除
        // copyIcalUrl: function(...) { ... },

        // モーダルを開く関数は modalLogic に移動したので削除
        // openModal: function(...) { ... },

        // モーダルを閉じる関数は modalLogic に移動したので削除
        // closeModals: function(...) { ... },

        // 削除確認モーダルを開く関数は modalLogic に移動したので削除
        // openDeleteConfirmModal: function(...) { ... },

        // 削除確認をキャンセルする関数は modalLogic に移動したので削除
        // cancelDelete: function(...) { ... },


        // サービス新規登録処理
        async addService() {
            // バリデーションは serviceFormLogic のメソッドを呼び出し
            if (!this.validateAddForm()) { // forms.validateAddForm() ではなく this.validateAddForm() で呼び出し
                console.log('新規登録フォームにバリデーションエラーがあります。');
                // notificationLogic の showToastNotification を呼び出し
                this.showToastNotification('入力内容に不備があります。ご確認ください。', 'error', 5000);
                return;
            }

            console.log('登録処理を実行');
            this.isLoading = true;
            this.loadingMessage = 'サービスを登録中...';

            try {
                // API呼び出しは serviceApi.js の関数を呼び出し
                const newService = await addServiceApi(this.addModalForm); // forms.addModalForm ではなく this.addModalForm を渡す
                console.log('新しいサービスを正常に登録しました', newService);

                // 登録後、サービス一覧を再取得 (現在のページ・ソート設定、現在の検索キーワードで)
                await this.fetchServices(this.pagination.current_page, this.sortBy, this.sortDirection, this.searchTerm);

                // modalLogic の closeAddModalOnSuccess を呼び出し
                this.closeAddModalOnSuccess(); // modal.closeAddModalOnSuccess() ではなく this.closeAddModalOnSuccess() で呼び出し
                // notificationLogic の showToastNotification を呼び出し
                this.showToastNotification('新しいサービスを追加しました！', 'success', 3000);

            } catch (error) {
                console.error('サービスの登録中にエラーが発生しました:', error);
                // notificationLogic の showToastNotification を呼び出し
                this.showToastNotification(error.message || 'サービスの登録中にエラーが発生しました。', 'error', 5000);
            } finally {
                this.isLoading = false;
                this.loadingMessage = 'データを読み込み中...';
            }
        },

        // サービス保存/更新処理
        async saveService() {
            // editingService は modalLogic から来ているが、app.js の state で保持されているので this.editingService でアクセス
            if (!this.editingService || !this.editingService.id) {
                console.error('編集対象サービスが指定されていません。');
                // notificationLogic の showToastNotification を呼び出し
                this.showToastNotification('編集対象サービスが見つかりません。', 'error', 5000);
                // modalLogic の closeModals を呼び出し
                this.closeModals(); // modal.closeModals() ではなく this.closeModals() で呼び出し
                return;
            }

            // バリデーションは serviceFormLogic のメソッドを呼び出し (編集用)
            if (!this.validateEditForm(this.editingService)) { // forms.validateEditForm(...) ではなく this.validateEditForm(...) で呼び出し
                console.log('編集フォームにバリデーションエラーがあります。');
                // notificationLogic の showToastNotification を呼び出し
                this.showToastNotification('入力内容に不備があります。ご確認ください。', 'error', 5000);
                return;
            }

            console.log('保存処理を実行');
            this.isLoading = true;
            this.loadingMessage = 'サービスを保存中...';

            try {
                // API呼び出しは serviceApi.js の関数を呼び出し
                const updatedService = await saveServiceApi(this.editingService.id, this.editingService); // this.editingService を渡す
                console.log('サービスを正常に保存しました', updatedService);

                // 更新後、サービス一覧を再取得 (現在のページ・ソート設定、現在の検索キーワードで)
                await this.fetchServices(this.pagination.current_page, this.sortBy, this.sortDirection, this.searchTerm);

                // modalLogic の closeEditModalOnSuccess を呼び出し
                this.closeEditModalOnSuccess(); // modal.closeEditModalOnSuccess() ではなく this.closeEditModalOnSuccess() で呼び出し
                // notificationLogic の showToastNotification を呼び出し
                this.showToastNotification('サービスを保存しました！', 'success', 3000);

            } catch (error) {
                console.error('サービスの保存中にエラーが発生しました:', error);
                // notificationLogic の showToastNotification を呼び出し
                this.showToastNotification(error.message || 'サービスの保存中にエラーが発生しました。', 'error', 5000);
            } finally {
                this.isLoading = false;
                this.loadingMessage = 'データを読み込み中...';
            }
        },

        // サービス削除処理
        async deleteService() {
            // serviceToDelete は modalLogic から来ているが、app.js の state で保持されているので this.serviceToDelete でアクセス
            console.log('削除処理を実行', this.serviceToDelete);
            if (!this.serviceToDelete || !this.serviceToDelete.id) {
                console.error('削除対象サービスが指定されていません。');
                // notificationLogic の showToastNotification を呼び出し
                this.showToastNotification('削除対象サービスが見つかりません。', 'error', 5000);
                // modalLogic の closeDeleteConfirmModalOnSuccess を呼び出し
                this.closeDeleteConfirmModalOnSuccess(); // modal.closeDeleteConfirmModalOnSuccess() ではなく this.closeDeleteConfirmModalOnSuccess() で呼び出し
                return;
            }

            this.isLoading = true;
            this.loadingMessage = 'サービスを削除中...';

            try {
                // API呼び出しは serviceApi.js の関数を呼び出し
                await deleteServiceApi(this.serviceToDelete.id);
                console.log('サービスを正常に削除しました', this.serviceToDelete.id);

                // 削除後、サービス一覧を再取得 (1ページ目、現在のソート設定、現在の検索キーワードで)
                await this.fetchServices(1, this.sortBy, this.sortDirection, this.searchTerm);

                // modalLogic の closeDeleteConfirmModalOnSuccess を呼び出し
                this.closeDeleteConfirmModalOnSuccess(); // modal.closeDeleteConfirmModalOnSuccess() ではなく this.closeDeleteConfirmModalOnSuccess() で呼び出し

                // notificationLogic の showToastNotification を呼び出し
                this.showToastNotification('サービスを削除しました！', 'success', 3000);

            } catch (error) {
                console.error('サービスの削除中にエラーが発生しました:', error);
                // notificationLogic の showToastNotification を呼び出し
                this.showToastNotification(error.message || 'サービスの削除中にエラーが発生しました。', 'error', 5000);
            } finally {
                this.isLoading = false;
                this.loadingMessage = 'データを読み込み中...';
            }
        },


        // ページの初期化処理 (URLパラメータから状態を読み込む) は app.js に維持
        init() {
            console.log('Alpine component initialized');

            const urlParams = new URLSearchParams(window.location.search);
            const initialPage = parseInt(urlParams.get('page') || '1', 10);
            const initialSortBy = urlParams.get('sb') || 'notification_date';
            const initialSortDirection = urlParams.get('sd') || 'asc';
            const initialSearchTerm = urlParams.get('q') || '';

            console.log('Initial params from URL:', {
                page: initialPage,
                sortBy: initialSortBy,
                sortDirection: initialSortDirection,
                searchTerm: initialSearchTerm
            });

            // ソートの状態を初期化 (sortingLogic のメソッドを呼び出し)
            this.setInitialSort(initialSortBy, initialSortDirection); // sorting.setInitialSort(...) ではなく this.setInitialSort(...)

            // 検索キーワードの状態を初期化
            this.searchTerm = initialSearchTerm;


            const initialUrl = new URL(window.location.origin + window.location.pathname);
            initialUrl.searchParams.set('page', initialPage);
            initialUrl.searchParams.set('sb', initialSortBy);
            initialUrl.searchParams.set('sd', initialSortDirection);
            if (initialSearchTerm) {
                initialUrl.searchParams.set('q', initialSearchTerm);
            }
            history.replaceState({}, '', initialUrl);


            // 初期読み込み時に fetchServices を呼び出す (検索キーワードを渡す)
            this.fetchServices(initialPage, initialSortBy, initialSortDirection, this.searchTerm);

            // 認証済みユーザー情報を取得
            this.fetchAuthenticatedUser(); // app.js に維持したメソッドを呼び出し

            window.addEventListener('popstate', () => {
                console.log('Popstate event triggered.');
                const currentUrlParams = new URLSearchParams(window.location.search);
                const currentPage = parseInt(currentUrlParams.get('page') || '1', 10);
                const currentSortBy = currentUrlParams.get('sb') || 'notification_date';
                const currentSortDirection = currentUrlParams.get('sd') || 'asc';
                const currentSearchTerm = currentUrlParams.get('q') || '';

                // 現在のAlpineの状態とURLの状態が異なる場合のみfetchServicesを呼び出す
                if (this.pagination.current_page !== currentPage ||
                    this.sortBy !== currentSortBy ||
                    this.sortDirection !== currentSortDirection ||
                    this.searchTerm !== currentSearchTerm) {
                    console.log('State mismatch detected, refetching services.');
                    // ソート状態を更新
                    this.setInitialSort(currentSortBy, currentSortDirection);
                    // 検索キーワードの状態も更新
                    this.searchTerm = currentSearchTerm;
                    // ページ番号、新しいソート設定、新しい検索キーワードを渡して再取得
                    this.fetchServices(currentPage, currentSortBy, currentSortDirection, currentSearchTerm);
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
