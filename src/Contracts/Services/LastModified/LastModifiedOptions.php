<?php

declare(strict_types=1);

namespace App\Contracts\Services\LastModified;

use InvalidArgumentException;

/**
 * Last modified time service options. This defines the options available for
 * the {@link \App\Contracts\Services\LastModified\LastModifiedService} service.
 *
 * @author    Brandon Clothier <brandon14125@gmail.com>
 *
 * @version   1.0.0
 *
 * @license   MIT
 * @copyright 2018
 */
class LastModifiedOptions
{
    /**
     * Whether to cache the timestamp or not.
     *
     * @var bool
     */
    protected $isCacheEnabled;

    /**
     * How long to cache the last modified timestamp for.
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
     * Default timestamp format.
     *
     * @var string
     */
    protected $timestampFormat;

    /**
     * Constructs a new set of {@link \App\Contracts\Services\LastModified\LastModifiedService} options.
     *
     * @SuppressWarnings("BooleanArgumentFlag")
     *
     * @param bool   $isCacheEnabled
     * @param int    $cacheTtl
     * @param string $cacheKey
     * @param string $timestampFormat
     *
     * @throws \InvalidArgumentException
     *
     * @return void
     */
    public function __construct(
        bool $isCacheEnabled = true,
        int $cacheTtl = 30,
        string $cacheKey = 'last_modified',
        string $timestampFormat = 'F jS, Y \a\t h:i:s A T'
    ) {
        $this->isCacheEnabled = $isCacheEnabled;
        $this->cacheTtl = $cacheTtl;
        $this->cacheKey = $cacheKey;
        $this->timestampFormat = $timestampFormat;

        if (!date($this->timestampFormat)) {
            throw new InvalidArgumentException("Invalid default timestamp format [{$this->timestampFormat}] provided.");
        }
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

    /**
     * Get timestamp format option.
     *
     * @return string
     */
    public function getTimestampFormat(): string
    {
        return $this->timestampFormat;
    }
}
