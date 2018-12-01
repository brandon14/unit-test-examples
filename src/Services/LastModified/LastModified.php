<?php

declare(strict_types=1);

namespace App\Services\LastModified;

use App\Contracts\Services\LastModified\LastModifiedCacheException;
use App\Contracts\Services\LastModified\LastModifiedOptions;
use App\Contracts\Services\LastModified\LastModifiedProviderNotRegisteredException;
use App\Contracts\Services\LastModified\LastModifiedService;
use App\Contracts\Services\LastModified\LastModifiedTimeProvider;
use Carbon\Carbon;
use InvalidArgumentException;
use Psr\SimpleCache\CacheException;
use Psr\SimpleCache\CacheInterface;
use Throwable;
use function array_filter;
use function count;
use function implode;
use function is_string;
use function time;

/**
 * Last modified time service. Allows registering different
 * {@link \App\Contracts\Services\LastModified\LastModifiedTimeProvider}
 * and will return the most recent timestamp from the providers.
 *
 * @author    Brandon Clothier <brandon14125@gmail.com>
 *
 * @version   1.0.0
 *
 * @license   MIT
 * @copyright 2018
 */
class LastModified implements LastModifiedService
{
    /**
     * Application cache store.
     *
     * @var \Psr\SimpleCache\CacheInterface
     */
    protected $cache;

    /**
     * Whether to cache the timestamp or not.
     *
     * @var bool
     */
    protected $isCacheEnabled;

    /**
     * How long to cache the last modified timestamp for.
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
     * Timestamp format.
     *
     * @var string
     */
    protected $timestampFormat;

    /**
     * Associative array of 'name' => {@link \App\Contracts\Services\LastModified\LastModifiedTimeProvider}.
     *
     * @var array
     */
    protected $providers;

    /**
     * Constructs a LastModified service object.
     *
     * @param \Psr\SimpleCache\CacheInterface|null                            $cache
     * @param \App\Contracts\Services\LastModified\LastModifiedOptions|null   $options
     * @param \App\Contracts\Services\LastModified\LastModifiedTimeProvider[] $providers
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
            throw new InvalidArgumentException(
                'Must provide a ['.CacheInterface::class.'] implementation if caching is enabled.'
            );
        }

        // Set service options.
        $this->cache = $cache;
        $this->isCacheEnabled = $options->isCacheEnabled();
        $this->cacheTtl = $options->getCacheTtl();
        $this->cacheKey = $options->getCacheKey();
        $this->timestampFormat = $options->getTimestampFormat();

        // Filter out invalid providers.
        $this->providers = array_filter(
            $providers,
            function ($provider) {
                return $provider instanceof LastModifiedTimeProvider;
            }
        );
    }

    /**
     * {@inheritdoc}
     */
    public function addProvider(string $providerName, LastModifiedTimeProvider $provider): bool
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
        if (!isset($this->providers[$providerName])) {
            throw new InvalidArgumentException("No provider registered with name [{$providerName}].");
        }

        unset($this->providers[$providerName]);

        return !isset($this->providers[$providerName]);
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
    public function getLastModifiedTime(?string $providerName = 'all'): Carbon
    {
        // Treat null as fetching all providers.
        if ($providerName === null || $providerName === 'all') {
            return Carbon::createFromTimestamp(
                $this->resolveProviderArray(array_keys($this->providers), $this->cacheKey.'_all')
            );
        }

        $timestamp = $this->resolveTimestamp($providerName);

        // Prevent negative and future timestamps.
        if ($timestamp < 0 || $timestamp > time()) {
            $timestamp = time();
        }

        return Carbon::createFromTimestamp($timestamp);
    }

    /**
     * {@inheritdoc}
     */
    public function getLastModifiedTimeByArray(array $providers): Carbon
    {
        // Must provide a list of providers to resolve.
        if (count($providers) === 0) {
            throw new InvalidArgumentException('No providers specified.');
        }

        // Filter out provider array to only allow non-empty strings. It's PHP
        // so deal with it.
        $providerNames = array_filter(
            $providers,
            function ($string) {
                return is_string($string) && $string !== '';
            }
        );

        return Carbon::createFromTimestamp(
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
     * @param array  $providerNames
     * @param string $cacheKey
     *
     * @throws \App\Contracts\Services\LastModified\LastModifiedCacheException
     * @throws \App\Contracts\Services\LastModified\LastModifiedProviderNotRegisteredException
     *
     * @return int
     */
    protected function resolveProviderArray(array $providerNames, string $cacheKey): int
    {
        // Check cache for this group of providers.
        if ($this->isCacheEnabled && ($timestamp = $this->checkCache($cacheKey)) !== null) {
            return $timestamp;
        }

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

        // Save in cache this provider group.
        if ($this->isCacheEnabled) {
            $this->saveInCache($cacheKey, $timestamp);
        }

        return $timestamp;
    }

    /**
     * Resolve timestamp for a specific provider.
     *
     * @param string $providerName
     *
     * @throws \App\Contracts\Services\LastModified\LastModifiedCacheException
     * @throws \App\Contracts\Services\LastModified\LastModifiedProviderNotRegisteredException
     *
     * @return int
     */
    protected function resolveTimestamp(string $providerName): int
    {
        // Invalid (not registered) provider.
        if (!isset($this->providers[$providerName])) {
            throw new LastModifiedProviderNotRegisteredException("No provider registered with name [{$providerName}].");
        }

        $cacheKey = $this->cacheKey.'_'.$providerName;

        // Check the cache for the provider if enabled.
        if ($this->isCacheEnabled && ($timestamp = $this->checkCache($cacheKey)) !== null) {
            return $timestamp;
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
     * @param string $cacheKey
     *
     * @throws \App\Contracts\Services\LastModified\LastModifiedCacheException
     *
     * @return int|null
     */
    protected function checkCache(string $cacheKey): ?int
    {
        try {
            // Check the cache.
            if ($this->cache->has($cacheKey)) {
                // Coerce cache value into an integer.
                $timestamp = (int) $this->cache->get($cacheKey, null);

                // If the resulting timestamp is 0 (signifying an invalid timestamp) return null.
                return $timestamp === 0 ? null : $timestamp;
            }
        } catch (CacheException | Throwable $exception) {
            throw new LastModifiedCacheException($exception->getMessage());
        }

        return null;
    }

    /**
     * Saves timestamp in cache.
     *
     * @param string $cacheKey
     * @param int    $timestamp
     *
     * @throws \App\Contracts\Services\LastModified\LastModifiedCacheException
     *
     * @return void
     */
    protected function saveInCache(string $cacheKey, int $timestamp): void
    {
        try {
            // Make sure cache entry was saved. If not throw a cache exception.
            $saved = $this->cache->set($cacheKey, $timestamp, $this->cacheTtl);

            if ($saved === false) {
                throw new LastModifiedCacheException("Unable to save timestamp in cache for cache key [{$cacheKey}].");
            }
        } catch (CacheException | Throwable $exception) {
            throw new LastModifiedCacheException($exception->getMessage());
        }
    }
}
