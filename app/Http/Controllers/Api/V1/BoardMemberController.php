<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\BoardMemberRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\BoardMember\UpdateBoardMemberRequest;
use App\Http\Resources\BoardMemberResource;
use App\Models\Board;
use App\Models\BoardMember;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

    /**
     * Transfer the owner role to a different board member.
     */
    public function transferOwnerRole(Board $board, Request $request, BoardMember $member)
    {
        $this->authorize('transfer', $member);

        // Ensure there is only one owner to start with
        if ($board->members()->where('role', BoardMemberRole::OWNER)->count() > 1) {
            return;
        }

        $members = DB::transaction(function () use ($board, $request, $member) {
            $previousOwner = $board->members()->where('user_id', $request->user()->id)->firstOrFail();

            $member->update(['role' => BoardMemberRole::OWNER]);
            $previousOwner->update(['role' => BoardMemberRole::ADMIN]);

            return [$member, $previousOwner];
        });

        return BoardMemberResource::collection($members);
    }
}
