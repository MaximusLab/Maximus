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

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Maximus\Entity\Setting;
use Maximus\Setting\Settings;

/**
 * SettingRepository
 */
class SettingRepository extends ServiceEntityRepository
{
    /**
     * @return Settings
     */
    public function getSettings()
    {
        $settings = [];
        $rows = $this->createQueryBuilder('setting')
            ->select(['setting.key', 'setting.value'])
            ->getQuery()
            ->getArrayResult()
        ;

        foreach ($rows as $row) {
            $settings[$row['key']] = $this->decodeValue($row['key'], $row['value']);
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
            $value = $this->encodeValue($key, $value);

            $setting->setValue($value);

            $this->_em->persist($setting);
        }

        $this->_em->flush();

        return true;
    }

    /**
     * @param string $key
     * @param mixed $value
     *
     * @return string
     */
    private function encodeValue($key, $value)
    {
        switch ($key) {
            default:
                $return = json_encode($value);
        }

        return $return;
    }

    /**
     * @param string $key
     * @param string $value
     *
     * @return mixed
     */
    private function decodeValue($key, $value)
    {
        switch ($key) {
            case 'themeVariables':
                return empty($value) ? [] : @json_decode($value, true);
            case 'themeMenus':
                $return = empty($value) ? [] : @json_decode($value, true);

                foreach ($return as &$menu) {
                    if (!isset($menu['route_params'])) {
                        $menu['route_params'] = [];
                    }

                    $menu['route_params'] = (array) $menu['route_params'];
                }

                return $return;
        }

        // Default decode process
        $return = @json_decode($value, true);
        $return = is_null($return) ? '' : $return;

        return $return;
    }
}
