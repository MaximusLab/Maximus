<?php
/*
 * This file is part of the Maximus package.
 *
 * (c) Leo <leo.on.the.earth@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Maximus\Service;

use Maximus\Setting\Settings;
use Symfony\Component\HttpFoundation\File\File;

/**
 * Class FileUploader
 *
 * @package Maximus\Service
 */
class FileUploader
{
    /**
     * @var Settings
     */
    private $settings;

    /**
     * FileUploader constructor.
     *
     * @param Settings $settings
     */
    public function __construct(Settings $settings)
    {
        $this->settings = $settings;
    }

    /**
     * @param File $file
     * @param string $dir The directory path related to upload root directory
     *                    (e.g. /file/dir is related to /upload/file/dir, '/upload' is upload root directory)
     *
     * @return string File link url
     */
    public function upload(File $file, string $dir)
    {
        $fileName = md5_file($file->getRealPath()).'.'.$file->guessExtension();
        $dir = '/'.trim($dir, '/ ');
        $filePath = $this->settings->getUploadPath().$dir.'/'.substr($fileName, 0, 2);
        $targetDirectory = $this->settings->getWebRoot().$filePath;
        $fullFilePath = $targetDirectory.'/'.$fileName;

        if (!file_exists($fullFilePath)) {
            $file->move($targetDirectory, $fileName);
        }

        return $filePath.'/'.$fileName;
    }
}
