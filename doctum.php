<?php

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
    'source_dir'        => dirname($dir) . '/',
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
