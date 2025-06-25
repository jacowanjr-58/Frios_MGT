<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Franchise;
use App\Models\User;

class FranchiseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $franchises = [
            [
                'business_name' => 'Frios Gourmet Pops - Downtown Austin',
                'contact_number' => '(512) 555-0101',
                'frios_territory_name' => 'Austin Downtown',
                'address1' => '123 Congress Avenue',
                'address2' => 'Suite 200',
                'city' => 'Austin',
                'zip_code' => '78701',
                'state' => 'TX',
                'location_zip' => '78701',
                'ACH_data_API' => json_encode(['account' => 'ACH_AUSTIN_001', 'routing' => '111000025']),
                'pos_service_API' => json_encode(['terminal_id' => 'POS_AUSTIN_001', 'merchant_id' => 'MERCHANT_001']),
            ],
            [
                'business_name' => 'Austin',
                'contact_number' => '(214) 555-0102',
                'frios_territory_name' => 'Austin',
                'address1' => '456 Preston Road',
                'address2' => null,
                'city' => 'Dallas',
                'zip_code' => '75240',
                'state' => 'TX',
                'location_zip' => '75240',
                'ACH_data_API' => json_encode(['account' => 'ACH_DALLAS_001', 'routing' => '111000026']),
                'pos_service_API' => json_encode(['terminal_id' => 'POS_DALLAS_001', 'merchant_id' => 'MERCHANT_002']),
            ],
            [
                'business_name' => 'Houston Heights',
                'contact_number' => '(713) 555-0103',
                'frios_territory_name' => 'Houston Heights',
                'address1' => '789 Heights Boulevard',
                'address2' => 'Building A',
                'city' => 'Houston',
                'zip_code' => '77008',
                'state' => 'TX',
                'location_zip' => '77008',
                'ACH_data_API' => json_encode(['account' => 'ACH_HOUSTON_001', 'routing' => '111000027']),
                'pos_service_API' => json_encode(['terminal_id' => 'POS_HOUSTON_001', 'merchant_id' => 'MERCHANT_003']),
            ],
            [
                'business_name' => 'San Antonio Riverwalk',
                'contact_number' => '(210) 555-0104',
                'frios_territory_name' => 'San Antonio Central',
                'address1' => '321 River Walk',
                'address2' => 'Unit 15',
                'city' => 'San Antonio',
                'zip_code' => '78205',
                'state' => 'TX',
                'location_zip' => '78205',
                'ACH_data_API' => json_encode(['account' => 'ACH_SANANTONIO_001', 'routing' => '111000028']),
                'pos_service_API' => json_encode(['terminal_id' => 'POS_SANANTONIO_001', 'merchant_id' => 'MERCHANT_004']),
            ],
            [
                'business_name' => 'Fort Worth Stockyards',
                'contact_number' => '(817) 555-0105',
                'frios_territory_name' => 'Fort Worth West',
                'address1' => '654 Stockyards Boulevard',
                'address2' => null,
                'city' => 'Fort Worth',
                'zip_code' => '76164',
                'state' => 'TX',
                'location_zip' => '76164',
                'ACH_data_API' => json_encode(['account' => 'ACH_FORTWORTH_001', 'routing' => '111000029']),
                'pos_service_API' => json_encode(['terminal_id' => 'POS_FORTWORTH_001', 'merchant_id' => 'MERCHANT_005']),
            ],
            // [
            //     'business_name' => 'El Paso West',
            //     'contact_number' => '(915) 555-0106',
            //     'frios_territory_name' => 'El Paso West Side',
            //     'address1' => '987 Mesa Street',
            //     'address2' => 'Floor 2',
            //     'city' => 'El Paso',
            //     'zip_code' => '79912',
            //     'state' => 'TX',
            //     'location_zip' => '79912',
            //     'ACH_data_API' => json_encode(['account' => 'ACH_ELPASO_001', 'routing' => '111000030']),
            //     'pos_service_API' => json_encode(['terminal_id' => 'POS_ELPASO_001', 'merchant_id' => 'MERCHANT_006']),
            // ],
            // [
            //     'business_name' => 'Plano Legacy',
            //     'contact_number' => '(972) 555-0107',
            //     'frios_territory_name' => 'Plano Legacy West',
            //     'address1' => '147 Legacy Drive',
            //     'address2' => 'Suite 300',
            //     'city' => 'Plano',
            //     'zip_code' => '75023',
            //     'state' => 'TX',
            //     'location_zip' => '75023',
            //     'ACH_data_API' => json_encode(['account' => 'ACH_PLANO_001', 'routing' => '111000031']),
            //     'pos_service_API' => json_encode(['terminal_id' => 'POS_PLANO_001', 'merchant_id' => 'MERCHANT_007']),
            // ],
            // [
            //     'business_name' => 'Arlington Entertainment',
            //     'contact_number' => '(817) 555-0108',
            //     'frios_territory_name' => 'Arlington Entertainment District',
            //     'address1' => '258 Entertainment Way',
            //     'address2' => null,
            //     'city' => 'Arlington',
            //     'zip_code' => '76011',
            //     'state' => 'TX',
            //     'location_zip' => '76011',
            //     'ACH_data_API' => json_encode(['account' => 'ACH_ARLINGTON_001', 'routing' => '111000032']),
            //     'pos_service_API' => json_encode(['terminal_id' => 'POS_ARLINGTON_001', 'merchant_id' => 'MERCHANT_008']),
            // ],
            // [
            //     'business_name' => 'The Woodlands Market',
            //     'contact_number' => '(281) 555-0109',
            //     'frios_territory_name' => 'The Woodlands Market Street',
            //     'address1' => '369 Market Street',
            //     'address2' => 'Pavilion C',
            //     'city' => 'The Woodlands',
            //     'zip_code' => '77380',
            //     'state' => 'TX',
            //     'location_zip' => '77380',
            //     'ACH_data_API' => json_encode(['account' => 'ACH_WOODLANDS_001', 'routing' => '111000033']),
            //     'pos_service_API' => json_encode(['terminal_id' => 'POS_WOODLANDS_001', 'merchant_id' => 'MERCHANT_009']),
            // ],
            // [
            //     'business_name' => 'Corpus Christi Bay',
            //     'contact_number' => '(361) 555-0110',
            //     'frios_territory_name' => 'Corpus Christi Bayfront',
            //     'address1' => '741 Ocean Drive',
            //     'address2' => 'Marina Plaza',
            //     'city' => 'Corpus Christi',
            //     'zip_code' => '78401',
            //     'state' => 'TX',
            //     'location_zip' => '78401',
            //     'ACH_data_API' => json_encode(['account' => 'ACH_CORPUSCHRISTI_001', 'routing' => '111000034']),
            //     'pos_service_API' => json_encode(['terminal_id' => 'POS_CORPUSCHRISTI_001', 'merchant_id' => 'MERCHANT_010']),
            // ],
        ];

        // Create franchise records and track their IDs
        $createdFranchises = [];
        foreach ($franchises as $franchiseData) {
            $franchise = Franchise::create($franchiseData);
            $createdFranchises[] = $franchise;
        }
        $this->command->info('Created 10 franchise records successfully!');
        // Create user-franchise relationships (optional - assigns franchises to existing users)
        $user = User::where('email', 'franchiseadmin@friospops.com')->first(); // Get first 10 users if they exist
        if ($user) {
            foreach ($createdFranchises as $index => $franchise) {
                    // Create relationship in user_franchises pivot table
                    $user->franchises()->attach($franchise->id);
                    $this->command->info("Assigned franchise '{$franchise->business_name}' to user '{$user->email}'");
            }
        } else {
            $this->command->info('No users found to assign franchises to. Run user seeders first if you want user-franchise relationships.');
        }
    }
} 