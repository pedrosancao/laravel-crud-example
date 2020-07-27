<?php

use App\Department as Model;
use Illuminate\Database\Seeder;

class DepartmentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $models = [[
            'name'        => 'IT',
            'slug'        => 'it',
            'description' => 'Information Technology',
        ],[
            'name'        => 'HR',
            'slug'        => 'hr',
            'description' => 'Human Resources',
        ],[
            'name'        => 'Accounts',
            'slug'        => 'accounts',
            'description' => 'Accounts department',
        ]];
        foreach ($models as $model) {
            if (Model::where('slug', $model['slug'])->count() === 0) {
                Model::create($model);
            }
        }
    }
}
