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

namespace App\Services\Status\Providers;

use Throwable;
use function filter_var;
use InvalidArgumentException;
use const FILTER_VALIDATE_URL;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use App\Contracts\Services\Status\StatusServiceProvider;

/**
 * Class WebsiteProvider.
 *
 * Website status provider. Will make an HTTP request using
 * {@link \Psr\Http\Client\ClientInterface} to see if a website
 * responds.
 *
 * @author Brandon Clothier <brandon14125@gmail.com>
 */
class WebsiteProvider implements StatusServiceProvider
{
    /**
     * PSR HTTP client.
     */
    protected ClientInterface $httpClient;

    /**
     * PSR HTTP request factory.
     */
    protected RequestFactoryInterface $requestFactory;

    /**
     * Route to hit to check website status.
     */
    protected string $routeToPing;

    /**
     * Construct a new website status provider.
     *
     * @param \Psr\Http\Client\ClientInterface          $httpClient     PSR HTTP client instance
     * @param \Psr\Http\Message\RequestFactoryInterface $requestFactory PSR HTTP request factory instance
     * @param string                                    $routeToPing    Route to hit using PSR HTTP client
     *
     * @throws \InvalidArgumentException
     *
     * @return void
     */
    public function __construct(ClientInterface $httpClient, RequestFactoryInterface $requestFactory, string $routeToPing)
    {
        // Validate the URL to ping.
        if (filter_var($routeToPing, FILTER_VALIDATE_URL) === false) {
            throw new InvalidArgumentException("Invalid URL [{$routeToPing}] provided.");
        }

        $this->httpClient = $httpClient;
        $this->requestFactory = $requestFactory;
        $this->routeToPing = $routeToPing;
    }

    /**
     * {@inheritdoc}
     */
    public function getStatus(): array
    {
        try {
            $request = $this->requestFactory->createRequest('GET', $this->routeToPing);

            // Get the PSR-7 response from HTTP client.
            $response = $this->httpClient->sendRequest($request);

            // Suppress psalm because we can't always assume every PSR standard library will completely adhere
            // to the interface documentation.
            /** @psalm-suppress RedundantCastGivenDocblockType */
            $code = (int) $response->getStatusCode();

            // Check for a successful HTTP response.
            if ($code >= 200 && $code < 300) {
                return ['status' => StatusServiceProvider::STATUS_OK];
            }
        } catch (Throwable $e) {
            // Swallow exceptions on purpose.
        }

        return ['status' => StatusServiceProvider::STATUS_ERROR];
    }
}
