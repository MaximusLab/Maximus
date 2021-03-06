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

use Maximus\Entity\Author;
use Maximus\Form\Type\AuthorType;
use Maximus\Session\Flash;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/console/author", name="console_author_")
 */
class AuthorController extends AbstractController
{
    /**
     * @Route("/", name="index")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        $authorRepo = $this->getDoctrine()->getRepository('Maximus:Author');
        $authors = $authorRepo->findAll();

        $viewData = [
            'authors' => $authors,
        ];

        return $this->render('console/author/index.html.twig', $viewData);
    }

    /**
     * @Route("/create", name="create")
     * @Route("/edit/{id}", name="edit", requirements={"id": "\d+"}, defaults={"id": 0})
     *
     * @param Request $request
     * @param int $id Author ID
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, int $id = 0)
    {
        $author = 0 === $id ? new Author() : $this->getDoctrine()->getRepository('Maximus:Author')->find($id);

        if (!$author instanceof Author) {
            $this->addFlash(Flash::ERROR, 'Invalid author id!');

            return $this->redirectToRoute('console_author_index');
        }

        $action = empty($author->getId()) ? 'Create' : 'Edit';
        $form = $this->createForm(AuthorType::class, $author);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $em = $this->getDoctrine()->getManager();

                $em->persist($author);
                $em->flush();

                $this->addFlash(Flash::SUCCESS, $action.' author (id: '.$author->getId().') successfully!');

                return $this->redirectToRoute('console_author_index');
            }
        }

        $viewData = [
            'action' => $action,
            'form' => $form->createView(),
        ];

        return $this->render('console/author/edit.html.twig', $viewData);
    }
}
