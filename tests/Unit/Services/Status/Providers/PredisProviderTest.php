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

use Predis\ClientInterface;
use Predis\PredisException;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use App\Services\Status\Providers\PredisProvider;
use App\Contracts\Services\Status\StatusServiceProvider;

/**
 * Class PredisProviderTest.
 *
 * PredisProvider tests.
 *
 * In this test, we want to be able to test the functionality of our class that relies on
 * an {@link \Predis\ClientInterface}, but we don't actually want to require a redis server
 * set up just to unit test. So we pass that interface into the class, and that allows us to
 * mock it away during testing. As long as our mock behaves as a {@link \Predis\ClientInteraface}
 * would, then we can be confident our class performs like we expect it to.
 *
 * @author Brandon Clothier <brandon14125@gmail.com>
 */
class PredisProviderTest extends TestCase
{
    /**
     * Get mocked Predis client class.
     *
     * @return \Predis\ClientInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected function getPredisMock(): MockObject
    {
        return $this->getMockBuilder(ClientInterface::class)
                    ->addMethods(['ping'])
                    ->onlyMethods(
                        [
                            'getProfile',
                            'getOptions',
                            'connect',
                            'disconnect',
                            'getConnection',
                            'createCommand',
                            'executeCommand',
                            '__call',
                        ]
                    )->getMock();
    }

    /**
     * Test that the provider will handle when Predis client throws an exception.
     */
    final public function testProviderHandlesExceptionThrownFromPredis(): void
    {
        $mock = $this->getPredisMock();

        // Tell mocked Predis client to throw an exception when we hit the ping method.
        $mock->expects($this::once())->method('ping')->will(
            $this::throwException(new MockPredisException('This is a test.'))
        );

        $instance = new PredisProvider($mock);

        $status = $instance->getStatus();

        // Should return an error status array.
        $this::assertSame(['status' => StatusServiceProvider::STATUS_ERROR], $status);
    }

    /**
     * Test that provider will return an error status if it gets not PONG from redis.
     */
    final public function testProvidersReturnsErrorStatusIfPingNotSuccessful(): void
    {
        $mock = $this->getPredisMock();

        // Tell mocked Predis client to return a string other than PONG.
        $mock->expects($this::once())->method('ping')->willReturn('NOT PONG');

        $instance = new PredisProvider($mock);

        $status = $instance->getStatus();

        // Should return an error status array.
        $this::assertSame(['status' => StatusServiceProvider::STATUS_ERROR], $status);
    }

    /**
     * Test that provider will return an OK status when it gets a PONG back from redis.
     */
    final public function testProviderReturnsOkStatusOnSuccessfulPong(): void
    {
        $mock = $this->getPredisMock();

        // Tell mocked Predis client to return PONG.
        $mock->expects($this::once())->method('ping')->willReturn('PONG');

        $instance = new PredisProvider($mock);

        $status = $instance->getStatus();

        // Should return an OK status array.
        $this::assertSame(['status' => StatusServiceProvider::STATUS_OK], $status);
    }
}

/**
 * Class MockPredisException.
 *
 * A mock {@link \Predis\PredisException} to force the client to throw during testing.
 *
 * @author Brandon Clothier <brandon14125@gmail.com>
 */
final class MockPredisException extends PredisException
{
    // intentionally left blank.
}
