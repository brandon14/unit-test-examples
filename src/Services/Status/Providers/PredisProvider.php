<?php

declare(strict_types=1);

namespace App\Services\Status\Providers;

use App\Contracts\Services\Status\StatusServiceProvider;
use Predis\ClientInterface as Predis;
use Throwable;

/**
 * Predis status provider. Allows pining a redis cache database via
 * {@link \Predis\Predis} to check the status of the database.
 *
 * @author    Brandon Clothier <brandon14125@gmail.com>
 *
 * @version   1.0.0
 *
 * @license   MIT
 * @copyright 2018
 */
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
            // Predis client returns 'PONG' on a successful ping.
            return (string) $this->redis->ping() === 'PONG'
                ? ['status' => StatusServiceProvider::STATUS_OK]
                : ['status' => StatusServiceProvider::STATUS_ERROR];
        } catch (Throwable $e) {
            // Swallow exceptions on purpose.
        }

        return ['status' => StatusServiceProvider::STATUS_ERROR];
    }
}
