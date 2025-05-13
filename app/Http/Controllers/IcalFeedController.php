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
            // 通知対象日を YYYYMMDD 形式にフォーマット
            $notificationDate = \Carbon\Carbon::parse($service->notification_date)->format('Ymd');

            // iCalenderイベントの生成
            $icalContent .= "BEGIN:VEVENT\r\n";
            $icalContent .= "UID:{$service->id}@subscru.jp\r\n"; // サービスIDを元にしたユニークなUID
            $icalContent .= "DTSTAMP:" . \Carbon\Carbon::now()->format('Ymd\THis\Z') . "\r\n"; // 現在時刻
            $icalContent .= "DTSTART;VALUE=DATE:{$notificationDate}\r\n"; // 通知対象日 (終日イベントとして扱います)
            $icalContent .= "SUMMARY:" . addcslashes($service->name, ",;\\") . "の通知対象日\r\n"; // サービス名
            $icalContent .= "DESCRIPTION:" . addcslashes($service->memo ?? 'メモなし', ",;\\") . "\r\n"; // メモ
            // LOCATION や URL など、必要に応じて他のプロパティも追加できます
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
