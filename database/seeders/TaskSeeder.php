<?php

namespace Database\Seeders;

use App\Enums\BoardMemberRole;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();

        foreach ($users as $user) {
            foreach ($user->boards as $board) {
                // Viewers won't get tasks
                if ($board->userHasRole($user, [BoardMemberRole::VIEWER])) {
                    continue;
                }

                $taskCount = rand(0, 5);

                // Either the user themselves, or any admin or owner user
                $validCreators = $board->members()
                    ->whereIn('role', [BoardMemberRole::OWNER, BoardMemberRole::ADMIN])
                    ->orWhere('user_id', $user->id)->distinct()->pluck('user_id');

                for ($i = 0; $i < $taskCount; $i++) {
                    Task::factory()->recycle([$board, $user])->create([
                        'created_by' => $validCreators->random(),
                        'category_id' => $board->categories()->get()->shuffle()->first(),
                    ]);
                }
            }
        }
    }
}
