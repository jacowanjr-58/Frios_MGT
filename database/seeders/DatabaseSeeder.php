<?php

namespace Database\Seeders;
use Spatie\Permission\Models\Role;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run()
    {
        // Run roles and permissions seeder first
        $this->call([
            RolesAndPermissionsSeeder::class,
        ]);
    
        // Creating a sample corporate admin
        $user = User::create([
            'name' => 'Corporate Admin',
            'email' => 'corporateadmin@friospops.com',
            'password' => bcrypt('password'),
            'role' => 'corporate_admin', 
        ]);

        $user->assignRole('corporate_admin');

        // Creating a sample franchise admin
        $user = User::create([
                'name' => 'Franchise Admin',
                'email' => 'franchiseadmin@friospops.com',
                'password' => bcrypt('password'),
                'role' => 'franchise_admin',
            ]);
        $user->assignRole('franchise_admin');
        

        // Creating a sample franchise manager
        $user = User::create([
            'name' => 'Franchise Manager',
            'email' => 'franchisemanager@friospops.com',
            'password' => bcrypt('password'),
            'role' => 'franchise_manager',
        ]);
        $user->assignRole('franchise_manager');

         // Creating a sample franchise staff
         $user = User::create([
            'name' => 'Franchise Staff',
            'email' => 'franchisestaff@friospops.com',
            'password' => bcrypt('password'),
            'role' => 'franchise_staff',
        ]);
        $user->assignRole('franchise_staff');
        

        // Creating a sample corporate admin
        $user = User::create([
            'name' => 'Samad',
            'email' => 'abdulsamadalvi73@gmail.com',
            'password' => bcrypt('AbdulSamadPassword'),
            'role' => 'corporate_admin', 
        ]);
        $user->assignRole('corporate_admin');
    }
}
