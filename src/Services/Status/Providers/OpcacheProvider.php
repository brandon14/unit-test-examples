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

namespace App\Services\Status\Providers;

use function function_exists;
use App\Contracts\Services\Status\StatusServiceProvider;

/**
 * Class OpcacheProvider.
 *
 * PHP Opcache status provider. Class will check to see if opcache is enabled, and if so return the
 * status array from opcache.
 *
 * **NOTE:**
 * We are not testing the functionality of this class. Sometimes it is hard to test internal functions,
 * and since this is a core PHP function so long as opcache is enabled, we check that opcache status is
 * available, and if so we get the opcache status array. According to the PHP docs, that either returns
 * false on failure, or an array of the status details, so if it returns false, we return a status of
 * an error, otherwise we return the opcache status array. If opcache isn't present, we return that it
 * is disabled.
 *
 * @author Brandon Clothier <brandon14125@gmail.com>
 */
class OpcacheProvider implements StatusServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function getStatus(): array
    {
        // @codeCoverageIgnoreStart
        // It's hard to mock internal PHP functions. I mean its opcache_get_status, should
        // either return an array of the status, or false on failure.
        if (function_exists('opcache_get_status')) {
            $status = \opcache_get_status(false);

            return $status === false
                ? ['status' => StatusServiceProvider::STATUS_ERROR]
                : $status;
        }

        return ['status' => StatusServiceProvider::STATUS_DISABLED];
        // @codeCoverageIgnoreEnd
    }
}
