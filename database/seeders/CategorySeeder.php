<?php

namespace Database\Seeders;

use App\Models\Category;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $time = [
            'created_at' => Carbon::now()->toDateTimeString(),
            'updated_at' => Carbon::now()->toDateTimeString()
        ];

        $data = [
            'language' => array_merge([
                'name' => 'Language',
                'description' => 'Learning Language',
                'parent_id' => 0,
            ], $time),
            'health' => array_merge([
                'name' => 'Health',
                'description' => 'Everything about your health. Something like that remember for eating pills',
                'parent_id' => 0,
            ], $time)
        ];

        Category::query()->insert($data);
    }
}
