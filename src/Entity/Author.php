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

use Doctrine\ORM\Mapping as ORM;

/**
 * Author info
 *
 * @ORM\Table(name="authors")
 * @ORM\Entity()
 */
class Author
{
    /**
     * Tag ID
     *
     * @var int
     *
     * @ORM\Column(name="id", type="integer", options={"unsigned":true,"comment":"Author ID"})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * Tag title
     *
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=128, options={"comment":"Author name"})
     */
    private $name;

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
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return Author
     */
    public function setName(string $name)
    {
        $this->name = $name;

        return $this;
    }
}
