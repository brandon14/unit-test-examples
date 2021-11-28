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

namespace Tests\Unit\Services\LastModified;

use DateTime;
use Exception;
use TypeError;
use DateInterval;
use Carbon\Carbon;
use function array_keys;
use function array_values;
use PHPUnit\Framework\TestCase;
use Psr\SimpleCache\CacheInterface;
use App\Contracts\Services\CacheException;
use App\Services\LastModified\LastModified;
use Psr\SimpleCache\CacheException as PsrCacheException;
use App\Contracts\Services\ProviderRegistrationException;
use App\Contracts\Services\LastModified\LastModifiedOptions;
use App\Contracts\Services\CacheImplementationNeededException;
use App\Contracts\Services\LastModified\LastModifiedTimeProvider;

/**
 * Class LastModifiedTest.
 *
 * LastModified service unit tests.
 *
 * What is important to note about the test for this class, is we don't rely on any external service. The cache
 * implementation the service relies on it mocked away so we have complete control over the service's dependencies.
 * Also it relies on registered providers which are also mocked. Those providers should be tested separately from the
 * service, and the tests on the services should not depend upon concrete provider implementations, so those are mocked
 * away as well so the only logic we test it the logic of the service itself.
 *
 * @author Brandon Clothier <brandon14125@gmail.com>
 *
 * @SuppressWarnings("ExcessiveClassLength")
 * @SuppressWarnings("TooManyMethods")
 * @SuppressWarnings("TooManyPublicMethods")
 */
class LastModifiedTest extends TestCase
{
    /**
     * Whether to cache timestamp.
     *
     * @var bool|null
     */
    private bool $cacheTimestamp;

    /**
     * Cache time-to-live.
     *
     * @var int|null
     */
    private int $cacheTtl;

    /**
     * Cache key.
     *
     * @var string|null
     */
    private string $cacheKey;

    /**
     * Default timestamp format config option.
     *
     * @var string|null
     */
    private string $defaultTimestampFormat;

    /**
     * Associative array of 'name' => {@link \App\Contracts\Services\LastModified\LastModifiedTimeProvider}.
     *
     * @var array|null
     */
    private array $providers;

    /**
     * Set up LastModified service test.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Set a mock time for our tests.
        Carbon::setTestNow(Carbon::create(2001, 5, 15));

        $this->cacheTimestamp = false;
        $this->cacheTtl = 30;
        $this->cacheKey = 'last_modified';
        $this->defaultTimestampFormat = 'F jS, Y \a\t h:i:s A T';

        $this->providers = [
            'null_provider' => $this->createMock(LastModifiedTimeProvider::class),
        ];
    }

    /**
     * Returns an {@link \App\Contracts\Services\LastModified\LastModifiedOptions} instance.
     *
     * @return \App\Contracts\Services\LastModified\LastModifiedOptions Service options
     */
    protected function getConfig(): LastModifiedOptions
    {
        return new LastModifiedOptions(
            $this->cacheTimestamp,
            $this->cacheTtl,
            $this->cacheKey,
            $this->defaultTimestampFormat
        );
    }

    /**
     * Simple test to hit the `getDefaultTimestampFormat` function.
     */
    final public function testReturnsDefaultTimestampFormat(): void
    {
        $cache = $this->createMock(CacheInterface::class);

        $instance = new LastModified(
            $cache,
            $this->getConfig(),
            $this->providers
        );

        $this::assertSame($this->defaultTimestampFormat, $instance->getDefaultTimestampFormat());
    }

    /**
     * Test that service can return a list of registered providers.
     */
    final public function testReturnsProviders(): void
    {
        $cache = $this->createMock(CacheInterface::class);

        $instance = new LastModified(
            $cache,
            $this->getConfig(),
            $this->providers
        );

        $providers = $instance->getProviders();

        $this::assertSame(array_values($this->providers), $providers);
    }

    /**
     * Test that service can return a list of registered provider names.
     */
    final public function testReturnsProviderNames(): void
    {
        $cache = $this->createMock(CacheInterface::class);

        $instance = new LastModified(
            $cache,
            $this->getConfig(),
            $this->providers
        );

        $providerNames = $instance->getProviderNames();

        $this::assertSame(array_keys($this->providers), $providerNames);
    }

    /**
     * Test that service properly filters out invalid providers when class is constructed.
     */
    final public function testFiltersInvalidProvidersFromArrayUponConstruction(): void
    {
        $cache = $this->createMock(CacheInterface::class);

        $instance = new LastModified(
            $cache,
            $this->getConfig(),
            ['invalid_provider' => 'Obviously this is not a provider']
        );

        $providerNames = $instance->getProviderNames();

        // Should be no provider names registered since the one we provided was invalid.
        $this::assertSame([], $providerNames);
    }

    /**
     * Test that the service throws an {@link \App\Contracts\Services\ProviderRegistrationException} if trying to register
     * a provider with a key that already exists.
     */
    final public function testThrowsExceptionIfRegisteringAProviderWhenOneWithNameAlreadyExists(): void
    {
        // We expect an ProviderRegistrationException to be thrown.
        $this->expectException(ProviderRegistrationException::class);

        $cache = $this->createMock(CacheInterface::class);

        $instance = new LastModified(
            $cache,
            $this->getConfig(),
            $this->providers
        );

        // Add the same provider twice.
        $instance->addProvider('provider', $this->createMock(LastModifiedTimeProvider::class));
        $instance->addProvider('provider', $this->createMock(LastModifiedTimeProvider::class));
    }

    /**
     * Test that the service throws an {@link \App\Contracts\Services\ProviderRegistrationException} if you remove
     * a provider that does not exist.
     */
    final public function testThrowsExceptionIfRemovingProviderThatDoesNotExist(): void
    {
        // We expect an ProviderRegistrationException to be thrown.
        $this->expectException(ProviderRegistrationException::class);

        $cache = $this->createMock(CacheInterface::class);

        $instance = new LastModified(
            $cache,
            $this->getConfig(),
            $this->providers
        );

        // No provider by that name exists.
        $instance->removeProvider('provider');
    }

