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

namespace App\Services\LastModified;

use DateTime;
use Throwable;
use function time;
use function count;
use function implode;
use DateTimeInterface;
use function is_string;
use function array_filter;
use Psr\SimpleCache\CacheInterface;
use App\Contracts\Services\CacheException;
use App\Contracts\Services\ProviderRegistrationException;
use App\Contracts\Services\LastModified\LastModifiedOptions;
use App\Contracts\Services\LastModified\LastModifiedService;
use App\Contracts\Services\CacheImplementationNeededException;
use App\Contracts\Services\LastModified\LastModifiedTimeProvider;

/**
 * Class LastModified.
 *
 * Last modified time service. Allows registering different
 * {@link \App\Contracts\Services\LastModified\LastModifiedTimeProvider}
 * and will return the most recent timestamp from the providers.
 *
 * @author Brandon Clothier <brandon14125@gmail.com>
 */
class LastModified implements LastModifiedService
{
    /**
     * Application cache store.
     */
    protected ?CacheInterface $cache;

    /**
     * Whether to cache the timestamp or not.
     */
    protected bool $isCacheEnabled;

    /**
     * How long to cache the last modified timestamp for.
     */
    protected int $cacheTtl;

    /**
     * Cache key.
     */
    protected string $cacheKey;

    /**
     * Timestamp format.
     */
    protected string $timestampFormat;

    /**
     * Associative array of 'name' => {@link \App\Contracts\Services\LastModified\LastModifiedTimeProvider}.
     *
     * @psalm-var array<string, \App\Contracts\Services\LastModified\LastModifiedTimeProvider>
     */
    protected array $providers;

    /**
     * Constructs a LastModified service object.
     *
     * @param \Psr\SimpleCache\CacheInterface|null                          $cache   PSR-16 cache implementation
     * @param \App\Contracts\Services\LastModified\LastModifiedOptions|null $options Service options
     * @psalm-param array<string, \App\Contracts\Services\LastModified\LastModifiedTimeProvider> $providers
     *
     * @param array $providers Array of {@link \App\Contracts\Services\LastModified\LastModifiedTimeProvider}
     *
     * @throws \App\Contracts\Services\InvalidDateFormatException
     * @throws \App\Contracts\Services\CacheImplementationNeededException
     *
     * @return void
     */
    public function __construct(
        ?CacheInterface $cache = null,
        ?LastModifiedOptions $options = null,
        array $providers = []
    ) {
        // Ignore code coverage for this line. Its just setting a default set of options, so no need to really
        // write a unit test to cover this.
        // @codeCoverageIgnoreStart
        if ($options === null) {
            $options = new LastModifiedOptions();
        }
        // @codeCoverageIgnoreEnd

        // Make sure a valid cache implementation is provided if caching is enabled.
        if ($cache === null && $options->isCacheEnabled()) {
            throw CacheImplementationNeededException::cacheImplementationNeeded();
        }

        // Set service options.
        $this->cache = $cache;
        $this->isCacheEnabled = $options->isCacheEnabled();
        $this->cacheTtl = $options->getCacheTtl();
        $this->cacheKey = $options->getCacheKey();
        $this->timestampFormat = $options->getTimestampFormat();

        unset($options);

        // Filter out invalid providers.
        // Psalm complains because with the annotated types, it "should" be a correct provider type, but
        // since its PHP, we filter out any incorrect providers.
        /** @psalm-suppress RedundantConditionGivenDocblockType */
        $this->providers = array_filter(
            $providers,
            /**
             * Filter out providers that are not of instance {@link \App\Contracts\Services\LastModified\LastModifiedTimeProvider}.
             *
             * @param mixed $provider {@link \App\Contracts\Services\LastModified\LastModifiedTimeProvider}
             *
             * @return bool true iff it is an instance of {@link \App\Contracts\Services\LastModified\LastModifiedTimeProvider},
             *              false otherwise
             */
            static fn ($provider): bool => $provider instanceof LastModifiedTimeProvider
        );
    }

