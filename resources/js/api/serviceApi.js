// resources/js/api/serviceApi.js

// CSRFトークンを取得するためのヘルパー関数
function getCsrfToken() {
    const token = document.querySelector('meta[name="csrf-token"]');
    return token ? token.content : null;
}

// API レスポンスのエラー（特に認証エラー）をハンドリングする共通関数
// Alpine.js のコンテキストから独立させるため、エラーメッセージの表示は呼び出し元で行うように変更
async function handleApiResponse(response) {
    // レスポンスのステータスコードをチェック
    if (!response.ok) {
        // 認証エラー (401 Unauthorized) の場合、ログイン画面にリダイレクト
        if (response.status === 401) {
            // console.log('DEBUG: 401 Unauthorized. Redirecting to login.');
            window.location.href = '/login'; // Fortify のログインルートにリダイレクト
            // エラーハンドリングを中断
            throw new Error('認証されていません。ログインしてください。'); // エラーをスロー
        }

        // その他のエラーレスポンスの場合
        const error = await response.json();
        // console.error('API Error:', error);
        // エラーメッセージを返す (またはスロー)
        throw new Error(error.message || `APIリクエスト中にエラーが発生しました (${response.status})。`); // エラーをスロー
    }

    // 成功レスポンスの場合はそのままレスポンスオブジェクトを返す
    return response;
}

// サービス一覧を取得するAPI呼び出し関数
export async function fetchServicesApi() {
    try {
        const response = await fetch('/api/services', {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                // Sanctum stateful 認証には CSRF トークンが必要です (GETリクエストでは必須ではないが含めても良い)
                'X-CSRF-TOKEN': getCsrfToken()
            }
        });

        // 共通のエラーハンドリング関数を使用
        const handledResponse = await handleApiResponse(response);

        // JSON形式でレスポンスボディを取得
        const data = await handledResponse.json();

        // サービスデータのみを返す
        return data.services;

    } catch (error) {
        console.error('Failed to fetch services in API module:', error);
        throw error; // エラーを呼び出し元に再スロー
    }
}

// 認証済みユーザー情報を取得するAPI呼び出し関数
export async function fetchAuthenticatedUserApi() {
    try {
        const response = await fetch('/api/user', {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                // Sanctum stateful 認証には CSRF トークンが必要です
                'X-CSRF-TOKEN': getCsrfToken()
            }
        });

        const handledResponse = await handleApiResponse(response);

        const data = await handledResponse.json(); // レスポンス全体を取得

        // ユーザー情報とiCalフィードURLを返す
        return {
            user: data.user,
            icalFeedUrl: data.ical_feed_url
        };

    } catch (error) {
        console.error('Failed to fetch authenticated user in API module:', error);
        throw error; // エラーを呼び出し元に再スロー
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
                'X-CSRF-TOKEN': getCsrfToken() // CSRFトークンを含める
            },
            body: JSON.stringify({
                name: formData.name,
                type: formData.type,
                notification_date: formData.notification_date,
                notification_timing: parseInt(formData.notification_timing, 10),
                memo: formData.memo,
                // category_icon はフォームにないので含めないか、デフォルト値をバックエンドで設定
            })
        });

        // 共通のエラーハンドリング関数を使用
        const handledResponse = await handleApiResponse(response);

        // 成功レスポンスの場合、作成されたサービスデータを返す
        const result = await handledResponse.json();
        return result.service; // 作成されたサービスオブジェクトを返す

    } catch (error) {
        console.error('Failed to add service in API module:', error);
        throw error; // エラーを呼び出し元に再スロー
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
                'X-CSRF-TOKEN': getCsrfToken() // CSRFトークンを含める
            },
            body: JSON.stringify({
                name: formData.name,
                type: formData.type,
                notification_date: formData.notification_date,
                notification_timing: parseInt(formData.notification_timing, 10),
                memo: formData.memo,
                category_icon: formData.category_icon, // 編集では category_icon も更新可能とする場合
            })
        });

        // 共通のエラーハンドリング関数を使用
        const handledResponse = await handleApiResponse(response);

        // 成功レスポンスの場合、更新後のサービスデータを返す
        const result = await handledResponse.json();
        return result.service; // 更新後のサービスオブジェクトを返す

    } catch (error) {
        console.error('Failed to save service in API module:', error);
        throw error; // エラーを呼び出し元に再スロー
    }
}

// サービスを削除するAPI呼び出し関数
export async function deleteServiceApi(serviceId) {
    try {
        const response = await fetch(`/api/services/${serviceId}`, {
            method: 'DELETE',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken() // CSRFトークンを含める
            }
        });

        // 共通のエラーハンドリング関数を使用
        const handledResponse = await handleApiResponse(response);

        // 削除APIは通常成功してもボディがないことが多いので、ここでは成功ステータスのみ確認
        // 必要に応じてレスポンスボディをパースしても良い
        // const result = await handledResponse.json();
        return true; // 成功した場合は true を返す

    } catch (error) {
        console.error('Failed to delete service in API module:', error);
        throw error; // エラーを呼び出し元に再スロー
    }
}
