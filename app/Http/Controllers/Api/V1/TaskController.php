<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Task\ChangeStatusTaskRequest;
use App\Http\Requests\Task\TaskRequest;
use App\Http\Resources\Task\BoardTaskResource;
use App\Http\Resources\Task\UserTaskResource;
use App\Models\Board;
use App\Models\Task;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class TaskController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display all tasks belonging to the current user.
     */
    public function indexOwn()
    {
        $this->authorize('viewAny', [Task::class, null]);

        $tasks = request()->user()->tasks()->get();

        return UserTaskResource::collection($tasks);
    }

    /**
     * Display a all tasks on the board.
     */
    public function index(Board $board)
    {
        $this->authorize('viewAny', [Task::class, $board]);

        $tasks = $board->tasks;

        return BoardTaskResource::collection($tasks);
    }

    /**
     * Store a newly created task in storage.
     */
    public function store(TaskRequest $request, Board $board)
    {
        $this->authorize('create', [Task::class, $board]);

        $validated = $request->validated();

        $task = Task::create([
            'board_id' => $board->id,
            'created_by' => $request->user()->id,
            'assigned_to' => $validated['assigned_to'],
            'title' => $validated['title'],
            'description' => $validated['description'],
            'category_id' => $validated['category_id'],
        ]);

        return new BoardTaskResource($task);
    }

    /**
     * Display the specified task.
     */
    public function show(Board $board, Task $task)
    {
        $this->authorize('view', $task);

        return new BoardTaskResource($task);
    }

    /**
     * Update the specified task in storage.
     */
    public function update(TaskRequest $request, Board $board, Task $task)
    {
        $this->authorize('update', $task);

        $validated = $request->validated();

        $task->update($validated);

        return new BoardTaskResource($task);
    }

    /**
     * Remove the specified task from storage.
     */
    public function destroy(Board $board, Task $task)
    {
        $this->authorize('delete', $task);

        $task->delete();

        return response()->noContent();
    }

    /**
     * Change the status of the specified task in storage.
     */
    public function changeStatus(ChangeStatusTaskRequest $request, Board $board, Task $task)
    {
        $this->authorize('update', $task);

        $validated = $request->validated();

        if ($task->category_id === $validated['category_id']) {
            return;
        }

        $task->update($validated);

        return new BoardTaskResource($task);
    }
}
