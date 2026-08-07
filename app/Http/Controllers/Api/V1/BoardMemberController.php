<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\BoardMemberRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\BoardMemberRequest;
use App\Http\Resources\BoardMemberResource;
use App\Models\Board;
use App\Models\BoardMember;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class BoardMemberController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index(Board $board)
    {
        $this->authorize('viewAny', [BoardMember::class, $board]);

        $members = $board->members;

        return BoardMemberResource::collection($members);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(BoardMemberRequest $request, Board $board)
    {
        $this->authorize('create', [BoardMember::class, $board]);

        $validated = $request->validated();

        $member = BoardMember::create([
            'board_id' => $board->id,
            'user_id' => $validated['user_id'],
            'role' => $validated['role'],
        ]);

        return new BoardMemberResource($member);
    }

    /**
     * Display the specified resource.
     */
    public function show(Board $board, BoardMember $member)
    {
        $this->authorize('view', $member);

        return new BoardMemberResource($member);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Board $board, BoardMemberRequest $request, BoardMember $member)
    {
        $this->authorize('update', $member);

        $validated = $request->validated();

        $member->update($validated);

        return new BoardMemberResource($member);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Board $board, BoardMember $member)
    {
        $this->authorize('delete', $member);

        $member->delete();

        return response()->noContent();
    }
}
