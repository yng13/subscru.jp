<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // UserSeeder が存在し、Userテーブルを作成・データ投入する場合は先に実行
        // $this->call(UserSeeder::class);

        // ServiceSeeder を呼び出し
        $this->call(ServiceSeeder::class);
    }
}
