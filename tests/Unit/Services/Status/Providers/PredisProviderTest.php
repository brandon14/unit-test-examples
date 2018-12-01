<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Status\Providers;

use Predis\ClientInterface;
use Predis\PredisException;
use PHPUnit\Framework\TestCase;
use App\Services\Status\Providers\PredisProvider;
use App\Contracts\Services\Status\StatusServiceProvider;

/**
 * PredisProvider tests.
 *
 * In this test, we want to be able to test the functionality of our class that relies on
 * an {@link \Predis\ClientInterface}, but we don't actually want to require a redis server
 * set up just to unit test. So we pass that interface into the class, and that allows us to
 * mock it away during testing. As long as our mock behaves as a {@link \Predis\ClientInteraface}
 * would, then we can be confident our class performs like we expect it to.
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
        $mock->expects($this::once())->method('ping')->willReturn('NOT PONG');

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
        $mock->expects($this::once())->method('ping')->willReturn('PONG');

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
