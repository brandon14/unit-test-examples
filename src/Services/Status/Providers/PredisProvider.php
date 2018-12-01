<?php

declare(strict_types=1);

namespace App\Services\Status\Providers;

use Throwable;
use Predis\ClientInterface as Predis;
use App\Contracts\Services\Status\StatusServiceProvider;

class PredisProvider implements StatusServiceProvider
{
    /**
     * Predis client instance.
     *
     * @var \Predis\ClientInterface
     */
    protected $redis;

    /**
     * Construct a new predis status provider.
     *
     * @param \Predis\ClientInterface $redis
     *
     * @return void
     */
    public function __construct(Predis $redis)
    {
        $this->redis = $redis;
    }

    /**
     * {@inheritdoc}
     */
    public function getStatus(): array
    {
        try {
            // Predis client returns 'PONG' on a successfull ping.
            return (string) $this->redis->ping() === 'PONG'
                ? ['status' => StatusServiceProvider::STATUS_OK]
                : ['status' => StatusServiceProvider::STATUS_ERROR];
        } catch (Throwable $e) {
            // Swallow exceptions on purpose.
        }

        return ['status' => StatusServiceProvider::STATUS_ERROR];
    }
}
