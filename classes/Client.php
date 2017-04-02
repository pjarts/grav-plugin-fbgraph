<?php

namespace Grav\Plugin\FBGraph;

use Grav\Common\GPM\Response;

class Client {
    protected $config;
    protected $resource;

    /**
     * Constructor
     * @param array $config Client config
     * @param array $resource Resource config
     */
    public function __construct($config, $resource = [])
    {
        $this->config = $config;
        $this->setResource($resource);
    }

    /**
     * Set a parameter on the resource
     * @param string $key
     * @param mixed $value
     * @return $this
     */
    public function setParam($key, $value)
    {
        $this->resource['params'][$key] = $value;
        return $this;
    }

    /**
     * Resource setter
     * @param array $resource
     * @return $this
     */
    public function setResource($resource)
    {
        $this->resource = $resource;
        $this->resource['params'] = $this->resource['params'] ?: [];
        return $this;
    }

    /**
     * Compose a query string from one ore more parameters
     * @param  mixed $params
     * @return string
     */
    protected function paramsToQueryString($params)
    {
        if (is_array($params) && key_exists('key', $params) && key_exists('value', $params)) {
            $str = $params['key'] . '{' . $this->paramsToQueryString($params['value']) . '}';
            return $str;
        } elseif (is_array($params)) {
            return join(',', array_map([$this, 'paramsToQueryString'], $params));
        } elseif (is_scalar($params)) {
            return "$params";
        } else {
            return null;
        }
    }

    /**
     * Compose an authentication string from app_id and secret
     * @return string
     */
    protected function getAuthParam()
    {
        return $this->config['app_id'] . '|' . $this->config['secret'];
    }

    /**
     * Build a URI from a resource config object
     * @return string
     */
    protected function buildUri()
    {
        $pageId = $this->resource['page_id'];
        $edge = $this->resource['edge'];
        $url = $this->config['host'] . $pageId . $edge;
        $params = [];
        foreach ($this->resource['params'] as $param => $val) {
            $params[$param] = $this->paramsToQueryString($val);
        }
        $params['access_token'] = $this->getAuthParam();
        $url .= '?' . http_build_query($params);
        return $url;
    }

    /**
     * Send a GET request on a configured resource
     * @return string JSON response
     */
    public function get()
    {
        $url = $this->buildUri();
        $json = Response::get($url);
        return $json;
    }
}
