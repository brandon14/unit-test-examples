<?php

/**
 * This file is part of the brandon14/unit-test-examples package.
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

namespace Tests\Unit\Services\Status\Providers;

use Exception;
use GuzzleHttp\Psr7\Response;
use InvalidArgumentException;
use GuzzleHttp\ClientInterface;
use PHPUnit\Framework\TestCase;
use GuzzleHttp\Exception\GuzzleException;
use App\Services\Status\Providers\WebsiteProvider;
use App\Contracts\Services\Status\StatusServiceProvider;

/**
 * Class WebsiteProviderTest.
 *
 * WebsiteProvider tests.
 *
 * Again in this example we have a class that depends upon some external service (in this case
 * an HTTP connection), so we mock that {@link \GuzzleHttp\ClientInterface} and so long as we
 * make sure the mock behaves as the actual implementation would (i.e. adhere to that interface),
 * then we can rest assured that our class behaves as intended.
 *
 * @author Brandon Clothier <brandon14125@gmail.com>
 */
class WebsiteProviderTest extends TestCase
{
    /**
     * Test that the provider will throw an {@link \InvalidArgument\Exception} when constructed
     * with an invalid argument.
     *
     * @return void
     */
    public function testThrowsInvalidArgumentExceptionInvalidUrl(): void
    {
        // Expect an InvalidArgumentException when providing an invalid URL.
        $this->expectException(InvalidArgumentException::class);

        $mockClient = $this->createMock(ClientInterface::class);

        $url = 'this_is_not_a_valid_url';

        new WebsiteProvider($mockClient, $url);
    }

    /**
     * Test that provider will handle exceptions thrown from the {@link \GuzzleHttp\ClientInterface}.
     *
     * @return void
     */
    public function testProviderHandlesGuzzleException(): void
    {
        $url = 'https://www.example.com';

        // Create Guzzle client mock.
        $mockClient = $this->createMock(ClientInterface::class);
        // Force mock to throw a GuzzleException to simulate Guzzle being unable to complete
        // request.
        $mockClient->expects($this::once())
            ->method('request')
            ->with('GET', $url)
            ->will($this::throwException(new MockGuzzleException('This is an exception')));

        $instance = new WebsiteProvider($mockClient, $url);

        $status = $instance->getStatus();

        // Should return a status of an error.
        $this::assertEquals(['status' => StatusServiceProvider::STATUS_ERROR], $status);
    }

    /**
     * Test that provider only returns an OK status when the response from Guzzle is
     * a response with a status in the 200 range.
     *
     * @return void
     */
    public function testProviderOnlyReturnsOkForStatusInTwoHundredRange(): void
    {
        $url = 'https://www.example.com';

        // Create a mock Guzzle pSR7 response class.
        $mockResponse = $this->createMock(Response::class);

        // Have mock response be a successful 200 HTML response.
        $mockResponse->expects($this::once())->method('getStatusCode')->willReturn(200);

        // Create Guzzle client mock.
        $mockClient = $this->createMock(ClientInterface::class);
        // Force mock to return the mocked PSR7 response.
        $mockClient->expects($this::once())
            ->method('request')
            ->with('GET', $url)
            ->willReturn($mockResponse);

        $instance = new WebsiteProvider($mockClient, $url);

        $status = $instance->getStatus();

        // Should be an OK status.
        $this::assertEquals(['status' => StatusServiceProvider::STATUS_OK], $status);
    }

    /**
     * Test that provider returns error status for responses not in 200 range.
     *
     * @return void
     */
    public function testProviderReturnsErrorForStatusNotInTwoHundredRange(): void
    {
        $url = 'https://www.example.com';

        // Create a mock Guzzle PSR7 response class.
        $mockResponse = $this->createMock(Response::class);

        // Have mock response be a 404 not found
        $mockResponse->expects($this::once())->method('getStatusCode')->willReturn(404);

        // Create Guzzle client mock.
        $mockClient = $this->createMock(ClientInterface::class);
        // Force mock to return the mocked PSR7 response.
        $mockClient->expects($this::once())
            ->method('request')
            ->with('GET', $url)
            ->willReturn($mockResponse);

        $instance = new WebsiteProvider($mockClient, $url);

        $status = $instance->getStatus();

        // Should be an error status.
        $this::assertEquals(['status' => StatusServiceProvider::STATUS_ERROR], $status);
    }
}

/**
 * Class MockGuzzleException.
 *
 * Mock GuzzleHttp exception class.
 *
 * @author Brandon Clothier <brandon14125@gmail.com>
 */
class MockGuzzleException extends Exception implements GuzzleException
{
    // Intentionally left blank.
}
