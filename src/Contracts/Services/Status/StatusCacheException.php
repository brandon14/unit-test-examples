<?php

declare(strict_types=1);

namespace App\Contracts\Services\Status;

use Exception;

/**
 * Exception thrown when the StatusService cannot store or
 * retrieve from the {@link \Psr\SimpleCache\CacheInterface}.
 *
 * @author    Brandon Clothier <brandon14125@gmail.com>
 *
 * @version   1.0.0
 *
 * @license   MIT
 * @copyright 2018
 */
class StatusCacheException extends Exception
{
    // No content.
}
