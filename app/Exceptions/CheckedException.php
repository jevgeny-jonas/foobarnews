<?php

namespace App\Exceptions;

/**
 * By analogy with Java's checked and unchecked exceptions.
 * This is base class for exceptions that indicate abnormal conditions which
 * must be handled gracefully and are not bugs in code (for example, invalid input file from user or
 * invalid data from external service).
 */
class CheckedException extends \Exception
{
    //nothing
}
