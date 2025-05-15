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
    console.log('serviceListPage function called, initializing data...');

    const forms = serviceFormLogic();

    return {
        // === State properties ===
        ...forms,

        isDrawerOpen: false,

        showAddModal: false,
        showEditModal: false,
        showGuideModal: false,
        showDeleteConfirmModal: false,
        serviceToDelete: null,
        editingService: null,

        showToast: false,
        toastMessage: '',
        toastType: null,

        userIcalUrl: '',

        services: [], // ページごとのサービスデータ

        // === ページネーション関連のプロパティ ===
        pagination: {
            current_page: 1,
            last_page: 1,
            total: 0,
            per_page: 15,
            links: [],
            first_page_url: null,
            last_page_url: null,
            prev_page_url: null, // last_page_url と prev_page_url の定義漏れを修正
            next_page_url: null,
        },
        // ===============================================

        // === ソートの状態 ===
        // 初期値は init メソッドで URL パラメータから読み込むように変更
        sortBy: 'notification_date',
        sortDirection: 'asc',
        // =========================================

        // データのロード状態
        isLoading: false,
        loadingMessage: 'データを読み込み中...',


        // === Methods ===

        // サービスのデータをバックエンドから取得するメソッド (ページネーション & ソート対応)
        async fetchServices(page = this.pagination.current_page, sortBy = this.sortBy, sortDirection = this.sortDirection) { // デフォルト引数に現在の状態を使用
            if (this.isLoading) return; // ロード中の重複リクエスト防止

            this.isLoading = true;
            this.loadingMessage = `サービスを読み込み中 (ページ ${page})...`;

            // === URLを更新 ===
            const url = new URL(window.location);
            url.searchParams.set('page', page);
            url.searchParams.set('sb', sortBy); // 短いパラメータ名 'sb'
            url.searchParams.set('sd', sortDirection); // 短いパラメータ名 'sd'

            // 履歴スタックに新しいURLを追加 (戻る/進むボタンで前の状態に戻れる)
            // または replaceState で履歴を残さない (リロード時のみ現在の状態を維持)
            // 今回はページ切り替えやソート変更時に履歴を残す pushState を使用します
            // もし履歴を残したくない場合は history.replaceState に変更してください
            history.pushState({}, '', url); // 状態オブジェクト, タイトル (通常空), URL
            // ====================


            try {
                // serviceApi.js からインポートした関数を呼び出す
                // ページ番号、ソートキー、ソート方向を渡す
                const response = await fetchServicesApi(page, sortBy, sortDirection);

                // 取得したデータとページネーション情報をそれぞれのプロパティにセット
                this.services = response.data.map(service => {
                    return {
                        ...service,
                        notification_timing: parseInt(service.notification_timing, 10)
                    };
                });
                // ページネーション情報をセット
                this.pagination = {
                    current_page: response.current_page,
                    last_page: response.last_page,
                    total: response.total,
                    per_page: response.per_page,
                    links: response.links,
                    first_page_url: response.first_page_url,
                    last_page_url: response.last_page_url,
                    next_page_url: response.next_page_url,
                    prev_page_url: response.prev_page_url, // prev_page_url もセット
                };

                // 成功した場合、現在のソート状態を更新
                this.sortBy = sortBy;
                this.sortDirection = sortDirection;


                console.log('サービス一覧とページネーション情報を正常に取得しました', this.services, this.pagination);


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

        // ページを切り替えるメソッド
        goToPage(pageUrl) {
            if (!pageUrl || this.isLoading) {
                return;
            }

            // URLからページ番号を抽出 (LaravelのページネーションリンクのURL構造を想定)
            const url = new URL(pageUrl);
            const page = url.searchParams.get('page');

            if (page) {
                console.log('ページ切り替え:', page);
                // ページ番号を指定してサービスを取得 (ソートパラメータは fetchServices 内で付加される)
                this.fetchServices(parseInt(page, 10));
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

        // 通知対象日の残り日数を計算する関数
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

        // 日付をYYYY/MM/DD 形式にフォーマットする関数
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

        // ソート処理を行う関数 (バックエンドでソートするように変更)
        sortServices(key, toggleDirection = true) {
            // ソートキーと方向の状態を更新
            let newSortDirection = this.sortDirection;
            if (this.sortBy === key) {
                if (toggleDirection) {
                    newSortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
                }
            } else {
                // 異なるキーの場合はデフォルトの方向（asc）
                newSortDirection = 'asc';
            }

            console.log(`ソート設定変更: ${key}, 方向: ${newSortDirection}`);

            // 状態を更新してから fetchServices を呼び出すのではなく、
            // fetchServices に新しいソート設定を渡して、成功したら fetchServices の中で状態を更新する
            this.fetchServices(1, key, newSortDirection); // ソート基準が変わったら1ページ目に戻るのが一般的
        },


        // iCal URLをクリップボードにコピーする関数
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
                const newService = await addServiceApi(this.addModalForm);
                console.log('新しいサービスを正常に登録しました', newService);

                // 新規登録後、サービス一覧を再取得 (現在のページ・ソート設定で)
                await this.fetchServices(this.pagination.current_page, this.sortBy, this.sortDirection);

                this.closeModals();
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

            if (!this.validateEditForm(this.editingService)) {
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
                const updatedService = await saveServiceApi(this.editingService.id, this.editingService);
                console.log('サービスを正常に保存しました', updatedService);

                // 更新後、サービス一覧を再取得 (現在のページ・ソート設定で)
                await this.fetchServices(this.pagination.current_page, this.sortBy, this.sortDirection);

                this.closeModals();
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
                await deleteServiceApi(this.serviceToDelete.id);
                console.log('サービスを正常に削除しました', this.serviceToDelete.id);

                // 削除後、サービス一覧を再取得 (現在のページ・ソート設定で)
                await this.fetchServices(this.pagination.current_page, this.sortBy, this.sortDirection);

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


        // ページの初期化処理 (URLパラメータから状態を読み込む)
        init() {
            console.log('Alpine component initialized');

            // URLのGETパラメータを解析
            const urlParams = new URLSearchParams(window.location.search);
            const initialPage = parseInt(urlParams.get('page') || '1', 10); // 'page' パラメータ、なければデフォルト1
            const initialSortBy = urlParams.get('sb') || 'notification_date'; // 'sb' パラメータ、なければデフォルト notification_date
            const initialSortDirection = urlParams.get('sd') || 'asc'; // 'sd' パラメータ、なければデフォルト asc

            console.log('Initial params from URL:', {
                page: initialPage,
                sortBy: initialSortBy,
                sortDirection: initialSortDirection
            });

            // 取得したパラメータで初期表示サービスを読み込む
            // sortBy と sortDirection の状態もここで初期化
            this.sortBy = initialSortBy;
            this.sortDirection = initialSortDirection;
            this.fetchServices(initialPage, initialSortBy, initialSortDirection); // fetchServices に初期パラメータを渡す

            this.fetchAuthenticatedUser();

            // ブラウザの「戻る」「進む」ボタンによる履歴変更を検知して再読み込み
            window.addEventListener('popstate', () => {
                console.log('Popstate event triggered.');
                const currentUrlParams = new URLSearchParams(window.location.search);
                const currentPage = parseInt(currentUrlParams.get('page') || '1', 10);
                const currentSortBy = currentUrlParams.get('sb') || 'notification_date';
                const currentSortDirection = currentUrlParams.get('sd') || 'asc';

                // 現在のAlpineの状態とURLの状態が異なる場合のみfetchServicesを呼び出す
                // これにより、pushState でURLを変えた直後の popstate イベントでの重複呼び出しを防ぐ
                if (this.pagination.current_page !== currentPage ||
                    this.sortBy !== currentSortBy ||
                    this.sortDirection !== currentSortDirection) {
                    console.log('State mismatch detected, refetching services.');
                    this.sortBy = currentSortBy; // popstate でも状態を更新
                    this.sortDirection = currentSortDirection; // popstate でも状態を更新
                    this.fetchServices(currentPage, currentSortBy, currentSortDirection); // 新しいパラメータで再取得
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
