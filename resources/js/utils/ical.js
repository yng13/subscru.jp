// resources/js/utils/ical.js

// iCalフィードURLとコピー機能に関するロジックをまとめたオブジェクトを返す関数
// このロジックは Alpine.js の data オブジェクトに展開して使用されます。
export function icalLogic() {
    return {
        // ユーザーのiCalフィードURL
        userIcalUrl: '',

        // 認証済みユーザーの情報を取得し、iCal URLをセットするメソッド
        // fetchAuthenticatedUserApi は api/serviceApi.js からインポートされた関数です。
        // notificationLogic の showToastNotification メソッドを使用するため、
        // app.js のコンテキストから呼び出すか、notificationLogic オブジェクトを引数として渡す必要があります。
        // ここでは app.js のコンテキストから showToastNotification を呼び出す想定で実装します (this.$data.showToastNotification)。
        async fetchAuthenticatedUser() {
            console.log('icalLogic: fetchAuthenticatedUser called');
            try {
                // fetchAuthenticatedUserApi は app.js でインポートされているため、ここでは直接呼び出しません。
                // app.js の fetchAuthenticatedUser メソッドがこのロジックを呼び出す設計とします。
                // もしここで直接 API を呼び出す場合は、fetchAuthenticatedUserApi をインポートする必要があります。
                // 例: const userData = await fetchAuthenticatedUserApi();
                // 現在の設計では、app.js 側で API を呼び出し、その結果をこのロジックに渡す方がシンプルです。
                // app.js の fetchAuthenticatedUser メソッドをそのまま使用し、icalLogic に含めないことにします。
                // このファイルには copyIcalUrl メソッドのみを置く方が責務が明確になります。

                // fetchAuthenticatedUser メソッドは app.js に残し、icalLogic に含めない方がシンプルです。
                // このファイルは copyIcalUrl のヘルパーとしてのみ機能させましょう。

            } catch (error) {
                // エラーハンドリングは app.js 側で行う想定
                console.error('icalLogic: Error in fetchAuthenticatedUser (should be handled in app.js):', error);
                // showToastNotification を app.js のコンテキストから呼び出す想定
                if (typeof this.showToastNotification === 'function') { // showToastNotification が存在するか確認
                    this.showToastNotification('カレンダーURLの取得中にエラーが発生しました。', 'error', 5000);
                }
            }
        },

        // iCal URLをクリップボードにコピーするメソッド
        // notificationLogic の showToastNotification メソッドを使用するため、
        // app.js のコンテキストから呼び出す必要があります (this.$data.showToastNotification)。
        copyIcalUrl() {
            console.log('icalLogic: copyIcalUrl called');
            const urlElement = document.getElementById('ical-url');

            // iCal URL が存在し、かつデフォルトメッセージやエラーメッセージでないことを確認
            if (urlElement && this.userIcalUrl && this.userIcalUrl !== 'ログインすると表示されます。' && this.userIcalUrl !== 'エラーにより取得できませんでした。') {
                // クリップボードAPIを使用してURLをコピー
                navigator.clipboard.writeText(urlElement.innerText)
                    .then(() => {
                        console.log('icalLogic: URL copied successfully.');
                        // showToastNotification を app.js のコンテキストから呼び出す想定
                        if (typeof this.showToastNotification === 'function') { // showToastNotification が存在するか確認
                            this.showToastNotification('URLをコピーしました！', 'success', 3000);
                        }
                    })
                    .catch(err => {
                        console.error('icalLogic: Failed to copy URL: ', err);
                        // showToastNotification を app.js のコンテキストから呼び出す想定
                        if (typeof this.showToastNotification === 'function') { // showToastNotification が存在するか確認
                            this.showToastNotification('URLのコピーに失敗しました。手動でコピーしてください。', 'error', 5000);
                        }
                    });
            } else {
                console.warn('icalLogic: No iCal URL to copy.');
                // showToastNotification を app.js のコンテキストから呼び出す想定
                if (typeof this.showToastNotification === 'function') { // showToastNotification が存在するか確認
                    this.showToastNotification('コピーできるiCal URLがありません。', 'error', 3000);
                }
            }
        },

        // userIcalUrl を外部から設定するためのメソッド (app.js から呼び出す用)
        setIcalUrl(url) {
            this.userIcalUrl = url;
        }
    };
}
