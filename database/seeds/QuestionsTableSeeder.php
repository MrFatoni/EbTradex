<?php

use App\Models\User\Question;
use Illuminate\Database\Seeder;

class QuestionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker\Factory::create();

        $posts = [];

        foreach (range(1, 20, 1) as $key) {
            $posts[$key] = [
                'user_id' => $faker->numberBetween(1, 4),
                'title' => $faker->sentence,
                'content' => $faker->paragraph,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        Question::insert($posts);
    }
}
