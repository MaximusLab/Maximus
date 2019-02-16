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
use Maximus\HttpFoundation\Response\PlainTextResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{
    /**
     * @param int    $year  The year part of date when the article published
     * @param int    $month The month part of date when the article published
     * @param int    $day   The day part of date when the article published
     * @param string $alias The english title
     * @param string $format The page format, include 'html', 'md' (for markdown contents), default is 'html'
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function article($year, $month, $day, $alias, $format = 'html')
    {
        $article = $this->getDoctrine()->getRepository(Article::class)
            ->findOneBy(['alias' => $alias]);

        if (!$article instanceof Article) {
            return $this->redirectToRoute('homepage');
        }

        if ('md' === $format) {
            return new PlainTextResponse($article->getMarkdownContent());
        }

        return $this->renderArticle($article);
    }

    /**
     * @Route("/doc/{path}", name="document", requirements={"path"=".+"})
     *
     * @param string $path
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function document($path)
    {
        $format = 'html';

        if ('.md' === substr($path, -3)) {
            $format = 'md';
            $path = substr($path, 0, -3);
        } elseif ('.html' === substr($path, -5)) {
            $path = substr($path, 0, -5);
        }

        $path = '/'.trim($path, '/ ');
        $path = trim($path, '. ');

        $article = $this->getDoctrine()->getRepository(Article::class)
            ->findOneBy(['docUrl' => $path]);

        if (!$article instanceof Article) {
            return $this->redirectToRoute('homepage');
        }

        if ('md' === $format) {
            return new PlainTextResponse($article->getMarkdownContent());
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
