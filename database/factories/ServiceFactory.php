<?php

namespace Database\Factories;

use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User; // Userモデルを使用

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Service>
 */
class ServiceFactory extends Factory
{
    /**
     * The name of the corresponding model.
     *
     * @var string
     */
    protected $model = Service::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // テスト用のデータをランダムに生成
        $types = ['contract', 'trial'];
        $icons = [
            'fas fa-music',
            'fas fa-cloud',
            'fas fa-database',
            'fas fa-paint-brush',
            'fas fa-gamepad',
            'fas fa-book',
            'fas fa-film',
            'fas fa-code',
        ];

        return [
            // 'user_id' は ServiceSeeder で指定するため、ここでは定義しない
            // 'user_id' => User::factory(), // UserFactory がある場合
            'name' => $this->faker->company . ' ' . $this->faker->word, // 会社名 + 単語 でそれっぽいサービス名
            'type' => $this->faker->randomElement($types),
            // 通知対象日をランダムに設定 (例: 過去1年から未来2年)
            'notification_date' => $this->faker->dateTimeBetween('-1 year', '+2 years')->format('Y-m-d'),
            'notification_timing' => $this->faker->randomElement([0, 1, 3, 7, 30]), // ランダムな通知タイミング
            'memo' => $this->faker->sentence, // ランダムな文章でメモ
            'category_icon' => $this->faker->randomElement($icons),
        ];
    }
}
