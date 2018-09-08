<?php

namespace SymfonyCasts\Tests;

use Doctrine\RST\Builder;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;
use SymfonyDocs\HtmlKernel;

class IntegrationTest extends TestCase
{
    public function testIntegration()
    {
        $kernel = new HtmlKernel();
        $builder = new Builder($kernel);
        $fs = new Filesystem();
        $fs->remove(__DIR__.'/_output');

        $builder->build(
            __DIR__.'/fixtures/source',
            __DIR__.'/_output',
            true // verbose
        );
    }
}