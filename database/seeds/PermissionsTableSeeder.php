<?php

use App\Permission as Model;
use Illuminate\Database\Seeder;

class PermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $models = [[
            'name'        => 'Create',
            'slug'        => 'create',
            'description' => 'Create records',
        ],[
            'name'        => 'Edit',
            'slug'        => 'edit',
            'description' => 'Edit records',
        ],[
            'name'        => 'Delete',
            'slug'        => 'delete',
            'description' => 'Delete records',
        ]];
        foreach ($models as $model) {
            if (Model::where('slug', $model['slug'])->count() === 0) {
                Model::create($model);
            }
        }
    }
}
