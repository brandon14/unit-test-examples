<?php

declare(strict_types=1);

/*
 * This file is part of the unit-test-examples package.
 *
 * Copyright 2018-2019 Brandon Clothier
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation
 * files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy,
 * modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software
 * is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
 * OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
 * LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR
 * IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 *
 */

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
