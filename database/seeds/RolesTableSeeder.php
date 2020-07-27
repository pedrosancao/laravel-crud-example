<?php

use App\Role as Model;
use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $models = [[
            'name'        => 'Director',
            'slug'        => 'director',
            'description' => 'Director role',
        ],[
            'name'        => 'Manager',
            'slug'        => 'manager',
            'description' => 'Manager role',
        ],[
            'name'        => 'Assistant',
            'slug'        => 'assistant',
            'description' => 'Assistant role',
        ]];
        foreach ($models as $model) {
            if (Model::where('slug', $model['slug'])->count() === 0) {
                Model::create($model);
            }
        }
    }
}
