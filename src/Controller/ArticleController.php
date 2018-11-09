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

use Maximus\Entity\Article;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ArticleController extends AbstractController
{
    /**
     * @param int    $year  The year part of date when the article published
     * @param int    $month The month part of date when the article published
     * @param int    $day   The day part of date when the article published
     * @param string $alias The english title
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction($year, $month, $day, $alias)
    {
        $article = $this->getDoctrine()->getRepository(Article::class)
            ->findOneBy(['alias' => $alias]);

        if (!$article instanceof Article) {
            return $this->redirectToRoute('homepage');
        }

        $viewData = [
            'article' => $article,
            'alias' => $alias,
            'year' => $article->getPublishedYear(),
            'month' => $article->getPublishedMonth(),
            'day' => $article->getPublishedDay(),
        ];

        return $this->render('@theme/article.html.twig', $viewData);
    }
}
