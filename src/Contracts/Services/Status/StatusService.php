<?php

declare(strict_types=1);

namespace App\Contracts\Services\Status;

/**
 * System status service. Allows registering multiple status providers (i.e. database, cache, services, etc)
 * and provides an interface to get the status of these providers.
 *
 * @author    Brandon Clothier <brandon14125@gmail.com>
 *
 * @version   1.0.0
 *
 * @license   MIT
 * @copyright 2018
 */
interface StatusService
{
    /**
     * Adds a {@link \App\Contracts\Services\Status\StatusServiceProvider} to the service.
     *
     * @param string                                               $providerName
     * @param \App\Contracts\Services\Status\StatusServiceProvider $provider
     *
     * @throws \InvalidArgumentException
     *
     * @return bool
     */
    public function addProvider(string $providerName, StatusServiceProvider $provider): bool;

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
     * {@link \App\Contracts\Services\Status\StatusServiceProvider}.
     *
     * @return \App\Contracts\Services\Status\StatusServiceProvider[]
     */
    public function getProviders(): array;

    /**
     * Get array of registered providers names.
     *
     * @return string[]
     */
    public function getProviderNames(): array;

    /**
     * Get the status for a provider (or all providers if string 'all' or  no param is passed in) or
     * list of providers.
     *
     * @param string|null $providerName
     *
     * @throws \App\Contracts\Services\Status\StatusCacheException
     * @throws \App\Contracts\Services\Status\StatusProviderNotRegisteredException
     *
     * @return array
     */
    public function getStatus(?string $providerName = 'all'): array;

    /**
     * Get the status for an array of provider names.
     *
     * @param array $providers
     *
     * @throws \InvalidArgumentException
     * @throws \App\Contracts\Services\Status\StatusCacheException
     * @throws \App\Contracts\Services\Status\StatusProviderNotRegisteredException
     *
     * @return array
     */
    public function getStatusByArray(array $providers): array;
}
