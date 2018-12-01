<?php

declare(strict_types=1);

namespace Tests\Unit\Services\LastModified;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use App\Contracts\Services\LastModified\LastModifiedOptions;

/**
 * LastModifiedOptions unit tests.
 *
 * The only real logic this class performs is in validating the timestamp format, so that
 * is really the only thing that needs to be tested.
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
