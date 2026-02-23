<?php

namespace App\Exceptions;

use RuntimeException;

class InvitationEmailMismatch extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('Invitation email does not match the logged-in user.');
    }
}
