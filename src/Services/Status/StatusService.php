<?php

/**
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

declare(strict_types=1);

namespace App\Services\Status;

use Throwable;
use function count;
use function implode;
use function is_array;
use function is_string;
use function serialize;
use function array_keys;
use function unserialize;
use function array_filter;
use InvalidArgumentException;
use Psr\SimpleCache\CacheInterface;
use App\Contracts\Services\Status\StatusOptions;
use App\Contracts\Services\Status\StatusCacheException;
use App\Contracts\Services\Status\StatusServiceProvider;
use App\Contracts\Services\Status\StatusProviderNotRegisteredException;
use App\Contracts\Services\Status\StatusService as StatusServiceInterface;

/**
 * Class StatusService.
 *
 * Status service. Allows registering different
 * {@link \App\Contracts\Services\Status\StatusServiceProvider}
 * and will return statuses from those providers.
 *
 * @author Brandon Clothier <brandon14125@gmail.com>
 */
class StatusService implements StatusServiceInterface
{
    /**
     * Application cache store.
     *
     * @var \Psr\SimpleCache\CacheInterface|null
     */
    protected $cache;

    /**
     * Whether to cache statuses as they are obtained.
     *
     * @var bool
     */
    protected $isCacheEnabled;

    /**
     * How long to cache the status for.
     *
     * @var int
     */
    protected $cacheTtl;

    /**
     * Cache key.
     *
     * @var string
     */
    protected $cacheKey;

    /**
     * Associative array of 'name' => {@link \App\Contracts\Services\Status\StatusServiceProvider}.
     *
     * @psalm-var array<string, \App\Contracts\Services\Status\StatusServiceProvider>
     *
     * @var array
     */
    protected $providers;

    /**
     * Construct a new status service.
     *
     * @param \Psr\SimpleCache\CacheInterface|null              $cache   PSR-16 cache implementation
     * @param \App\Contracts\Services\Status\StatusOptions|null $options Service options
     * @psalm-param array<string, \App\Contracts\Services\Status\StatusServiceProvider> $providers
     *
     * @param array $providers Array of {@link App\Contracts\Services\Status\StatusServiceProvider}
     *
     * @throws \InvalidArgumentException
     *
     * @return void
     */
    public function __construct(
        ?CacheInterface $cache = null,
        ?StatusOptions $options = null,
        array $providers = []
    ) {
        // Ignore code coverage for this line. Its just setting a default set of options, so no need to really
        // write a unit test to cover this.
        // @codeCoverageIgnoreStart
        if ($options === null) {
            $options = new StatusOptions();
        }
        // @codeCoverageIgnoreEnd

        // Make sure a valid cache implementation is provided if caching is enabled.
        if ($cache === null && $options->isCacheEnabled()) {
            throw new InvalidArgumentException(
                'Must provide a ['.CacheInterface::class.'] implementation if caching is enabled.'
            );
        }

        // Set service options.
        $this->cache = $cache;
        $this->isCacheEnabled = $options->isCacheEnabled();
        $this->cacheTtl = $options->getCacheTtl();
        $this->cacheKey = $options->getCacheKey();

        unset($options);

        // Filter out invalid providers.
        // Psalm complains because with the annotated types, it "should" be a correct provider type, but
        // since its PHP, we filter out any incorrect providers.
        /** @psalm-suppress RedundantConditionGivenDocblockType */
        $this->providers = array_filter(
            $providers,
            /**
             * Filter out providers that are not of instance {@link \App\Contracts\Services\Service\StatusServiceProvider}.
             *
             * @param mixed $provider {@link \App\Contracts\Services\Service\StatusServiceProvider}
             *
             * @return bool true iff it is an instance of {@link \App\Contracts\Services\Service\StatusServiceProvider},
             *              false otherwise
             */
            function ($provider): bool {
                return $provider instanceof StatusServiceProvider;
            }
        );
    }

    /**
     * {@inheritdoc}
     */
    public function addProvider(string $providerName, StatusServiceProvider $provider): bool
    {
        if (isset($this->providers[$providerName])) {
            throw new InvalidArgumentException("Provider has already been registered with name [{$providerName}].");
        }

        $this->providers[$providerName] = $provider;

        return isset($this->providers[$providerName]);
    }

    /**
     * {@inheritdoc}
     */
    public function removeProvider(string $providerName): bool
    {
        if (! isset($this->providers[$providerName])) {
            throw new InvalidArgumentException("No provider registered with name [{$providerName}].");
        }

        unset($this->providers[$providerName]);

        return ! isset($this->providers[$providerName]);
    }

    /**
     * {@inheritdoc}
     */
    public function getProviders(): array
    {
        return array_values($this->providers);
    }

    /**
     * {@inheritdoc}
     */
    public function getProviderNames(): array
    {
        return array_keys($this->providers);
    }

    /**
     * {@inheritdoc}
     */
    public function getStatus(?string $providerName = 'all'): array
    {
        // Treat null as fetching all provider statuses.
        if ($providerName === null || $providerName === 'all') {
            return $this->resolveProviderArray(array_keys($this->providers), $this->cacheKey.'_all');
        }

        return [$providerName => $this->resolveStatus($providerName)];
    }

    /**
     * {@inheritdoc}
     */
    public function getStatusByArray(array $providers): array
    {
        // Must provide a list of providers to resolve.
        if (count($providers) === 0) {
            throw new InvalidArgumentException('No providers specified.');
        }

        // Filter out provider array to only allow non-empty strings. It's PHP
        // so deal with it.
        $providerNames = array_filter(
            $providers,
            /**
             * Determine if provider name is a string and not empty.
             *
             * @param mixed $string Provider name
             *
             * @return bool true iff param is a string and not empty, false otherwise
             */
            function ($string): bool {
                return is_string($string) && $string !== '';
            }
        );

        return $this->resolveProviderArray($providerNames, $this->cacheKey.'_'.implode('_', $providerNames));
    }

