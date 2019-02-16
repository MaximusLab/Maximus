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

use GitWrapper\GitWrapper;
use Maximus\Entity\Article;
use Maximus\Entity\Tag;
use Maximus\Routing\Generator\ArticleUrlGenerator;
use Maximus\Setting\Settings;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/console/deploy", name="console_deploy_")
 */
class DeployController extends AbstractController
{
    /**
     * Prepare parameters for deploying
     *
     * @Route("/parameters", name="parameters", methods={"GET"})
     *
     * @param Settings $settings
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function parameters(Settings $settings)
    {
        return new JsonResponse([
            'htmlUrls' => array_merge(
                $this->getAllMenuUrls($settings),
                $this->getAllArticleUrls(),
                $this->getAllTagUrls()
            ),
            'deleteAssetUrl' => $this->generateUrl('console_deploy_delete_assets'),
            'copyAssetUrl' => $this->generateUrl('console_deploy_copy_assets'),
            'generateFileUrl' => $this->generateUrl('console_deploy_generate_file'),
            'prepareGitRepoUrl' => $this->generateUrl('console_deploy_prepare_git_repository'),
            'pushUrl' => $this->generateUrl('console_deploy_push'),
        ]);
    }

    /**
     * Prepare Git Repository config
     *
     * @Route("/prepare-git-repository", name="prepare_git_repository", methods={"POST"})
     *
     * @param Settings $settings
     *
     * @return JsonResponse
     */
    public function prepareGitRepository(Settings $settings)
    {
        $git = $this->getGit($settings);

        $git->init();
        $git->clean('-f', '-d');

        try {
            $git->remote('add', 'origin', $settings->getGitRepositoryUrl());
        } catch (\Exception $e) {
        }

        $git->config('user.name', $settings->getGitAuthorName());
        $git->config('user.email', $settings->getGitAuthorEmail());

        $git->fetch('origin', 'master');
        $git->checkout('master');

        return new JsonResponse(['success' => true]);
    }

    /**
     * Delete assets that never exists
     *
     * @Route("/delete-assets", name="delete_assets", methods={"POST"})
     *
     * @param Settings $settings
     *
     * @return JsonResponse
     */
    public function deleteAssets(Settings $settings)
    {
        /** @var SplFileInfo $file */
        $oldAssets = [];
        $newAssets = [];
        $excludes = $settings->getExcludeAssets();
        $deleteAssets = [];
        $git = $this->getGit($settings);

        foreach ((new Finder())->files()->in($this->getDeployDir()) as $file) {
            $path = $file->getRelativePathname();
            $path = str_replace('\\', '/', $path);
            $path = '/'.ltrim($path, '/ ');
            $oldAssets[] = $path;
        }

        foreach ($this->getDeployAssetDirs($settings) as $dir) {
            if (is_dir($dir['source'])) {
                $files = (new Finder())->ignoreUnreadableDirs()->files()->in($dir['source']);

                if (!empty($dir['exclude'])) {
                    $files->exclude($dir['exclude']);
                }

                foreach ($files as $file) {
                    $path = rtrim($dir['target'], '/ ').'/'.$file->getRelativePathname();
                    $path = str_replace('\\', '/', $path);
                    $path = '/'.ltrim($path, '/ ');
                    $newAssets[] = $path;
                }
            }
        }

        foreach ($this->getAllArticleUrls() as $url) {
            if ('.md' === substr($url, -3)) {
                $newAssets[] = $url.'.txt';
            } else {
                $newAssets[] = $url;
            }
        }

        foreach (array_merge($this->getAllMenuUrls($settings), $this->getAllTagUrls()) as $url) {
            $path = parse_url($url, PHP_URL_PATH);
            $path = rtrim($path, '/ ').'/index.html';
            $newAssets[] = $path;
        }

        foreach (array_diff($oldAssets, $newAssets) as $path) {
            if (!in_array($path, $excludes)) {
                $deleteAssets[] = $path;
            }
        }

        if (!empty($deleteAssets)) {
            foreach ($deleteAssets as $path) {
                $git->rm(substr($path, 1), ['cached' => true]);
                unlink($this->getDeployDir().$path);
            }

            $git->commit('Delete files at ' . date('Y-m-d H:i:s'));
        }

        return new JsonResponse(['success' => true]);
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
        $fs = new Filesystem();

        foreach ($this->getDeployAssetDirs($settings) as $dir) {
            if (is_dir($dir['source'])) {
                $fs->mirror($dir['source'], $this->getDeployDir().$dir['target']);
            }
        }

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
        $router = $this->get('router');
        $url = $request->request->get('url');
        $html = $request->request->get('html');
        $format = 'html';

        if ('.html' === substr($url, -5)) {
            $url = substr($url, 0, -5);
        } elseif ('.md' === substr($url, -3)) {
            $format = 'md.txt';
            $url = substr($url, 0, -3);
        }

        $dir = $this->getDeployDir().$this->generateOutputFilePath($settings->getUrlPrefix(), $url);
        $route = $router->match($url);

        if (!empty($route['_route']) && 'document' === $route['_route']) {
            $filePath = $dir.'.'.$format;
        } else {
            $filePath = $dir.'/index.html';
        }

        if (!file_exists($filePath) || md5_file($filePath) !== md5($html)) {
            $fs = new Filesystem();

            $fs->dumpFile($filePath, $html);
        }

        return new JsonResponse(['success' => true]);
    }

