<?php

/**
 * This file is part of the brandon14/unit-test-examples package.
 *
 * MIT License
 *
 * Copyright (c) 2018-2021 Brandon Clothier
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 *
 */

declare(strict_types=1);

namespace App\Services\Status\Providers;

use Redis;
use Throwable;
use App\Contracts\Services\Status\StatusServiceProvider;

/**
 * Class PhpRedisProvider.
 *
 * PHP redis status provider. Allows pinging a redis cache database via
 * {@link \Redis} to check the status of the database.
 *
 * @author Brandon Clothier <brandon14125@gmail.com>
 */
class PhpRedisProvider implements StatusServiceProvider
{
    /**
     * Redis client instance.
     */
    protected Redis $redis;

    /**
     * Construct a new PHP redis status provider.
     *
     * @param \Redis $redis Redis instance
     *
     * @return void
     */
    public function __construct(Redis $redis)
    {
        $this->redis = $redis;
    }

    /**
     * {@inheritdoc}
     */
    public function getStatus(): array
    {
        try {
            // PHP redis client returns true on a successful ping.
            return $this->redis->ping()
                ? ['status' => StatusServiceProvider::STATUS_OK]
                : ['status' => StatusServiceProvider::STATUS_ERROR];
        } catch (Throwable $e) {
            // Swallow exceptions on purpose.
        }

        return ['status' => StatusServiceProvider::STATUS_ERROR];
    }
}