    /**
     * Test that the service throws an {@link \App\Contracts\Services\CacheImplementationNeededException} if you want to cache timestamps
     * but don't provided an {@link \Psr\SimpleCache\CacheInterface} implementation.
     */
    final public function testThrowsExceptionIfCacheEnabledWithNoImplementation(): void
    {
        // We expect an CacheImplementationNeededException to be thrown.
        $this->expectException(CacheImplementationNeededException::class);

        // We want to cache timestamps, but provide a null cache implementation.
        $this->cacheTimestamp = true;

        new LastModified(
            null,
            $this->getConfig(),
            $this->providers
        );
    }

    /**
     * Test that we can add a provider to the service.
     */
    final public function testRegistersANewProvider(): void
    {
        $cache = $this->createMock(CacheInterface::class);

        $instance = new LastModified(
            $cache,
            $this->getConfig(),
            $this->providers
        );

        $result = $instance->addProvider('provider', $this->createMock(LastModifiedTimeProvider::class));

        $this::assertTrue($result);
    }

    /**
     * Test that we can remove a provider from the service.
     */
    final public function testRemovesAProvider(): void
    {
        $cache = $this->createMock(CacheInterface::class);

        $instance = new LastModified(
            $cache,
            $this->getConfig(),
            $this->providers
        );

        $instance->addProvider('provider', $this->createMock(LastModifiedTimeProvider::class));

        $result = $instance->removeProvider('provider');

        $this::assertTrue($result);
    }

    /**
     * Test the last modified service with the caching feature disabled when resolving all
     * providers.
     *
     * @throws \App\Contracts\Services\CacheException
     */
    final public function testGetsTimestampWithCacheDisabledAllProviders(): void
    {
        // Disable timestamp caching.
        $this->cacheTimestamp = false;

        // This will be our fixed last modified timestamp.
        $lastModified = new DateTime();

        $cache = $this->createMock(CacheInterface::class);

        // The cache should not be used if it is disabled.
        $cache->expects($this::never())->method('has');
        $cache->expects($this::never())->method('get');
        $cache->expects($this::never())->method('set');

        // Tell mock provider to return the set timestamp.
        $this->providers['null_provider']->expects($this::once())->method('getLastModifiedTime')
            ->willReturn($lastModified->getTimestamp());

        $instance = new LastModified(
            $cache,
            $this->getConfig(),
            $this->providers
        );

        $lastModifiedCall = $instance->getLastModifiedTime();

        // Assert the timestamp returned is our most "last modified file".
        $this::assertSame($lastModifiedCall->getTimestamp(), $lastModified->getTimestamp());
    }

    /**
     * Assert that the service checks the cache for a timestamp if the service
     * is configured to do so.
     *
     * @throws \App\Contracts\Services\CacheException
     */
    final public function testChecksCacheForTimestampAllProviders(): void
    {
        // Cache the timestamp.
        $this->cacheTimestamp = true;

        // This will be our fixed last modified timestamp.
        $lastModified = new DateTime();

        $cache = $this->createMock(CacheInterface::class);

        // Assert that the cache `has` method is called (once for the 'all' group and once for the mock provider) and they
        // both return false to mock no cache entries present.
        $cache->expects($this::exactly(2))
            ->method('has')
            ->willReturnOnConsecutiveCalls([$this->cacheKey.'_all'], [$this->cacheKey.'_null_provider'])
            ->will($this::onConsecutiveCalls(false, false));
        // If there is no cached timestamp, get should not be called.
        $cache->expects($this::never())->method('get');
        // With caching enabled, we should be able to call `set` to save the
        // timestamp to the cache. This will be called for the 'all' group and for the mock provider its self.
        $cache->expects($this::exactly(2))
            ->method('set')
            ->willReturnOnConsecutiveCalls(
                [
                    $this->cacheKey.'_null_provider',
                    $lastModified->getTimestamp(),
                    $this->cacheTtl,
                ],
                [
                    $this->cacheKey.'_all',
                    $lastModified->getTimestamp(),
                    $this->cacheTtl,
                ]
            )
            ->will($this::onConsecutiveCalls(true, true));

        // Tell mocked provider to return the set timestamp.
        $this->providers['null_provider']->expects($this::once())->method('getLastModifiedTime')
            ->willReturn($lastModified->getTimestamp());

        $instance = new LastModified(
            $cache,
            $this->getConfig(),
            $this->providers
        );

        $lastModifiedCall = $instance->getLastModifiedTime();

        // Assert the timestamp returned is our most "last modified file".
        $this::assertSame($lastModifiedCall->getTimestamp(), $lastModified->getTimestamp());
    }

    /**
     * Assert that the service checks the cache for a timestamp if the service
     * is configured to do so.
     *
     * @throws \App\Contracts\Services\CacheException
     */
    final public function testThrowsExceptionWhenCacheSaveFails(): void
    {
        // We expect service to throw a cache exception upon failure to persist to cache.
        $this->expectException(CacheException::class);

        // Cache the timestamp.
        $this->cacheTimestamp = true;

        // This will be our fixed last modified timestamp.
        $lastModified = new DateTime();

        $cache = $this->createMock(CacheInterface::class);

        // Assert that the cache `has` method is called (once for the 'all' group and once for the mock provider) and they
        // both return false to mock no cache entries present.
        $cache->expects($this::exactly(2))
            ->method('has')
            ->willReturnOnConsecutiveCalls([$this->cacheKey.'_all'], [$this->cacheKey.'_null_provider'])
            ->will($this::onConsecutiveCalls(false, false));
        // If there is no cached timestamp, get should not be called.
        $cache->expects($this::never())->method('get');

        // Force cache set method to return false to indicate a failure with saving as per PSR-16 implementation details.
        $cache->expects($this::once())
            ->method('set')->with(
                $this->cacheKey.'_null_provider',
                $lastModified->getTimestamp(),
                $this->cacheTtl
            )->willReturn(false);

        // Tell mocked provider to return the set timestamp.
        $this->providers['null_provider']->expects($this::once())->method('getLastModifiedTime')
            ->willReturn($lastModified->getTimestamp());

        $instance = new LastModified(
            $cache,
            $this->getConfig(),
            $this->providers
        );

        $instance->getLastModifiedTime();
    }

