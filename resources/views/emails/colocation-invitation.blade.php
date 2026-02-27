@php
    $invitationUrl = route('invitations.show', $invitation->token);
@endphp

<div style="background:#f3f4f6;padding:24px;font-family:Arial,sans-serif;color:#111827;">
    <div style="max-width:560px;margin:0 auto;background:#ffffff;border:1px solid #e5e7eb;border-radius:12px;padding:24px;">
        <p style="margin:0 0 8px;font-size:14px;color:#4b5563;">You are invited to join a colocation</p>
        <h2 style="margin:0 0 16px;font-size:24px;line-height:1.2;">{{ $invitation->colocation->name }}</h2>
        <p style="margin:0 0 20px;font-size:15px;line-height:1.5;color:#374151;">
            This invitation is for <strong>{{ $invitation->email }}</strong>.
            Open the invitation page to accept or refuse.
        </p>

        <p style="margin:0 0 20px;">
            <a href="{{ $invitationUrl }}"
               style="display:inline-block;background:#4f46e5;color:#ffffff;text-decoration:none;padding:10px 16px;border-radius:8px;font-weight:600;">
                Open invitation
            </a>
        </p>

        <p style="margin:0;font-size:13px;color:#6b7280;word-break:break-all;">
            If the button does not work, use this link:<br>
            <a href="{{ $invitationUrl }}" style="color:#4f46e5;">{{ $invitationUrl }}</a>
        </p>
    </div>
</div>
