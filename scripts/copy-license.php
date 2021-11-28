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

use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Simple script to add project license markdown into the generated packages licenses markdown file.
 *
 * @author Brandon Clothier <brandon14125@gmail.com>
 */
require_once __DIR__.'/../vendor/autoload.php';

$output = new ConsoleOutput(OutputInterface::VERBOSITY_NORMAL, true);

$output->writeln('');
$output->writeln('<info>Getting project license file...</info>');

$validLicenseNames = [
    'LICENSE',
    'LICENSE.md',
    'license',
    'license.md',
];
$validNames = implode(', ', $validLicenseNames);

$licenseName = null;

foreach ($validLicenseNames as $name) {
    if (file_exists(__DIR__."/../{$name}")) {
        $licenseName = __DIR__."/../{$name}";

        break;
    }
}

if ($licenseName === null) {
    $output->writeln(
        "<error>Unable to locate a valid license file in the root of the project. Valid names are [{$validNames}].</error>"
    );

    exit(1);
}

$license = file_get_contents($licenseName);

if (! $license) {
    $output->writeln(
        "<error>Could not find the license file at [{$licenseName}].</error>"
    );

    exit(1);
}

try {
    $composerJson = json_decode(file_get_contents(__DIR__.'/../composer.json'), true, 512, JSON_THROW_ON_ERROR);
} catch (Throwable $throwable) {
    $output->writeln(
        "<error>Unable to parse `composer.json` file with JSON exception [{$throwable->getMessage()}].</error>"
    );

    exit(1);
}

$name = $composerJson['name'] ?? 'Not configured.';
$description = $composerJson['description'] ?? 'Not configured.';
$homePage = isset($composerJson['homepage']) ? "[{$name}]({$composerJson['homepage']})" : 'Not configured.';
$sourceLicense = 'Not configured.';

if (isset($composerJson['license'])) {
    $sourceLicense = is_array($composerJson['license'])
        ? implode(', ', $composerJson['license'])
        : $composerJson['license'];
}

$license = "### {$name}\n{$description}\nHomepage: {$homePage}\nLicenses Used: {$sourceLicense}\n\n".rtrim($license);

$licenses = file_get_contents(__DIR__.'/../licenses.md');

if ($licenses) {
    $licenses = trim(preg_replace('/#\sProject\sLicenses.*##\sDependencies/sm', '', $licenses));
}

// Wrap all URLs detected with markdown URLs.
$licenses = preg_replace_callback('/\b(?:https?:\/\/)(?:www\d?\.)?[-\w\/?\.=&\+]+\b/', static function (array $matches) {
    return "[{$matches[0]}]($matches[0])";
}, $licenses);

$licenses = '# Project Licenses

This file was custom generated using the [PHP Legal Licenses](https://github.com/Comcast/php-legal-licenses) utility and
some custom logic to add in the source license as well. It contains the name, version and commit sha, description,
homepage, and license information for every dependency in this project.

## Source

'.$license.(mb_strlen($licenses) > 0 ? "\n\n## Dependencies\n\n{$licenses}" : '');

$output->writeln('<info>Writing new `licenses.md` file...</info>');

$written = file_put_contents(__DIR__.'/../licenses.md', $licenses);

if (! $written) {
    $output->writeln('<error>Unable to write new `licenses.md` file. Please try again.</error>');

    exit(1);
}

$output->writeln('<info>Created new `licenses.md` file.</info>');

exit(0);
