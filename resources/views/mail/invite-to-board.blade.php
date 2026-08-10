<x-mail::message>
# {{ $invitedBy->name }} has invited you to {{ $board->title }}!

Please click the following link to accept the invitation. If you don't have a KanbanGo account yet, you will have to
sign up first.

<x-mail::button :url="$url">
    Join Board
</x-mail::button>

This link will expire on {{ $expiresAt->format('d.m.Y, H:i') }}.

Your {{ config('app.name') }} team
</x-mail::message>
