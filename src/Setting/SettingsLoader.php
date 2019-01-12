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

use Maximus\Repository\SettingRepository;
use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * Maximus settings loader
 */
class SettingsLoader
{
    /**
     * @var string
     */
    private $projectDir;
    /**
     * @var SettingRepository
     */
    private $settingsRepo;

    /**
     * Settings constructor.
     *
     * @param string $projectDir
     * @param SettingRepository $settingsRepo
     */
    public function __construct($projectDir, SettingRepository $settingsRepo)
    {
        $this->projectDir = $projectDir;
        $this->settingsRepo = $settingsRepo;
    }

    /**
     * Load Maximus settings
     *
     * @return Settings
     */
    public function load()
    {
        static $settings = null;

        if ($settings instanceof Settings) {
            return $settings;
        }

        $settings = $this->settingsRepo->getSettings();

        // Load theme configuration file, and overwrite with configuration in database.
        $filePath = sprintf(
            '%s/themes_installed/%s/theme.json',
            $this->projectDir,
            $settings->getTheme()
        );

        if (file_exists($filePath)) {
            $config = new ParameterBag(@json_decode(file_get_contents($filePath), true));
            $variables = (array) $config->get('variables', []);
            $variables = $this->mergeThemeVariables($variables, $settings->getThemeVariables());
            $menus = (array) $config->get('menus', []);

            foreach ($menus as &$menu) {
                if (!isset($menu['route_params'])) {
                    $menu['route_params'] = [];
                }

                $menu['route_params'] = (array) $menu['route_params'];
            }

            $menus = $this->mergeThemeMenus($settings->getThemeMenus(), $menus);

            $settings->setThemeVariables($variables);
            $settings->setThemeVersion($config->get('version'));
            $settings->setThemeMenus($menus);
        }

        // Setup default value for "upload base path"
        if (empty($settings->getUploadBasePath())) {
            $settings->setUploadBasePath($this->projectDir.'/public/upload');
        }

        return $settings;
    }

    /**
     * Load Maximus Theme variables
     *
     * @return array
     */
    public function loadThemeVariables()
    {
        $settings = $this->load();

        return $settings->getThemeVariables();
    }

    /**
     * Load Maximus theme menus
     *
     * @return array
     */
    public function loadThemeMenus()
    {
        $settings = $this->load();

        return $settings->getThemeMenus();
    }

    /**
     * Merge theme variables
     *
     * @param array $config1
     * @param array $config2
     *
     * @return array
     */
    private function mergeThemeVariables(array $config1 = [], array $config2 = [])
    {
        foreach ($config2 as $key => $value) {
            if (is_array($value) && array_key_exists($key, $config1) && is_array($config1[$key])) {
                $config1[$key] = $this->mergeThemeVariables($config1[$key], $value);
                continue;
            }

            $config1[$key] = $value;
        }

        return $config1;
    }

    /**
     * Merge theme variables
     *
     * @param array $menu1
     * @param array $menu2
     *
     * @return array
     */
    private function mergeThemeMenus(array $menu1 = [], array $menu2 = [])
    {
        $menu1 = array_combine(array_column($menu1, 'route_name'), $menu1);
        $menu2 = array_combine(array_column($menu2, 'route_name'), $menu2);

        foreach ($menu2 as $routeName => $menu) {
            // Don't overwrite the original menu from theme.json
            $menu1[$routeName] = $menu2[$routeName];
        }

        return array_values($menu1);
    }
}
