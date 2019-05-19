<?php

use App\Models\Backend\Post;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class PostsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker\Factory::create();

        foreach (Storage::allFiles('images/posts') as $file){
            if(pathinfo($file, PATHINFO_EXTENSION) == 'jpg'){
                Storage::delete($file);
            }

        }
        $posts = [];

        foreach (range(1, 20, 1) as $key) {
            $posts[$key] = [
                'user_id' => $faker->numberBetween(1, 4),
                'title' => $faker->sentence,
                'content' => $faker->paragraph,
                'featured_image' => $faker->image(storage_path('app/public/images/posts'), 400, 400, 'business', false),
                'is_published' => $faker->boolean,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        Post::insert($posts);
    }
}
