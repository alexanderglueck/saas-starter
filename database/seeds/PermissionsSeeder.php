<?php

use App\Permission;
use Illuminate\Database\Seeder;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = [
            'view' => [
                // Add entities
            ],
            'create' => [

            ],
            'edit' => [

            ],
            'delete' => [

            ],
            'invite' => [

            ],
            'impersonate' => [

            ]
        ];

        foreach ($permissions as $action => $entities) {
            foreach ($entities as $entity) {
                Permission::firstOrCreate([
                    'name' => $action . ' ' . $entity
                ]);
            }
        }
    }

}
