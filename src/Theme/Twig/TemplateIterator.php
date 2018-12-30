<?php

namespace Maximus\Theme\Twig;

use Maximus\Setting\Settings;
use Symfony\Bundle\TwigBundle\TemplateIterator as BaseTemplateIterator;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Class TemplateIterator
 */
class TemplateIterator extends BaseTemplateIterator
{
    /**
     * {@inheritdoc}
     */
    public function __construct(KernelInterface $kernel, string $rootDir, array $paths = array(), string $defaultPath = null, $projectDir = null, Settings $settings = null)
    {
        ThemePathModifier::modify($paths, $settings->getTheme(), $projectDir);

        parent::__construct($kernel, $rootDir, $paths, $defaultPath);
    }
}
