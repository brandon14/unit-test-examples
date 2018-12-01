<?php

declare(strict_types=1);

namespace App\Contracts\Services\Status;

use RuntimeException;

/**
 * Exception thrown when trying to get the status for a provider that is not registered.
 *
 * @author    Brandon Clothier <brandon14125@gmail.com>
 *
 * @version   1.0.0
 *
 * @license   MIT
 * @copyright 2018
 */
class StatusProviderNotRegisteredException extends RuntimeException
{
    // No content.
}