    /**
     * Assert that the service will get the timestamp from the cache if it is
     * present for all configured providers. This is mocking that it is cached
     * at the 'all' group level.
     *
     * @throws \App\Contracts\Services\CacheException
     */
    final public function testGetsTimestampFromCacheIfPresentAllProviders(): void
    {
        // Cache the timestamp.
        $this->cacheTimestamp = true;

        // This will be our fixed last modified timestamp.
        $lastModified = new DateTime();

        $cache = $this->createMock(CacheInterface::class);

        // Assert that the cache `has` method is called with cache key and
        // it returns true to mock that the timestamp is already present in
        // the cache.
        $cache->expects($this::once())->method('has')->with($this->cacheKey.'_all')->willReturn(true);
        // If there is a cached timestamp, `get` should be called and should
        // return our mock latest timestamp.
        $cache->expects($this::once())->method('get')->with($this->cacheKey.'_all', null)->willReturn(
            $lastModified->getTimestamp()
        );
        // With caching enabled, and a timestamp present in the cache, we shouldn't
        // call set to update the timestamp.
        $cache->expects($this::never())->method('set');

        // Assert the providers `getLastModifiedTime` function is not called since it is being
        // retrieved from the cache.
        $this->providers['null_provider']->expects($this::never())->method('getLastModifiedTime');

        $instance = new LastModified(
            $cache,
            $this->getConfig(),
            $this->providers
        );

        $lastModifiedCall = $instance->getLastModifiedTime();

        // Assert the timestamp returned is our most "last modified file".
        $this::assertSame($lastModifiedCall->getTimestamp(), $lastModified->getTimestamp());
    }

    /**
     * Assert that the service will get the timestamp from the providers if caching is enabled,
     * and there is a cache key but the actual call to resolve the value from cache fails (i.e
     *  call to {@link \Psr\SimpleCache\CacheInterface::get} returns false).
     *
     * @throws \App\Contracts\Services\CacheException
     */
    final public function testGetsTimestampFromProviderIfCacheCheckFails(): void
    {
        // Cache the timestamp.
        $this->cacheTimestamp = true;

        // This will be our fixed last modified timestamp.
        $lastModified = new DateTime();

        $cache = $this->createMock(CacheInterface::class);

        // Assert that the cache `has` method is called with cache key and
        // it returns true to mock that the timestamp is already present in
        // the cache. Also it should also receive a call to has for the individual
        // provider because we mocked a failure to get the cache value.
        $cache->expects($this::exactly(2))
            ->method('has')
            ->willReturnOnConsecutiveCalls([$this->cacheKey.'_all'], [$this->cacheKey.'_null_provider'])
            ->will($this::onConsecutiveCalls(true, true));
        // Simulate a failure to retrieve cached item by forcing get to return null on the all provider check.
        // Also the call to get the individual provider from cache with success.
        $cache->expects($this::exactly(2))
            ->method('get')
            ->willReturnOnConsecutiveCalls([$this->cacheKey.'_all', null], [$this->cacheKey.'_null_provider', null])
            ->will($this::onConsecutiveCalls(null, $lastModified->getTimestamp()));

        // With caching enabled, and a failure to resolve cached value, set should be called
        // once since the function continues on with its logic and the call to get the individual provider
        // timestamp out of cache succeeds (thus not triggering a call to save it in cache).
        $cache->expects($this::once())
            ->method('set')
            ->with(
                $this->cacheKey.'_all',
                $lastModified->getTimestamp(),
                $this->cacheTtl
            )
            ->willReturn(true);

        // Tell mocked provider not to expect the getLastModifiedTime to be invoked as the second call to the cache
        // is mocked to succeed.
        $this->providers['null_provider']->expects($this::never())->method('getLastModifiedTime');

        $instance = new LastModified(
            $cache,
            $this->getConfig(),
            $this->providers
        );

        $lastModifiedCall = $instance->getLastModifiedTime();

        // Assert the timestamp returned is our most "last modified file".
        $this::assertSame($lastModifiedCall->getTimestamp(), $lastModified->getTimestamp());
    }

    /**
     * Assert that the service will get the timestamp from the cache if it is
     * present for all configured providers. This is to test with no cache entry at
     * the 'all' group level, but a cached provider entry.
     *
     * @throws \App\Contracts\Services\CacheException
     */
    final public function testGetsTimestampFromCacheIfPresentAllProvidersNoAllCache(): void
    {
        // Cache the timestamp.
        $this->cacheTimestamp = true;

        // This will be our fixed last modified timestamp.
        $lastModified = new DateTime();

        $cache = $this->createMock(CacheInterface::class);

        // Assert that the cache `has` method is called with cache key and
        // it returns true to mock that the timestamp is already present in
        // the cache. We mock the call to get the all providers cache to false
        // to force it to check the cache for each individual provider.
        $cache->expects($this::exactly(2))
            ->method('has')
            ->willReturnOnConsecutiveCalls([$this->cacheKey.'_all'], [$this->cacheKey.'_null_provider'])
            ->will($this::onConsecutiveCalls(false, true));

        // If there is a cached timestamp, `get` should be called and should
        // return our mock latest timestamp.
        $cache->expects($this::once())
            ->method('get')
            ->with($this->cacheKey.'_null_provider', null)
            ->willReturn($lastModified->getTimestamp());
        // With caching enabled, and a timestamp present in the cache for the provider but not the 'all' group, we should
        // save that in the cache.
        $cache->expects($this::once())
            ->method('set')
            ->with(
                $this->cacheKey.'_all',
                $lastModified->getTimestamp(),
                $this->cacheTtl
            )
            ->willReturn(true);

        // Assert the providers `getLastModifiedTime` function is not called since it is being
        // retrieved from the cache.
        $this->providers['null_provider']->expects($this::never())->method('getLastModifiedTime');

        $instance = new LastModified(
            $cache,
            $this->getConfig(),
            $this->providers
        );

        $lastModifiedCall = $instance->getLastModifiedTime();

        // Assert the timestamp returned is our most "last modified file".
        $this::assertSame($lastModifiedCall->getTimestamp(), $lastModified->getTimestamp());
    }

