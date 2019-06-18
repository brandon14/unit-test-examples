<?php

/*
 * This file is part of the unit-test-examples package.
 *
 * Copyright 2018-2019 Brandon Clothier
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation
 * files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy,
 * modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software
 * is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
 * OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
 * LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR
 * IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 *
 */

namespace App\Services\LastModified\Providers;

use Iterator;
use function is_dir;
use DirectoryIterator;
use function is_array;
use function array_filter;
use InvalidArgumentException;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
use App\Contracts\Services\LastModified\LastModifiedTimeProvider;

/**
 * Filesystem last modified provider. Will iterate over a list opf directories
 * and get the most recently modified file time.
 *
 * @author    Brandon Clothier <brandon14125@gmail.com>
 *
 * @version   1.0.0
 *
 * @license   MIT
 * @copyright 2018
 */
class FilesystemLastModifiedTimeProvider implements LastModifiedTimeProvider
{
    /**
     * Base path to start the file traversal.
     *
     * @var string
     */
    protected $basePath;

    /**
     * List of directories to traverse to determine last modified file time.
     *
     * @var array
     */
    protected $includedDirectories;

    /**
     * Constructs a LastModified service object.
     *
     * @param array $config
     *
     * @throws \InvalidArgumentException
     *
     * @return void
     */
    public function __construct(array $config)
    {
        // Validate the base path.
        if (! isset($config['base_path']) || ! is_dir($config['base_path'])) {
            throw new InvalidArgumentException('You must provide a valid base path for this provider.');
        }

        $this->basePath = $config['base_path'];
        $this->includedDirectories = isset($config['included_directories']) && is_array($config['included_directories'])
            ? $config['included_directories']
            : [];
    }

    /**
     * {@inheritdoc}
     */
    public function getLastModifiedTime(): int
    {
        $basePathFiles = new DirectoryIterator($this->basePath);

        // Iterate over each file in the base directory.
        $timestamp = $this->findLastModifiedFileTime($basePathFiles);

        // Filter out invalid paths.
        $validPaths = array_filter($this->includedDirectories, 'is_dir');

        // Make sure we have some subdirectories to iterate through.
        if (count($validPaths) === 0) {
            return $timestamp;
        }

        // Iterate over each included directory recursively to find the last
        // modified timestamp.
        foreach ($this->includedDirectories as $directory) {
            $dir = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory));

            $subDirTimestamp = $this->findLastModifiedFileTime($dir);
            $timestamp = $subDirTimestamp > $timestamp ? $subDirTimestamp : $timestamp;
        }

        return $timestamp;
    }

    /**
     * Function to iterate over an array of files/directories and return
     * the greatest file modified time.
     *
     * @param \Iterator $files
     *
     * @return int
     */
    protected function findLastModifiedFileTime(Iterator $files): int
    {
        $timestamp = -1;

        foreach ($files as $file) {
            if (! $file->isDir()) {
                $mTime = $file->getMTime();
                $timestamp = $mTime > $timestamp ? $mTime : $timestamp;
            }
        }

        return $timestamp;
    }
}
