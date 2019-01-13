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

use Doctrine\ORM\EntityRepository;

/**
 * TagRepository
 */
class TagRepository extends EntityRepository
{
    /**
     * @return array
     */
    public function getArticleCounts()
    {
        $rows = $this->createQueryBuilder('tag')
            ->leftJoin('tag.articles', 'article')
            ->select(['tag.title', 'COUNT(article.id) AS count'])
            ->where('article.published = true')
            ->groupBy('tag.id')
            ->orderBy('tag.title', 'ASC')
            ->getQuery()
            ->getArrayResult()
        ;

        return array_combine(array_column($rows, 'title'), array_column($rows, 'count'));
    }

    /**
     * @return array
     */
    public function getTitles()
    {
        $rows = $this->createQueryBuilder('tag')
            ->select(['tag.title'])
            ->orderBy('tag.title', 'ASC')
            ->getQuery()
            ->getArrayResult()
        ;

        return array_column($rows, 'title');
    }
}
