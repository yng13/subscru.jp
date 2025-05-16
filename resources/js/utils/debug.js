// resources/js/utils/debug.js

// Vite の環境変数からアプリケーション環境を取得
// import.meta.env.VITE_APP_ENV は .env ファイルで設定した VITE_APP_ENV の値になります
const APP_ENV = import.meta.env.VITE_APP_ENV;

// 開発環境かどうかを判定
// 'development' または 'local' の場合に true となります
const IS_DEVELOPMENT = APP_ENV === 'development' || APP_ENV === 'local';

/**
 * デバッグログを出力する関数。
 * 環境が 'development' または 'local' の場合のみコンソールにログを出力します。
 * @param {...any} args - console.log に渡す引数
 */
export function debugLog(...args) {
    if (IS_DEVELOPMENT) {
        console.log(...args);
    }
}

/**
 * デバッグ警告を出力する関数。
 * 環境が 'development' または 'local' の場合のみコンソールに警告を出力します。
 * @param {...any} args - console.warn に渡す引数
 */
export function debugWarn(...args) {
    if (IS_DEVELOPMENT) {
        console.warn(...args);
    }
}

/**
 * デバッグエラーを出力する関数。
 * 環境が 'development' または 'local' の場合のみコンソールにエラーを出力します。
 * @param {...any} args - console.error に渡す引数
 */
export function debugError(...args) {
    if (IS_DEVELOPMENT) {
        console.error(...args);
    }
}
