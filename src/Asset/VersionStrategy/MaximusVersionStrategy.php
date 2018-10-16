<?php
/*
 * This file is part of the Maximus package.
 *
 * (c) Leo <leo.on.the.earth@gmail.com>
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
     * @var string Theme asset file version
     */
    private $version;

    /**
     * MaximusVersionStrategy constructor.
     *
     * @param string $theme   The theme name
     * @param string $version The theme version
     */
    public function __construct($theme, $version)
    {
        $this->theme = $theme;
        $this->version = $version;
    }

    /**
     * {@inheritdoc}
     */
    public function getVersion($path)
    {
        if (0 === strpos(ltrim($path, '/ '), 'theme/'.$this->theme)) {
            return $this->version;
        }

        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function applyVersion($path)
    {
        if ('' === $this->version) {
            return $path;
        }

        $versionized = sprintf('%s?%s', ltrim($path, '/'), $this->version);

        if ($path && '/' === $path[0]) {
            return '/'.$versionized;
        }

        return $versionized;
    }
}
