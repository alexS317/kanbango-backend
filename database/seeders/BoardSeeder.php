<?php

namespace Database\Seeders;

use App\Models\Board;
use App\Models\BoardMember;
use App\Models\User;
use Illuminate\Database\Seeder;

class BoardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();

        foreach ($users as $user) {
            $boardCount = rand(0, 3);

            for ($i = 0; $i < $boardCount; $i++) {
                $board = Board::factory()->create();

                BoardMember::factory()->recycle($board)->recycle($user)->create([
                    'role' => 'owner',
                ]);

                $otherUsers = $users->where('id', '!=', $user->id)->shuffle()->take(rand(0, 3));

                foreach ($otherUsers as $otherUser) {
                    BoardMember::factory()->recycle($board)->recycle($otherUser)->create();
                }
            }
        }
    }
}
