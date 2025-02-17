<?php

namespace App\Application\Shared\Exceptions;

use Exception;
use Throwable;

class ResourceNotFoundException extends Exception
{
    protected $code = 404;

    protected $message = 'Resource not found';

    public function __construct(string $message = "", ?Throwable $previous = null)
    {
        parent::__construct($message, $this->code, $previous);
    }

    public function getStatusCode(): int
    {
        return $this->code;
    }
}
