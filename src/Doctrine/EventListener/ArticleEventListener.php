<?php
/*
 * This file is part of the Maximus package.
 *
 * (c) Leo <leo.on.the.earth@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Maximus\Doctrine\EventListener;

use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Maximus\Entity\Article;
use Maximus\Service\FileUploader;
use Michelf\MarkdownExtra;
use Symfony\Component\HttpFoundation\File\File;

/**
 * Class ArticleEventListener
 *
 * @package Maximus\Doctrine\EventListener
 */
class ArticleEventListener
{
    /**
     * @var MarkdownExtra
     */
    private $markdown;

    /**
     * @var FileUploader
     */
    private $uploader;

    /**
     * ArticlePreFlushEventSubscriber constructor.
     *
     * @param MarkdownExtra $markdown
     * @param FileUploader $uploader
     */
    public function __construct(MarkdownExtra $markdown, FileUploader $uploader)
    {
        $this->markdown = $markdown;
        $this->uploader = $uploader;
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $object = $args->getObject();

        if ($object instanceof Article) {
            $this->prepareArticle($object);
        }
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function preUpdate(LifecycleEventArgs $args)
    {
        $object = $args->getObject();

        if ($object instanceof Article) {
            $this->prepareArticle($object);
        }
    }

    /**
     * @param Article $article
     */
    private function prepareArticle(Article $article)
    {
        $article->setHtmlContent($this->markdown->transform(trim($article->getMarkdownContent())));

        if (empty($article->getId())) {
            $article->setCreatedAt(new \DateTime());
        }
        if ($article->getPublished() && empty($article->getPublishedAt())) {
            $article->setPublishedAt(new \DateTime());
        }

        $this->uploadBackgroundImage($article);
    }

    /**
     * @param Article $article
     */
    private function uploadBackgroundImage(Article $article)
    {
        $file = $article->getBackgroundImagePath();

        if (is_string($file)) {
            $file = new File($file);
        }

        if ($file instanceof File) {
            $imagePath = $this->uploader->upload($file, Article::MEDIA_UPLOAD_PATH);

            $article->setBackgroundImagePath($imagePath);
        }
    }
}
