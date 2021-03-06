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
use Maximus\Setting\Settings;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints as Assert;

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
            ->add('files', FileType::class, [
                'multiple' => true,
                'constraints' => [
                    new Assert\All([
                        new Assert\File([
                            'mimeTypes' => Article::VALID_UPLOAD_MIME_TYPES,
                            'mimeTypesMessage' => 'Please upload a valid image',
                        ]),
                    ]),
                ],
            ])
            ->add('dir', TextType::class)
            ->getForm()
        ;

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $urls = [];
            $dir = $data['dir'];

            foreach ($data['files'] as $file) {
                $urls[] = $uploader->upload($file, $dir);
            }

            return new JsonResponse(['success' => true, 'data' => ['urls' => $urls]]);
        }

        return new JsonResponse(['success' => false, 'error' => ['message' => 'No file uploaded!']]);
    }

    /**
     * Delete uploaded article background image
     *
     * @param Article $article
     * @param Settings $settings
     *
     * @return JsonResponse
     *
     * @Route("/delete-article-background-image/{id}", name="delete_article_background_image",
     *     requirements={"id": "\d+"}, methods={"POST"})
     */
    public function deleteArticleBackgroundImage(Article $article, Settings $settings)
    {
        $em = $this->getDoctrine()->getManager();
        $filePath = $settings->getWebRoot().$article->getBackgroundImagePath();

        $article->setBackgroundImagePath('');

        $em->persist($article);
        $em->flush();

        if (file_exists($filePath)) {
            unlink($filePath);
        }

        return new JsonResponse(['success' => true]);
    }
}
