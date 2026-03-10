<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        Role::firstOrCreate(['name' => 'super_admin']);
        Role::firstOrCreate(['name' => 'agent']);

        $admin = User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
        ]);
        $admin->assignRole('super_admin');

        $agent = User::factory()->create([
            'name' => 'Agent',
            'email' => 'agent@example.com',
        ]);
        $agent->assignRole('agent');

        User::factory(5)->create();

        $this->call([
            DepartmentSeeder::class,
            TicketCategorySeeder::class,
            SlaPolicySeeder::class,
            KnowledgebaseCategorySeeder::class,
            KnowledgebaseArticleSeeder::class,
            TicketSeeder::class,
        ]);
    }
}