    /**
     * Resolve statuses of an array of provider names.
     *
     * @param string[] $providerNames Array of provider names
     * @param string   $cacheKey      Cache key
     *
     * @throws \App\Contracts\Services\Status\StatusCacheException
     * @throws \App\Contracts\Services\Status\StatusProviderNotRegisteredException
     *
     * @psalm-return array<mixed, mixed>
     *
     * @return array Resolved provider statuses
     */
    protected function resolveProviderArray(array $providerNames, string $cacheKey): array
    {
        $statuses = [];

        // Check the cache for this particular grouping of providers.
        if ($this->isCacheEnabled) {
            $status = $this->checkCache($cacheKey);

            if ($status !== null) {
                return $status;
            }
        }

        // Resolve each provider and store in our array.
        foreach ($providerNames as $provider) {
            $statuses[$provider] = $this->resolveStatus($provider);
        }

        // Cache statuses for this status provider group.
        if ($this->isCacheEnabled) {
            $this->saveInCache($cacheKey, $statuses);
        }

        return $statuses;
    }

    /**
     * Resolves a status for a specific provider.
     *
     * @param string $providerName Provider name
     *
     * @throws \App\Contracts\Services\Status\StatusCacheException
     * @throws \App\Contracts\Services\Status\StatusProviderNotRegisteredException
     *
     * @psalm-return array<mixed, mixed>
     *
     * @return array Resolved provider status
     */
    protected function resolveStatus(string $providerName): array
    {
        // Invalid (not registered) provider.
        if (! isset($this->providers[$providerName])) {
            throw new StatusProviderNotRegisteredException("No provider registered with name [{$providerName}].");
        }

        $cacheKey = $this->cacheKey.'_'.$providerName;

        // Check the cache for the provider if enabled.
        if ($this->isCacheEnabled) {
            $status = $this->checkCache($cacheKey);

            if ($status !== null) {
                return $status;
            }
        }

        $status = $this->providers[$providerName]->getStatus();

        // Cache status for this provider.
        if ($this->isCacheEnabled) {
            $this->saveInCache($cacheKey, $status);
        }

        return $status;
    }

    /**
     * Check the cache for the given key and return it if it exists, otherwise return null.
     *
     * @psalm-suppress PossiblyNullReference
     *
     * @param string $cacheKey Cache key
     *
     * @throws \App\Contracts\Services\Status\StatusCacheException
     *
     * @psalm-return array<mixed, mixed>|null
     *
     * @return array|null Status array if present, null iff no cache hit
     */
    protected function checkCache(string $cacheKey): ?array
    {
        try {
            // Check the cache.
            // PSR's throws annotation are incorrect because the base CacheException is an interface.
            /** @psalm-suppress MissingThrowsDocblock */
            if ($this->cache->has($cacheKey)) {
                return $this->resolveCachedStatus($cacheKey);
            }
        } catch (Throwable $exception) {
            throw new StatusCacheException($exception->getMessage(), (int) $exception->getCode(), $exception);
        }

        return null;
    }

    /**
     * Resolve cached status from cache. If no cache entry is found or cannot be resolve, null will
     * be returned.
     *
     * @psalm-suppress PossiblyNullReference
     * @psalm-suppress DocblockTypeContradiction
     *
     * @param string $cacheKey Cache key
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \App\Contracts\Services\Status\StatusCacheException
     *
     * @psalm-return array<mixed, mixed>|null
     *
     * @return array|null Status array if present, null iff no cache hit
     */
    protected function resolveCachedStatus(string $cacheKey): ?array
    {
        /** @var string|null */
        $cache = $this->cache->get($cacheKey, null);

        // Nothing was returned from the cache, return null.
        if ($cache === null) {
            return $cache;
        }

        // We don't have a serialized string, so return null since we can't
        // serialize it.
        // Since we can't guarantee the return type from the cache, this explicit check is still
        // needed even though we say it will either be a string or null since PSR's cache get return
        // type if very loose (mixed).
        if (! is_string($cache)) {
            return null;
        }

        // Unserialize what is returned from the cache. Also it should
        // only ever be an array, so no unserializing classes (RCE anyone?)
        /** @var array */
        $status = unserialize($cache, ['allowed_classes' => false]);

        // If the unserialization failed, or it does not result in an array, return
        // null.
        // Psalm complains, but because this is PHP, we want to be sure we return either
        // an array or null to abide by the documented return type.
        /** @psalm-suppress RedundantConditionGivenDocblockType */
        return ! is_array($status) ? null : $status;
    }

    /**
     * Saves status in cache.
     *
     * @psalm-suppress PossiblyNullReference
     *
     * @psalm-param array<mixed, mixed> $status
     *
     * @param string $cacheKey Cache key
     * @param array  $status   Status to save in cache
     *
     * @throws \App\Contracts\Services\Status\StatusCacheException
     *
     * @return void
     */
    protected function saveInCache(string $cacheKey, array $status): void
    {
        try {
            // Attempt to save cache item. If that fails, throw an exception.
            // PSR's throws annotation are incorrect because the base CacheException is an interface.
            /** @psalm-suppress MissingThrowsDocblock */
            $saved = $this->cache->set($cacheKey, serialize($status), $this->cacheTtl);

            // Failed to save status, raise an exception.
            if ($saved === false) {
                throw new StatusCacheException("Unable to save status in cache for cache key n[;{$cacheKey}].");
            }
        } catch (Throwable $exception) {
            throw new StatusCacheException($exception->getMessage(), (int) $exception->getCode(), $exception);
        }
    }
}
