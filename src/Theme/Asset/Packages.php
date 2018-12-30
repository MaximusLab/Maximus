<?php

namespace Maximus\Theme\Asset;

use Maximus\Asset\VersionStrategy\ThemeVersionStrategy;
use Maximus\Setting\Settings;
use Symfony\Component\Asset\PackageInterface;
use Symfony\Component\Asset\Packages as BasePackages;
use Symfony\Component\Asset\PathPackage;

/**
 * Helps manage asset URLs.
 */
class Packages extends BasePackages
{
    /**
     * {@inheritdoc}
     *
     * @param Settings $settings
     */
    public function __construct(PackageInterface $defaultPackage = null, array $packages = array(), Settings $settings = null)
    {
        $packages['theme'] = new PathPackage(
            'theme/'.$settings->getTheme(),
            new ThemeVersionStrategy($settings)
        );

        parent::__construct($defaultPackage, $packages);
    }
}
