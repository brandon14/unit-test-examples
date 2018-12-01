<?php

declare(strict_types=1);

namespace App\Contracts\Services\LastModified;

use Carbon\Carbon;

/**
 * Last modified service interface. Allows registering
 * {@link \App\Contracts\LastModified\LastModifiedTimeProvider}
 * and will iterate through registered providers and return the
 * most recent timestamp.
 *
 * @author    Brandon Clothier <brandon14125@gmail.com>
 *
 * @version   1.0.0
 *
 * @license   MIT
 * @copyright 2018
 */
interface LastModifiedService
{
    /**
     * Adds a {@link \App\Contracts\Services\LastModified\LastModifiedTimeProvider} to the service.
     *
     * @param string                                                        $providerName
     * @param \App\Contracts\Services\LastModified\LastModifiedTimeProvider $provider
     *
     * @throws \InvalidArgumentException
     *
     * @return bool
     */
    public function addProvider(string $providerName, LastModifiedTimeProvider $provider): bool;

    /**
     * Removes the named provider from the service.
     *
     * @param string $providerName
     *
     * @throws \InvalidArgumentException
     *
     * @return bool
     */
    public function removeProvider(string $providerName): bool;

    /**
     * Get array of providers registered. Returns an array of
     * {@link \App\Contracts\Services\LastModified\LastModifiedTimeProvider}.
     *
     * @return \App\Contracts\Services\LastModified\LastModifiedTimeProvider[]
     */
    public function getProviders(): array;

    /**
     * Get array of registered providers names.
     *
     * @return string[]
     */
    public function getProviderNames(): array;

    /**
     * Gets the last modified time from a specific provider or if all is passed in, will
     * resolve timestamp from all providers.
     *
     * @param string|null $providerName
     *
     * @throws \App\Contracts\Services\LastModified\LastModifiedCacheException
     * @throws \App\Contracts\Services\LastModified\LastModifiedProviderNotRegisteredException
     *
     * @return \Carbon\Carbon
     */
    public function getLastModifiedTime(?string $providerName = 'all'): Carbon;

    /**
     * Gets the last modified time from an array of providers.
     *
     * @param array $providers
     *
     * @throws \InvalidArgumentException
     * @throws \App\Contracts\Services\LastModified\LastModifiedCacheException
     * @throws \App\Contracts\Services\LastModified\LastModifiedProviderNotRegisteredException
     *
     * @return \Carbon\Carbon
     */
    public function getLastModifiedTimeByArray(array $providers): Carbon;

    /**
     * Get the default timestamp format.
     *
     * @return string
     */
    public function getDefaultTimestampFormat(): string;
}
