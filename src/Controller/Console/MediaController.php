<?php
/*
 * This file is part of the Maximus package.
 *
 * (c) Leo <leo.on.the.earth@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Maximus\Controller\Console;

use Maximus\Entity\Article;
use Maximus\Service\FileUploader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/console/media", name="console_media_")
 */
class MediaController extends AbstractController
{
    /**
     * Upload media files
     *
     * @param Request $request
     * @param FileUploader $uploader
     *
     * @return JsonResponse
     *
     * @Route("/upload", name="upload", methods={"POST"})
     */
    public function uploadAction(Request $request, FileUploader $uploader)
    {
        $form = $this->container->get('form.factory')->createNamedBuilder('', FormType::class)
            ->add('files', FileType::class, ['multiple' => true])
            ->getForm()
        ;

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $urls = [];

            foreach ($data['files'] as $file) {
                $urls[] = $uploader->upload($file, Article::MEDIA_UPLOAD_PATH);
            }

            return new JsonResponse(['success' => true, 'data' => ['urls' => $urls]]);
        }

        return new JsonResponse(['success' => false, 'error' => ['message' => 'No file uploaded!']]);
    }
}
