<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\BoardCategory\BoardCategoryRequest;
use App\Http\Requests\BoardCategory\ReorderBoardCategoryRequest;
use App\Http\Resources\BoardCategoryResource;
use App\Models\Board;
use App\Models\BoardCategory;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;

class BoardCategoryController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index(Board $board)
    {
        $this->authorize('viewAny', [BoardCategory::class, $board]);

        $categories = $board->categories;

        return BoardCategoryResource::collection($categories);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(BoardCategoryRequest $request, Board $board)
    {
        $this->authorize('create', [BoardCategory::class, $board]);

        $validated = $request->validated();

        // Always append new category at the end
        $nextPosition = $board->categories()->count();

        $category = BoardCategory::create([
            'board_id' => $board->id,
            'name' => $validated['name'],
            'position' => $nextPosition,
        ]);

        return new BoardCategoryResource($category);
    }

    /**
     * Display the specified resource.
     */
    public function show(Board $board, BoardCategory $category)
    {
        $this->authorize('view', $category);

        return new BoardCategoryResource($category);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(BoardCategoryRequest $request, Board $board, BoardCategory $category)
    {
        $this->authorize('update', $category);

        $validated = $request->validated();

        $category->update($validated);

        return new BoardCategoryResource($category);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Board $board, BoardCategory $category)
    {
        $this->authorize('delete', $category);

        // Close position gap when category is deleted
        DB::transaction(function () use ($board, $category) {
            $deletedPosition = $category->position;

            $category->delete();
            $board->categories()->where('position', '>', $deletedPosition)->decrement('position');
        });

        return response()->noContent();
    }

    /**
     * Update the order of board categories.
     */
    public function reorder(ReorderBoardCategoryRequest $request, Board $board, BoardCategory $category)
    {
        $this->authorize('update', $category);

        $validated = $request->validated();

        $oldPosition = $category->position;
        $newPosition = $validated['position'];

        if ($newPosition === $oldPosition || $newPosition >= $board->categories()->count()) {
            return;
        }

        // Iterating over each row separately required to work with unique constraint on table
        DB::transaction(function () use ($board, $category, $validated, $oldPosition, $newPosition) {

            // Temporarily move item out of the way to not trigger unique constraint
            $category->update(['position' => PHP_INT_MIN]);

            // Move position up
            if ($newPosition > $oldPosition) {
                $board->categories()
                    ->where('position', '<=', $newPosition)
                    ->where('position', '>', $oldPosition)
                    ->orderBy('position')
                    ->get()
                    ->each(fn ($c) => $c->decrement('position'));
            }
            // Move position down
            if ($newPosition < $oldPosition) {
                $board->categories()
                    ->where('position', '>=', $newPosition)
                    ->where('position', '<', $oldPosition)
                    ->orderByDesc('position')
                    ->get()
                    ->each(fn ($c) => $c->increment('position'));
            }

            $category->update($validated);
        });

        return new BoardCategoryResource($category);
    }
}
