// resources/js/app.js

// Alpine.js のインポート
import Alpine from 'alpinejs';
import intersect from '@alpinejs/intersect';

// API 呼び出し関数をインポート
import {
    fetchServicesApi,
    fetchAuthenticatedUserApi,
    addServiceApi,
    saveServiceApi,
    deleteServiceApi,
    // === ここを修正 ===
    getCsrfToken // getCsrfToken 関数をインポートリストに追加
    // ====================
} from './api/serviceApi';

// フォームロジック関数をインポート
import {serviceFormLogic} from './forms/serviceForms';

// 日付・ユーティリティ関数をインポート
import {getDaysRemaining, formatDate} from './utils/datetime';

// ソートロジック関数をインポート
import {sortingLogic} from './utils/sorting';

// トースト通知ロジック関数をインポート
import {notificationLogic} from './utils/notification';

// モーダル関連と iCal 関連のロジックをインポート
import {modalLogic} from './utils/modal';
import {icalLogic} from './utils/ical';


// Alpine.js プラグインの登録
Alpine.plugin(intersect);


// x-data で使用するデータとメソッドを定義する関数
function serviceListPage() {
    console.log('serviceListPage function called, initializing data...');

    // 各ロジックオブジェクトを生成
    const forms = serviceFormLogic();
    const notification = notificationLogic();
    const sorting = sortingLogic();
    const modal = modalLogic();
    const ical = icalLogic();


    return {
        // === 各ロジックオブジェクトのプロパティとメソッドを展開して統合 ===
        ...forms,
        ...notification,
        ...sorting,
        ...modal,
        ...ical,
        // ===============================================================

        isDrawerOpen: false,

        services: [],

        pagination: {
            current_page: 1, last_page: 1, total: 0, per_page: 15, links: [],
            first_page_url: null, last_page_url: null, prev_page_url: null, next_page_url: null,
        },

        searchTerm: '',

        getDaysRemaining: getDaysRemaining,
        formatDate: formatDate,


        // === Methods ===

        async fetchServices(page = this.pagination.current_page, sortBy = this.sortBy, sortDirection = this.sortDirection, searchTerm = this.searchTerm) {
            console.log('fetchServices: メソッド開始');
            if (this.isLoading) {
                console.log('fetchServices: isLoading が true のため早期リターン');
                return;
            }

            this.isLoading = true;
            this.loadingMessage = 'サービスを読み込み中...';

            const url = new URL(window.location);
            url.searchParams.set('page', page);
            url.searchParams.set('sb', sortBy);
            url.searchParams.set('sd', sortDirection);
            if (searchTerm) {
                url.searchParams.set('q', searchTerm);
            } else {
                url.searchParams.delete('q');
            }
            // URLの更新は fetchServicesApi の中で行う方が責務が分かれるかもしれないですが、
            // 現在の設計を踏襲し、ここで history API を使用します。
            // fetchServicesApi は純粋なAPI呼び出しロジックに集中させ、
            // URL更新や Alpine.js 状態更新は app.js で行うのが良いでしょう。
            history.pushState({}, '', url); // fetchServicesApi の前に移動

            // API呼び出し時に searchTerm を渡す (serviceApi.js は既に修正済み)
            // fetchServicesApi に CSRF トークンは渡しません。serviceApi.js の内部で取得します。
            try {
                console.log('fetchServices: fetchServicesApi を呼び出します'); // 確認用ログ
                // URLSearchParams から直接文字列を渡すのではなく、fetchServicesApi 内でクエリパラメータを構築するように戻します
                // これは前の修正で誤って fetchServices 内でURLSearchParamsを文字列化して渡すように変更したためです。
                // fetchServicesApi にはページ番号、ソート情報、検索キーワードを引数として渡します。
                const responseData = await fetchServicesApi(page, sortBy, sortDirection, searchTerm); // serviceApi.js 内でURL構築と fetch を行う

                console.log('fetchServices: fetchServicesApi 呼び出し完了'); // 確認用ログ


                this.services = responseData.data.map(service => {
                    return {
                        ...service,
                        notification_timing: parseInt(service.notification_timing, 10)
                    };
                });
                this.pagination = {
                    current_page: responseData.current_page,
                    last_page: responseData.last_page,
                    total: responseData.total,
                    per_page: responseData.per_page,
                    links: responseData.links,
                    first_page_url: responseData.first_page_url,
                    last_page_url: responseData.last_page_url,
                    next_page_url: responseData.next_page_url,
                    prev_page_url: responseData.prev_page_url,
                };

                console.log('サービス一覧とページネーション情報を正常に取得しました', this.services, this.pagination);

            } catch (error) {
                console.error('サービスの取得中にエラーが発生しました:', error);
                this.showToastNotification(error.message || 'サービスの取得中にエラーが発生しました。', 'error', 5000);
            } finally {
                this.isLoading = false;
                this.loadingMessage = 'データを読み込み中...';
                console.log('fetchServices: メソッド終了'); // 確認用ログ
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
                // URLSearchParams から抽出したパラメータを渡す
                const sortBy = url.searchParams.get('sb') || this.sortBy;
                const sortDirection = url.searchParams.get('sd') || this.sortDirection;
                const searchTerm = url.searchParams.get('q') || this.searchTerm;

                this.fetchServices(parseInt(page, 10), sortBy, sortDirection, searchTerm);
            } else {
                console.warn('ページURLからページ番号を抽出できませんでした:', pageUrl);
            }
        },

        // 認証済みユーザーの情報を取得するメソッド (app.js に維持)
        async fetchAuthenticatedUser() {
            console.log('app.js: fetchAuthenticatedUser called');
            try {
                // fetchAuthenticatedUserApi は serviceApi.js 内で getCsrfToken を呼び出す
                const userData = await fetchAuthenticatedUserApi();
                if (userData && userData.icalFeedUrl) {
                    this.setIcalUrl(userData.icalFeedUrl);
                    console.log('app.js: iCal URL:', this.userIcalUrl);
                } else {
                    console.warn('app.js: Authenticated user or iCal feed URL not found.');
                    this.setIcalUrl('ログインすると表示されます。');
                }
            } catch (error) {
                console.error('app.js: 認証ユーザーまたはiCalフィードURLの取得中にエラーが発生しました:', error);
                this.setIcalUrl('エラーにより取得できませんでした。');
                this.showToastNotification('カレンダーURLの取得中にエラーが発生しました。', 'error', 5000);
            }
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
                // addServiceApi は serviceApi.js 内で getCsrfToken を呼び出す
                const newService = await addServiceApi(this.addModalForm);
                console.log('新しいサービスを正常に登録しました', newService);

                // 登録後、サービス一覧を再取得 (現在のページ・ソート設定、現在の検索キーワードで)
                await this.fetchServices(this.pagination.current_page, this.sortBy, this.sortDirection, this.searchTerm);

                this.closeAddModalOnSuccess();
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
                // saveServiceApi は serviceApi.js 内で getCsrfToken を呼び出す
                const updatedService = await saveServiceApi(this.editingService.id, this.editingService);
                console.log('サービスを正常に保存しました', updatedService);

                // 更新後、サービス一覧を再取得 (現在のページ・ソート設定、現在の検索キーワードで)
                await this.fetchServices(this.pagination.current_page, this.sortBy, this.sortDirection, this.searchTerm);

                this.closeEditModalOnSuccess();
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
                this.closeDeleteConfirmModalOnSuccess();
                return;
            }

            this.isLoading = true; // 削除処理開始時に isLoading を true
            this.loadingMessage = 'サービスを削除中...';

            try {
                console.log('deleteServiceApi を呼び出します Service ID:', this.serviceToDelete.id);
                await deleteServiceApi(this.serviceToDelete.id);
                console.log('サービスを正常に削除しました Service ID:', this.serviceToDelete.id);

                // === ここを修正 ===
                // isLoading を false に戻してから fetchServices を呼び出す
                this.isLoading = false;
                this.loadingMessage = 'データを読み込み中...'; // メッセージも更新
                console.log('deleteService: isLoading を false に設定後、fetchServices を呼び出します...'); // 確認用ログ
                // 削除後、サービス一覧を再取得 (1ページ目、現在のソート設定、現在の検索キーワードで)
                await this.fetchServices(1, this.sortBy, this.sortDirection, this.searchTerm);
                console.log('deleteService: fetchServices 呼び出し完了');
                // ====================

                this.closeDeleteConfirmModalOnSuccess();
                console.log('削除確認モーダルを閉じました');

                this.showToastNotification('サービスを削除しました！', 'success', 3000);

            } catch (error) {
                console.error('サービスの削除中にエラーが発生しました:', error);
                this.showToastNotification(error.message || 'サービスの削除中にエラーが発生しました。', 'error', 5000);
            } finally {
                // 削除処理の完了時にも isLoading を false に設定 (念のため。try/catch の中で既に設定済み)
                this.isLoading = false;
                this.loadingMessage = 'データを読み込み中...';
                console.log('deleteService: メソッド終了');
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

            this.setInitialSort(initialSortBy, initialSortDirection);
            this.searchTerm = initialSearchTerm;

            const initialUrl = new URL(window.location.origin + window.location.pathname);
            initialUrl.searchParams.set('page', initialPage);
            initialUrl.searchParams.set('sb', initialSortBy);
            initialUrl.searchParams.set('sd', initialSortDirection);
            if (initialSearchTerm) {
                initialUrl.searchParams.set('q', initialSearchTerm);
            }
            history.replaceState({}, '', initialUrl);

            // 初期読み込み時に fetchServices を呼び出す
            // fetchServicesApi は serviceApi.js 内で getCsrfToken を呼び出す
            this.fetchServices(initialPage, initialSortBy, initialSortDirection, this.searchTerm);

            // 認証済みユーザー情報を取得
            // fetchAuthenticatedUserApi は serviceApi.js 内で getCsrfToken を呼び出す
            this.fetchAuthenticatedUser();

            window.addEventListener('popstate', () => {
                console.log('Popstate event triggered.');
                const currentUrlParams = new URLSearchParams(window.location.search);
                const currentPage = parseInt(currentUrlParams.get('page') || '1', 10);
                const currentSortBy = currentUrlParams.get('sb') || 'notification_date';
                const currentSortDirection = currentUrlParams.get('sd') || 'asc';
                const currentSearchTerm = currentUrlParams.get('q') || '';

                if (this.pagination.current_page !== currentPage ||
                    this.sortBy !== currentSortBy ||
                    this.sortDirection !== currentSortDirection ||
                    this.searchTerm !== currentSearchTerm) {
                    console.log('State mismatch detected, refetching services.');
                    this.setInitialSort(currentSortBy, currentSortDirection);
                    this.searchTerm = currentSearchTerm;
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
