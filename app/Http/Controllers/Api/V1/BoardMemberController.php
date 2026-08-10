<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateBoardMemberRequest;
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
    public function update(Board $board, UpdateBoardMemberRequest $request, BoardMember $member)
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
