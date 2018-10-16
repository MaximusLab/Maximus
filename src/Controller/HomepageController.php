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
     * @param int $page
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/", name="homepage")
     * @Route("/page/{page}", name="homepage_with_page", requirements={"page":"\d+"}, defaults={"page":1})
     */
    public function indexAction($page = 1)
    {
        $viewData = [
            'articlesPerYear' => [
                '2018' => [
                    ['title' => '這是Game標tool123題 123The story, the most popular youth wear.123口嘗味123，她123多疼xyz你！', 'tags' => ['PHP', 'Paginator'], 'publishedAt' => new \DateTime('2018-02-18'), 'year' => '2018', 'month' => '02', 'day' => '18', 'englishTitle' => 'foo-bar'],
                    ['title' => '圓滿這全程的寂寞，稍稍疏洩我的積愫，想起怎不可傷？', 'tags' => ['PHP', 'Symfony', 'Paginator'], 'publishedAt' => '2018-02-07', 'year' => '2018', 'month' => '02', 'day' => '18', 'englishTitle' => 'foo-bar'],
                    ['title' => 'The best love story you\'ve ever read.', 'tags' => ['PHP'], 'publishedAt' => '2018-01-08', 'year' => '2018', 'month' => '02', 'day' => '18', 'englishTitle' => 'foo-bar'],
                ],
                '2017' => [
                    ['title' => '這是標題 The story, the most popular youth wear.口嘗味，她多疼你！', 'tags' => ['PHP', 'Symfony', 'Paginator'], 'publishedAt' => new \DateTime('2017-02-18'), 'year' => '2018', 'month' => '02', 'day' => '18', 'englishTitle' => 'foo-bar'],
                    ['title' => '圓滿這全程的寂寞，稍稍疏洩我的積愫，想起怎不可傷？', 'tags' => ['PHP', 'Talk', 'Paginator'], 'publishedAt' => '2017-02-07', 'year' => '2018', 'month' => '02', 'day' => '18', 'englishTitle' => 'foo-bar'],
                ],
                '2016' => [
                    ['title' => '這是標題 The story, the most popular youth wear.口嘗味，她多疼你！', 'tags' => ['Symfony', 'Paginator'], 'publishedAt' => new \DateTime('2016-02-18'), 'year' => '2018', 'month' => '02', 'day' => '18', 'englishTitle' => 'foo-bar'],
                    ['title' => '圓滿這全程的寂寞，稍稍疏洩我的積愫，想起怎不可傷？', 'tags' => ['PHP', 'Symfony', 'Paginator'], 'publishedAt' => '2016-02-07', 'year' => '2018', 'month' => '02', 'day' => '18', 'englishTitle' => 'foo-bar'],
                    ['title' => 'The best love story you\'ve ever read.', 'tags' => ['PHP'], 'publishedAt' => '2016-01-08', 'year' => '2018', 'month' => '02', 'day' => '18', 'englishTitle' => 'foo-bar'],
                ],
            ],
            'page' => $page,
        ];

        return $this->render('@theme/homepage.html.twig', $viewData);
    }
}
