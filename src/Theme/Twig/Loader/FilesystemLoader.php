<?php

namespace Maximus\Theme\Twig\Loader;

use Maximus\Setting\Settings;
use Maximus\Theme\Twig\ThemePathModifier;
use Twig\Loader\FilesystemLoader as BaseFilesystemLoader;

/**
 * Loads Maximus theme templates from the filesystem.
 */
class FilesystemLoader extends BaseFilesystemLoader
{
    /**
     * {@inheritdoc}
     *
     * @param Settings $settings
     *
     * @throws \Twig_Error_Loader
     */
    public function __construct($paths = [], $rootPath = null, $projectDir = null, Settings $settings = null)
    {
        parent::__construct($paths, $rootPath);

        $themePaths = [];
        ThemePathModifier::modify($themePaths, $settings->getTheme(), $projectDir);
        foreach ($themePaths as $path => $namespace) {
            $this->addPath($path, $namespace);
        }
    }
}
