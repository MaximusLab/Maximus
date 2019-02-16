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

use Doctrine\Common\Persistence\ObjectManager;
use Maximus\Entity\Article;
use Maximus\Form\Type\ArticleType;
use Maximus\Markdown\Markdown;
use Maximus\Session\Flash;
use Maximus\Setting\Settings;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
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
     * @param int     $id      Author ID
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
                $settings = $this->get(Settings::class);
                $em = $this->getDoctrine()->getManager();

                $em->persist($article);
                $em->flush();

                $this->moveUploadedTempFiles($article, $settings, $em);
                $this->cleanInvalidUploadedFiles($article, $settings);

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
     * @param Request  $request  Request instance
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

    /**
     * Move temp media files to article's own media directory
     *
     * @param Article       $article
     * @param Settings      $settings
     * @param ObjectManager $em
     */
    private function moveUploadedTempFiles(Article $article, Settings $settings, ObjectManager $em)
    {
        $webRoot = $settings->getWebRoot();
        $dir = $settings->getUploadPath().Article::ARTICLE_UPLOAD_PATH.'/'.$article->getId();
        $tempDir = $settings->getUploadPath().Article::TEMP_UPLOAD_PATH;
        $searches = [];
        $replaces = [];
        $update = false;

        foreach ($article->getValidImagesInContent() as $path) {
            if (0 === strpos($path, $tempDir)) {
                $newPath = str_replace($tempDir, $dir, $path);
                $searches[] = $path;
                $replaces[] = $newPath;
                $update = true;

                $this->renameTempFile($path, $newPath, $webRoot);
            }
        }

        if (!empty($searches)) {
            $article->setMarkdownContent(str_replace($searches, $replaces, $article->getMarkdownContent()));
        }

        if (0 === strpos($article->getBackgroundImagePath(), $tempDir)) {
            $path = $article->getBackgroundImagePath();
            $newPath = str_replace($tempDir, $dir, $path);
            $update = true;

            $this->renameTempFile($path, $newPath, $webRoot);
            $article->setBackgroundImagePath($newPath);
        }

        if ($update) {
            $em->persist($article);
            $em->flush();
        }
    }

    /**
     * Rename temp file path to new file path
     *
     * @param string $path
     * @param string $newPath
     * @param string $webRoot
     */
    private function renameTempFile($path, $newPath, $webRoot)
    {
        $fs = $this->getFileSystem();

        $fs->copy($webRoot.$path, $webRoot.$newPath, true);
        $fs->remove($webRoot.$path);
    }

    /**
     * Clean invalid uploaded files in article's own media directory
     *
     * @param Article  $article
     * @param Settings $settings
     */
    private function cleanInvalidUploadedFiles(Article $article, Settings $settings)
    {
        $fs = $this->getFileSystem();
        $dir = $settings->getWebRoot().$settings->getUploadPath().Article::ARTICLE_UPLOAD_PATH.'/'.$article->getId();

        if (!is_dir($dir)) {
            return;
        }

        $validFiles = $article->getValidImagesInContent();

        if (!empty($article->getBackgroundImagePath())) {
            $validFiles[] = $article->getBackgroundImagePath();
        }

        $webRoot = str_replace('\\', '/', $settings->getWebRoot());
        $validFiles = array_flip($validFiles);
        $removeFiles = [];

        /** @var SplFileInfo $file */
        foreach ((new Finder())->files()->in($dir) as $file) {
            $path = str_replace('\\', '/', $file->getRealPath());
            $path = str_replace($webRoot, '', $path);

            if (!isset($validFiles[$path])) {
                $removeFiles[] = $file->getRealPath();
            }
        }

        $fs->remove($removeFiles);
    }

    /**
     * @return Filesystem
     */
    private function getFileSystem()
    {
        static $fs;

        if (!$fs instanceof Filesystem) {
            $fs = new Filesystem();
        }

        return $fs;
    }

    public static function getSubscribedServices()
    {
        return array_merge(parent::getSubscribedServices(), [
            Settings::class => '?'.Settings::class,
        ]);
    }
}
