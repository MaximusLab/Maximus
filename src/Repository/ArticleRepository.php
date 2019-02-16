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
