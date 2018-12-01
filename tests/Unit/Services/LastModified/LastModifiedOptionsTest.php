<?php

declare(strict_types=1);

namespace Tests\Unit\Services\LastModified;

use App\Contracts\Services\LastModified\LastModifiedOptions;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * LastModifiedOptions unit tests.
 *
 * @author    Brandon Clothier <brandon14125@gmail.com>
 *
 * @version   1.0.0
 *
 * @license   MIT
 * @copyright 2018
 */
class LastModifiedOptionsTest extends TestCase
{
    /**
     * Test that {@link \App\Contracts\Services\LastModified\LastModifiedOptions} validates a valid
     * timestamp format has been provided.
     *
     * @return void
     */
    public function testThrowsInvalidArgumentExceptionWhenInvalidDateFormatIsProvided(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new LastModifiedOptions(
            true,
            30,
            'last_modified',
            ''
        );
    }

    /**
     * Test that {@link \App\Contracts\Services\LastModified\LastModifiedOptions} validates a valid
     * timestamp format has been provided.
     *
     * @return void
     */
    public function testDoesNotThrowsInvalidArgumentExceptionWhenValidDateFormatIsProvided(): void
    {
        new LastModifiedOptions(
            true,
            30,
            'last_modified',
            'F jS, Y \a\t h:i:s A T'
        );

        $this::assertTrue(true);
    }
}
