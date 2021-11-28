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

namespace App\Services\LastModified\Providers;

use Throwable;
use Psr\SimpleCache\CacheInterface;
use App\Contracts\Services\LastModified\LastModifiedTimeProvider;

/**
 * Class CacheLastModifiedTimeProvider.
 *
 * Cache last modified provider. Will check a cache key for the last modified time.
 *
 * @author Brandon Clothier <brandon14125@gmail.com>
 */
class CacheLastModifiedTimeProvider implements LastModifiedTimeProvider
{
    /**
     * PSR cache implementation.
     */
    protected CacheInterface $cache;

    /**
     * Cache key.
     */
    protected string $cacheKey;

    /**
     * Constructs a cache based last modified provider.
     *
     * @param \Psr\SimpleCache\CacheInterface $cache    PSR cache implementation
     * @param string                          $cacheKey Cache key
     *
     * @return void
     */
    public function __construct(CacheInterface $cache, string $cacheKey = 'last_modified')
    {
        $this->cache = $cache;
        $this->cacheKey = $cacheKey;
    }

    /**
     * {@inheritdoc}
     */
    public function getLastModifiedTime(): int
    {
        try {
            // Check cache for the last modified key, and return that time if present.
            // PSR's throws annotation are incorrect because the base CacheException is an interface.
            /** @psalm-suppress MissingThrowsDocblock */
            if ($this->cache->has($this->cacheKey)) {
                // PSR's throws annotation are incorrect because the base CacheException is an interface.
                /** @psalm-suppress MissingThrowsDocblock */
                return (int) $this->cache->get($this->cacheKey);
            }

            return -1;
        } catch (Throwable $t) {
            return -1;
        }
    }
}
