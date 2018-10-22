<?php
/*
 * This file is part of the Maximus package.
 *
 * (c) Leo <leo.on.the.earth@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Maximus\Twig;

/**
 * Class Breadcrumb
 *
 * @package Maximus\Twig
 */
class Breadcrumb
{
    /**
     * @var array
     */
    private $items = [];

    /**
     * Add a breadcrumb item
     *
     * @param string $title
     * @param string $link
     * @param bool $active
     * @param string $icon
     *
     * @return Breadcrumb
     */
    public function add($title, $link, $active = false, $icon = '')
    {
        $this->items[] = compact('title', 'link', 'active', 'icon');

        return $this;
    }

    /**
     * @return array
     */
    public function all()
    {
        return $this->items;
    }

    /**
     * Reset all breadcrumb items
     *
     * @return Breadcrumb
     */
    public function reset()
    {
        $this->items = [];

        return $this;
    }
}
