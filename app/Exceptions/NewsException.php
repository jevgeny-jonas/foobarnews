<?php

namespace App\Exceptions;

/**
 * Base class for exceptions thrown on errors that occur while fetching news feed
 * (can't fetch from the news server, or fetched data has incorrect format, etc.).
 */
class NewsException extends CheckedException
{
    //nothing
}
