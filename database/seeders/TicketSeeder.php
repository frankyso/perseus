<?php

namespace Database\Seeders;

use App\Models\Ticket;
use App\Models\TicketReply;
use App\Models\User;
use Illuminate\Database\Seeder;

class TicketSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::query()->limit(5)->get();

        if ($users->isEmpty()) {
            return;
        }

        foreach ($users as $user) {
            Ticket::factory()
                ->count(2)
                ->has(TicketReply::factory()->count(2)->state(['user_id' => $user->id]), 'replies')
                ->create(['user_id' => $user->id]);
        }
    }
}
