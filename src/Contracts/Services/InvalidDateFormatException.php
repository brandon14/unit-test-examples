<?php

/**
 * This file is part of the brandon14/unit-test-examples package.
 *
 *             _ _       _          _                               _
 *   _  _ _ _ (_) |_ ___| |_ ___ __| |_ ___ _____ ____ _ _ __  _ __| |___ ___
 *  | || | ' \| |  _|___|  _/ -_|_-<  _|___/ -_) \ / _` | '  \| '_ \ / -_|_-<
 *   \_,_|_||_|_|\__|    \__\___/__/\__|   \___/_\_\__,_|_|_|_| .__/_\___/__/
 *                                                            |_|
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

namespace App\Contracts\Services;

use Throwable;
use InvalidArgumentException;

/**
 * Class InvalidDateFormatException.
 *
 * Exception thrown when an invalid date format is provided.
 *
 * @author Brandon Clothier <brandon14125@gmail.com>
 */
class InvalidDateFormatException extends InvalidArgumentException
{
    /**
     * Creates a new exception when an invlaid date format is encountered.
     *
     * @param string     $foramt   Date format provided
     * @param int        $code     Error code
     * @param \Throwable $previous Previous exception
     */
    public static function invalidFormat(string $format, int $code = 0, ?Throwable $previous = null): self
    {
        return new self("Invalid default timestamp format [{$format}] provided.", $code, $previous);
    }
}
