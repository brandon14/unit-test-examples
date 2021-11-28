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

namespace Tests\Unit\Services\LastModified\Providers;

use Exception;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;
use Psr\SimpleCache\CacheInterface;
use App\Services\LastModified\Providers\CacheLastModifiedTimeProvider;

/**
 * Class CacheLastModifiedProviderTest.
 *
 * Cache last modified provider unit tests.
 *
 * What is important to note about the test for this class, is we don't rely on any external service. The cache
 * dependency for the provider is based on an easily mockable interface, so we don't actually rely on an actual
 * cache service in order to test the functionality of our provider.
 *
 * @author Brandon Clothier <brandon14125@gmail.com>
 */
class CacheLastModifiedTimeProviderTest extends TestCase
{
    /**
     * Set up Carbon mock time.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Set a mock time for our tests.
        Carbon::setTestNow(Carbon::create(2001, 5, 15));
    }

    /**
     * Assert that the service will get the timestamp from the cache if it is
     * present.
     */
    final public function testGetsTimestampFromCache(): void
    {
        $cache = $this->createMock(CacheInterface::class);
        $cacheKey = 'last_modified';

        // This will be our fixed last modified timestamp.
        $lastModified = Carbon::now();

        // Make assertions on cache dependency.
        // Tell provider cache will have timestamp in provided key, and will return the timestamp when
        // invoking get.
        $cache->expects($this::once())->method('has')->with($cacheKey)->willReturn(true);
        $cache->expects($this::once())->method('get')->with($cacheKey)->willReturn((string) $lastModified->timestamp);

        $instance = new CacheLastModifiedTimeProvider($cache, $cacheKey);

        // Call getLastModifiedTime to get the last modified file time.
        $lastModifiedCall = $instance->getLastModifiedTime();

        // Assert the timestamp returned is our most "last modified file".
        $this::assertSame($lastModified->timestamp, $lastModifiedCall);
    }

    /**
     * Test that if no cache entry is found, the provider will return -1.
     */
    final public function testReturnsNegativeOneWithNoCacheEntry(): void
    {
        $cache = $this->createMock(CacheInterface::class);
        $cacheKey = 'last_modified';

        // Make assertions on cache dependency.
        // Tell provider cache will not have timestamp in provided key, and get will never be invoked.
        $cache->expects($this::once())->method('has')->with($cacheKey)->willReturn(false);
        $cache->expects($this::never())->method('get');

        $instance = new CacheLastModifiedTimeProvider($cache, $cacheKey);

        // Call getLastModifiedTime to get the last modified file time.
        $lastModifiedCall = $instance->getLastModifiedTime();

        // Assert the timestamp returned is -1.
        $this::assertSame(-1, $lastModifiedCall);
    }

    /**
     * Test that if the cache has method throws an exception, -1 will be returned.
     */
    final public function testReturnsNegativeOneWithExceptionOnHasCall(): void
    {
        $cache = $this->createMock(CacheInterface::class);
        $cacheKey = 'last_modified';

        // Make assertions on cache dependency.
        // Tell provider cache will throw an exception when checking for the key, and get will never be invoked.
        $cache->expects($this::once())->method('has')->with($cacheKey)->will($this::throwException(new Exception('This is a mocked cache has() exception.')));
        $cache->expects($this::never())->method('get');

        $instance = new CacheLastModifiedTimeProvider($cache, $cacheKey);

        // Call getLastModifiedTime to get the last modified file time.
        $lastModifiedCall = $instance->getLastModifiedTime();

        // Assert the timestamp returned is -1.
        $this::assertSame(-1, $lastModifiedCall);
    }

    /**
     * Test that if the cache get method throws an exception, -1 will be returned.
     */
    final public function testReturnsNegativeOneWithExceptionOnGetCall(): void
    {
        $cache = $this->createMock(CacheInterface::class);
        $cacheKey = 'last_modified';

        // Make assertions on cache dependency.
        // Tell provider cache will throw an exception when checking for the key, and get will never be invoked.
        $cache->expects($this::once())->method('has')->with($cacheKey)->willReturn(true);
        $cache->expects($this::once())->method('get')->with($cacheKey)->will($this::throwException(new Exception('This is a mocked cache get() exception.')));

        $instance = new CacheLastModifiedTimeProvider($cache, $cacheKey);

        // Call getLastModifiedTime to get the last modified file time.
        $lastModifiedCall = $instance->getLastModifiedTime();

        // Assert the timestamp returned is -1.
        $this::assertSame(-1, $lastModifiedCall);
    }
}
