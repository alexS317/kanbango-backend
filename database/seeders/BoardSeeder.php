<?php

namespace Database\Seeders;

use App\Enums\BoardMemberRole;
use App\Models\Board;
use App\Models\BoardCategory;
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
            $boardCount = rand(0, 2);

            for ($i = 0; $i < $boardCount; $i++) {
                $board = Board::factory()->create();

                BoardMember::factory()->recycle([$board, $user])->create([
                    'role' => BoardMemberRole::OWNER,
                ]);

                $otherUsers = $users->where('id', '!=', $user->id)->shuffle()->take(rand(0, 3));

                foreach ($otherUsers as $otherUser) {
                    BoardMember::factory()->recycle([$board, $otherUser])->create();
                }

                $defaultCategories = (array) explode(',', env('DEFAULT_BOARD_CATEGORIES'));

                foreach ($defaultCategories as $index => $category) {
                    BoardCategory::factory()->recycle($board)->create([
                        'name' => str_replace('_', ' ', $category),
                        'position' => $index,
                    ]);
                }
            }
        }
    }
}
