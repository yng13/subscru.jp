// resources/js/utils/sorting.js

// ソートの状態とロジックをまとめたオブジェクトを返す関数
// fetchServicesCallback 引数を削除
export function sortingLogic() {
    return {
        // ソートの状態プロパティ
        sortBy: 'notification_date', // 初期ソートキー
        sortDirection: 'asc', // 'asc' or 'desc'

        // ソート処理を行うメソッド
        // fetchServicesCallback を直接呼び出す代わりに、$data を経由して fetchServices を呼び出す
        sortServices(key, toggleDirection = true) {
            let newSortDirection = this.sortDirection;
            if (this.sortBy === key) {
                if (toggleDirection) {
                    newSortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
                }
            } else {
                newSortDirection = 'asc';
            }

            console.log(`ソート設定変更: ${key}, 方向: ${newSortDirection}`);

            // ソートの状態を即時更新
            this.sortBy = key;
            this.sortDirection = newSortDirection;

            // Alpine.js の $data マジックプロパティを使って、serviceListPage() 全体のデータオブジェクトにアクセス
            // そして、その中の fetchServices メソッドを呼び出す
            // $data は Alpine の内部で提供されるため、this.$data のようにアクセス可能
            // ただし、$data を経由すると型推論が効きにくくなる場合がある
            // または、以下のように直接 this を通して呼び出すこともできるはずだが、
            // 以前のエラーはこの部分のコンテキストの問題なので、$data を試す
            // this.fetchServices(1, this.sortBy, this.sortDirection);

            // Alpine の $data マジックプロパティを使用して fetchServices を呼び出す
            // $data は serviceListPage() が返すオブジェクト全体を指す
            this.$data.fetchServices(1, this.sortBy, this.sortDirection); //  $data を経由して呼び出し
        },

        // init メソッドで初期ソート状態を設定する場合などに使用するヘルパー
        setInitialSort(sortBy, sortDirection) {
            this.sortBy = sortBy;
            this.sortDirection = sortDirection;
        }
    };
}
