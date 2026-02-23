<?php

namespace App\Exceptions;

use RuntimeException;

class InvitationNotPending extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('Invitation is no longer pending.');
    }
}
