<?php declare(strict_types=1);

namespace SymfonyDocsBuilder;

use Doctrine\RST\Configuration;
use Doctrine\RST\Event\PostBuildRenderEvent;
use Doctrine\RST\Kernel;
use SymfonyDocsBuilder\Directive as SymfonyDirectives;
use SymfonyDocsBuilder\Listener\CopyImagesDirectoryListener;
use SymfonyDocsBuilder\Reference as SymfonyReferences;

/**
 * Class KernelFactory
 */
final class KernelFactory
{
    public static function createKernel(
        string $sourceDir,
        string $htmlOutputDir,
        string $parseOnlyPath = null
    ): Kernel {
        $configuration = new Configuration();
        $configuration->setCustomTemplateDirs([__DIR__.'/Templates']);
        $configuration->setCacheDir(__DIR__.'/../var/cache');
        $configuration->addFormat(
            new SymfonyHTMLFormat(
                $configuration->getTemplateRenderer(),
                $configuration->getFormat()
            )
        );

        if ($parseOnlyPath) {
            $configuration->setBaseUrl(
                sprintf(
                    SymfonyDocConfiguration::getSymfonyDocUrl(),
                    SymfonyDocConfiguration::getVersion()
                )
            );
            $configuration->setBaseUrlEnabledCallable(
                static function (string $path) use ($parseOnlyPath) : bool {
                    return strpos($path, $parseOnlyPath) !== 0;
                }
            );
        }

        $configuration->getEventManager()->addEventListener(
            PostBuildRenderEvent::POST_BUILD_RENDER,
            new CopyImagesDirectoryListener($sourceDir, $htmlOutputDir)
        );

        return new Kernel(
            $configuration,
            self::getDirectives(),
            self::getReferences()
        );
    }

    private static function getDirectives(): array
    {
        return [
            new SymfonyDirectives\AdmonitionDirective(),
            new SymfonyDirectives\CautionDirective(),
            new SymfonyDirectives\CodeBlockDirective(),
            new SymfonyDirectives\ConfigurationBlockDirective(),
            new SymfonyDirectives\IndexDirective(),
            new SymfonyDirectives\RoleDirective(),
            new SymfonyDirectives\NoteDirective(),
            new SymfonyDirectives\SeeAlsoDirective(),
            new SymfonyDirectives\SidebarDirective(),
            new SymfonyDirectives\TipDirective(),
            new SymfonyDirectives\TopicDirective(),
            new SymfonyDirectives\WarningDirective(),
            new SymfonyDirectives\VersionAddedDirective(),
            new SymfonyDirectives\BestPracticeDirective(),
        ];
    }

    private static function getReferences(): array
    {
        return [
            new SymfonyReferences\ClassReference(),
            new SymfonyReferences\MethodReference(),
            new SymfonyReferences\NamespaceReference(),
            new SymfonyReferences\PhpFunctionReference(),
            new SymfonyReferences\PhpMethodReference(),
            new SymfonyReferences\PhpClassReference(),
        ];
    }
}
