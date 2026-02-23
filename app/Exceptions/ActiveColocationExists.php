<?php

namespace App\Exceptions;

use RuntimeException;

class ActiveColocationExists extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('User already has an active colocation.');
    }
}
