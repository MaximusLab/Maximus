<?php
/*
 * This file is part of the Maximus package.
 *
 * (c) Leo <leo.on.the.earth@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Maximus\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Maximus\Entity\Setting;
use Maximus\Setting\Settings;

/**
 * SettingRepository
 */
class SettingRepository extends EntityRepository
{
    /**
     * @return Settings
     */
    public function getSettings()
    {
        $rows = $this->createQueryBuilder('setting')
            ->select(['setting.key', 'setting.value'])
            ->getQuery()
            ->getArrayResult()
        ;
        $settings = [];

        foreach ($rows as $row) {
            $settings[$row['key']] = $row['value'];
        }

        return new Settings($settings);
    }

    /**
     * @param Settings $settings
     *
     * @return bool
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function saveSettings(Settings $settings)
    {
        foreach ($settings->all() as $key => $value) {
            $setting = $this->findOneBy(['key' => $key]);
            $setting = $setting instanceof Setting ? $setting : new Setting($key);
            $value = is_null($value) ? '' : $value;

            $setting->setValue($value);

            $this->_em->persist($setting);
        }

        $this->_em->flush();

        return true;
    }
}