    /**
     * Test that the service gets the most recent timestamp from multiple providers.
     *
     * @throws \App\Contracts\Services\CacheException
     */
    final public function testReturnsMostRecentTimestampFromAllProviders(): void
    {
        // Add another provider.
        $this->providers['null_provider_2'] = $this->createMock(LastModifiedTimeProvider::class);

        // Disable timestamp caching.
        $this->cacheTimestamp = false;

        // This will be our fixed last modified timestamp.
        $lastModified = new DateTime();
        // Second timestamp is earlier than the first, so it should chose the latest.
        $lastModified2 = (new DateTime())->sub(new DateInterval('P1D'));

        $cache = $this->createMock(CacheInterface::class);

        // No cache methods should be called.
        $cache->expects($this::never())->method('has');
        $cache->expects($this::never())->method('get');
        $cache->expects($this::never())->method('set');

        // Specify the timestamps the mocked providers should return.
        $this->providers['null_provider']->expects($this::once())->method('getLastModifiedTime')
            ->willReturn($lastModified->getTimestamp());
        $this->providers['null_provider_2']->expects($this::once())->method('getLastModifiedTime')
            ->willReturn($lastModified2->getTimestamp());

        $instance = new LastModified(
            $cache,
            $this->getConfig(),
            $this->providers
        );

        $lastModifiedCall = $instance->getLastModifiedTime();

        // Assert the timestamp returned is our most "last modified file".
        $this::assertSame($lastModifiedCall->getTimestamp(), $lastModified->getTimestamp());
        $this::assertGreaterThan($lastModified2->getTimestamp(), $lastModifiedCall->getTimestamp());
    }

    /**
     * Test that the service prevents returning timestamps past the current time.
     *
     * @throws \App\Contracts\Services\CacheException
     */
    final public function testPreventsFutureTimestampsAllProviders(): void
    {
        // Disable timestamp caching.
        $this->cacheTimestamp = false;

        // This will be our fixed last modified timestamp (in the future).
        $lastModified = (new DateTime())->add(new DateInterval('P1D'));

        $cache = $this->createMock(CacheInterface::class);

        // No cache methods should be called.
        $cache->expects($this::never())->method('has');
        $cache->expects($this::never())->method('get');
        $cache->expects($this::never())->method('set');

        $this->providers['null_provider']->expects($this::once())->method('getLastModifiedTime')
            ->willReturn($lastModified->getTimestamp());

        $instance = new LastModified(
            $cache,
            $this->getConfig(),
            $this->providers
        );

        $lastModifiedCall = $instance->getLastModifiedTime();

        // Assert the timestamp returned is less than or equal to current time.
        $this::assertLessThanOrEqual(time(), $lastModifiedCall->getTimestamp());
    }

    /**
     * Test that the service prevents returning timestamps past the current time.
     *
     * @throws \App\Contracts\Services\CacheException
     */
    final public function testPreventsNegativeTimestampsAllProviders(): void
    {
        // Disable timestamp caching.
        $this->cacheTimestamp = false;

        $cache = $this->createMock(CacheInterface::class);

        // No cache methods should be called.
        $cache->expects($this::never())->method('has');
        $cache->expects($this::never())->method('get');
        $cache->expects($this::never())->method('set');

        $this->providers['null_provider']->expects($this::once())->method('getLastModifiedTime')
            ->willReturn(-1);

        $instance = new LastModified(
            $cache,
            $this->getConfig(),
            $this->providers
        );

        $lastModifiedCall = $instance->getLastModifiedTime();

        // Assert the timestamp returned is less than or equal to current time.
        $this::assertGreaterThan(-1, $lastModifiedCall->getTimestamp());
    }

    /**
     * Assert that the service resolves the timestamp with caching disabled.
     *
     * @throws \App\Contracts\Services\CacheException
     */
    final public function testGetsTimestampWithCacheDisabledSingleProvider(): void
    {
        // Disable the cache.
        $this->cacheTimestamp = false;

        // This will be our fixed last modified timestamp.
        $lastModified = new DateTime();

        $cache = $this->createMock(CacheInterface::class);

        // No cache methods should be called.
        $cache->expects($this::never())->method('has');
        $cache->expects($this::never())->method('get');
        $cache->expects($this::never())->method('set');

        // Tell mocked provider to return the set timestamp.
        $this->providers['null_provider']->expects($this::once())
            ->method('getLastModifiedTime')
            ->willReturn($lastModified->getTimestamp());

        $instance = new LastModified(
            $cache,
            $this->getConfig(),
            $this->providers
        );

        $lastModifiedCall = $instance->getLastModifiedTime('null_provider');

        // Assert the timestamp returned is our most "last modified file".
        $this::assertSame($lastModifiedCall->getTimestamp(), $lastModified->getTimestamp());
    }

    /**
     * Assert that the service checks the cache for a timestamp if the service
     * is configured to do so.
     *
     * @throws \App\Contracts\Services\CacheException
     */
    final public function testChecksCacheForTimestampSingleProvider(): void
    {
        // Cache the timestamp.
        $this->cacheTimestamp = true;

        // This will be our fixed last modified timestamp.
        $lastModified = new DateTime();

        $cache = $this->createMock(CacheInterface::class);

        // Assert that the cache `has` method is called with cache key and
        // it returns false to mock the timestamp not being in cache.
        $cache->expects($this::once())
            ->method('has')
            ->with($this->cacheKey.'_null_provider')
            ->willReturn(false);
        // If there is no cached timestamp, get should not be called.
        $cache->expects($this::never())->method('get');

        $cache->expects($this::once())->method('set')->with(
            $this->cacheKey.'_null_provider',
            $lastModified->getTimestamp(),
            $this->cacheTtl
        )->willReturn(true);

        // Tell mocked provider to return the set timestamp.
        $this->providers['null_provider']->expects($this::once())
            ->method('getLastModifiedTime')
            ->willReturn($lastModified->getTimestamp());

        $instance = new LastModified(
            $cache,
            $this->getConfig(),
            $this->providers
        );

        $lastModifiedCall = $instance->getLastModifiedTime('null_provider');

        // Assert the timestamp returned is our most "last modified file".
        $this::assertSame($lastModifiedCall->getTimestamp(), $lastModified->getTimestamp());
    }

