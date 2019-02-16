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
     * @param string $format
     *
     * @return string
     */
    public function generate(Article $article, $format = '')
    {
        if (!empty($article->getDocUrl())) {
            $url = trim($article->getDocUrl(), '/ ');
            $suffix = '' === $format ? '.html' : '.'.$format;
            $parameters = ['path' => $url.$suffix];

            return $this->generator->generate('document', $parameters);
        }

        $parameters = ['format' => $format] + $article->getRouteParams();

        return $this->generator->generate('article', $parameters);
    }
}
