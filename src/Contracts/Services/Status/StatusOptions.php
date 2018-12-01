<?php

declare(strict_types=1);

namespace App\Contracts\Services\Status;

/**
 * Status service options. This defines the options available for
 * the {@link \App\Contracts\Services\Status\StatusService} service.
 *
 * @codeCoverageIgnore
 * Ignore code coverage for this file. It performs no logic and only provides a
 * consistent definition for options for the Status service.
 *
 * @author    Brandon Clothier <brandon14125@gmail.com>
 *
 * @version   1.0.0
 *
 * @license   MIT
 * @copyright 2018
 */
class StatusOptions
{
    /**
     * Whether to cache the statuses or not.
     *
     * @var bool
     */
    protected $isCacheEnabled;

    /**
     * How long to cache the statuses for.
     *
     * @var int
     */
    protected $cacheTtl;

    /**
     * Cache key.
     *
     * @var string
     */
    protected $cacheKey;

    /**
     * Constructs a new set of {@link \App\Contracts\Services\Status\StatusService} options.
     *
     * @SuppressWarnings("BooleanArgumentFlag")
     *
     * @param bool   $isCacheEnabled
     * @param int    $cacheTtl
     * @param string $cacheKey
     *
     * @throws \InvalidArgumentException
     *
     * @return void
     */
    public function __construct(
        bool $isCacheEnabled = true,
        int $cacheTtl = 30,
        string $cacheKey = 'statuses'
    ) {
        $this->isCacheEnabled = $isCacheEnabled;
        $this->cacheTtl = $cacheTtl;
        $this->cacheKey = $cacheKey;
    }

    /**
     * Get whether caching is enabled.
     *
     * @return bool
     */
    public function isCacheEnabled(): bool
    {
        return $this->isCacheEnabled;
    }

    /**
     * Get cache TTL option.
     *
     * @return int
     */
    public function getCacheTtl(): int
    {
        return $this->cacheTtl;
    }

    /**
     * Get cache key option.
     *
     * @return string
     */
    public function getCacheKey(): string
    {
        return $this->cacheKey;
    }
}
