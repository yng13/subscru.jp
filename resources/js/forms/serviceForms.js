// resources/js/forms/serviceForms.js

// デバッグユーティリティ関数をインポート
import {debugLog, debugWarn, debugError} from '../utils/debug';

// バリデーションエラーメッセージの定義
const validationMessages = {
    required: 'この項目は必須です。',
    invalidDate: '有効な日付を入力してください。'
};

// フォームの状態とバリデーションロジックをまとめたオブジェクトを返す関数
export function serviceFormLogic() {
    return {
        // サービス登録モーダルのフォーム状態とバリデーションエラー
        addModalForm: {
            name: '',
            type: '', // ラジオボタンは初期値 null または '' が良い
            notification_date: '',
            notification_timing: '0', // select の初期値
            memo: '',
            errors: {
                name: '',
                type: '',
                notification_date: ''
            },
            // isValid: false // フォーム全体の有効性はメソッドで計算する方がシンプル
        },

        // サービス編集モーダルのフォーム状態とバリデーションエラー
        // editingService は app.js で管理されるため、ここではエラー状態のみを管理
        editModalFormErrors: {
            name: '',
            type: '',
            notification_date: ''
        },

        // 特定のフィールドのバリデーションを行う関数
        // field: フィールド名 ('name', 'type', 'notification_date')
        // value: フィールドの値
        // formType: 'add' or 'edit'
        validateField(field, value, formType) {
            let errorMessage = '';
            let formErrors;

            if (formType === 'add') {
                formErrors = this.addModalForm.errors;
            } else if (formType === 'edit') {
                formErrors = this.editModalFormErrors;
            } else {
                debugError('Invalid formType provided to validateField:', formType);
                return; // 不正な formType
            }

            // 必須チェック
            // 値が null, undefined, 空文字列, またはスペースのみの文字列でないかを確認
            if (value == null || (typeof value === 'string' && value.trim() === '')) {
                errorMessage = validationMessages.required;
            } else {
                // 日付フィールドの追加バリデーション
                if (field === 'notification_date') {
                    const date = new Date(value);
                    // 値が空でない、かつ有効な日付でない場合にエラー
                    if (value !== '' && isNaN(date.getTime())) {
                        errorMessage = validationMessages.invalidDate;
                    }
                }
                // type フィールドの追加バリデーション (in チェックはバックエンドに任せるが、クライアントでも必須チェック)
                // 上記の必須チェックに含まれるため、ここでは不要
                // if (field === 'type' && value === '') {
                //     errorMessage = validationMessages.required;
                // }
            }

            // エラーメッセージを更新
            formErrors[field] = errorMessage;

            // フォーム全体の有効性は、この関数ではなく呼び出し元 (app.js) で計算する方がシンプル
        },

        // サービス登録フォーム全体のバリデーションを行う関数
        validateAddForm() {
            this.validateField('name', this.addModalForm.name, 'add');
            this.validateField('type', this.addModalForm.type, 'add');
            this.validateField('notification_date', this.addModalForm.notification_date, 'add');

            // フォーム全体の有効性を計算して返す
            return this.addModalForm.errors.name === '' &&
                this.addModalForm.errors.type === '' &&
                this.addModalForm.errors.notification_date === '';
        },

        // サービス編集フォーム全体のバリデーションを行う関数
        // editingService は呼び出し元 (app.js) で提供される必要がある
        validateEditForm(editingService) {
            // editingService が存在するか確認
            if (!editingService) {
                debugWarn('editingService is null during validateEditForm call.');
                return false; // editingService がなければ無効
            }

            this.validateField('name', editingService.name, 'edit');
            this.validateField('type', editingService.type, 'edit');
            this.validateField('notification_date', editingService.notification_date, 'edit');

            // フォーム全体の有効性を計算して返す
            return this.editModalFormErrors.name === '' &&
                this.editModalFormErrors.type === '' &&
                this.editModalFormErrors.notification_date === '';
        },

        // フォームの状態をリセットする関数 (新規登録用)
        resetAddForm() {
            debugLog('新規登録フォームをリセット');
            this.addModalForm.name = '';
            this.addModalForm.type = '';
            this.addModalForm.notification_date = '';
            this.addModalForm.notification_timing = '0';
            this.addModalForm.memo = '';
            // エラーメッセージもクリア
            this.addModalForm.errors.name = '';
            this.addModalForm.errors.type = '';
            this.addModalForm.errors.notification_date = '';
        },

        // 編集モーダルを開く際にエラー状態をリセットする関数
        resetEditFormErrors() {
            debugLog('編集フォームのエラーをリセット');
            this.editModalFormErrors.name = '';
            this.editModalFormErrors.type = '';
            this.editModalFormErrors.notification_date = '';
        },
    };
}
