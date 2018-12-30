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
     * @var string
     */
    private $baseDirectory;

    /**
     * FileUploader constructor.
     *
     * @param Settings $settings
     */
    public function __construct(Settings $settings)
    {
        $this->baseDirectory = $settings->getUploadBasePath();
    }

    /**
     * @param File $file
     * @param string $dir
     *
     * @return string File link url
     */
    public function upload(File $file, string $dir)
    {
        $fileName = md5_file($file->getRealPath()).'.'.$file->guessExtension();
        $dir = '/'.trim($dir, '/ ');
        $filePath = $dir.'/'.substr($fileName, 0, 2);
        $targetDirectory = $this->baseDirectory.$filePath;
        $fullFilePath = $targetDirectory.'/'.$fileName;

        if (!file_exists($fullFilePath)) {
            $file->move($targetDirectory, $fileName);
        }

        return $filePath.'/'.$fileName;
    }
}
