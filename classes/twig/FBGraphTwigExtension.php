<?php

namespace Grav\Plugin;

use Grav\Plugin\FBGraph\Client;
use Grav\Common\GravTrait;

require_once(__DIR__ . '/../Client.php');

class FBGraphTwigExtension extends \Twig_Extension
{
    use GravTrait;

    public function getName()
    {
        return 'FBGraphTwigExtension';
    }
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('fbgraph', [$this, 'getResourceData'])
        ];
    }
    public function getResourceData($resource)
    {
        $grav = static::getGrav();
        $client = new Client($grav['config']->get('plugins.fbgraph'));
        $client->setResource($resource);
        return $client->get();
    }
}
