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

namespace Tests\Unit\Services\LastModified\Providers;

use Carbon\Carbon;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use org\bovigo\vfs\vfsStream as VfsStream;
use App\Services\LastModified\Providers\FilesystemLastModifiedTimeProvider;

/**
 * Class FilesystemLastModifiedProviderTest.
 *
 * Filesystem last modified provider unit tests.
 *
 * What is important to note about the test for this class, is we don't rely on any external service. The filesystem
 * that this class relies on is mocked away using a package called vfs that allows for creating a virtual in-memory
 * filesystem. If our tests were to rely on an actual filesystem, the tests would be much more brittle because it would
 * have to cross the boundary of the application in order to access the filesystem.
 *
 * @author Brandon Clothier <brandon14125@gmail.com>
 */
class FilesystemLastModifiedProviderTest extends TestCase
{
    /**
     * Set up Carbon mock time.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Set a mock time for our tests.
        Carbon::setTestNow(Carbon::create(2001, 5, 15));
    }

    /**
     * Test that if provided an invalid base path (i.e. non-existent directory) the
     * class will throw an {@link \InvalidArgumentException}.
     */
    final public function testThrowsInvalidArgumentExceptionForInvalidBasePath(): void
    {
        $this->expectException(InvalidArgumentException::class);

        // Set up empty mock filesystem.
        VfsStream::setup('root');

        new FilesystemLastModifiedTimeProvider('/foo');
    }

    /**
     * Test that if provided an invalid included directory (i.e. non-existent directory) the
     * class will throw an {@link \InvalidArgumentException}.
     */
    final public function testThrowsInvalidArgumentExceptionForInvalidIncludedDIrectories(): void
    {
        $this->expectException(InvalidArgumentException::class);

        // Set up empty mock filesystem.
        VfsStream::setup('root');

        new FilesystemLastModifiedTimeProvider('/', ['/bar', '/baz']);
    }

    /**
     * Assert that the service will get the timestamp from the filesystem if it is
     * present.
     */
    final public function testGetsTimestampFromFilesystem(): void
    {
        // Set up virtual mocked filesystem
        $fs = VfsStream::setup('root');

        $directoryTests = VfsStream::newDirectory('tests');
        $directoryApp = VfsStream::newDirectory('app');
        $directoryExample = VfsStream::newDirectory('exampleFolder');

        $testFileOne = VfsStream::newFile('someTest.php', 644)->withContent('<?php echo "this is a test.";');
        $testFileTwo = VfsStream::newFile('this_is_a_test.txt', 644)->withContent('Some text here I think.');
        $testFileThree = VfsStream::newFile('anotherFile.php', 644)->withContent('<?php echo "Hello world!";');

        $directoryTests->addChild($testFileOne);
        $directoryApp->addChild($testFileTwo);
        $directoryExample->addChild($testFileThree);
        $directoryApp->addChild($directoryExample);

        $fs->addChild($directoryTests);
        $fs->addChild($directoryApp);

        /**
         * Directory structure looks like this:.
         *
         * - tests
         *   - someTest.php
         * - app
         *   - exampleFolder
         *     - anotherFile.php
         *   - this_is_a_test.txt
         */

        // This will be our fixed last modified timestamp.
        $lastModified = Carbon::now();
        // Give the other test file a previous timestamp.
        $previousTime = Carbon::now()->subDay(1);
        $anotherPrevious = Carbon::now()->subDay(2);

        // Set the file timestamps.
        $testFileOne->lastModified($lastModified->timestamp);
        $testFileTwo->lastModified($previousTime->timestamp);
        $testFileThree->lastModified($anotherPrevious->timestamp);

        $baseDir = VfsStream::url($fs->path());

        $instance = new FilesystemLastModifiedTimeProvider($baseDir, [$baseDir.'/tests', $baseDir.'/app']);

        // Call getLastModifiedTime to get the last modified file time.
        $lastModifiedCall = $instance->getLastModifiedTime();

        // Assert the timestamp returned is our most "last modified file".
        $this::assertSame($lastModified->timestamp, $lastModifiedCall);
    }

    /**
     * Test that if no files are found, the provider will return -1.
     */
    final public function testReturnsNegativeOneWithNoFiles(): void
    {
        // Set up empty filesystem.
        $fs = VfsStream::setup('root');

        $baseDir = VfsStream::url($fs->path());

        $instance = new FilesystemLastModifiedTimeProvider($baseDir);

        // Call getLastModifiedTime to get the last modified file time.
        $lastModifiedCall = $instance->getLastModifiedTime();

        // Assert the timestamp returned is -1 since we have no files.
        $this::assertSame(-1, $lastModifiedCall);
    }

    /**
     * Test that if provided no directories to recurse through, it will still find the last
     * modified time in the base directory.
     */
    final public function testReturnsLastModifiedTimeWithNoRecursiveDirectories(): void
    {
        // Set up virtual mocked filesystem
        $fs = VfsStream::setup('root');

        $testFileOne = VfsStream::newFile('someTest.php', 644)->withContent('<?php echo "this is a test.";');
        $testFileTwo = VfsStream::newFile('this_is_a_test.txt', 644)->withContent('Some text here I think.');

        $fs->addChild($testFileOne);
        $fs->addChild($testFileTwo);

        /**
         * Filesystem looks like this:
         * - someTest.php
         * - this_is_a_test.txt.
         */

        // This will be our fixed last modified timestamp.
        $lastModified = Carbon::now();
        // Give the other test file a previous timestamp.
        $previousTime = Carbon::now()->subDay(1);

        $testFileOne->lastModified($lastModified->timestamp);
        $testFileTwo->lastModified($previousTime->timestamp);

        $baseDir = VfsStream::url($fs->path());

        $instance = new FilesystemLastModifiedTimeProvider($baseDir);

        // Call getLastModifiedTime to get the last modified file time.
        $lastModifiedCall = $instance->getLastModifiedTime();

        // Assert the timestamp returned is our most "last modified file".
        $this::assertSame($lastModified->timestamp, $lastModifiedCall);
    }
}
