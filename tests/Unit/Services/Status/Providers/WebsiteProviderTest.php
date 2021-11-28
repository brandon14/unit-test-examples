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

namespace Tests\Unit\Services\Status\Providers;

use Exception;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Message\RequestFactoryInterface;
use App\Services\Status\Providers\WebsiteProvider;
use App\Contracts\Services\Status\StatusServiceProvider;

/**
 * Class WebsiteProviderTest.
 *
 * WebsiteProvider tests.
 *
 * Again in this example we have a class that depends upon some external service (in this case
 * an HTTP connection), so we mock that {@link \Psr\Http\Client\ClientInterface} and so long as we
 * make sure the mock behaves as the actual implementation would (i.e. adhere to that interface),
 * then we can rest assured that our class behaves as intended.
 *
 * We are using the PSR-18 HTTP client interface as this allows a wide range of satisfiable HTTP client
 * to be used (including Guzzle via an adapter).
 *
 * @author Brandon Clothier <brandon14125@gmail.com>
 */
class WebsiteProviderTest extends TestCase
{
    /**
     * Test that the provider will throw an {@link \InvalidArgument\Exception} when constructed
     * with an invalid argument.
     */
    final public function testThrowsInvalidArgumentExceptionInvalidUrl(): void
    {
        // Expect an InvalidArgumentException when providing an invalid URL.
        $this->expectException(InvalidArgumentException::class);

        $mockClient = $this->createMock(ClientInterface::class);
        $mockFactory = $this->createMock(RequestFactoryInterface::class);

        $url = 'this_is_not_a_valid_url';

        new WebsiteProvider($mockClient, $mockFactory, $url);
    }

    /**
     * Test that provider will handle exceptions thrown from the {@link \Psr\Http\Client\ClientInterface}.
     */
    final public function testProviderHandlesGuzzleException(): void
    {
        $url = 'https://www.example.com';
        $request = (new Psr17Factory())->createRequest('GET', $url);

        // Create PSR HTTP client mock.
        $mockClient = $this->createMock(ClientInterface::class);
        // Force mock to throw a HTTP client exception to simulate client being unable to complete
        // request.
        $mockClient->expects($this::once())
            ->method('sendRequest')
            ->with($request)
            ->will($this::throwException(new MockClientException('This is an exception')));

        $mockFactory = $this->createMock(RequestFactoryInterface::class);

        $mockFactory->expects($this::once())
            ->method('createRequest')
            ->with('GET', $url)
            ->willReturn($request);

        $instance = new WebsiteProvider($mockClient, $mockFactory, $url);

        $status = $instance->getStatus();

        // Should return a status of an error.
        $this::assertSame(['status' => StatusServiceProvider::STATUS_ERROR], $status);
    }

    /**
     * Test that provider only returns an OK status when the response from PSR HTTP client is
     * a response with a status in the 200 range.
     */
    final public function testProviderOnlyReturnsOkForStatusInTwoHundredRange(): void
    {
        $url = 'https://www.example.com';
        $request = (new Psr17Factory())->createRequest('GET', $url);

        // Create a mock PSR7 response class.
        $mockResponse = $this->createMock(ResponseInterface::class);

        // Have mock response be a successful 200 HTML response.
        $mockResponse->expects($this::once())->method('getStatusCode')->willReturn(200);

        // Create PSR HTTP client mock.
        $mockClient = $this->createMock(ClientInterface::class);
        // Force mock to return the mocked PSR7 response.
        $mockClient->expects($this::once())
            ->method('sendRequest')
            ->with($request)
            ->willReturn($mockResponse);

        $mockFactory = $this->createMock(RequestFactoryInterface::class);

        $mockFactory->expects($this::once())
            ->method('createRequest')
            ->with('GET', $url)
            ->willReturn($request);

        $instance = new WebsiteProvider($mockClient, $mockFactory, $url);

        $status = $instance->getStatus();

        // Should be an OK status.
        $this::assertSame(['status' => StatusServiceProvider::STATUS_OK], $status);
    }

    /**
     * Test that provider returns error status for responses not in 200 range.
     */
    final public function testProviderReturnsErrorForStatusNotInTwoHundredRange(): void
    {
        $url = 'https://www.example.com';
        $request = (new Psr17Factory())->createRequest('GET', $url);

        // Create a mock PSR7 response class.
        $mockResponse = $this->createMock(ResponseInterface::class);

        // Have mock response be a 404 not found
        $mockResponse->expects($this::once())->method('getStatusCode')->willReturn(404);

        // Create PSR HTTP client mock.
        $mockClient = $this->createMock(ClientInterface::class);
        // Force mock to return the mocked PSR7 response.
        $mockClient->expects($this::once())
            ->method('sendRequest')
            ->with($request)
            ->willReturn($mockResponse);

        $mockFactory = $this->createMock(RequestFactoryInterface::class);

        $mockFactory->expects($this::once())
            ->method('createRequest')
            ->with('GET', $url)
            ->willReturn($request);

        $instance = new WebsiteProvider($mockClient, $mockFactory, $url);

        $status = $instance->getStatus();

        // Should be an error status.
        $this::assertSame(['status' => StatusServiceProvider::STATUS_ERROR], $status);
    }
}

/**
 * Class MockClientException.
 *
 * Mock Http client exception class.
 *
 * @author Brandon Clothier <brandon14125@gmail.com>
 */
final class MockClientException extends Exception implements ClientExceptionInterface
{
    // Intentionally left blank.
}
