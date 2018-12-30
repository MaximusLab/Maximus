<?php
/*
 * This file is part of the Maximus package.
 *
 * (c) Leo <leo.on.the.earth@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Maximus\Setting;

use Maximus\Validator\Constraints as MaximusAssert;

/**
 * Maximus settings
 */
class Settings
{
    /**
     * Theme name
     *
     * @var string
     */
    private $theme = 'default';

    /**
     * Theme version
     *
     * @var string
     */
    private $themeVersion = '';

    /**
     * Theme variables
     *
     * @MaximusAssert\Json
     *
     * @var array
     */
    private $themeVariables = [];

    /**
     * Menu settings
     *
     * For example:
     *
     * <code><pre>
     * [
     *     {"route_name": "homepage", "title": "Home"},
     *     {"route_name": "tags", "title": "Tags"},
     *     {"route_name": "custom_page", "route_params": {"viewName": "author"}, "title": "About Me"}
     * ]
     * </pre></code>
     *
     * @MaximusAssert\Json
     *
     * @var array
     */
    private $themeMenus = [];

    /**
     * Upload base path
     *
     * @var string
     */
    private $uploadBasePath = '';

    /**
     * Google Analytics ID
     *
     * @var string
     */
    private $gaTrackingId = '';

    /**
     * Google Analytics scripts (javascript)
     *
     * @var string
     */
    private $gaTrackingScripts = '';

    /**
     * Disqus short name
     *
     * @var string
     */
    private $disqusShortName = '';

    /**
     * Settings constructor.
     *
     * @param array $settings
     */
    public function __construct(array $settings)
    {
        foreach ($settings as $key => $value) {
            $method = 'set'.ucfirst($key);

            if (method_exists($this, $method)) {
                $this->$method($value);
            }
        }
    }

    /**
     * Get all settings
     *
     * @return array
     */
    public function all()
    {
        return get_object_vars($this);
    }

    /**
     * @return string
     */
    public function getTheme()
    {
        return $this->theme;
    }

    /**
     * @param string $theme
     *
     * @return Settings
     */
    public function setTheme(string $theme)
    {
        $this->theme = $theme;

        return $this;
    }

    /**
     * @return string
     */
    public function getThemeVersion()
    {
        return $this->themeVersion;
    }

    /**
     * @param string $themeVersion
     *
     * @return Settings
     */
    public function setThemeVersion(string $themeVersion)
    {
        $this->themeVersion = $themeVersion;

        return $this;
    }

    /**
     * @return array
     */
    public function getThemeVariables()
    {
        return $this->themeVariables;
    }

    /**
     * @param array $themeVariables
     *
     * @return Settings
     */
    public function setThemeVariables(array $themeVariables)
    {
        $this->themeVariables = $themeVariables;

        return $this;
    }

    /**
     * @return string
     */
    public function getUploadBasePath()
    {
        return $this->uploadBasePath;
    }

    /**
     * @return array
     */
    public function getThemeMenus()
    {
        return $this->themeMenus;
    }

    /**
     * @param array $themeMenus
     *
     * @return Settings
     */
    public function setThemeMenus(array $themeMenus)
    {
        $this->themeMenus = [];

        foreach ($themeMenus as $menu) {
            if (!isset($menu['route_params'])) {
                $menu['route_params'] = [];
            }

            $this->themeMenus[] = $menu;
        }

        return $this;
    }

    /**
     * @param string $uploadBasePath
     *
     * @return Settings
     */
    public function setUploadBasePath($uploadBasePath)
    {
        $this->uploadBasePath = $uploadBasePath;

        return $this;
    }

    /**
     * @return string
     */
    public function getGaTrackingId()
    {
        return $this->gaTrackingId;
    }

    /**
     * @param string $gaTrackingId
     *
     * @return Settings
     */
    public function setGaTrackingId(string $gaTrackingId = null)
    {
        $this->gaTrackingId = $gaTrackingId;

        return $this;
    }

    /**
     * @return string
     */
    public function getGaTrackingScripts()
    {
        return $this->gaTrackingScripts;
    }

    /**
     * @param string $gaTrackingScripts
     *
     * @return Settings
     */
    public function setGaTrackingScripts(string $gaTrackingScripts = null)
    {
        $this->gaTrackingScripts = $gaTrackingScripts;

        return $this;
    }

    /**
     * @return string
     */
    public function getDisqusShortName()
    {
        return $this->disqusShortName;
    }

    /**
     * @param string $disqusShortName
     *
     * @return Settings
     */
    public function setDisqusShortName(string $disqusShortName = null)
    {
        $this->disqusShortName = $disqusShortName;

        return $this;
    }
}
