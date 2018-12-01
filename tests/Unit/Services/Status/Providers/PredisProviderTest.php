<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Status\Providers;

use App\Contracts\Services\Status\StatusServiceProvider;
use App\Services\Status\Providers\PredisProvider;
use PHPUnit\Framework\TestCase;
use Predis\ClientInterface;
use Predis\PredisException;

/**
 * PredisProvider tests.
 *
 * @author    Brandon Clothier <brandon14125@gmail.com>
 *
 * @version   1.0.0
 *
 * @license   MIT
 * @copyright 2018
 */
class PredisProviderTest extends TestCase
{
    public function testProviderHandlesExceptionThrownFromPredis(): void
    {
        $mock = $this->getMockBuilder(ClientInterface::class)
            ->setMethods(
                [
                    'ping',
                    'getProfile',
                    'getOptions',
                    'connect',
                    'disconnect',
                    'getConnection',
                    'createCommand',
                    'executeCommand',
                    '__call',
                ]
            )
            ->getMock();

        // Tell mocked Predis client to throw an exception when we hit the ping method.
        $mock->expects($this::once())->method('ping')->will(
            $this::throwException(new MockPredisException('This is a test.'))
        );

        $instance = new PredisProvider($mock);

        $status = $instance->getStatus();

        // Should return an error status array.
        $this::assertEquals(['status' => StatusServiceProvider::STATUS_ERROR], $status);
    }

    public function testProvidersReturnsErrorStatusIfPingNotSuccessful(): void
    {
        $mock = $this->getMockBuilder(ClientInterface::class)
            ->setMethods(
                [
                    'ping',
                    'getProfile',
                    'getOptions',
                    'connect',
                    'disconnect',
                    'getConnection',
                    'createCommand',
                    'executeCommand',
                    '__call',
                ]
            )
            ->getMock();

        // Tell mocked Predis client to return a string other than PONG.
        $mock->expects($this::once())->method('ping')->will($this::returnValue('NOT PONG'));

        $instance = new PredisProvider($mock);

        $status = $instance->getStatus();

        // Should return an error status array.
        $this::assertEquals(['status' => StatusServiceProvider::STATUS_ERROR], $status);
    }

    public function testProviderReturnsOkStatusOnSuccessfulPong(): void
    {
        $mock = $this->getMockBuilder(ClientInterface::class)
            ->setMethods(
                [
                    'ping',
                    'getProfile',
                    'getOptions',
                    'connect',
                    'disconnect',
                    'getConnection',
                    'createCommand',
                    'executeCommand',
                    '__call',
                ]
            )
            ->getMock();

        // Tell mocked Predis client to return PONG.
        $mock->expects($this::once())->method('ping')->will($this::returnValue('PONG'));

        $instance = new PredisProvider($mock);

        $status = $instance->getStatus();

        // Should return an OK status array.
        $this::assertEquals(['status' => StatusServiceProvider::STATUS_OK], $status);
    }
}

class MockPredisException extends PredisException
{
    // intentionally left blank.
}