    /**
     * Assert that the service will get the timestamp from the cache if it is
     * present.
     *
     * @throws \App\Contracts\Services\CacheException
     */
    final public function testGetsTimestampFromCacheIfPresentSingle(): void
    {
        // Cache the timestamp.
        $this->cacheTimestamp = true;

        // This will be our fixed last modified timestamp.
        $lastModified = new DateTime();

        $cache = $this->createMock(CacheInterface::class);

        // Assert that the cache `has` method is called with cache key and
        // it returns true to mock that the timestamp is already present in
        // the cache.
        $cache->expects($this::once())
            ->method('has')
            ->with($this->cacheKey.'_null_provider')
            ->willReturn(true);
        // If there is a cached timestamp, `get` should be called and should
        // return our mock latest timestamp.
        $cache->expects($this::once())
            ->method('get')
            ->with($this->cacheKey.'_null_provider', null)
            ->willReturn($lastModified->getTimestamp());
        // With caching enabled, and a timestamp present in the cache, we shouldn't
        // call put to update the timestamp.
        $cache->expects($this::never())->method('set');

        // Assert the providers `getLastModifiedTime` function is not called since it is being
        // retrieved from the cache.
        $this->providers['null_provider']->expects($this::never())->method('getLastModifiedTime');

        $instance = new LastModified(
            $cache,
            $this->getConfig(),
            $this->providers
        );

        $lastModifiedCall = $instance->getLastModifiedTime('null_provider');

        // Assert the timestamp returned is our most "last modified file".
        $this::assertSame($lastModifiedCall->getTimestamp(), $lastModified->getTimestamp());
    }

    /**
     * Test that the service prevents returning timestamps past the current time.
     *
     * @throws \App\Contracts\Services\CacheException
     */
    final public function testPreventsFutureTimestampsSingleProviders(): void
    {
        // Disable timestamp caching.
        $this->cacheTimestamp = false;

        // This will be our fixed last modified timestamp (in the future).
        $lastModified = (new DateTime())->add(new DateInterval('P1D'));

        $cache = $this->createMock(CacheInterface::class);

        // No cache methods should be called.
        $cache->expects($this::never())->method('has');
        $cache->expects($this::never())->method('get');
        $cache->expects($this::never())->method('set');

        $this->providers['null_provider']->expects($this::once())->method('getLastModifiedTime')
            ->willReturn($lastModified->getTimestamp());

        $instance = new LastModified(
            $cache,
            $this->getConfig(),
            $this->providers
        );

        $lastModifiedCall = $instance->getLastModifiedTime('null_provider');

        // Assert the timestamp returned is less than or equal to current time.
        $this::assertLessThanOrEqual(time(), $lastModifiedCall->getTimestamp());
    }

    /**
     * Test that the service prevents returning timestamps past the current time.
     *
     * @throws \App\Contracts\Services\CacheException
     */
    final public function testPreventsNegativeTimestampsSingleProvider(): void
    {
        // Disable timestamp caching.
        $this->cacheTimestamp = false;

        $cache = $this->createMock(CacheInterface::class);

        // No cache methods should be called.
        $cache->expects($this::never())->method('has');
        $cache->expects($this::never())->method('get');
        $cache->expects($this::never())->method('set');

        $this->providers['null_provider']->expects($this::once())->method('getLastModifiedTime')
            ->willReturn(-1);

        $instance = new LastModified(
            $cache,
            $this->getConfig(),
            $this->providers
        );

        $lastModifiedCall = $instance->getLastModifiedTime('null_provider');

        // Assert the timestamp returned is less than or equal to current time.
        $this::assertGreaterThan(-1, $lastModifiedCall->getTimestamp());
    }

    /**
     * Assert that the service resolves arrays of providers with the cache disabled.
     *
     * @throws \App\Contracts\Services\CacheException
     */
    final public function testGetsTimestampWithCacheDisabledArray(): void
    {
        // Add another provider.
        $this->providers['null_provider_2'] = $this->createMock(LastModifiedTimeProvider::class);

        // Disable caching.
        $this->cacheTimestamp = false;

        // This will be our fixed last modified timestamp.
        $lastModified = new DateTime();
        // Second timestamp is earlier than the first, so it should chose the latest.
        $lastModified2 = (new DateTime())->sub(new DateInterval('P1D'));

        $cache = $this->createMock(CacheInterface::class);

        // No cache methods should be called.
        $cache->expects($this::never())->method('has');
        $cache->expects($this::never())->method('get');
        $cache->expects($this::never())->method('set');

        // Tell mocked provider to return the set timestamp.
        $this->providers['null_provider']->expects($this::once())->method('getLastModifiedTime')
            ->willReturn($lastModified->getTimestamp());
        $this->providers['null_provider_2']->expects($this::once())->method('getLastModifiedTime')
            ->willReturn($lastModified2->getTimestamp());

        $instance = new LastModified(
            $cache,
            $this->getConfig(),
            $this->providers
        );

        $lastModifiedCall = $instance->getLastModifiedTimeByArray(['null_provider', 'null_provider_2']);

        // Assert the timestamp returned is our most "last modified file".
        $this::assertSame($lastModifiedCall->getTimestamp(), $lastModified->getTimestamp());
    }

