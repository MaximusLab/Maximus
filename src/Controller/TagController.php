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
use Maximus\Entity\Tag;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class TagController extends AbstractController
{
    /**
     * @param string $tag Tag name
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/tag/{tag}", name="tag")
     */
    public function tagAction(string $tag)
    {
        $tag = $this->getDoctrine()->getRepository(Tag::class)->findOneBy(['title' => $tag]);

        if (!$tag instanceof Tag) {
            return $this->redirectToRoute('homepage');
        }

        $articles = $this->getDoctrine()->getRepository(Article::class)->getPublishedArticlesByTagTitle($tag->getTitle());
        $articlesPerYear = [];

        /** @var Article $article */
        foreach ($articles as $article) {
            $year = $article->getPublishedYear();

            if (!array_key_exists($year, $articlesPerYear)) {
                $articlesPerYear[$year] = [];
            }

            $articlesPerYear[$year][] = $article;
        }

        $viewData = [
            'tag' => $tag,
            'articlesPerYear' => $articlesPerYear,
        ];

        return $this->render('@theme/tag.html.twig', $viewData);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/tags", name="tags")
     */
    public function listAction()
    {
        $viewData = [
            'tags' => $this->getDoctrine()->getRepository(Tag::class)->getArticleCounts(),
        ];

        return $this->render('@theme/tags.html.twig', $viewData);
    }
}