    /**
     * {@inheritdoc}
     */
    public function addProvider(string $providerName, LastModifiedTimeProvider $provider): bool
    {
        if (isset($this->providers[$providerName])) {
            throw ProviderRegistrationException::providerAlreadyRegistered($providerName);
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
            throw ProviderRegistrationException::noProviderRegistered($providerName);
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
    public function getLastModifiedTime(?string $providerName = 'all'): DateTimeInterface
    {
        // Treat null as fetching all providers.
        if ($providerName === null || $providerName === 'all') {
            return (new DateTime())->setTimeStamp(
                $this->resolveProviderArray(array_keys($this->providers), $this->cacheKey.'_all')
            );
        }

        $timestamp = $this->resolveTimestamp($providerName);

        // Prevent negative and future timestamps.
        if ($timestamp < 0 || $timestamp > time()) {
            $timestamp = time();
        }

        return (new DateTime())->setTimestamp($timestamp);
    }

    /**
     * {@inheritdoc}
     */
    public function getLastModifiedTimeByArray(array $providers): DateTimeInterface
    {
        // Must provide a list of providers to resolve.
        if (count($providers) === 0) {
            throw ProviderRegistrationException::noProvidersSpecified();
        }

        // Filter out provider array to only allow non-empty strings. It's PHP
        // so deal with it.
        /** @psalm-suppress RedundantConditionGivenDocblockType */
        $providerNames = array_filter(
            $providers,
            /**
             * Determine if provider name is a string and not empty.
             *
             * @param mixed $string Provider name
             *
             * @return bool true iff param is a string and not empty, false otherwise
             */
            static fn ($string): bool => is_string($string) && $string !== ''
        );

        return (new DateTime())->setTimestamp(
            $this->resolveProviderArray($providerNames, $this->cacheKey.'_'.implode('_', $providerNames))
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultTimestampFormat(): string
    {
        return $this->timestampFormat;
    }

    /**
     * Get last modified timestamp for an array of providers.
     *
     * @param string[] $providerNames Provider names
     * @param string   $cacheKey      Cache key
     *
     * @throws \App\Contracts\Services\CacheException
     * @throws \App\Contracts\Services\ProviderRegistrationException
     *
     * @return int Resolved timestamp
     */
    protected function resolveProviderArray(array $providerNames, string $cacheKey): int
    {
        // Check cache for this group of providers.
        if ($this->isCacheEnabled) {
            $timestamp = $this->checkCache($cacheKey);

            if ($timestamp !== null) {
                return $timestamp;
            }
        }

        $timestamp = $this->resolveProviderTimestamps($providerNames);

        // Save in cache this provider group.
        if ($this->isCacheEnabled) {
            $this->saveInCache($cacheKey, $timestamp);
        }

        return $timestamp;
    }

    /**
     * Resolve latest timestamp from an array of provider names.
     *
     * @param string[] $providerNames Provider names
     *
     * @throws \App\Contracts\Services\CacheException
     * @throws \App\Contracts\Services\ProviderRegistrationException
     *
     * @return int Resolved timestamp
     */
    protected function resolveProviderTimestamps(array $providerNames): int
    {
        $timestamp = -1;

        // Resolve all providers, keeping track of the most recent one.
        foreach ($providerNames as $providerName) {
            $providerTimestamp = $this->resolveTimestamp($providerName);

            $timestamp = $providerTimestamp > $timestamp ? $providerTimestamp : $timestamp;
        }

        // Prevent negative and future timestamps.
        if ($timestamp < 0 || $timestamp > time()) {
            $timestamp = time();
        }

        return $timestamp;
    }

    /**
     * Resolve timestamp for a specific provider.
     *
     * @param string $providerName Provider name
     *
     * @throws \App\Contracts\Services\CacheException
     * @throws \App\Contracts\Services\ProviderRegistrationException
     *
     * @return int Resolved timestamp
     */
    protected function resolveTimestamp(string $providerName): int
    {
        // Invalid (not registered) provider.
        if (! isset($this->providers[$providerName])) {
            throw ProviderRegistrationException::noProviderRegistered($providerName);
        }

        $cacheKey = $this->cacheKey.'_'.$providerName;

        // Check the cache for the provider if enabled.
        if ($this->isCacheEnabled) {
            $timestamp = $this->checkCache($cacheKey);

            if ($timestamp !== null) {
                return $timestamp;
            }
        }

        $timestamp = $this->providers[$providerName]->getLastModifiedTime();

        // Cache status for this provider.
        if ($this->isCacheEnabled) {
            $this->saveInCache($cacheKey, $timestamp);
        }

        return $timestamp;
    }

    /**
     * Check the cache for the given key and return it iff it exists, otherwise return null.
     *
     * @psalm-suppress PossiblyNullReference
     *
     * @param string $cacheKey Cache key
     *
     * @throws \App\Contracts\Services\CacheException
     *
     * @return int|null Resolved timestamp iff found, null otherwise
     */
    protected function checkCache(string $cacheKey): ?int
    {
        try {
            // Check the cache.
            // PSR's throws annotation are incorrect because the base CacheException is an interface.
            /** @psalm-suppress MissingThrowsDocblock */
            if ($this->cache->has($cacheKey)) {
                // Coerce cache value into an integer.
                $timestamp = (int) $this->cache->get($cacheKey, null);

                // If the resulting timestamp is 0 (signifying an invalid timestamp) return null.
                return $timestamp === 0 ? null : $timestamp;
            }
        } catch (Throwable $exception) {
            throw CacheException::createFromException($exception);
        }

        return null;
    }

    /**
     * Saves timestamp in cache.
     *
     * @psalm-suppress PossiblyNullReference
     *
     * @param string $cacheKey  Cache key
     * @param int    $timestamp Timestamp to save in cache
     *
     * @throws \App\Contracts\Services\CacheException
     */
    protected function saveInCache(string $cacheKey, int $timestamp): void
    {
        try {
            // Make sure cache entry was saved. If not throw a cache exception.
            // PSR's throws annotation are incorrect because the base CacheException is an interface.
            /** @psalm-suppress MissingThrowsDocblock */
            $saved = $this->cache->set($cacheKey, $timestamp, $this->cacheTtl);

            if ($saved === false) {
                throw CacheException::createForTimestampSaveFailure($cacheKey);
            }
        } catch (Throwable $exception) {
            throw CacheException::createFromException($exception);
        }
    }
}
