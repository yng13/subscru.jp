// resources/js/api/serviceApi.js

// CSRFトークンを取得するためのヘルパー関数
function getCsrfToken() {
    const token = document.querySelector('meta[name="csrf-token"]');
    return token ? token.content : null;
}

// API レスポンスのエラー（特に認証エラー）をハンドリングする共通関数
async function handleApiResponse(response) {
    if (!response.ok) {
        if (response.status === 401) {
            window.location.href = '/login';
            throw new Error('認証されていません。ログインしてください。');
        }

        const error = await response.json();
        throw new Error(error.message || `APIリクエスト中にエラーが発生しました (${response.status})。`);
    }

    // 成功レスポンスの場合、JSONとしてパースして返す
    return response.json();
}


// サービス一覧を取得するAPI呼び出し関数 (ページネーション & ソート & 検索対応)
// page, sortBy, sortDirection, searchTerm パラメータを受け取る
export async function fetchServicesApi(page = 1, sortBy = 'notification_date', sortDirection = 'asc', searchTerm = '') { // searchTerm 引数を追加し、デフォルト値を設定
    try {
        // === クエリパラメータを生成 (短いパラメータ名を使用) ===
        const queryParams = new URLSearchParams();
        queryParams.append('page', page);
        // ソートパラメータを追加 (短い名前 'sb' と 'sd' を使用)
        queryParams.append('sb', sortBy);
        queryParams.append('sd', sortDirection);
        // === 検索キーワードパラメータを追加 (短い名前 'q' を使用) ===
        if (searchTerm) { // 検索キーワードが空でない場合のみ追加
            queryParams.append('q', searchTerm);
        }
        // ==================================================

        // APIエンドポイントURLにクエリ文字列を追加
        // 例: /api/services?page=1&sb=name&sd=desc&q=keyword
        const url = `/api/services?${queryParams.toString()}`;
        // ==================================================

        const response = await fetch(url, { // 構築したURLを使用
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken()
            }
        });

        const data = await handleApiResponse(response);

        return data;

    } catch (error) {
        console.error('Failed to fetch services in API module:', error);
        throw error;
    }
}

// ... 他のAPI関数 (fetchAuthenticatedUserApi, addServiceApi, saveServiceApi, deleteServiceApi) は変更なし ...

// 認証済みユーザー情報を取得するAPI呼び出し関数
export async function fetchAuthenticatedUserApi() {
    try {
        const response = await fetch('/api/user', {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken()
            }
        });

        const data = await handleApiResponse(response);

        return {
            user: data.user,
            icalFeedUrl: data.ical_feed_url
        };

    } catch (error) {
        console.error('Failed to fetch authenticated user in API module:', error);
        throw error;
    }
}


// 新しいサービスを登録するAPI呼び出し関数
export async function addServiceApi(formData) {
    try {
        const response = await fetch('/api/services', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken()
            },
            body: JSON.stringify({
                name: formData.name,
                type: formData.type,
                notification_date: formData.notification_date,
                notification_timing: parseInt(formData.notification_timing, 10),
                memo: formData.memo,
            })
        });

        const result = await handleApiResponse(response);

        return result.service;

    } catch (error) {
        console.error('Failed to add service in API module:', error);
        throw error;
    }
}

// サービスを更新するAPI呼び出し関数
export async function saveServiceApi(serviceId, formData) {
    try {
        const response = await fetch(`/api/services/${serviceId}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken()
            },
            body: JSON.stringify({
                name: formData.name,
                type: formData.type,
                notification_date: formData.notification_date,
                notification_timing: parseInt(formData.notification_timing, 10),
                memo: formData.memo,
                category_icon: formData.category_icon,
            })
        });

        const result = await handleApiResponse(response);

        return result.service;

    } catch (error) {
        console.error('Failed to save service in API module:', error);
        throw error;
    }
}

// サービスを削除するAPI呼び出し関数
export async function deleteServiceApi(serviceId) {
    try {
        const response = await fetch(`/api/services/${serviceId}`, {
            method: 'DELETE',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken()
            }
        });

        const result = await handleApiResponse(response);

        return true;

    } catch (error) {
        console.error('Failed to delete service in API module:', error);
        throw error;
    }
}
