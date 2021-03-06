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

use Maximus\Form\Type\SettingsType;
use Maximus\Session\Flash;
use Maximus\Setting\Settings;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/console/setting", name="console_setting_")
 */
class SettingController extends AbstractController
{
    /**
     * @Route("/", name="index")
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function indexAction(Request $request)
    {
        $settingsRepo = $this->getDoctrine()->getRepository('Maximus:Setting');
        $settings = $this->get(Settings::class);
        $form = $this->createForm(SettingsType::class, $settings);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $settingsRepo->saveSettings($settings);

                $this->addFlash(Flash::SUCCESS, 'Update settings successfully!');

                return $this->redirectToRoute('console_setting_index');
            }
        }

        $viewData = [
            'form' => $form->createView(),
        ];

        return $this->render('console/setting/index.html.twig', $viewData);
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedServices()
    {
        return array_merge(parent::getSubscribedServices(), [
            Settings::class => Settings::class,
        ]);
    }
}
