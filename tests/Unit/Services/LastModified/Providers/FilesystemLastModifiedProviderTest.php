<?php

declare(strict_types=1);

namespace Tests\Unit\Services\LastModified\Providers;

use App\Services\LastModified\Providers\FilesystemLastModifiedTimeProvider;
use Carbon\Carbon;
use InvalidArgumentException;
use org\bovigo\vfs\vfsStream as VfsStream;
use PHPUnit\Framework\TestCase;

/**
 * Filesystem last modified provider unit tests.
 *
 * What is important to note about the test for this class, is we don't rely on any external service. The filesystem
 * that this class relies on is mocked away using a package called vfs that allows for creating a virtual in-memory
 * filesystem. If our tests were to rely on an actual filesystem, the tests would be much more brittle because it would
 * have to cross the boundary of the application in order to access the filesystem.
 *
 * @author    Brandon Clothier <brandon14125@gmail.com>
 *
 * @version   1.0.0
 *
 * @license   MIT
 * @copyright 2018
 */
class FilesystemLastModifiedProviderTest extends TestCase
{
    /**
     * Test that if provided no base path config option, the class will throw an
     * {@link \InvalidArgumentException}.
     *
     * @return void
     */
    public function testThrowsInvalidArgumentExceptionForInvalidBasePathNoBasePath(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new FilesystemLastModifiedTimeProvider([]);
    }

    /**
     * Test that if provided and invalid base path (i.e. non-existent directory) the
     * class will throw an {@link \InvalidArgumentException}.
     *
     * @return void
     */
    public function testThrowsInvalidArgumentExceptionForInvalidBasePath(): void
    {
        $this->expectException(InvalidArgumentException::class);

        // Set up empty mock filesystem.
        VfsStream::setup('root');

        new FilesystemLastModifiedTimeProvider(['base_path' => '/foo']);
    }

    /**
     * Assert that the service will get the timestamp from the cache if it is
     * present.
     *
     * @return void
     */
    public function testGetsTimestampFromFilesystem(): void
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

        $baseDir = VfsStream::url($fs->path('root'));

        $instance = new FilesystemLastModifiedTimeProvider(
            [
                'base_path'            => $baseDir,
                'included_directories' => [
                    $baseDir.'/tests',
                    $baseDir.'/app',
                ],
            ]
        );

        // Call getLastModifiedTime to get the last modified file time.
        $lastModifiedCall = $instance->getLastModifiedTime();

        // Assert the timestamp returned is our most "last modified file".
        $this::assertEquals($lastModified->timestamp, $lastModifiedCall);
    }

    /**
     * Test that if no files are found, the provider will return -1.
     *
     * @return void
     */
    public function testReturnsNegativeOneWithNoFiles(): void
    {
        // Set up empty filesystem.
        $fs = VfsStream::setup('root');

        $baseDir = VfsStream::url($fs->path('root'));

        $instance = new FilesystemLastModifiedTimeProvider(
            [
                'base_path' => $baseDir,
            ]
        );

        // Call getLastModifiedTime to get the last modified file time.
        $lastModifiedCall = $instance->getLastModifiedTime();

        // Assert the timestamp returned is -1 since we have no files.
        $this::assertEquals(-1, $lastModifiedCall);
    }

    /**
     * Test that if provided no directories to recurse through, it will still find the last
     * modified time in the base directory.
     *
     * @return void
     */
    public function testReturnsLastModifiedTimeWithNoRecursiveDirectories(): void
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

        $baseDir = VfsStream::url($fs->path('root'));

        $instance = new FilesystemLastModifiedTimeProvider(
            [
                'base_path'            => $baseDir,
                'included_directories' => [
                    $baseDir.'/tests',
                    $baseDir.'/app',
                ],
            ]
        );

        // Call getLastModifiedTime to get the last modified file time.
        $lastModifiedCall = $instance->getLastModifiedTime();

        // Assert the timestamp returned is our most "last modified file".
        $this::assertEquals($lastModified->timestamp, $lastModifiedCall);
    }
}
