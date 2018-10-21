<?php
/*
 * This file is part of the Maximus package.
 *
 * (c) Leo <leo.on.the.earth@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Maximus\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\MenuItem;

/**
 * Class Builder
 *
 * @package Maximus\Menu
 */
class Builder
{
    /**
     * @var FactoryInterface
     */
    private $factory;

    /**
     * @param FactoryInterface $factory
     */
    public function __construct(FactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    /**
     * @return \Knp\Menu\ItemInterface
     */
    public function consoleMenu()
    {
        $menu = $this->factory->createItem('root');

        $menu->addChild('Articles', ['route' => 'console_index']);
        $menu->addChild('Tags', ['route' => 'console_tag_index']);
        $menu->addChild('Authors', ['route' => 'console_author_index']);
        $menu->addChild('Settings', ['route' => 'console_setting_index']);

        $menu->setChildrenAttribute('class', 'navbar-nav ml-auto');

        /** @var MenuItem $child */
        foreach ($menu as $child) {
            $child->setLinkAttribute('class', 'nav-link')
                ->setAttribute('class', 'nav-item ');
        }

        return $menu;
    }
}
