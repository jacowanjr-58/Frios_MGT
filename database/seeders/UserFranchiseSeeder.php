<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Franchise;
use Illuminate\Support\Facades\DB;

class UserFranchiseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get existing users and franchises
        $users = User::all();
        $franchises = Franchise::all();

        if ($users->isEmpty()) {
            $this->command->error('No users found. Please run user seeders first.');
            return;
        }

        if ($franchises->isEmpty()) {
            $this->command->error('No franchises found. Please run FranchiseSeeder first.');
            return;
        }

        // Clear existing relationships
        DB::table('user_franchises')->truncate();

        $relationships = [
            // Super Admin - Access to multiple franchises for oversight
            [
                'user_email' => 'superadmin@friospops.com',
                'franchise_count' => 3,
                'description' => 'Super Admin with oversight access'
            ],
            // Corporate Admin - Access to all franchises for corporate oversight
            [
                'user_email' => 'corporateadmin@friospops.com',
                'franchise_count' => 0,
                'description' => 'Corporate Admin with broad access'
            ],
            // Franchise Admin - Typically manages 1-2 specific franchises
            [
                'user_email' => 'franchiseadmin@friospops.com',
                'franchise_count' => 2,
                'description' => 'Franchise Admin managing specific locations'
            ],
            // Franchise Manager - Usually manages one franchise
            [
                'user_email' => 'franchisemanager@friospops.com',
                'franchise_count' => 1,
                'description' => 'Franchise Manager for single location'
            ],
            // Franchise Staff - Works at one specific franchise
            [
                'user_email' => 'franchisestaff@friospops.com',
                'franchise_count' => 1,
                'description' => 'Franchise Staff at single location'
            ],
        ];

        $createdRelationships = 0;
        $franchisePool = $franchises->pluck('id')->toArray();

        foreach ($relationships as $relationship) {
            $user = $users->where('email', $relationship['user_email'])->first();
            
            if (!$user) {
                $this->command->warn("User with email {$relationship['user_email']} not found. Skipping...");
                continue;
            }

            // Get random franchises for this user
            $assignedFranchises = collect($franchisePool)
                ->shuffle()
                ->take($relationship['franchise_count'])
                ->toArray();

            foreach ($assignedFranchises as $franchiseId) {
                $franchise = $franchises->find($franchiseId);
                
                DB::table('user_franchises')->insert([
                    'user_id' => $user->id,
                    'franchise_id' => $franchiseId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $this->command->info("Assigned {$user->name} ({$user->email}) to franchise: {$franchise->business_name}");
                $createdRelationships++;
            }
        }

        // Add some additional relationships for demonstration
        // Assign some franchise admins to multiple locations (realistic scenario)
        $additionalUsers = $users->whereNotIn('email', collect($relationships)->pluck('user_email'))->take(3);
        
        foreach ($additionalUsers as $user) {
            $randomFranchise = $franchises->random();
            
            // Check if relationship already exists
            $exists = DB::table('user_franchises')
                ->where('user_id', $user->id)
                ->where('franchise_id', $randomFranchise->id)
                ->exists();
                
            if (!$exists) {
                DB::table('user_franchises')->insert([
                    'user_id' => $user->id,
                    'franchise_id' => $randomFranchise->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $this->command->info("Additional assignment: {$user->name} to {$randomFranchise->business_name}");
                $createdRelationships++;
            }
        }

        $this->command->info("Created {$createdRelationships} user-franchise relationships successfully!");
        
        // Display summary
        $this->displaySummary();
    }

    private function displaySummary(): void
    {
        $this->command->info("\n--- User-Franchise Relationship Summary ---");
        
        $users = User::with('franchises')->get();
        
        foreach ($users as $user) {
            if ($user->franchises->count() > 0) {
                $franchiseNames = $user->franchises->pluck('business_name')->implode(', ');
                $this->command->info("{$user->name} ({$user->role}): {$user->franchises->count()} franchise(s) - {$franchiseNames}");
            } else {
                $this->command->warn("{$user->name} ({$user->role}): No franchises assigned");
            }
        }
    }
} 