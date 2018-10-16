<?php

namespace Maximus;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\RouteCollectionBuilder;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    const CONFIG_EXTS = '.{php,xml,yaml,yml}';

    public function getCacheDir()
    {
        return $this->getProjectDir().'/var/cache/'.$this->environment;
    }

    public function getLogDir()
    {
        return $this->getProjectDir().'/var/log';
    }

    public function registerBundles()
    {
        $contents = require $this->getProjectDir().'/config/bundles.php';
        foreach ($contents as $class => $envs) {
            if (isset($envs['all']) || isset($envs[$this->environment])) {
                yield new $class();
            }
        }
    }

    protected function configureContainer(ContainerBuilder $container, LoaderInterface $loader)
    {
        $container->addResource(new FileResource($this->getProjectDir().'/config/bundles.php'));
        // Feel free to remove the "container.autowiring.strict_mode" parameter
        // if you are using symfony/dependency-injection 4.0+ as it's the default behavior
        $container->setParameter('container.autowiring.strict_mode', true);
        $container->setParameter('container.dumper.inline_class_loader', true);
        $confDir = $this->getProjectDir().'/config';

        $loader->load($confDir.'/{packages}/*'.self::CONFIG_EXTS, 'glob');
        $loader->load($confDir.'/{packages}/'.$this->environment.'/**/*'.self::CONFIG_EXTS, 'glob');
        $loader->load($confDir.'/{services}'.self::CONFIG_EXTS, 'glob');
        $loader->load($confDir.'/{services}_'.$this->environment.self::CONFIG_EXTS, 'glob');

        $this->configMaximus($container);
    }

    protected function configureRoutes(RouteCollectionBuilder $routes)
    {
        $confDir = $this->getProjectDir().'/config';

        $routes->import($confDir.'/{routes}/*'.self::CONFIG_EXTS, '/', 'glob');
        $routes->import($confDir.'/{routes}/'.$this->environment.'/**/*'.self::CONFIG_EXTS, '/', 'glob');
        $routes->import($confDir.'/{routes}'.self::CONFIG_EXTS, '/', 'glob');
    }

    private function configMaximus(ContainerBuilder $container)
    {
        $filePath = sprintf(
            '%s/themes_installed/%s/theme.json',
            $this->getProjectDir(),
            $container->getParameter('maximus.theme')
        );

        if (!file_exists($filePath)) {
            return;
        }

        $container->addResource(new FileResource($filePath));

        $config = new ParameterBag(@json_decode(file_get_contents($filePath), true));
        $variables = $config->get('variables');

        if ($container->hasParameter('maximus.theme_variables')) {
            $variables = $this->mergeThemeVariables($variables, $container->getParameter('maximus.theme_variables'));
        }

        $container->setParameter('maximus.menu', $this->getMaximusMenu($container, $config));
        $container->setParameter('maximus.theme_version', $config->get('version'));
        $container->setParameter('maximus.theme_variables', $variables);
    }

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

    private function getMaximusMenu(ContainerBuilder $container, ParameterBag $config)
    {
        $menu = $config->get('menu');

        if ($container->hasParameter('maximus.menu')) {
            $menu = $container->getParameter('maximus.menu');
        }

        // Default menu
        if (empty($menu)) {
            $menu = [
                ['route_name' => 'homepage', 'title' => 'Home'],
                ['route_name' => 'tags', 'title' => 'Tags'],
            ];
        }

        foreach ($menu as &$info) {
            if (!array_key_exists('route_params', $info) || !is_array($info['route_params'])) {
                $info['route_params'] = [];
            }
        }

        return $menu;
    }
}
