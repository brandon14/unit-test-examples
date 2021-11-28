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

namespace Tests\Unit\Services\LastModified;

use PHPUnit\Framework\TestCase;
use App\Contracts\Services\InvalidDateFormatException;
use App\Contracts\Services\LastModified\LastModifiedOptions;

/**
 * Class LastModifiedOptionsTest.
 *
 * LastModifiedOptions unit tests.
 *
 * The only real logic this class performs is in validating the timestamp format, so that
 * is really the only thing that needs to be tested.
 *
 * @author Brandon Clothier <brandon14125@gmail.com>
 */
class LastModifiedOptionsTest extends TestCase
{
    /**
     * Test that {@link \App\Contracts\Services\LastModified\LastModifiedOptions} validates a valid
     * timestamp format has been provided.
     */
    final public function testThrowsInvalidArgumentExceptionWhenInvalidDateFormatIsProvided(): void
    {
        $this->expectException(InvalidDateFormatException::class);

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
     */
    final public function testDoesNotThrowsInvalidArgumentExceptionWhenValidDateFormatIsProvided(): void
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
