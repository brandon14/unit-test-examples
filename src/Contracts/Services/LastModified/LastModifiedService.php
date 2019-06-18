<?php

declare(strict_types=1);

/*
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
