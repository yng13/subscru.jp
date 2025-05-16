// resources/js/utils/notification.js

// デバッグユーティリティ関数をインポート
import {debugLog, debugWarn, debugError} from './debug';

// トースト通知の状態とロジックをまとめたオブジェクトを返す関数
export function notificationLogic() {
    return {
        // トースト通知の状態プロパティ
        showToast: false,
        toastMessage: '',
        toastType: null, // 'success', 'error', null

        // トースト通知を表示するメソッド
        showToastNotification(message, type = null, duration = 3000) { // durationのデフォルトは3秒
            debugLog(`Showing toast: "${message}" (${type})`);
            this.toastMessage = message;
            this.toastType = type;
            this.showToast = true;

            // 指定時間後にトーストを非表示にする
            setTimeout(() => {
                this.hideToastNotification();
            }, duration);
        },

        // トースト通知を非表示にするメソッド
        hideToastNotification() {
            debugLog('Hiding toast.');
            this.showToast = false;
            // 非表示後にメッセージとタイプをクリア
            setTimeout(() => { // アニメーション後にクリアするために少し遅延
                this.toastMessage = '';
                this.toastType = null;
            }, 300); // 300msの遅延 (CSSアニメーションの長さに合わせる)
        }
    };
}