    /**
     * Assert that the service checks the cache for a timestamp if the service
     * is configured to do so for an array of providers.
     *
     * @throws \App\Contracts\Services\CacheException
     */
    final public function testChecksCacheForTimestampArray(): void
    {
        // Add another provider.
        $this->providers['null_provider_2'] = $this->createMock(LastModifiedTimeProvider::class);

        // Cache the timestamp.
        $this->cacheTimestamp = true;

        // This will be our fixed last modified timestamp.
        $lastModified = new DateTime();
        // Second timestamp is earlier than the first, so it should chose the latest.
        $lastModified2 = (new DateTime())->sub(new DateInterval('P1D'));

        $cache = $this->createMock(CacheInterface::class);

        // Assert that the cache `has` method is called with cache key and
        // it returns false to mock the timestamp not being in cache.
        // Second provider is not cached as well....
        $cache->expects($this::exactly(3))
            ->method('has')
            ->willReturnOnConsecutiveCalls(
                [$this->cacheKey.'_null_provider_null_provider_2'],
                [$this->cacheKey.'_null_provider'],
                [$this->cacheKey.'_null_provider_2']
            )->will($this::onConsecutiveCalls(false, false, false));
        // If there is no cached timestamp, get should not be called.
        $cache->expects($this::never())->method('get');

        // Should cache all providers and the group cache value.
        $cache->expects($this::exactly(3))
            ->method('set')
            ->willReturnOnConsecutiveCalls(
                [
                    $this->cacheKey.'_null_provider',
                    $lastModified->getTimestamp(),
                    $this->cacheTtl,
                ],
                [
                    $this->cacheKey.'_null_provider_2',
                    $lastModified2->getTimestamp(),
                    $this->cacheTtl,
                ],
                [
                    $this->cacheKey.'_null_provider_null_provider_2',
                    $lastModified->getTimestamp(),
                    $this->cacheTtl,
                ]
            )
            ->will($this::onConsecutiveCalls(true, true, true));

        // Tell mocked provider to return the set timestamp.
        $this->providers['null_provider']->expects($this::once())->method('getLastModifiedTime')
            ->willReturn($lastModified->getTimestamp());
        $this->providers['null_provider_2']->expects($this::once())->method('getLastModifiedTime')
            ->willReturn($lastModified2->getTimestamp());

        $instance = new LastModified(
            $cache,
            $this->getConfig(),
            $this->providers
        );

        $lastModifiedCall = $instance->getLastModifiedTimeByArray(['null_provider', 'null_provider_2']);

        // Assert the timestamp returned is our most "last modified file".
        $this::assertSame($lastModifiedCall->getTimestamp(), $lastModified->getTimestamp());
    }

    /**
     * Assert that the service will get the timestamp from the cache if it is
     * present for an array of providers.
     *
     * @throws \App\Contracts\Services\CacheException
     */
    final public function testGetsTimestampFromCacheIfPresentArray(): void
    {
        // Add another provider.
        $this->providers['null_provider_2'] = $this->createMock(LastModifiedTimeProvider::class);

        // Cache the timestamp.
        $this->cacheTimestamp = true;

        // This will be our fixed last modified timestamp.
        $lastModified = new DateTime();
        // Second timestamp is earlier than the first, so it should chose the latest.
        $lastModified2 = (new DateTime())->sub(new DateInterval('P1D'));

        $cache = $this->createMock(CacheInterface::class);

        // Check for group cache key and return false.
        // Assert that the cache `has` method is called with cache key and
        // it returns true to mock that the timestamp is already present in
        // the cache. Assuming all providers are cached.
        $cache->expects($this::exactly(3))
            ->method('has')
            ->willReturnOnConsecutiveCalls(
                [$this->cacheKey.'_null_provider_null_provider_2'],
                [$this->cacheKey.'_null_provider'],
                [$this->cacheKey.'_null_provider_2']
            )->will($this::onConsecutiveCalls(false, true, true));

        // If there is a cached timestamp, `get` should be called and should
        // return our mock latest timestamp.
        $cache->expects($this::exactly(2))
            ->method('get')
            ->willReturnOnConsecutiveCalls(
                [$this->cacheKey.'_null_provider', null],
                [$this->cacheKey.'_null_provider_2', null]
            )->will($this::onConsecutiveCalls($lastModified->getTimestamp(), $lastModified2->getTimestamp()));

        // Should cache the group cache key.
        $cache->expects($this::once())
            ->method('set')
            ->with(
                $this->cacheKey.'_null_provider_null_provider_2',
                $lastModified->getTimestamp(),
                $this->cacheTtl
            )
            ->willReturn(true);

        // Assert the providers `getLastModifiedTime` function is not called since it is being
        // retrieved from the cache.
        $this->providers['null_provider']->expects($this::never())
            ->method('getLastModifiedTime');
        $this->providers['null_provider_2']->expects($this::never())
            ->method('getLastModifiedTime');

        $instance = new LastModified(
            $cache,
            $this->getConfig(),
            $this->providers
        );

        $lastModifiedCall = $instance->getLastModifiedTimeByArray(['null_provider', 'null_provider_2']);

        // Assert the timestamp returned is our most "last modified file".
        $this::assertSame($lastModifiedCall->getTimestamp(), $lastModified->getTimestamp());
    }

    /**
     * Assert that the service will get the timestamp from the cache if it is
     * present from an array of providers when the group of providers is cached.
     *
     * @throws \App\Contracts\Services\CacheException
     */
    final public function testGetsTimestampFromCacheIfPresentArrayGroupCached(): void
    {
        // Add another provider.
        $this->providers['null_provider_2'] = $this->createMock(LastModifiedTimeProvider::class);

        // Cache the timestamp.
        $this->cacheTimestamp = true;

        // This will be our fixed last modified timestamp.
        $lastModified = new DateTime();

        $cache = $this->createMock(CacheInterface::class);

        // Check for group cache key and return true. This should be only invocation of has.
        $cache->expects($this::once())
            ->method('has')
            ->with($this->cacheKey.'_null_provider_null_provider_2')
            ->willReturn(true);
        $cache->expects($this::once())
            ->method('get')
            ->with($this->cacheKey.'_null_provider_null_provider_2')
            ->willReturn($lastModified->getTimestamp());

        $cache->expects($this::never())->method('set');

        // Assert the providers `getLastModifiedTime` function is not called since it is being
        // retrieved from the cache.
        $this->providers['null_provider']->expects($this::never())->method('getLastModifiedTime');
        $this->providers['null_provider_2']->expects($this::never())->method('getLastModifiedTime');

        $instance = new LastModified(
            $cache,
            $this->getConfig(),
            $this->providers
        );

        $lastModifiedCall = $instance->getLastModifiedTimeByArray(['null_provider', 'null_provider_2']);

        // Assert the timestamp returned is our most "last modified file".
        $this::assertSame($lastModifiedCall->getTimestamp(), $lastModified->getTimestamp());
    }

