<?php

namespace Database\Seeders;

use App\Features\User\Models\Role;
use App\Features\User\Models\RoleName;
use App\Features\User\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class InitialSetupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminRole = Role::updateOrCreate(
            ['name' => RoleName::ADMIN->value],
            ['description' => 'Administrador del sistema']
        );

        Role::updateOrCreate(
            ['name' => RoleName::VETERINARIAN->value],
            ['description' => 'Veterinario']
        );

        Role::updateOrCreate(
            ['name' => RoleName::OWNER->value],
            ['description' => 'Dueño de mascota']
        );

        $adminUser = User::updateOrCreate(
            ['dni' => env('ADMIN_DNI')],
            [
                'name'      => 'Brayan Cárdenas',
                'email'     => env('ADMIN_EMAIL'),
                'password'  => Hash::make(env('ADMIN_PASSWORD')),
                'phone'     => env('ADMIN_PHONE'),
                'address'   => 'Sede Central Municipal',
                'latitude'  => null,
                'longitude' => null,
                'active'    => true
            ]
        );

        $adminUser->roles()->syncWithoutDetaching([$adminRole->id]);
    }
}
