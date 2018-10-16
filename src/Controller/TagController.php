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

class TagController extends AbstractController
{
    /**
     * @param string $tag Tag name
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/tag/{tag}", name="tag")
     */
    public function tagAction($tag)
    {
        $viewData = [
            'tag' => $tag,
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
        ];

        return $this->render('@theme/tag.html.twig', $viewData);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/tag/list", name="tags")
     */
    public function listAction()
    {
        $viewData = [
            'tags' => [
                ['title' => 'PHP', 'description' => 'PHP related articles'],
                ['title' => 'Paginator', 'description' => 'PHP related articles'],
                ['title' => 'Symfony', 'description' => 'Symfony related articles'],
                ['title' => 'Talk', 'description' => 'Some talk for life'],
            ],
        ];

        return $this->render('@theme/tags.html.twig', $viewData);
    }
}
