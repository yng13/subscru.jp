<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',             // ユーザーID
        'name',                // サービス名
        'type',                // 種別 ('contract' または 'trial')
        'notification_date',   // 通知対象日
        'notification_timing', // 通知タイミング
        'memo',                // メモ
        'category_icon',       // カテゴリアイコン
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        // 必要に応じて非表示にするカラムを追加 (例: 'password')
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'notification_date' => 'date', // notification_date を日付としてキャスト
        'created_at' => 'datetime',    // created_at を日付時刻としてキャスト
        'updated_at' => 'datetime',    // updated_at を日付時刻としてキャスト
    ];

    // サービスの user (リレーション)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
