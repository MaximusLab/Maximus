<?php
/*
 * This file is part of the Maximus package.
 *
 * (c) Leo <leo.on.the.earth@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Maximus\Repository;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityRepository;
use Maximus\Entity\Article;

/**
 * ArticleRepository
 */
class ArticleRepository extends EntityRepository
{
    /**
     * @return Collection|Article[]
     */
    public function getPublishedArticles()
    {
        return $this->findBy(['published' => true], ['publishedAt' => 'DESC']);
    }

    /**
     * @return array Return route parameters, For example:
     *
     * <code><pre>
     * [
     *     ['alias' => 'article-title-alias-1', 'year' => '2019', 'month' => '01', 'day' => '02'],
     *     ['alias' => 'article-title-alias-2', 'year' => '2019', 'month' => '01', 'day' => '03'],
     *     ...
     * ]
     * </pre></code>
     */
    public function getPublishedArticleRouteParameters()
    {
        $rows = $this->createQueryBuilder('article')
            ->select(['article.alias', 'article.publishedAt'])
            ->where('article.published = true')
            ->orderBy('article.publishedAt', 'DESC')
            ->getQuery()
            ->getArrayResult()
        ;
        $parameters = [];

        foreach ($rows as $row) {
            /** @var \DateTime $publishedAt */
            $publishedAt = $row['publishedAt'];
            $parameters[] = [
                'alias' => $row['alias'],
                'year' => $publishedAt->format('Y'),
                'month' => $publishedAt->format('m'),
                'day' => $publishedAt->format('d'),
            ];
        }

        return $parameters;
    }

    /**
     * @param string $alias
     *
     * @return Collection|Article[]
     */
    public function getPublishedArticlesByTagTitle($alias)
    {
        return $this->createQueryBuilder('article')
            ->leftJoin('article.tags', 'tag')
            ->where('tag.alias = :alias')
            ->andWhere('article.published = true')
            ->setParameter('alias', $alias)
            ->orderBy('tag.title', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }
}
