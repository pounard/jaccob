<?php

namespace Jaccob\MediaBundle\Twig;

use Jaccob\MediaBundle\Model\Media;
use Jaccob\MediaBundle\Util\MediaHelperAwareTrait;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use PommProject\Foundation\Pager;

class PommPagerExtension extends \Twig_Extension implements ContainerAwareInterface
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $container;

    /**
     * {@inheritdoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('pager', [$this, 'createPager'], [
                'is_safe' => ['html'],
                'needs_environment' => true,
            ]),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'jaccob_media_pomm_pager';
    }

    /**
     * Generate a responsive version of the image, including all configured
     * sizes
     *
     * @param \Twig_Environment $twig
     * @param string $routeName
     *   Route name.
     * @param mixed[] $routeArgs
     *   Route parameters
     * @param Pager $pager
     *   Pomm pager.
     * @param int $maxPages
     *   Number of pages to display before and after current page.
     * @param string $pageParam
     *   Change this if you have a GET parameter conflict name.
     *
     * @return string
     */
    public function createPager(\Twig_Environment $twig, $routeName, $routeArgs, Pager $pager, $maxPages = 3, $pageParam = 'page')
    {
        if ($pager->getLastPage() <= 1) {
            return;
        }

        /* @var $router \Symfony\Component\Routing\RouterInterface */
        $router = $this->container->get('router');
        $query = $this->container->get('request_stack')->getCurrentRequest()->request->all() + $routeArgs;

        $variables = ['pages' => []];

        $last = $pager->getLastPage();
        $page = $pager->getPage();
        $min  = max([1, $page - $maxPages]);
        $max  = min([$page + $maxPages, $last]);

        if ($pager->isNextPage()) {
            // @todo set 'first'
            // @todo set 'previous'
        }
        if ($pager->isPreviousPage()) {
            // @todo set 'next'
            // @todo set 'last'
        }

        // Add pages before.
        for ($i = $min; $i < $page; ++$i) {
            $variables['pages'][] = [
                'uri'   => $router->generate($routeName, [$pageParam => $i] + $query),
                'page'  => $i,
            ];
        }
        // Add page in disabled state.
        $variables['pages'][] = [
            'uri'     => $router->generate($routeName, [$pageParam => $page] + $query),
            'page'    => $page,
            'active'  => true,
        ];
        // Add pages after.
        for ($i = $page + 1; $i <= $max; ++$i) {
            $variables['pages'][] = [
                'uri'   => $router->generate($routeName, [$pageParam => $i] + $query),
                'page'  => $i,
            ];
        }

        return $twig->render('JaccobMediaBundle:Helper:pager.html.twig', $variables);
    }
}
