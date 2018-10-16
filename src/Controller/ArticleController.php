<?php
/*
 * This file is part of the Maximus package.
 *
 * (c) Leo <leo.on.the.earth@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Maximus\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{
    /**
     * @param int    $year  The year part of date when the article published
     * @param int    $month The month part of date when the article published
     * @param int    $day   The day part of date when the article published
     * @param string $title The english title
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/{year}/{month}/{day}/{title}", name="article")
     */
    public function indexAction($year, $month, $day, $title)
    {
        $viewData = [
            'article' => [
                'title' => '這是Game標tool123題 123The story, the most popular youth wear.123口嘗味123，她123多疼xyz你！',
                'tags' => ['PHP', 'Paginator'],
                'publishedAt' => new \DateTime('2018-02-18 13:04:05'),
            ],
            'englishTitle' => $title,
            'year' => $year,
            'month' => $month,
            'day' => $day,
        ];

        return $this->render('@theme/article.html.twig', $viewData);
    }
}