    /**
     * Test that the service prevents returning timestamps past the current time for an array
     * of providers.
     *
     * @throws \App\Contracts\Services\CacheException
     */
    final public function testPreventsFutureTimestampsArray(): void
    {
        // Disable timestamp caching.
        $this->cacheTimestamp = false;

        // Add another provider.
        $this->providers['null_provider_2'] = $this->createMock(LastModifiedTimeProvider::class);

        // This will be our fixed last modified timestamp (in the future).
        $lastModified = (new DateTime())->add(new DateInterval('P1D'));
        $lastModified2 = (new DateTime())->add(new DateInterval('P2D'));

        $cache = $this->createMock(CacheInterface::class);

        $this->providers['null_provider']->expects($this::once())->method('getLastModifiedTime')
            ->willReturn($lastModified->getTimestamp());
        $this->providers['null_provider_2']->expects($this::once())->method('getLastModifiedTime')
            ->willReturn($lastModified2->getTimestamp());

        $instance = new LastModified(
            $cache,
            $this->getConfig(),
            $this->providers
        );

        $lastModifiedCall = $instance->getLastModifiedTimeByArray(['null_provider', 'null_provider_2']);

        // Assert the timestamp returned is less than or equal to current time.
        $this::assertLessThanOrEqual(time(), $lastModifiedCall->getTimestamp());
    }

    /**
     * Test that the service prevents returning timestamps past the current time for an array
     * of providers.
     *
     * @throws \App\Contracts\Services\CacheException
     */
    final public function testPreventsNegativeTimestampsArray(): void
    {
        // Disable timestamp caching.
        $this->cacheTimestamp = false;

        // Add another provider.
        $this->providers['null_provider_2'] = $this->createMock(LastModifiedTimeProvider::class);

        $cache = $this->createMock(CacheInterface::class);

        $this->providers['null_provider']->expects($this::once())->method('getLastModifiedTime')
            ->willReturn(-1);
        $this->providers['null_provider_2']->expects($this::once())->method('getLastModifiedTime')
            ->willReturn(-42);

        $instance = new LastModified(
            $cache,
            $this->getConfig(),
            $this->providers
        );

        $lastModifiedCall = $instance->getLastModifiedTime();

        // Assert the timestamp returned is less than or equal to current time.
        $this::assertGreaterThan(-1, $lastModifiedCall->getTimestamp());
    }

    /**
     * Test that we properly handle empty arrays.
     *
     * @throws \App\Contracts\Services\CacheException
     * @throws \App\Contracts\Services\ProviderRegistrationException
     */
    final public function testThrowsExceptionWhenGettingTimestampForEmptyArray(): void
    {
        $this->expectException(ProviderRegistrationException::class);

        $cache = $this->createMock(CacheInterface::class);

        $instance = new LastModified(
            $cache,
            $this->getConfig(),
            $this->providers
        );

        // Try to resolve an empty array.
        $instance->getLastModifiedTimeByArray([]);
    }

    /**
     * Test handling an unregistered provider.
     *
     * @throws \App\Contracts\Services\CacheException
     * @throws \App\Contracts\Services\ProviderRegistrationException
     */
    final public function testThrowsExceptionWhenResolvingProviderThatDoesntExist(): void
    {
        // We expect an ProviderRegistrationException to be thrown.
        $this->expectException(ProviderRegistrationException::class);

        $cache = $this->createMock(CacheInterface::class);

        $instance = new LastModified(
            $cache,
            $this->getConfig(),
            $this->providers
        );

        // Try to resolve an invalid provider.
        $instance->getLastModifiedTime('invalid_provider');
    }

    /**
     * Test handling an unregistered provider.
     *
     * @throws \App\Contracts\Services\CacheException
     * @throws \App\Contracts\Services\ProviderRegistrationException
     */
    final public function testThrowsExceptionWhenResolvingProviderThatDoesntExistArray(): void
    {
        // We expect an ProviderRegistrationException to be thrown.
        $this->expectException(ProviderRegistrationException::class);

        $cache = $this->createMock(CacheInterface::class);

        $instance = new LastModified(
            $cache,
            $this->getConfig(),
            $this->providers
        );

        // Try to resolve an invalid provider.
        $instance->getLastModifiedTimeByArray(['invalid_provider']);
    }

    /**
     * Test we handle any cache exception from the PSR cache implementation on check and transform it into
     * an {@link \App\Contracts\Services\CacheException}.
     *
     * @throws \App\Contracts\Services\CacheException
     * @throws \App\Contracts\Services\ProviderRegistrationException
     */
    final public function testTransformsCacheExceptionIntoCacheExceptionOnHas(): void
    {
        $this->expectException(CacheException::class);
        // Cache the timestamp.
        $this->cacheTimestamp = true;

        $cache = $this->createMock(CacheInterface::class);

        // Throw a mock PSR cache exception when checking the cache.
        $cache->expects($this::once())
            ->method('has')
            ->with($this->cacheKey.'_null_provider')
            ->will($this::throwException(new MockCacheException('Test exception')));

        $instance = new LastModified(
            $cache,
            $this->getConfig(),
            $this->providers
        );

        $instance->getLastModifiedTime('null_provider');
    }

    /**
     * Test it transforms a throwable caught when checking the cache into an
     * {@link \App\Contracts\Services\CacheException}.
     *
     * @throws \App\Contracts\Services\CacheException
     * @throws \App\Contracts\Services\ProviderRegistrationException
     */
    final public function testTransformsThrowableIntoCacheExceptionOnHas(): void
    {
        $this->expectException(CacheException::class);
        // Cache the timestamp.
        $this->cacheTimestamp = true;

        $cache = $this->createMock(CacheInterface::class);

        // Throw an error (to force a Throwable catch) when checking the cache.
        $cache->expects($this::once())
            ->method('has')
            ->with($this->cacheKey.'_null_provider')
            ->will($this::throwException(new TypeError('Test exception')));

        $instance = new LastModified(
            $cache,
            $this->getConfig(),
            $this->providers
        );

        $instance->getLastModifiedTime('null_provider');
    }

