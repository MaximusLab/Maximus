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

use Maximus\Setting\Settings;
use Symfony\Component\Asset\VersionStrategy\VersionStrategyInterface;

/**
 * Class ThemeVersionStrategy
 */
class ThemeVersionStrategy implements VersionStrategyInterface
{
    /**
     * @var Settings
     */
    private $settings;

    /**
     * MaximusVersionStrategy constructor.
     *
     * @param Settings $settings
     */
    public function __construct(Settings $settings)
    {
        $this->settings = $settings;
    }

    /**
     * {@inheritdoc}
     */
    public function getVersion($path)
    {
        return $this->settings->getThemeVersion();
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

        $versionized = sprintf('%s?v%s', ltrim($path, '/'), $version);

        if ($path && '/' === $path[0]) {
            return '/'.$versionized;
        }

        return $versionized;
    }
}
