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
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Tag info
 *
 * @ORM\Table(name="tags")
 * @ORM\Entity(repositoryClass="Maximus\Repository\TagRepository")
 */
class Tag
{
    /**
     * Tag ID
     *
     * @var int
     *
     * @ORM\Column(name="id", type="integer", options={"unsigned":true,"comment":"Tag ID"})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * Tag title
     *
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=128, options={"comment":"Tag title"})
     */
    private $title;

    /**
     * Tag title alias
     *
     * @var string
     *
     * @ORM\Column(name="alias", type="string", length=128, options={"comment":"Tag title alias"})
     *
     * @Assert\Regex("/^[a-z0-9-]+$/")
     */
    private $alias;

    /**
     * @ORM\ManyToMany(targetEntity="Maximus\Entity\Article", inversedBy="tags")
     * @ORM\JoinTable(
     *     name="article_tag_mapping",
     *     joinColumns={@ORM\JoinColumn(name="tag_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="article_id", referencedColumnName="id")}
     * )
     * @ORM\OrderBy({"publishedAt" = "DESC"})
     *
     * @var Article[]|Collection
     */
    private $articles;

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
     * @return Tag
     */
    public function setTitle(string $title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return Article[]|Collection
     */
    public function getArticles()
    {
        if (!($this->articles instanceof Collection)) {
            $this->articles = new ArrayCollection();
        }

        return $this->articles;
    }

    /**
     * @param Article $article
     *
     * @return Tag
     */
    public function addArticle(Article $article)
    {
        $this->getArticles()->add($article);

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
     * @return Tag
     */
    public function setAlias($alias)
    {
        $this->alias = $alias;

        return $this;
    }

    /**
     * @param Article[]|Collection $articles
     *
     * @return Tag
     */
    public function setArticles($articles)
    {
        if (!$articles instanceof Collection) {
            if (!is_array($articles)) {
                return $this;
            }

            $articles = new ArrayCollection($articles);
        }

        foreach ($articles as $article) {
            if ($this->getArticles()->contains($article)) {
                continue;
            }

            $this->addArticle($article);
        }

        return $this;
    }

    /**
     * @param Article $article
     *
     * @return Tag
     */
    public function removeArticle(Article $article)
    {
        if ($this->getArticles()->contains($article)) {
            $this->getArticles()->removeElement($article);
        }

        return $this;
    }
}
