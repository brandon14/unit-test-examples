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

namespace App\Contracts\Services\Status;

/**
 * Class StatusOptions.
 *
 * Status service options. This defines the options available for
 * the {@link \App\Contracts\Services\Status\StatusService} service.
 *
 * @codeCoverageIgnore
 * Ignore code coverage for this file. It performs no logic and only provides a
 * consistent definition for options for the Status service.
 *
 * @author Brandon Clothier <brandon14125@gmail.com>
 */
class StatusOptions
{
    /**
     * Whether to cache the statuses or not.
     */
    protected bool $isCacheEnabled;

    /**
     * How long to cache the statuses for.
     */
    protected int $cacheTtl;

    /**
     * Cache key.
     */
    protected string $cacheKey;

    /**
     * Constructs a new set of {@link \App\Contracts\Services\Status\StatusService} options.
     *
     * @SuppressWarnings("BooleanArgumentFlag")
     *
     * @param bool   $isCacheEnabled Whether caching is enabled
     * @param int    $cacheTtl       Cache time-to-live
     * @param string $cacheKey       Cache key
     *
     * @return void
     */
    public function __construct(
        bool $isCacheEnabled = true,
        int $cacheTtl = 30,
        string $cacheKey = 'statuses'
    ) {
        $this->isCacheEnabled = $isCacheEnabled;
        $this->cacheTtl = $cacheTtl;
        $this->cacheKey = $cacheKey;
    }

    /**
     * Get whether caching is enabled.
     *
     * @return bool Whether cache is enabled
     */
    public function isCacheEnabled(): bool
    {
        return $this->isCacheEnabled;
    }

    /**
     * Get cache TTL option.
     *
     * @return int Cache time-to-live
     */
    public function getCacheTtl(): int
    {
        return $this->cacheTtl;
    }

    /**
     * Get cache key option.
     *
     * @return string Cache key
     */
    public function getCacheKey(): string
    {
        return $this->cacheKey;
    }
}
