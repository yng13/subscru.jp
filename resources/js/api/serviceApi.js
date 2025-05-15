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
    // ページネーション情報はボディに含まれるため、ここでパースして呼び出し元に渡す
    return response.json(); // ここで response.json() を呼び出す
}


// サービス一覧を取得するAPI呼び出し関数 (ページネーション対応)
// page パラメータを受け取るように修正
export async function fetchServicesApi(page = 1) { // デフォルト値を1に設定
    try {
        // APIエンドポイントURLにページ番号をクエリパラメータとして追加
        // 例: /api/services?page=2
        const response = await fetch(`/api/services?page=${page}`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken() // GETでも含めるのが安全
            }
        });

        // 共通のエラーハンドリング関数を使用し、JSONレスポンスを直接返す
        // handleApiResponse 関数内で response.json() が呼び出されるようになりました
        const data = await handleApiResponse(response); // response.json() の結果が data に入る

        // Laravel の paginate() メソッドのレスポンス構造を想定
        // data オブジェクト全体を返す (データ配列とページネーション情報を含む)
        return data;

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
                'X-CSRF-TOKEN': getCsrfToken()
            }
        });

        // handleApiResponse 関数内で response.json() が呼び出されるようになりました
        const data = await handleApiResponse(response); // response.json() の結果が data に入る

        // ユーザー情報とiCalフィードURLを返す
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
                // category_icon はフォームにないので含めないか、デフォルト値をバックエンドで設定
                // バックエンドのServiceController::storeでcategory_iconのバリデーションとデフォルト値が設定されているため、ここでは含めなくてもOK
            })
        });

        // handleApiResponse 関数内で response.json() が呼び出されるようになりました
        const result = await handleApiResponse(response); // response.json() の結果が result に入る

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

        // handleApiResponse 関数内で response.json() が呼び出されるようになりました
        const result = await handleApiResponse(response); // response.json() の結果が result に入る

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

        // handleApiResponse 関数内で response.json() が呼び出されるようになりました
        // 削除APIは通常成功してもボディがないことが多いが、handleApiResponse は json() を返すため
        // レスポンスボディがない場合はエラーにならないように handleApiResponse を少し調整するか、
        // ここで response.json() を削除して handleApiResponse の戻り値を調整する
        // 今回は handleApiResponse の戻り値をそのまま使い、ボディがなくてもエラーにならないようにする
        const result = await handleApiResponse(response); // response.json() の結果が result に入る (空オブジェクトなど)

        return true; // 成功した場合は true を返す

    } catch (error) {
        console.error('Failed to delete service in API module:', error);
        throw error;
    }
}
