<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\BoardMember\InviteBoardMemberRequest;
use App\Http\Resources\BoardInvitationResource;
use App\Http\Resources\BoardMemberResource;
use App\Mail\InviteToBoard;
use App\Models\Board;
use App\Models\BoardInvitation;
use App\Models\BoardMember;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class BoardInvitationController extends Controller
{
    use AuthorizesRequests;

    public function invite(InviteBoardMemberRequest $request, Board $board)
    {
        $this->authorize('create', [BoardMember::class, $board]);

        $validated = $request->validated();

        $existingUser = User::where('email', $validated['email'])->first();
        if ($existingUser && $board->members()->where('user_id', $existingUser->id)->exists()) {
            return response()->json(['message' => 'This user is already a member.'], 422);
        }

        $invitation = BoardInvitation::create([
            'email' => $validated['email'],
            'role' => $validated['role'],
            'token' => Str::random(64),
            'board_id' => $board->id,
            'invited_by' => $request->user()->id,
            'expires_at' => now()->addDays(7),
        ]);

        Mail::to($invitation->email)->send(new InviteToBoard($invitation));

        return new BoardInvitationResource($invitation);
    }

    public function accept(Request $request, string $token)
    {
        $invitation = BoardInvitation::where([
            ['token', '=', $token],
            ['expires_at', '>', now()],
        ])->firstOrFail();

        if ($request->user()->email !== $invitation->email) {
            return response()->json(['message' => 'This invitation is not for the currently logged in account.'], 403);
        }

        // Return the BoardMember unchanged if it already exists, only create a new one if it doesn't
        $member = DB::transaction(function () use ($request, $invitation) {
            BoardMember::firstOrCreate([
                'board_id' => $invitation->board_id,
                'user_id' => $request->user()->id,
            ],
                ['role' => $invitation->role],
            );

            $invitation->delete();
        });

        return new BoardMemberResource($member);
    }
}
