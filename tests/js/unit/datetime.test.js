// tests/js/unit/datetime.test.js

// テスト対象の関数をインポート
import {getDaysRemaining, formatDate} from '../../../resources/js/utils/datetime'; // ファイルパスは適宜修正

// getDaysRemaining 関数のテストスイート
describe('getDaysRemaining', () => {
    // 個別のテストケース
    it('should return 0 for today\'s date', () => {
        // 今日の日付を YYYY-MM-DD 形式で取得
        const today = new Date();
        const year = today.getFullYear();
        const month = (today.getMonth() + 1).toString().padStart(2, '0');
        const day = today.getDate().toString().padStart(2, '0');
        const todayString = `${year}-${month}-${day}T00:00:00`;

        // 関数を実行し、結果を検証 (expect と toBe を使用)
        expect(getDaysRemaining(todayString)).toBe(0);
    });

    it('should return a positive number for a future date', () => {
        // 5日後の日付を YYYY-MM-DD 形式で取得
        const futureDate = new Date();
        futureDate.setDate(futureDate.getDate() + 5);
        const year = futureDate.getFullYear();
        const month = (futureDate.getMonth() + 1).toString().padStart(2, '0');
        const day = futureDate.getDate().toString().padStart(2, '0');
        const futureDateString = `${year}-${month}-${day}T00:00:00`;

        expect(getDaysRemaining(futureDateString)).toBe(5);
    });

    it('should return a negative number for a past date', () => {
        // 10日前の日付を YYYY-MM-DD 形式で取得
        const pastDate = new Date();
        pastDate.setDate(pastDate.getDate() - 10);
        const year = pastDate.getFullYear();
        const month = (pastDate.getMonth() + 1).toString().padStart(2, '0');
        const day = pastDate.getDate().toString().padStart(2, '0');
        const pastDateString = `<span class="math-inline">\{year\}\-</span>{month}-${day}`;

        expect(getDaysRemaining(pastDateString)).toBeLessThan(0); // 0より小さいことを検証
    });

    it('should return Infinity for invalid or empty dates', () => {
        expect(getDaysRemaining('')).toBe(Infinity);
        expect(getDaysRemaining(null)).toBe(Infinity);
        expect(getDaysRemaining('invalid-date')).toBe(Infinity);
    });
});

// formatDate 関数のテストスイート (同様に追加できます)
describe('formatDate', () => {
    it('should format a date string to YYYY/MM/DD format', () => {
        expect(formatDate('2025-05-17')).toBe('2025/05/17');
        expect(formatDate('2026-12-01')).toBe('2026/12/01');
    });

    it('should return "N/A" for empty date string', () => {
        expect(formatDate('')).toBe('N/A');
        expect(formatDate(null)).toBe('N/A');
    });

    it('should return "Invalid Date" for invalid date string', () => {
        expect(formatDate('invalid-date')).toBe('Invalid Date');
    });
});
