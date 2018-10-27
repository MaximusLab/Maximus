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

use Maximus\Entity\Tag;
use Maximus\Form\Type\TagType;
use Maximus\Session\Flash;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/console/tag", name="console_tag_")
 */
class TagController extends AbstractController
{
    /**
     * @Route("/", name="index")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        $tagRepo = $this->getDoctrine()->getRepository('Maximus:Tag');
        $tags = $tagRepo->findAll();

        $viewData = [
            'tags' => $tags,
        ];

        return $this->render('console/tag/index.html.twig', $viewData);
    }

    /**
     * @Route("/create", name="create")
     * @Route("/edit/{id}", name="edit", requirements={"id": "\d+"}, defaults={"id": 0})
     *
     * @param Request $request
     * @param int $id Tag ID
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, int $id = 0)
    {
        $tag = 0 === $id ? new Tag() : $this->getDoctrine()->getRepository('Maximus:Tag')->find($id);

        if (!$tag instanceof Tag) {
            $this->addFlash(Flash::ERROR, 'Invalid tag id!');

            return $this->redirectToRoute('console_tag_index');
        }

        $action = empty($tag->getId()) ? 'Create' : 'Edit';
        $form = $this->createForm(TagType::class, $tag);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $em = $this->getDoctrine()->getManager();

                $em->persist($tag);
                $em->flush();

                $this->addFlash(Flash::SUCCESS, $action.' tag (id: '.$tag->getId().') successfully!');

                return $this->redirectToRoute('console_tag_index');
            }
        }

        $viewData = [
            'action' => $action,
            'form' => $form->createView(),
        ];

        return $this->render('console/tag/edit.html.twig', $viewData);
    }
}