    /**
     * Push static files to remote (e.g., GitHub)
     *
     * @Route("/push", name="push", methods={"POST"})
     *
     * @param Settings $settings
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function push(Settings $settings)
    {
        $git = $this->getGit($settings);
        $push = true;

        try {
            $git->add('.');
            $git->commit('Update at ' . date('Y-m-d H:i:s'));
        } catch (\Exception $e) {
            $push = false;
        }

        if ($push) {
            try {
                $git->push('origin', 'master');
            } catch (\Exception $e) {
            }
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
        $articles = $articleRepo->getPublishedArticles();
        $articleUrlGenerator = $this->get(ArticleUrlGenerator::class);
        $urls = [];

        foreach ($articles as $article) {
            $urls[] = $articleUrlGenerator->generate($article, 'html');
            $urls[] = $articleUrlGenerator->generate($article, 'md');
        }

        return $urls;
    }

    /**
     * @return array
     */
    private function getAllTagUrls()
    {
        $tagRepo = $this->getDoctrine()->getRepository(Tag::class);
        $tagAliases = $tagRepo->getAliases();
        $urls = [];

        foreach ($tagAliases as $alias) {
            $url = $this->generateUrl('tag', ['alias' => $alias]);

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

        if (strlen($path) < strlen($prefixPath)) {
            $path = $prefixPath;
        }

        if ($path === $prefixPath) {
            return '';
        }

        if (0 === strpos($prefixPath, $path)) {
            $path = substr($path, strlen($prefixPath));
            $path = '/'.trim($path, ' /');
        }

        return $path;
    }

    /**
     * Get asset deploy directory pairs
     *
     * @param Settings $settings
     *
     * @return array
     */
    private function getDeployAssetDirs(Settings $settings)
    {
        $projectDir = $this->getParameter('kernel.project_dir');
        $themeDir = $projectDir.'/themes_installed/'.$settings->getTheme().'/public';
        $articleUploadPath = $settings->getUploadPath().Article::ARTICLE_UPLOAD_PATH;
        $articleUploadDir = $settings->getWebRoot().$articleUploadPath;
        $assetsDir = $settings->getWebRoot().'/assets';

        return [
            ['source' => $themeDir, 'target' => '/theme/'.$settings->getTheme(), 'exclude' => []],
            ['source' => $articleUploadDir, 'target' => $articleUploadPath, 'exclude' => []],
            ['source' => $assetsDir, 'target' => '/assets', 'exclude' => []],
        ];
    }

    /**
     * @return string
     */
    private function getDeployDir()
    {
        return $this->getParameter('kernel.project_dir').'/var/deploy';
    }

    /**
     * @param Settings $settings
     *
     * @return \GitWrapper\GitWorkingCopy
     */
    private function getGit(Settings $settings)
    {
        $gitWrapper = new GitWrapper($settings->getGitBinaryPath());

        $gitWrapper->setPrivateKey($settings->getGitSSHPrivateKeyPath());

        return $gitWrapper->workingCopy($this->getDeployDir());
    }

    public static function getSubscribedServices()
    {
        return array_merge(parent::getSubscribedServices(), [
            ArticleUrlGenerator::class => '?'.ArticleUrlGenerator::class,
        ]);
    }
}
