<?php
namespace Grav\Plugin;

use Grav\Common\Plugin;
use Grav\Common\Page\Page;
use RocketTheme\Toolbox\Event\Event;
use Grav\Plugin\Fbgraph\Client;

/**
 * Class FBGraphPlugin
 * @package Grav\Plugin
 */
class FBGraphPlugin extends Plugin
{
    public static function getSubscribedEvents()
    {
        return [
            'onPluginsInitialized' => ['onPluginsInitialized', 0],
            'onTwigTemplatePaths' => ['onTwigTemplatePaths', 0],
        ];
    }

    protected function getResources()
    {
        return $this->config->get('plugins.fbgraph.resources');
    }

    /**
     * Add current directory to twig lookup paths.
     */
    public function onTwigTemplatePaths()
    {
        $this->grav['twig']->twig_paths[] = __DIR__ . '/templates';
    }

    public function onPageInitialized()
    {
        $uri = $this->grav['uri'];
        $resources = $this->getResources();
        $rsName =  $uri->query('resource');
        if (key_exists($rsName, $resources)) {
            require_once(__DIR__ . '/classes/Client.php');
            $client = new Client($this->config->get('plugins.fbgraph'));
            $client->setResource($resources[$rsName])
                ->setParam('before', $uri->query('before'))
                ->setParam('after', $uri->query('after'));
            $page = new Page();
            $page->init(new \SplFileInfo(__DIR__ . '/pages/fbg_response.md'));
            $page->setRawContent($client->get());
            unset($this->grav['page']);
            $this->grav['page'] = $page;
        }
    }

    public function onPluginsInitialized()
    {
        // Don't proceed if we are in the admin plugin
        if ($this->isAdmin()) {
            return;
        }
        $uri = $this->grav['uri'];
        $pluginRoute = $this->config->get('plugins.fbgraph.route');
        if ($uri->path() === $pluginRoute && strpos($uri->basename(), '.json') !== false) {
            $this->enable([
                'onPageInitialized' => ['onPageInitialized', 0]
            ]);
        }
        $this->enable([
            'onTwigInitialized' => ['onTwigInitialized', 0]
        ]);
    }

    public function onTwigInitialized(Event $e)
    {
        require_once(__DIR__ . '/classes/twig/FBGraphTwigExtension.php');
        $this->grav['twig']->twig->addExtension(new FBGraphTwigExtension());
    }
}
