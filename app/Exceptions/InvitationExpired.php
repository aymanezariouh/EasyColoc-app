<?php

namespace App\Exceptions;

use RuntimeException;

class InvitationExpired extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('Invitation has expired.');
    }
}
