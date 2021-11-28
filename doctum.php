<?php

/**
 * This file is part of the brandon14/unit-test-examples package.
 *
 * MIT License
 *             _ _       _          _                               _
 *   _  _ _ _ (_) |_ ___| |_ ___ __| |_ ___ _____ ____ _ _ __  _ __| |___ ___
 *  | || | ' \| |  _|___|  _/ -_|_-<  _|___/ -_) \ / _` | '  \| '_ \ / -_|_-<
 *   \_,_|_||_|_|\__|    \__\___/__/\__|   \___/_\_\__,_|_|_|_| .__/_\___/__/
 *                                                            |_|
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

use Doctum\Doctum;
use Symfony\Component\Finder\Finder;
use Doctum\RemoteRepository\GitHubRemoteRepository;

$dir = __DIR__.'/src';
$iterator = Finder::create()
    ->files()
    ->name('*.php')
    ->in($dir);

return new Doctum($iterator, [
    'title'             => 'brandon14/unit-test-examples Documentation',
    'source_dir'        => dirname($dir).'/',
    'remote_repository' => new GitHubRemoteRepository('brandon14/unit-test-examples', dirname($dir)),
    'build_dir'         => __DIR__.'/docs',
    'cache_dir'         => __DIR__.'/doctum_cache',
    'footer_link'       => [
        'href'        => 'https://github.com/brandon14/unit-test-examples',
        'rel'         => 'noreferrer noopener',
        'target'      => '_blank',
        'before_text' => 'You can edit the configuration',
        'link_text'   => 'on this', // Required if the href key is set
        'after_text'  => 'repository',
    ],
]);
