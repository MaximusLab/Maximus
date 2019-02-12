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
use Maximus\Routing\Generator\ArticleUrlGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

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
    public function article($year, $month, $day, $alias)
    {
        $article = $this->getDoctrine()->getRepository(Article::class)
            ->findOneBy(['alias' => $alias]);

        if (!$article instanceof Article) {
            return $this->redirectToRoute('homepage');
        }

        return $this->renderArticle($article);
    }

    /**
     * @Route("/doc/{path}", name="doc-article", requirements={"path"=".+"})
     *
     * @param string $path
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function doc($path)
    {
        if ('.html' === substr($path, -5)) {
            $path = substr($path, 0, -5);
        }

        $path = '/'.ltrim($path, '/ ');
        $article = $this->getDoctrine()->getRepository(Article::class)
            ->findOneBy(['docUrl' => $path]);

        if (!$article instanceof Article) {
            return $this->redirectToRoute('homepage');
        }

        return $this->renderArticle($article);
    }

    /**
     * @param Article $article
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    private function renderArticle(Article $article)
    {
        $viewData = [
            'article' => $article,
        ];

        return $this->render('@theme/article.html.twig', $viewData);
    }
}
