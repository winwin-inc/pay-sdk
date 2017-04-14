<?php

namespace winwin\pay\sdk\payment;

use winwin\pay\sdk\core\Http;
use winwin\pay\sdk\core\Util;
use winwin\pay\sdk\Config;
use winwin\support\XML;
use winwin\support\Collection;
use Psr\Http\Message\ResponseInterface;

class API
{
    /**
     * Http instance.
     *
     * @var Http
     */
    protected $http;

    /**
     * @var Config
     */
    protected $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * 获取接口配置
     *
     * @return Config
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * 获取 Http 客户端
     * 
     * @return Http
     */
    public function getHttp()
    {
        if ($this->http === null) {
            $this->setHttp(new Http());
        }
        return $this->http;
    }

    /**
     * 设置 Http 客户端
     * 
     * @param Http
     *
     * @return static
     */
    public function setHttp(Http $http)
    {
        $this->http = $http;

        return $this;
    }

    /**
     * Prepare order to pay.
     *
     * @param Order $order
     *
     * @return Collection
     */
    public function prepare(Order $order)
    {
        if (is_null($order->spbill_create_ip)) {
            $order->spbill_create_ip = Util::getServerIp();
        }

        return $this->request($order->all());
    }

    /**
     * Make a API request.
     *
     * @param string $api
     * @param array  $params
     * @param string $method
     * @param array  $options
     * @param bool   $returnResponse
     *
     * @return Collection|\Psr\Http\Message\ResponseInterface
     */
    protected function request(array $params, $method = 'post', array $options = [], $returnResponse = false)
    {
        $params = array_merge($params, $this->config->only(['appid', 'charset', 'sign_type', 'version']));

        $params['nonce_str'] = uniqid();
        $params = array_filter($params, function($val) {
            return isset($val) && $val !== '';
        });
        $params['sign'] = Util::generateSign($params, $this->config->secret, $this->config->sign_type ?: 'md5');

        $options = array_merge([
            'body' => XML::build($params),
        ], $options);

        $response = $this->getHttp()->request($method, $this->config->gateway, $options);

        return $returnResponse ? $response : $this->parseResponse($response);
    }

    /**
     * Parse Response XML to array.
     *
     * @param ResponseInterface $response
     *
     * @return Collection
     */
    protected function parseResponse($response)
    {
        if ($response instanceof ResponseInterface) {
            $response = $response->getBody();
        }

        return new Collection((array) XML::parse($response));
    }
}
