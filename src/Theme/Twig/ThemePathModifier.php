<?php

namespace Maximus\Theme\Twig;

use Twig\Loader\FilesystemLoader as BaseFilesystemLoader;

/**
 * Modify exists template paths for theme
 */
class ThemePathModifier extends BaseFilesystemLoader
{
    const PATH_NAMESPACE = 'theme';

    /**
     * Modify exists template paths for theme
     *
     * @param array $paths
     * @param string $themeName
     * @param string $projectDir
     */
    public static function modify(array &$paths, $themeName, $projectDir)
    {
        if (empty($projectDir)) {
            return;
        }

        $paths[$projectDir.'/themes_custom/'.$themeName] = self::PATH_NAMESPACE;
        $paths[$projectDir.'/themes/'.$themeName] = self::PATH_NAMESPACE;
    }
}
