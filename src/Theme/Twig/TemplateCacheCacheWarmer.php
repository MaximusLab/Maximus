<?php

namespace Maximus\Theme\Twig;

use Maximus\Setting\Settings;
use Psr\Container\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\CacheWarmer\TemplateFinderInterface;
use Symfony\Bundle\TwigBundle\CacheWarmer\TemplateCacheCacheWarmer as BaseTemplateCacheCacheWarmer;

/**
 * Class TemplateCacheCacheWarmer
 */
class TemplateCacheCacheWarmer extends BaseTemplateCacheCacheWarmer
{
    /**
     * {@inheritdoc}
     *
     * @param string   $projectDir
     * @param Settings $settings
     */
    public function __construct(ContainerInterface $container, TemplateFinderInterface $finder = null, array $paths = array(), $projectDir = null, Settings $settings = null)
    {
        ThemePathModifier::modify($paths, $settings->getTheme(), $projectDir);

        parent::__construct($container, $finder, $paths);
    }
}
