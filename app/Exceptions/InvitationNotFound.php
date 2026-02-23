<?php

namespace App\Exceptions;

use RuntimeException;

class InvitationNotFound extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('Invitation not found.');
    }
}
