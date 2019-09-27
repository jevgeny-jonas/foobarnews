<?php

namespace App;

interface NewsProviderInterface
{
    /**
     * @return array Feed in format ['items' => [['title' => ..., 'description' => ..., 'permalink' => ...], ...]].
     * @throws App\Exceptions\NewsException If failed to fetch feed from remote server
     *         or format is invalid.
     */
    public function getNews(): array;
}
