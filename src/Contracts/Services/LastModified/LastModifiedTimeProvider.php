<?php

declare(strict_types=1);

namespace App\Contracts\Services\LastModified;

/**
 * Last modified time provider interface. Must implement a method
 * to return the last modified timestamp as an int.
 *
 * @author    Brandon Clothier <brandon14125@gmail.com>
 *
 * @version   1.0.0
 *
 * @license   MIT
 * @copyright 2018
 */
interface LastModifiedTimeProvider
{
    /**
     * Gets the last modified time for the provider.
     *
     * @return int
     */
    public function getLastModifiedTime(): int;
}
