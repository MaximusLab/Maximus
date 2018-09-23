<?php
/*
 * This file is part of the Maximus package.
 *
 * (c) Leo Tsun <leo.on.the.earth@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Maximus\Asset\VersionStrategy;

use Symfony\Component\Asset\VersionStrategy\VersionStrategyInterface;

class MaximusVersionStrategy implements VersionStrategyInterface
{
    /**
     * @var string The theme name
     */
    private $theme;

    /**
     * @var string The directory path that contains all theme files
     */
    private $themeDir;

    /**
     * @var string Theme asset file version
     */
    private $themeVersion;

    /**
     * MaximusVersionStrategy constructor.
     *
     * @param string $theme    The theme name
     * @param string $themeDir The directory path that contains all theme files
     */
    public function __construct($theme, $themeDir)
    {
        $this->theme = $theme;
        $this->themeDir = $themeDir;
        $this->themeVersion = $this->getThemeVersion();
    }

    /**
     * {@inheritdoc}
     */
    public function getVersion($path)
    {
        if (0 === strpos(ltrim($path, '/ '), 'theme/'.$this->theme)) {
            return $this->themeVersion;
        }

        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function applyVersion($path)
    {
        $version = $this->getVersion($path);

        if ('' === $version) {
            return $path;
        }

        $versionized = sprintf('%s?%s', ltrim($path, '/'), $version);

        if ($path && '/' === $path[0]) {
            return '/'.$versionized;
        }

        return $versionized;
    }

    /**
     * @return string
     */
    private function getThemeVersion()
    {
        $configFilePath = $this->themeDir.'/theme.json';

        if (!file_exists($configFilePath)) {
            return '';
        }

        $config = @json_decode(file_get_contents($configFilePath), true);

        if (is_array($config) && !empty($config['version'])) {
            return $config['version'];
        }

        return '';
    }
}
