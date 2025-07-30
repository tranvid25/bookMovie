<?php

namespace App\Exceptions;

use Exception;

class UserException extends Exception
{
    protected $message='User not found';
    protected $code=404;
}
