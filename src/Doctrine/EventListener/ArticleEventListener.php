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
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Maximus\Entity\Article;
use Maximus\Markdown\Markdown;
use Maximus\Service\FileUploader;
use Symfony\Component\HttpFoundation\File\File;

/**
 * Class ArticleEventListener
 *
 * @package Maximus\Doctrine\EventListener
 */
class ArticleEventListener
{
    /**
     * @var Markdown
     */
    private $markdown;

    /**
     * @var FileUploader
     */
    private $uploader;

    /**
     * ArticlePreFlushEventSubscriber constructor.
     *
     * @param Markdown $markdown
     * @param FileUploader $uploader
     */
    public function __construct(Markdown $markdown, FileUploader $uploader)
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
     * @param PreUpdateEventArgs $args
     */
    public function preUpdate(PreUpdateEventArgs $args)
    {
        $object = $args->getObject();

        if ($object instanceof Article) {
            $changes = $args->getEntityChangeSet();

            // Set previous backgroundImagePath value when no new upload file was sent
            if (is_null($object->getBackgroundImagePath()) && !empty($changes['backgroundImagePath'])) {
                $object->setBackgroundImagePath($changes['backgroundImagePath'][0]);
            }

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

        if (is_string($file) && file_exists($file)) {
            $file = new File($file);
        }

        if ($file instanceof File) {
            $imagePath = $this->uploader->upload($file, Article::MEDIA_UPLOAD_PATH);

            $article->setBackgroundImagePath($imagePath);
        }
    }
}
