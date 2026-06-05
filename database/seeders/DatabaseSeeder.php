<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use App\Models\Area;
use App\Models\Outlet;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // প্রথমে RoleSeeder রান করুন
        $this->call(RoleSeeder::class);
        
        // Areas তৈরি করুন
        $areas = [
            ['name' => 'Gulshan', 'city' => 'Dhaka', 'delivery_charge' => 60],
            ['name' => 'Banani', 'city' => 'Dhaka', 'delivery_charge' => 60],
            ['name' => 'Uttara', 'city' => 'Dhaka', 'delivery_charge' => 70],
            ['name' => 'Dhanmondi', 'city' => 'Dhaka', 'delivery_charge' => 50],
            ['name' => 'Mohammadpur', 'city' => 'Dhaka', 'delivery_charge' => 55],
            ['name' => 'Chittagong', 'city' => 'Chittagong', 'delivery_charge' => 80],
            ['name' => 'Sylhet', 'city' => 'Sylhet', 'delivery_charge' => 75],
        ];
        
        foreach ($areas as $area) {
            Area::create($area);
        }
        
        // রোল আইডি গেট করুন
        $adminRole = Role::where('name', 'admin')->first();
        $headOfficeRole = Role::where('name', 'head-office')->first();
        $areaManagerRole = Role::where('name', 'area-manager')->first();
        $marketingOfficerRole = Role::where('name', 'marketing-officer')->first();
        $outletOwnerRole = Role::where('name', 'outlet-owner')->first();
        $riderRole = Role::where('name', 'rider')->first();
        
        // 1. এডমিন ইউজার তৈরি করুন
        $admin = User::create([
            'name' => 'Super Admin',
            'email' => 'admin@motolink.com',
            'phone' => '01710000001',
            'password' => Hash::make('password'),
            'role_id' => $adminRole->id,
            'area_id' => null,
            'wallet_balance' => 0,
            'is_active' => true,
        ]);
        
        // 2. হেড অফিস ইউজার
        $headOffice = User::create([
            'name' => 'Head Office Manager',
            'email' => 'headoffice@motolink.com',
            'phone' => '01710000002',
            'password' => Hash::make('password'),
            'role_id' => $headOfficeRole->id,
            'area_id' => null,
            'wallet_balance' => 0,
            'is_active' => true,
        ]);
        
        // 3. এরিয়া ম্যানেজার তৈরি করুন
        $area1 = Area::where('name', 'Gulshan')->first();
        $areaManager1 = User::create([
            'name' => 'Area Manager Gulshan',
            'email' => 'manager.gulshan@motolink.com',
            'phone' => '01710000003',
            'password' => Hash::make('password'),
            'role_id' => $areaManagerRole->id,
            'area_id' => $area1->id,
            'wallet_balance' => 0,
            'is_active' => true,
        ]);
        
        $area2 = Area::where('name', 'Banani')->first();
        $areaManager2 = User::create([
            'name' => 'Area Manager Banani',
            'email' => 'manager.banani@motolink.com',
            'phone' => '01710000004',
            'password' => Hash::make('password'),
            'role_id' => $areaManagerRole->id,
            'area_id' => $area2->id,
            'wallet_balance' => 0,
            'is_active' => true,
        ]);
        
        // 4. মার্কেটিং অফিসার তৈরি করুন
        $marketingOfficer = User::create([
            'name' => 'Marketing Officer',
            'email' => 'marketing@motolink.com',
            'phone' => '01710000005',
            'password' => Hash::make('password'),
            'role_id' => $marketingOfficerRole->id,
            'area_id' => null,
            'wallet_balance' => 0,
            'is_active' => true,
        ]);
        
        // 5. আউটলেট ওনার তৈরি করুন (Contracted/Large Shop)
        $outletOwner1 = User::create([
            'name' => 'John Doe',
            'email' => 'outlet1@motolink.com',
            'phone' => '01710000006',
            'password' => Hash::make('password'),
            'role_id' => $outletOwnerRole->id,
            'area_id' => $area1->id,
            'wallet_balance' => 10000,
            'is_active' => true,
        ]);
        
        // বড় দোকান (Contracted)
        $largeOutlet = Outlet::create([
            'name' => 'John Doe',
            'shop_name' => 'Super Bike Parts - Gulshan',
            'phone' => '01710000006',
            'email' => 'superbike@motolink.com',
            'address' => 'Gulshan-1, Dhaka',
            'latitude' => 23.7895,
            'longitude' => 90.4175,
            'area_id' => $area1->id,
            'user_id' => $outletOwner1->id,
            'type' => 'contracted',
            'is_verified' => true,
            'wallet_balance' => 10000,
        ]);
        
        // 6. ছোট দোকান তৈরি করুন (Small Outlet)
        $smallOutletOwner = User::create([
            'name' => 'Jane Smith',
            'email' => 'smalloutlet@motolink.com',
            'phone' => '01710000007',
            'password' => Hash::make('password'),
            'role_id' => $outletOwnerRole->id,
            'area_id' => $area2->id,
            'wallet_balance' => 5000,
            'is_active' => true,
        ]);
        
        $smallOutlet = Outlet::create([
            'name' => 'Jane Smith',
            'shop_name' => 'Local Bike Repair Shop',
            'phone' => '01710000007',
            'email' => 'localrepair@motolink.com',
            'address' => 'Banani-11, Dhaka',
            'latitude' => 23.7957,
            'longitude' => 90.4055,
            'area_id' => $area2->id,
            'user_id' => $smallOutletOwner->id,
            'type' => 'small',
            'is_verified' => true,
            'wallet_balance' => 5000,
        ]);
        
        // 7. রাইডার তৈরি করুন
        $rider1 = User::create([
            'name' => 'Rahim Uddin',
            'email' => 'rider1@motolink.com',
            'phone' => '01710000008',
            'password' => Hash::make('password'),
            'role_id' => $riderRole->id,
            'area_id' => $area1->id,
            'wallet_balance' => 0,
            'is_active' => true,
        ]);
        
        $rider2 = User::create([
            'name' => 'Karim Mia',
            'email' => 'rider2@motolink.com',
            'phone' => '01710000009',
            'password' => Hash::make('password'),
            'role_id' => $riderRole->id,
            'area_id' => $area2->id,
            'wallet_balance' => 0,
            'is_active' => true,
        ]);
        
        $rider3 = User::create([
            'name' => 'Shahadat Hossain',
            'email' => 'rider3@motolink.com',
            'phone' => '01710000010',
            'password' => Hash::make('password'),
            'role_id' => $riderRole->id,
            'area_id' => $area1->id,
            'wallet_balance' => 0,
            'is_active' => true,
        ]);
        
        // 8. প্রোডাক্ট ক্যাটাগরি তৈরি করতে ProductCategorySeeder কল করুন
        $this->call(ProductCategorySeeder::class);
        
        // 9. প্রোডাক্ট তৈরি করতে ProductSeeder কল করুন
        $this->call(ProductSeeder::class);
        
        // এই মেসেজ প্রিন্ট হবে
        $this->command->info('Database seeded successfully!');
        $this->command->info('Admin Email: admin@motolink.com, Password: password');
        $this->command->info('Rider Email: rider1@motolink.com, Password: password');
        $this->command->info('Outlet Email: outlet1@motolink.com, Password: password');
    }
}