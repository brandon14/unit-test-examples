<?php

declare(strict_types=1);

namespace App\Contracts\Services\LastModified;

use RuntimeException;

/**
 * Exception thrown when trying to get the last modified timestamp for a provider that is
 * not registered.
 *
 * @author    Brandon Clothier <brandon14125@gmail.com>
 *
 * @version   1.0.0
 *
 * @license   MIT
 * @copyright 2018
 */
class LastModifiedProviderNotRegisteredException extends RuntimeException
{
    // No content.
}
