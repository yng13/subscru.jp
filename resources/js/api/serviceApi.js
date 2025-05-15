// resources/js/api/serviceApi.js

// CSRFトークンを取得するためのヘルパー関数
export function getCsrfToken() {
    const token = document.querySelector('meta[name="csrf-token"]');
    if (!token) {
        console.error('CSRF token meta tag not found!'); // 確認用ログ
        return null;
    }
    console.log('API: getCsrfToken called, token:', token.content); // 確認用ログ
    return token ? token.content : null;
}

// API レスポンスのエラー（特に認証エラー）をハンドリングする共通関数
async function handleApiResponse(response) {
    console.log('API: handleApiResponse called with status:', response.status); // 確認用ログ
    if (!response.ok) {
        if (response.status === 401) {
            console.error('API: 401 Unauthorized, redirecting to login...'); // 確認用ログ
            window.location.href = '/login';
            throw new Error('認証されていません。ログインしてください。');
        }

        const error = await response.json();
        console.error('API: handleApiResponse error response:', error); // 確認用ログ
        throw new Error(error.message || `APIリクエスト中にエラーが発生しました (${response.status})。`);
    }

    // 成功レスポンスの場合、JSONとしてパースして返す
    const data = await response.json();
    console.log('API: handleApiResponse successful, parsed data:', data); // 確認用ログ
    return data;
}


// サービス一覧を取得するAPI呼び出し関数 (ページネーション & ソート & 検索対応)
export async function fetchServicesApi(page = 1, sortBy = 'notification_date', sortDirection = 'asc', searchTerm = '') {
    try {
        const queryParams = new URLSearchParams();
        queryParams.append('page', page);
        queryParams.append('sb', sortBy);
        queryParams.append('sd', sortDirection);
        if (searchTerm) {
            queryParams.append('q', searchTerm);
        }

        const url = `/api/services?${queryParams.toString()}`;

        console.log('API: fetchServicesApi called, URL:', url);

        const response = await fetch(url, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken() // ここで getCsrfToken() を呼び出す
            }
        });

        console.log('API: fetchServicesApi response status:', response.status);
        const data = await handleApiResponse(response);

        console.log('API: fetchServicesApi successful, data:', data);
        return data;

    } catch (error) {
        console.error('API: Failed to fetch services:', error);
        throw error;
    }
}

// 認証済みユーザー情報を取得するAPI呼び出し関数
export async function fetchAuthenticatedUserApi() {
    console.log('API: fetchAuthenticatedUserApi called');
    try {
        const response = await fetch('/api/user', {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken() // ここで getCsrfToken() を呼び出す
            }
        });
        console.log('API: fetchAuthenticatedUserApi response status:', response.status);

        const data = await handleApiResponse(response);
        console.log('API: fetchAuthenticatedUserApi successful, data:', data);

        return {
            user: data.user,
            icalFeedUrl: data.ical_feed_url
        };

    } catch (error) {
        console.error('API: Failed to fetch authenticated user:', error);
        throw error;
    }
}


// 新しいサービスを登録するAPI呼び出し関数
export async function addServiceApi(formData) {
    console.log('API: addServiceApi called with data:', formData);
    try {
        const response = await fetch('/api/services', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken() // ここで getCsrfToken() を呼び出す
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
        console.log('API: addServiceApi response status:', response.status);

        const result = await handleApiResponse(response);
        console.log('API: addServiceApi successful, result:', result);


        return result.service;

    } catch (error) {
        console.error('API: Failed to add service:', error);
        throw error;
    }
}

// サービスを更新するAPI呼び出し関数
export async function saveServiceApi(serviceId, formData) {
    console.log('API: saveServiceApi called with ID:', serviceId, 'data:', formData);
    try {
        const response = await fetch(`/api/services/${serviceId}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken() // ここで getCsrfToken() を呼び出す
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
        console.log('API: saveServiceApi response status:', response.status);

        const result = await handleApiResponse(response);
        console.log('API: saveServiceApi successful, result:', result);


        return result.service;

    } catch (error) {
        console.error('API: Failed to save service:', error);
        throw error;
    }
}

// サービスを削除するAPI呼び出し関数
export async function deleteServiceApi(serviceId) {
    console.log('API: deleteServiceApi called with ID:', serviceId);
    try {
        const response = await fetch(`/api/services/${serviceId}`, {
            method: 'DELETE',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken() // ここで getCsrfToken() を呼び出す
            }
        });

        console.log('API: deleteServiceApi response status:', response.status);
        const result = await handleApiResponse(response);

        console.log('API: deleteServiceApi successful, result:', result);
        return true;

    } catch (error) {
        console.error('API: Failed to delete service:', error);
        throw error;
    }
}
