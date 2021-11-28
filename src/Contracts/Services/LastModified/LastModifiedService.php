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

namespace App\Contracts\Services\LastModified;

use DateTimeInterface;

/**
 * Interface LastModifiedService.
 *
 * Last modified service interface. Allows registering
 * {@link \App\Contracts\LastModified\LastModifiedTimeProvider}
 * and will iterate through registered providers and return the
 * most recent timestamp.
 *
 * @author Brandon Clothier <brandon14125@gmail.com>
 */
interface LastModifiedService
{
    /**
     * Adds a {@link \App\Contracts\Services\LastModified\LastModifiedTimeProvider} to the service.
     *
     * @param string                                                        $providerName Provider name
     * @param \App\Contracts\Services\LastModified\LastModifiedTimeProvider $provider     Provider
     *
     * @throws \App\Contracts\Services\ProviderRegistrationException
     *
     * @return bool True iff provider was added, false otherwise
     */
    public function addProvider(string $providerName, LastModifiedTimeProvider $provider): bool;

    /**
     * Removes the named provider from the service.
     *
     * @param string $providerName Provider name
     *
     * @throws \App\Contracts\Services\ProviderRegistrationException
     *
     * @return bool True iff provider was removed, false otherwise
     */
    public function removeProvider(string $providerName): bool;

    /**
     * Get array of providers registered. Returns an array of
     * {@link \App\Contracts\Services\LastModified\LastModifiedTimeProvider}.
     *
     * @return \App\Contracts\Services\LastModified\LastModifiedTimeProvider[] Array of providers
     */
    public function getProviders(): array;

    /**
     * Get array of registered providers names.
     *
     * @return string[] Array of provider names
     */
    public function getProviderNames(): array;

    /**
     * Gets the last modified time from a specific provider or if all is passed in, will
     * resolve timestamp from all providers.
     *
     * @param string|null $providerName Provider name
     *
     * @throws \App\Contracts\Services\CacheException
     * @throws \App\Contracts\Services\ProviderRegistrationException
     *
     * @return \DateTimeInterface Last modified timestamp
     */
    public function getLastModifiedTime(?string $providerName = 'all'): DateTimeInterface;

    /**
     * Gets the last modified time from an array of providers.
     *
     * @param string[] $providers Array of provider names
     *
     * @throws \InvalidArgumentException
     * @throws \App\Contracts\Services\CacheException
     * @throws \App\Contracts\Services\ProviderRegistrationException
     *
     * @return \DateTimeInterface Last modified timestamp
     */
    public function getLastModifiedTimeByArray(array $providers): DateTimeInterface;

    /**
     * Get the default timestamp format.
     *
     * @return string Date-time format
     */
    public function getDefaultTimestampFormat(): string;
}
