<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\BoardMemberRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\BoardRequest;
use App\Http\Resources\BoardResource;
use App\Models\Board;
use App\Models\BoardCategory;
use App\Models\BoardMember;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;

class BoardController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', Board::class);

        $boards = request()->user()->boards()->get();

        return BoardResource::collection($boards);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(BoardRequest $request)
    {
        $this->authorize('create', Board::class);

        $validated = $request->validated();

        $board = DB::transaction(function () use ($request, $validated) {
            $board = Board::create([
                'title' => $validated['title'],
            ]);

            BoardMember::create([
                'board_id' => $board->id,
                'user_id' => $request->user()->id,
                'role' => BoardMemberRole::OWNER,
            ]);

            $defaultCategories = (array) explode(',', env('DEFAULT_BOARD_CATEGORIES'));
            foreach ($defaultCategories as $index => $category) {
                BoardCategory::create([
                    'board_id' => $board->id,
                    'name' => str_replace('_', ' ', $category),
                    'position' => $index,
                ]);
            }

            return $board;
        });

        return new BoardResource($board);
    }

    /**
     * Display the specified resource.
     */
    public function show(Board $board)
    {
        $this->authorize('view', $board);

        return new BoardResource($board);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(BoardRequest $request, Board $board)
    {
        $this->authorize('update', $board);

        $validated = $request->validated();

        $board->update($validated);

        return new BoardResource($board);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Board $board)
    {
        $this->authorize('delete', $board);

        $board->delete();

        return response()->noContent();
    }
}
