<?php

namespace App\Http\Controllers;

use App\Models\User; // Userモデルを使用
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response; // Responseファサードを使用
use App\Models\Service; // Serviceモデルを使用

class IcalFeedController extends Controller
{
    /**
     * 指定されたトークンを持つユーザーのiCalフィードを生成して返します。
     *
     * @param \Illuminate\Http\Request $request
     * @param string $token
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, string $token)
    {
        // 1. トークンを使用してユーザーを検索
        // トークンが一致するユーザーを取得します。
        $user = User::where('ical_token', $token)->first();

        // ユーザーが見つからない場合は404エラーを返す
        if (!$user) {
            return Response::make('Calendar not found.', 404);
        }

        // 2. そのユーザーに紐づくサービスを取得
        // eager loading を使用して、サービスの取得時にユーザー情報も一緒に取得すると効率が良いですが、
        // ここではすでに $user があるので直接 services リレーションにアクセスします。
        $services = $user->services; // user() はリレーションメソッド、services は取得済みデータのコレクション

        // 3. iCalender形式のデータを生成
        // ここでiCalender形式の文字列を作成します。
        // BEGIN:VCALENDAR から END:VCALENDAR までの形式で記述します。
        // 各サービスは BEGIN:VEVENT から END:VEVENT で表現します。

        $icalContent = "BEGIN:VCALENDAR\r\n";
        $icalContent .= "VERSION:2.0\r\n";
        $icalContent .= "PRODID:-//Subscru//Subscru Subscription Calendar Feed//EN\r\n";
        $icalContent .= "CALSCALE:GREGORIAN\r\n";
        $icalContent .= "METHOD:PUBLISH\r\n"; // PUBLISHメソッドを追加

        foreach ($services as $service) {
            // 通知対象日をCarbonオブジェクトとしてパース
            $notificationDate = \Carbon\Carbon::parse($service->notification_date);

            // 通知タイミングを考慮したイベント開始日を計算
            // notification_timing が0の場合は当日、それ以外はその日数分前の日付
            $eventStartDate = $notificationDate->copy()->subDays($service->notification_timing);

            // iCalender形式の日付フォーマット (終日イベントなので YYYYMMDD 形式)
            $formattedEventStartDate = $eventStartDate->format('Ymd');

            // iCalenderイベントの生成
            $icalContent .= "BEGIN:VEVENT\r\n";
            $icalContent .= "UID:" . uniqid() . "@subscru.jp\r\n"; // よりユニークなUIDを生成 (サービスIDだけだと衝突の可能性も)
            $icalContent .= "DTSTAMP:" . \Carbon\Carbon::now()->format('Ymd\THis\Z') . "\r\n"; // 現在時刻 (UTC)
            $icalContent .= "DTSTART;VALUE=DATE:{$formattedEventStartDate}\r\n"; // 通知対象日 (終日イベント)
            // 終日イベントの場合、DTEND は DTSTART の翌日を設定するのが一般的ですが、必須ではありません。
            // 簡単のため、DTENDは省略します。必要であれば追加します。
            // $icalContent .= "DTEND;VALUE=DATE:" . $eventStartDate->copy()->addDay()->format('Ymd') . "\r\n";

            $icalContent .= "SUMMARY:" . addcslashes($service->name, ",;\\") . "の通知対象日\r\n"; // サービス名
            // メモが空の場合は表示しない、または「メモなし」と表示
            if (!empty($service->memo)) {
                $icalContent .= "DESCRIPTION:" . addcslashes($service->memo, ",;\\") . "\r\n"; // メモ
            } else {
                $icalContent .= "DESCRIPTION:メモなし\r\n";
            }

            // 必要に応じて他のプロパティも追加
            // 例: CREATED (サービス登録日時), LAST-MODIFIED (サービス更新日時)
            $icalContent .= "CREATED:" . \Carbon\Carbon::parse($service->created_at)->format('Ymd\THis\Z') . "\r\n";
            $icalContent .= "LAST-MODIFIED:" . \Carbon\Carbon::parse($service->updated_at)->format('Ymd\THis\Z') . "\r\n";

            $icalContent .= "END:VEVENT\r\n";
        }

        $icalContent .= "END:VCALENDAR\r\n";

        // 4. iCalender形式のレスポンスを返す
        // Content-Type を text/calendar に設定します。
        return Response::make($icalContent, 200, [
            'Content-Type' => 'text/calendar; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="subscru_feed.ics"', // ファイル名を指定
        ]);
    }
}
