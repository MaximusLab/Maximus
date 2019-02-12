<?php
/*
 * This file is part of the Maximus package.
 *
 * (c) Leo <leo.on.the.earth@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Maximus\Controller\Console;

use Maximus\Entity\Article;
use Maximus\Form\Type\ArticleType;
use Maximus\Markdown\Markdown;
use Maximus\Session\Flash;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/console", name="console_")
 */
class ArticleController extends AbstractController
{
    /**
     * @Route("/", name="index")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        $articleRepo = $this->getDoctrine()->getRepository('Maximus:Article');
        /** @var Article[] $articles */
        $articles = $articleRepo->findAll();

        $viewData = [
            'articles' => $articles,
        ];

        return $this->render('console/article/index.html.twig', $viewData);
    }

    /**
     * @Route("/article/create", name="article_create")
     * @Route("/article/edit/{id}", name="article_edit", requirements={"id": "\d+"}, defaults={"id": 0})
     * @Route("/article/save/{id}", name="article_save", requirements={"id": "\d+"}, defaults={"id": 0}, methods={"POST"})
     *
     * @param Request $request
     * @param int $id Author ID
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, int $id = 0)
    {
        $article = 0 === $id ? new Article() : $this->getDoctrine()->getRepository('Maximus:Article')->find($id);

        if (!$article instanceof Article) {
            $this->addFlash(Flash::ERROR, 'Invalid article id!');

            return $this->redirectToRoute('console_index');
        }

        $action = empty($article->getId()) ? 'Create' : 'Edit';
        $form = $this->createForm(ArticleType::class, $article);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $em = $this->getDoctrine()->getManager();

                $em->persist($article);
                $em->flush();

                $this->addFlash(Flash::SUCCESS, $action.' article (id: '.$article->getId().') successfully!');

                return $this->redirectToRoute('console_article_edit', ['id' => $article->getId()]);
            }
        }

        $viewData = [
            'action' => $action,
            'form' => $form->createView(),
            'formUrl' => $this->generateUrl('console_article_save', 0 === $id ? [] : ['id' => $id]),
            'article' => $article,
        ];

        return $this->render('console/article/edit.html.twig', $viewData);
    }

    /**
     * Parse markdown content to HTML
     *
     * @param Request $request Request instance
     * @param Markdown $markdown
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/article/edit/parse-markdown", name="article_edit_parse_markdown", methods={"POST"})
     */
    public function parseMarkdownAction(Request $request, Markdown $markdown)
    {
        $markdownContent = $request->request->get('markdownContent', '');
        $htmlContent = $markdown->transform($markdownContent);

        return new Response($htmlContent);
    }
}
