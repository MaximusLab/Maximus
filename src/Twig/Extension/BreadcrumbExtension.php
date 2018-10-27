<?php
/*
 * This file is part of the Maximus package.
 *
 * (c) Leo <leo.on.the.earth@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Maximus\Twig\Extension;

use Maximus\Twig\Breadcrumb;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\Loader\ArrayLoader;
use Twig\TwigFunction;

class BreadcrumbExtension extends AbstractExtension
{
    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('breadcrumb', [$this, 'render'], ['is_safe' => ['html']]),
        ];
    }

    /**
     * Render breadcrumb
     *
     * @param array $items
     *
     * @return string
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function render(array $items = [])
    {
        $breadcrumb = new Breadcrumb();
        $template = <<<HTML
<nav aria-label="breadcrumb" id="breadcrumb-container">
    <div class="container">
        <ol class="breadcrumb">
        {% for item in breadcrumb.all %}
            <li class="breadcrumb-item {{ item.active ? 'active' : '' }}">
                {% if item.link is not empty %}<a href="{{ item.link }}">{% endif %}
                    {{ item.title }}
                {% if item.link is not empty %}</a>{% endif %}
            </li>
        {% endfor %}
        </ol>
    </div>
</nav>
HTML;
        foreach ($items as $item) {
            $active = !empty($item['active']);
            $icon = empty($item['icon']) ? '' : $item['icon'];

            $breadcrumb->add($item['title'], $item['link'], $active, $icon);
        }

        $loader = new ArrayLoader(['template.html' => $template]);
        $twig = new Environment($loader);

        return $twig->render('template.html', ['breadcrumb' => $breadcrumb]);
    }
}
