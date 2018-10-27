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
use Michelf\MarkdownExtra;

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
     * ArticlePreFlushEventSubscriber constructor.
     *
     * @param MarkdownExtra $markdown
     *
     */
    public function __construct(MarkdownExtra $markdown)
    {
        $this->markdown = $markdown;
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
    }
}
