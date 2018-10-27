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

class HomepageController extends AbstractController
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/", name="homepage")
     */
    public function indexAction()
    {
        $articles = $this->getDoctrine()
            ->getRepository('Maximus:Article')
            ->findBy(['published' => true], ['publishedAt' => 'DESC']);
        $articlesPerYear = [];

        foreach ($articles as $article) {
            $year = $article->getPublishedAt()->format('Y');

            if (!array_key_exists($year, $articlesPerYear)) {
                $articlesPerYear[$year] = [];
            }

            $articlesPerYear[$year][] = $article;
        }

        $viewData = [
            'articlesPerYear' => $articlesPerYear,
        ];

        return $this->render('@theme/homepage.html.twig', $viewData);
    }
}