    /**
     * Test we handle any cache exception from the PSR cache implementation on get and transform it into
     * an {@link \App\Contracts\Services\CacheException}.
     *
     * @throws \App\Contracts\Services\CacheException
     * @throws \App\Contracts\Services\ProviderRegistrationException
     */
    final public function testTransformsCacheExceptionIntoCacheExceptionOnGet(): void
    {
        $this->expectException(CacheException::class);
        // Cache the timestamp.
        $this->cacheTimestamp = true;

        $cache = $this->createMock(CacheInterface::class);

        // Assert that the cache `has` method is called with cache key and
        // it returns true to mock that the timestamp is already present in
        // the cache.
        $cache->expects($this::once())
            ->method('has')
            ->with($this->cacheKey.'_null_provider')
            ->willReturn(true);
        // Throw a mock PSR cache exception when fetching from the cache.
        $cache->expects($this::once())
            ->method('get')
            ->with($this->cacheKey.'_null_provider', null)
            ->will($this::throwException(new MockCacheException('Test exception')));

        $instance = new LastModified(
            $cache,
            $this->getConfig(),
            $this->providers
        );

        $instance->getLastModifiedTime('null_provider');
    }

    /**
     * Test we handle any throwable from the PSR cache implementation on get and transform it into
     * an {@link \App\Contracts\Services\CacheException}.
     *
     * @throws \App\Contracts\Services\CacheException
     * @throws \App\Contracts\Services\ProviderRegistrationException
     */
    final public function testTransformsThrowableIntoCacheExceptionOnGet(): void
    {
        $this->expectException(CacheException::class);

        // Cache the timestamp.
        $this->cacheTimestamp = true;

        $cache = $this->createMock(CacheInterface::class);

        // Assert that the cache `has` method is called with cache key and
        // it returns true to mock that the timestamp is already present in
        // the cache.
        $cache->expects($this::once())
            ->method('has')
            ->with($this->cacheKey.'_null_provider')
            ->willReturn(true);
        // Throw an error (to force a Throwable catch) when fetching from the cache.
        $cache->expects($this::once())
            ->method('get')
            ->with($this->cacheKey.'_null_provider', null)
            ->will($this::throwException(new TypeError('Test exception')));

        $instance = new LastModified(
            $cache,
            $this->getConfig(),
            $this->providers
        );

        $instance->getLastModifiedTime('null_provider');
    }

    /**
     * Test we handle any cache exception from the PSR cache implementation on save and transform it into
     * an {@link \App\Contracts\Services\CacheException}.
     *
     * @throws \App\Contracts\Services\CacheException
     * @throws \App\Contracts\Services\ProviderRegistrationException
     */
    final public function testTransformsCacheExceptionIntoCacheExceptionOnSave(): void
    {
        $this->expectException(CacheException::class);

        // Cache the timestamp.
        $this->cacheTimestamp = true;

        // This will be our fixed last modified timestamp.
        $lastModified = new DateTime();

        $cache = $this->createMock(CacheInterface::class);

        // Assert that the cache `has` method is called with cache key and
        // it returns false to mock the timestamp not being in cache.
        $cache->expects($this::once())
            ->method('has')
            ->with($this->cacheKey.'_null_provider')
            ->willReturn(false);
        // If there is no cached timestamp, get should not be called.
        $cache->expects($this::never())->method('get');

        // Throw a mock PSR cache exception when trying to save something in the cache.
        $cache->expects($this::once())->method('set')->with(
            $this->cacheKey.'_null_provider',
            $lastModified->getTimestamp(),
            $this->cacheTtl
        )->will($this::throwException(new MockCacheException('Test exception')));

        // Tell mocked provider to return the set timestamp.
        $this->providers['null_provider']->expects($this::once())->method('getLastModifiedTime')
            ->willReturn($lastModified->getTimestamp());

        $instance = new LastModified(
            $cache,
            $this->getConfig(),
            $this->providers
        );

        $instance->getLastModifiedTime('null_provider');
    }

    /**
     * Test we handle any throwable from the PSR cache implementation on save and transform it into
     * an {@link \App\Contracts\Services\CacheException}.
     *
     * @throws \App\Contracts\Services\CacheException
     * @throws \App\Contracts\Services\ProviderRegistrationException
     */
    final public function testTransformsThrowableIntoCacheExceptionOnSave(): void
    {
        $this->expectException(CacheException::class);

        // Cache the timestamp.
        $this->cacheTimestamp = true;

        // This will be our fixed last modified timestamp.
        $lastModified = new DateTime();

        $cache = $this->createMock(CacheInterface::class);

        // Assert that the cache `has` method is called with cache key and
        // it returns false to mock the timestamp not being in cache.
        $cache->expects($this::once())
            ->method('has')
            ->with($this->cacheKey.'_null_provider')
            ->willReturn(false);
        // If there is no cached timestamp, get should not be called.
        $cache->expects($this::never())->method('get');

        // Throw an error (to force catching a Throwable) when we call set to save the timestamp.
        $cache->expects($this::once())->method('set')->with(
            $this->cacheKey.'_null_provider',
            $lastModified->getTimestamp(),
            $this->cacheTtl
        )->will($this::throwException(new TypeError('Test exception')));

        // Tell mocked provider to return the set timestamp.
        $this->providers['null_provider']->expects($this::once())->method('getLastModifiedTime')
            ->willReturn($lastModified->getTimestamp());

        $instance = new LastModified(
            $cache,
            $this->getConfig(),
            $this->providers
        );

        $instance->getLastModifiedTime('null_provider');
    }
}

/**
 * Class MockCacheException.
 *
 * Mock class that implements the PSR cache exception interface.
 *
 * @author Brandon Clothier <brandon14125@gmail.com>
 */
final class MockCacheException extends Exception implements PsrCacheException
{
    // Intentionally left blank.
}
