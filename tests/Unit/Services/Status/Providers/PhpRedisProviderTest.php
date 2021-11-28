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

use Redis;
use RedisException;
use PHPUnit\Framework\TestCase;
use App\Services\Status\Providers\PhpRedisProvider;
use App\Contracts\Services\Status\StatusServiceProvider;

/**
 * Class PhpRedisProviderTest.
 *
 * PhpRedisProvider tests.
 *
 * In this test, we want to be able to test the functionality of our class that relies on
 * an {@link \Redis}, but we don't actually want to require a redis server set up just to
 * unit test. So we pass a mock instance in to ensure we have full control over the classes
 * required dependencies.
 *
 * @author Brandon Clothier <brandon14125@gmail.com>
 */
class PhpRedisProviderTest extends TestCase
{
    /**
     * Test that the provider will handle when the Redis client throws an exception.
     */
    final public function testProviderHandlesExceptionThrownFromPhpRedis(): void
    {
        $mock = $this->createMock(Redis::class);

        // Tell mocked PhpRedis client to throw an exception when we hit the ping method.
        $mock->expects($this::once())->method('ping')->will(
            $this::throwException(new RedisException('This is a test.'))
        );

        $instance = new PhpRedisProvider($mock);

        $status = $instance->getStatus();

        // Should return an error status array.
        $this::assertSame(['status' => StatusServiceProvider::STATUS_ERROR], $status);
    }

    /**
     * Test that the provider will return an error status if it doesn't get a PONG from redis.
     */
    final public function testProvidersReturnsErrorStatusIfPingNotSuccessful(): void
    {
        $mock = $this->createMock(Redis::class);

        // Tell mocked PhpRedis client to return a string other than PONG.
        $mock->expects($this::once())->method('ping')->willReturn(false);

        $instance = new PhpRedisProvider($mock);

        $status = $instance->getStatus();

        // Should return an error status array.
        $this::assertSame(['status' => StatusServiceProvider::STATUS_ERROR], $status);
    }

    /**
     * Test that the provider will return an OK status when it gets a PONG from redis.
     */
    final public function testProviderReturnsOkStatusOnSuccessfulPong(): void
    {
        $mock = $this->createMock(Redis::class);

        // Tell mocked PhpRedis client to return PONG.
        $mock->expects($this::once())->method('ping')->willReturn(true);

        $instance = new PhpRedisProvider($mock);

        $status = $instance->getStatus();

        // Should return an OK status array.
        $this::assertSame(['status' => StatusServiceProvider::STATUS_OK], $status);
    }
}
