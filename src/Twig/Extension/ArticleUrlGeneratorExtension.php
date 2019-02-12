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

use Maximus\Routing\Generator\ArticleUrlGenerator;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ArticleUrlGeneratorExtension extends AbstractExtension
{
    /**
     * @var ArticleUrlGenerator
     */
    private $articleUrlGenerator;

    /**
     * ArticleUrlGeneratorExtension constructor.
     *
     * @param ArticleUrlGenerator $articleUrlGenerator
     */
    public function __construct(ArticleUrlGenerator $articleUrlGenerator)
    {
        $this->articleUrlGenerator = $articleUrlGenerator;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('article_path', [$this->articleUrlGenerator, 'generate'], ['is_safe' => ['html']]),
        ];
    }
}
