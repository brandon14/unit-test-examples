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

namespace App\Contracts\Services;

use InvalidArgumentException;

/**
 * Class ProviderRegistrationException.
 *
 * Exception thrown when trying to add or remove service providers.
 *
 * @author Brandon Clothier <brandon14125@gmail.com>
 */
class ProviderRegistrationException extends InvalidArgumentException
{
    /**
     * Creates a new {@link \App\Contracts\Services\ProviderRegistrationException} when a provider
     * with name {@link $providerName} is already registered.
     *
     * @param string $providerName Provider name
     */
    public static function providerAlreadyRegistered(string $providerName): self
    {
        return new self("Provider has already been registered with name [{$providerName}].");
    }

    /**
     * Creates a new {@link \App\Contracts\Services\ProviderRegistrationException} for when
     * no provider with {@link $providerName} is registered.
     *
     * @param string $providerName Provider name
     */
    public static function noProviderRegistered(string $providerName): self
    {
        return new self("No provider registered with name [{$providerName}].");
    }

    /**
     * Creates a new {@link \App\Contracts\Services\ProviderRegistrationException} for
     * when no providers where specified.
     */
    public static function noProvidersSpecified(): self
    {
        return new self('No providers specified.');
    }
}
