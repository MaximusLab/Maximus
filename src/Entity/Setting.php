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
 * Setting
 *
 * @ORM\Table(name="settings")
 * @ORM\Entity(repositoryClass="Maximus\Repository\SettingRepository")
 */
class Setting
{
    /**
     * Config ID
     *
     * @var int
     *
     * @ORM\Column(name="id", type="integer", options={"unsigned":true,"comment":"Setting ID"})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * Config key
     *
     * @var string
     *
     * @ORM\Column(name="`key`", type="string", length=32, unique=true, options={"comment":"Setting key"})
     */
    private $key;

    /**
     * Config value
     *
     * @var mixed
     *
     * @ORM\Column(name="`value`", type="text", options={"comment":"Setting value"})
     */
    private $value;

    /**
     * Setting constructor.
     *
     * @param string $key
     */
    public function __construct(string $key)
    {
        $this->key = $key;
    }

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
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @param string $key
     *
     * @return Setting
     */
    public function setKey(string $key)
    {
        $this->key = $key;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     *
     * @return Setting
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }
}
