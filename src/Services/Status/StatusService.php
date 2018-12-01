<?php

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
use Psr\SimpleCache\CacheException;
use Psr\SimpleCache\CacheInterface;
use App\Contracts\Services\Status\StatusOptions;
use App\Contracts\Services\Status\StatusCacheException;
use App\Contracts\Services\Status\StatusServiceProvider;
use App\Contracts\Services\Status\StatusProviderNotRegisteredException;
use App\Contracts\Services\Status\StatusService as StatusServiceInterface;

/**
 * Status service. Allows registering different
 * {@link \App\Contracts\Services\Status\StatusServiceProvider}
 * and will return statuses from those providers.
 *
 * @author    Brandon Clothier <brandon14125@gmail.com>
 *
 * @version   1.0.0
 *
 * @license   MIT
 * @copyright 2018
 */
class StatusService implements StatusServiceInterface
{
    /**
     * Application cache store.
     *
     * @var \Psr\SimpleCache\CacheInterface
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
     * @var array
     */
    protected $providers;

    /**
     * Construct a new status service.
     *
     * @param \Psr\SimpleCache\CacheInterface|null                   $cache
     * @param \App\Contracts\Services\Status\StatusOptions|null      $options
     * @param \App\Contracts\Services\Status\StatusServiceProvider[] $providers
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

        // Filter out invalid providers.
        $this->providers = array_filter(
            $providers,
            function ($provider) {
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
            function ($string) {
                return is_string($string) && $string !== '';
            }
        );

        return $this->resolveProviderArray($providerNames, $this->cacheKey.'_'.implode('_', $providerNames));
    }

    /**
     * Resolve statuses of an array of provider names.
     *
     * @param array  $providerNames
     * @param string $cacheKey
     *
     * @throws \App\Contracts\Services\Status\StatusCacheException
     * @throws \App\Contracts\Services\Status\StatusProviderNotRegisteredException
     *
     * @return array
     */
    protected function resolveProviderArray(array $providerNames, string $cacheKey): array
    {
        $statuses = [];

        // Check the cache for this particular grouping of providers.
        if ($this->isCacheEnabled && ($status = $this->checkCache($cacheKey)) !== null) {
            return $status;
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
     * @param string $providerName
     *
     * @throws \App\Contracts\Services\Status\StatusCacheException
     * @throws \App\Contracts\Services\Status\StatusProviderNotRegisteredException
     *
     * @return array
     */
    protected function resolveStatus(string $providerName): array
    {
        // Invalid (not registered) provider.
        if (! isset($this->providers[$providerName])) {
            throw new StatusProviderNotRegisteredException("No provider registered with name [{$providerName}].");
        }

        $cacheKey = $this->cacheKey.'_'.$providerName;

        // Check the cache for the provider if enabled.
        if ($this->isCacheEnabled && ($status = $this->checkCache($cacheKey)) !== null) {
            return $status;
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
     * @param string $cacheKey
     *
     * @throws \App\Contracts\Services\Status\StatusCacheException
     *
     * @return array|null
     */
    protected function checkCache(string $cacheKey): ?array
    {
        try {
            // Check the cache.
            if ($this->cache->has($cacheKey)) {
                // Unserialize what is returned from the cache. Notice the default value of
                // null, which when serialized will be the literal `false`. Also it should
                // only ever be an array, so no unserializing classes (RCE anyone?)
                $status = unserialize($this->cache->get($cacheKey, null), ['allowed_classes' => false]);

                // If the unserialization failed, or it does not result in an array, return
                // null.
                return $status === false || ! is_array($status) ? null : $status;
            }
        } catch (CacheException | Throwable $exception) {
            throw new StatusCacheException($exception->getMessage());
        }

        return null;
    }

    /**
     * Saves status in cache.
     *
     * @param string $cacheKey
     * @param array  $status
     *
     * @throws \App\Contracts\Services\Status\StatusCacheException
     *
     * @return void
     */
    protected function saveInCache(string $cacheKey, array $status): void
    {
        try {
            // Attempt to save cache item. If that fails, throw an exception.
            $saved = $this->cache->set($cacheKey, serialize($status), $this->cacheTtl);

            // Failed to save status, raise an exception.
            if ($saved === false) {
                throw new StatusCacheException("Unable to save status in cache for cache key n[;{$cacheKey}].");
            }
        } catch (CacheException | Throwable $exception) {
            throw new StatusCacheException($exception->getMessage());
        }
    }
}
