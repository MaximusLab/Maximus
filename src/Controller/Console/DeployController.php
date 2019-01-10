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

use GuzzleHttp\Client;
use Maximus\Entity\Article;
use Maximus\Entity\Tag;
use Maximus\Setting\Settings;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @Route("/console/deploy", name="console_deploy_")
 */
class DeployController extends AbstractController
{
    /**
     * @Route("/", name="index")
     *
     * @param Settings $settings
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Settings $settings)
    {
        $viewData = [
            'urls' => array_merge(
                $this->getAllMenuUrls($settings),
                $this->getAllArticleUrls(),
                $this->getAllTagUrls()
            ),
        ];

        return $this->render('console/deploy/index.html.twig', $viewData);
    }

    /**
     * Copy asset files
     *
     * @Route("/copy-assets", name="copy_assets", methods={"POST"})
     *
     * @param Settings $settings
     *
     * @return JsonResponse
     */
    public function copyAssets(Settings $settings)
    {
        $themeDir = $this->getParameter('kernel.project_dir').'/themes_installed/'.$settings->getTheme().'/public';
        $deployThemeDir = $this->getDeployDir().'/theme/'.$settings->getTheme();
        $uploadDir = $this->getParameter('kernel.project_dir').'/public/upload';
        $deployUploadDir = $this->getDeployDir().'/upload';

        $fs = new Filesystem();

        $fs->mirror($themeDir, $deployThemeDir);
        $fs->mirror($uploadDir, $deployUploadDir);

        return new JsonResponse(['success' => true]);
    }

    /**
     * Generate deploy files
     *
     * @Route("/generate-file", name="generate_file", methods={"POST"})
     *
     * @param Request $request
     * @param Settings $settings
     *
     * @return JsonResponse
     */
    public function generateFile(Request $request, Settings $settings)
    {
        $url = $request->request->get('url');
        $html = $request->request->get('html');
        $dir = $this->getDeployDir().$this->generateOutputFilePath($settings->getUrlPrefix(), $url);
        $filePath = $dir.'/index.html';

        if (!file_exists($filePath) || md5_file($filePath) !== md5($html)) {
            $fs = new Filesystem();

            $fs->mkdir($dir);

            file_put_contents($filePath, $html);
        }

        return new JsonResponse(['success' => true]);
    }

    /**
     * @param Settings $settings
     *
     * @return array
     */
    private function getAllMenuUrls(Settings $settings)
    {
        $menus = $settings->getThemeMenus();
        $urls = [];

        foreach ($menus as $menu) {
            $routeParams = empty($menu['route_params']) ? [] : $menu['route_params'];
            $url = $this->generateUrl($menu['route_name'], $routeParams);

            $urls[] = $url;
        }

        return $urls;
    }

    /**
     * @return array
     */
    private function getAllArticleUrls()
    {
        $articleRepo = $this->getDoctrine()->getRepository(Article::class);
        $articleRouteParameters = $articleRepo->getPublishedArticleRouteParameters();
        $urls = [];

        foreach ($articleRouteParameters as $parameters) {
            $url = $this->generateUrl('article', $parameters);

            $urls[] = $url;
        }

        return $urls;
    }

    /**
     * @return array
     */
    private function getAllTagUrls()
    {
        $tagRepo = $this->getDoctrine()->getRepository(Tag::class);
        $tagTitles = $tagRepo->getTitles();
        $urls = [];

        foreach ($tagTitles as $tagTitle) {
            $url = $this->generateUrl('tag', ['tag' => $tagTitle]);

            $urls[] = $url;
        }

        return $urls;
    }

    /**
     * @param string $urlPrefix
     * @param string $url
     *
     * @return string
     */
    private function generateOutputFilePath($urlPrefix, $url)
    {
        $urlPrefixInfo = parse_url($urlPrefix);
        $urlInfo = parse_url($url);

        $prefixPath = empty($urlPrefixInfo['path']) ? '' : $urlPrefixInfo['path'];
        $prefixPath = '/'.trim($prefixPath, ' /');
        $path = empty($urlInfo['path']) ? '' : $urlInfo['path'];
        $path = '/'.trim($path, ' /');

        if (0 === strpos($prefixPath, $path)) {
            $path = substr($path, strlen($prefixPath));
            $path = '/'.trim($path, ' /');
        }

        return $path;
    }

    /**
     * @return string
     */
    private function getDeployDir()
    {
        return $this->getParameter('kernel.project_dir').'/var/deploy';
    }
}
