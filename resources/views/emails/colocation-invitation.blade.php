<p>You have been invited to join a colocation.</p>
<p>Colocation: {{ $invitation->colocation->name }}</p>
<p>Invitation email: {{ $invitation->email }}</p>
<p>Accept link: {{ url('/invitations/'.$invitation->token.'/accept') }}</p>
<p>Refuse link: {{ url('/invitations/'.$invitation->token.'/refuse') }}</p>
<p>Invitation page: {{ route('invitations.show', $invitation->token) }}</p>
