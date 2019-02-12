<?php
/*
 * This file is part of the Maximus package.
 *
 * (c) Leo <leo.on.the.earth@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Maximus\Routing\Generator;

use Maximus\Entity\Article;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ArticleUrlGenerator
{
    /**
     * @var UrlGeneratorInterface
     */
    private $generator;

    /**
     * ArticleUrlGeneratorExtension constructor.
     *
     * @param UrlGeneratorInterface $generator
     */
    public function __construct(UrlGeneratorInterface $generator)
    {
        $this->generator = $generator;
    }

    /**
     * @param Article $article
     *
     * @return string
     */
    public function generate(Article $article)
    {
        if (!empty($article->getDocUrl())) {
            $path = ltrim($article->getDocUrl(), '/ ');

            return $this->generator->generate('doc-article', ['path' => $path]).'.html';
        }

        return $this->generator->generate('article', $article->getRouteParams());
    }
}
