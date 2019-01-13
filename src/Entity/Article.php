<?php
/*
 * This file is part of the Maximus package.
 *
 * (c) Leo <leo.on.the.earth@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Maximus\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Article data
 *
 * @ORM\Table(name="articles")
 * @ORM\Entity(repositoryClass="Maximus\Repository\ArticleRepository")
 */
class Article
{
    const MEDIA_UPLOAD_PATH = '/media';
    const BACKGROUND_IMAGE_UPLOAD_PATH = '/article/background';

    /**
     * Article ID
     *
     * @var int
     *
     * @ORM\Column(name="id", type="integer", options={"unsigned":true,"comment":"Article ID"})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * Article title
     *
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=128, options={"comment":"Article title"})
     */
    private $title;

    /**
     * Article alias name, use alias to create article URL
     *
     * @var string
     *
     * @ORM\Column(name="alias", type="string", length=128, unique=true, options={"comment":"Article alias name, use alias to create article URL"})
     */
    private $alias;

    /**
     * Article background image path
     *
     * @var string
     *
     * @ORM\Column(name="background_image_path", type="string", length=256, nullable=true, options={"comment":"Article background image path"})
     */
    private $backgroundImagePath;

    /**
     * Article Markdown content
     *
     * @var string
     *
     * @ORM\Column(name="markdown_content", type="text", options={"comment":"Article Markdown content"})
     */
    private $markdownContent;

    /**
     * Article HTML content
     *
     * @var string
     *
     * @ORM\Column(name="html_content", type="text", options={"comment":"Article HTML content"})
     */
    private $htmlContent;

    /**
     * Article tags
     *
     * @ORM\ManyToMany(targetEntity="Maximus\Entity\Tag", mappedBy="articles")
     * @ORM\JoinTable(
     *     name="article_tag_mapping",
     *     joinColumns={@ORM\JoinColumn(name="article_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="tag_id", referencedColumnName="id")}
     * )
     * @ORM\OrderBy({"title" = "ASC"})
     *
     * @var Tag[]|Collection
     */
    private $tags;

    /**
     * Article author info
     *
     * @var Author
     *
     * @ORM\ManyToOne(targetEntity="Maximus\Entity\Author")
     * @ORM\JoinColumn(name="author_id", referencedColumnName="id")
     */
    private $author;

    /**
     * Article published or not
     *
     * @var bool
     *
     * @ORM\Column(name="published", type="boolean", options={"comment":"Article published or not"})
     */
    private $published = false;

    /**
     * Article published datetime
     *
     * @var \DateTime
     *
     * @ORM\Column(name="published_at", type="datetime", nullable=true, options={"comment":"Article published datetime"})
     */
    private $publishedAt;

    /**
     * Article created datetime
     *
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", options={"comment":"Article created datetime"})
     */
    private $createdAt;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     *
     * @return Article
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * @param string $alias
     *
     * @return Article
     */
    public function setAlias($alias)
    {
        $alias = preg_replace('/[^a-zA-Z0-9-]/u', '-', $alias);

        $this->alias = $alias;

        return $this;
    }

    /**
     * @return string
     */
    public function getBackgroundImagePath()
    {
        return $this->backgroundImagePath;
    }

    /**
     * @param string $backgroundImagePath
     *
     * @return Article
     */
    public function setBackgroundImagePath($backgroundImagePath)
    {
        $this->backgroundImagePath = $backgroundImagePath;

        return $this;
    }

    /**
     * @return string
     */
    public function getMarkdownContent()
    {
        return $this->markdownContent;
    }

    /**
     * @param string $markdownContent
     *
     * @return Article
     */
    public function setMarkdownContent($markdownContent)
    {
        $this->markdownContent = $markdownContent;

        return $this;
    }

    /**
     * @return string
     */
    public function getHtmlContent()
    {
        return $this->htmlContent;
    }

    /**
     * @param string $htmlContent
     *
     * @return Article
     */
    public function setHtmlContent($htmlContent)
    {
        $this->htmlContent = $htmlContent;

        return $this;
    }

    /**
     * @return Tag[]|Collection
     */
    public function getTags()
    {
        if (!($this->tags instanceof Collection)) {
            $this->tags = new ArrayCollection();
        }

        return $this->tags;
    }

    /**
     * @param Tag $tag
     *
     * @return Article
     */
    public function addTag(Tag $tag)
    {
        $tag->addArticle($this);
        $this->getTags()->add($tag);

        return $this;
    }

    /**
     * @param Tag[]|Collection $tags
     *
     * @return Article
     */
    public function setTags($tags)
    {
        if (!$tags instanceof Collection) {
            if (!is_array($tags)) {
                return $this;
            }

            $tags = new ArrayCollection($tags);
        }

        foreach ($this->getTags() as $tag) {
            if ($tags->contains($tag) || !$tag instanceof Tag) {
                continue;
            }

            $tag->removeArticle($this);
            $this->getTags()->removeElement($tag);
        }

        foreach ($tags as $tag) {
            if ($this->getTags()->contains($tag)) {
                continue;
            }

            $this->addTag($tag);
        }

        return $this;
    }

    /**
     * @return Author
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @param Author $author
     *
     * @return Article
     */
    public function setAuthor(Author $author)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * @return bool
     */
    public function getPublished()
    {
        return $this->published;
    }

    /**
     * @param bool $published
     *
     * @return Article
     */
    public function setPublished($published)
    {
        $this->published = $published;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getPublishedAt()
    {
        return $this->publishedAt;
    }

    /**
     * @param \DateTime $publishedAt
     *
     * @return Article
     */
    public function setPublishedAt(\DateTime $publishedAt)
    {
        $this->publishedAt = $publishedAt;

        return $this;
    }

    /**
     * @return string
     */
    public function getPublishedYear()
    {
        return $this->publishedAt->format('Y');
    }

    /**
     * @return string
     */
    public function getPublishedMonth()
    {
        return $this->publishedAt->format('m');
    }

    /**
     * @return string
     */
    public function getPublishedDay()
    {
        return $this->publishedAt->format('d');
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     *
     * @return Article
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return array
     */
    public function getLiveViewUrlParams()
    {
        return [
            'year' => $this->getPublishedYear(),
            'month' => $this->getPublishedMonth(),
            'day' => $this->getPublishedDay(),
            'alias' => $this->getAlias(),
        ];
    }
}
