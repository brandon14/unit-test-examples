<?php

declare(strict_types=1);

namespace App\Contracts\Services\Status;

/**
 * System status service provider interface. Allows implementation to retrieve the status
 * of a service.
 *
 * @author    Brandon Clothier <brandon14125@gmail.com>
 *
 * @version   1.0.0
 *
 * @license   MIT
 * @copyright 2018
 */
interface StatusServiceProvider
{
    /**
     * Status string for a service that is okay.
     *
     * @var string
     */
    public const STATUS_OK = 'OK';

    /**
     * Status string for a service that is unreachable or otherwise in error status.
     *
     * @var string
     */
    public const STATUS_ERROR = 'ERROR';

    /**
     * Status string for a service that has been disabled.
     *
     * @var string
     */
    public const STATUS_DISABLED = 'DISABLED';

    /**
     * Status for a unknown condition on the service.
     *
     * @var string
     */
    public const STATUS_UNKNOWN = 'UNKNOWN';

    /**
     * Get the status of the service.
     *
     * @return array
     */
    public function getStatus(): array;
}
