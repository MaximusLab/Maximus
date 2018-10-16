<?php
/*
 * This file is part of the Maximus package.
 *
 * (c) Leo <leo.on.the.earth@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Maximus\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CustomPageController extends AbstractController
{
    /**
     * @param string $viewName View name for this page
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function pageAction($viewName)
    {
        $viewData = [
        ];

        return $this->render('@theme/pages/'.$viewName.'.html.twig', $viewData);
    }
}
