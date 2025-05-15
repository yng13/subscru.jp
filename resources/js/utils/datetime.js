// resources/js/utils/datetime.js

// 通知対象日の残り日数を計算する関数
export function getDaysRemaining(dateString) {
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
}

// 日付をYYYY/MM/DD 形式にフォーマットする関数
export function formatDate(dateString) {
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
}
