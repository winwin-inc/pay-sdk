<?php

namespace winwin\pay\sdk\payment;

use winwin\pay\sdk\core\Http;
use winwin\pay\sdk\support\Util;
use winwin\pay\sdk\constants\TradeMethod;
use winwin\pay\sdk\Config;
use winwin\pay\sdk\requests\Order;
use winwin\pay\sdk\requests\OrderQuery;
use winwin\pay\sdk\requests\OrderReverse;
use winwin\pay\sdk\requests\OrderClose;
use winwin\pay\sdk\requests\Refund;
use winwin\pay\sdk\requests\RefundQuery;
use winwin\pay\sdk\support\XML;
use winwin\pay\sdk\support\Collection;
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
     * 微信JS支付预下单
     *
     * @param Order $order
     *
     * @return Collection
     */
    public function weixinPrepare(Order $order)
    {
        $order->method = TradeMethod::WEIXIN_JSAPI;
        if (is_null($order->spbill_create_ip)) {
            $order->spbill_create_ip = Util::getServerIp();
        }

        return $this->request($order->all());
    }

    /**
     * 支付宝JS支付预下单
     *
     * @param Order $order
     *
     * @return Collection
     */
    public function alipayPrepare(Order $order)
    {
        $order->method = TradeMethod::ALIPAY_JSAPI;
        if (is_null($order->spbill_create_ip)) {
            $order->spbill_create_ip = Util::getServerIp();
        }

        return $this->request($order->all());
    }

    /**
     * 微信扫码支付预下单
     *
     * @param Order $order
     *
     * @return Collection
     */
    public function weixinPrepareQr(Order $order)
    {
        $order->method = TradeMethod::WEIXIN_QR;
        if (is_null($order->spbill_create_ip)) {
            $order->spbill_create_ip = Util::getServerIp();
        }

        return $this->request($order->all());
    }

    /**
     * 支付宝扫码支付预下单
     *
     * @param Order $order
     *
     * @return Collection
     */
    public function alipayPrepareQr(Order $order)
    {
        $order->method = TradeMethod::ALIPAY_QR;
        if (is_null($order->spbill_create_ip)) {
            $order->spbill_create_ip = Util::getServerIp();
        }

        return $this->request($order->all());
    }

    /**
     * 关闭预下单
     *
     * @param OrderClose $close
     *
     * @return Collection
     */
    public function close(OrderClose $close)
    {
        $close->method = TradeMethod::CLOSE;
        return $this->request($close->all());
    }

    /**
     * 刷卡支付
     *
     * @param Order $order
     *
     * @return Collection
     */
    public function micropay(Order $order)
    {
        $order->method = TradeMethod::MICRO_PAY;
        if (is_null($order->spbill_create_ip)) {
            $order->spbill_create_ip = Util::getServerIp();
        }

        return $this->request($order->all());
    }

    /**
     * 撤消订单
     * @param OrderReverse $reverse
     * @return Collection
     */
    public function reverse(OrderReverse $reverse)
    {
        $reverse->method = TradeMethod::REVERSE;
        return $this->request($reverse->all());
    }


    /**
     * 订单查询
     *
     * @param OrderQuery $query
     * @param string $outTradeNo
     *
     * @return Collection
     */
    public function query(OrderQuery $query)
    {
        $query->method = TradeMethod::QUERY;
        return $this->request($query->all());
    }

    /**
     * 退款
     *
     * @param Refund $refund
     *
     * @return Collection
     */
    public function refund(Refund $refund)
    {
        $refund->method = TradeMethod::REFUND;
        return $this->request($refund->all());
    }

    /**
     * 退款查询
     *
     * @param RefundQuery $refundQuery
     *
     * @return Collection
     */
    public function queryRefund(RefundQuery $refundQuery)
    {
        $refundQuery->method = TradeMethod::REFUND_QUERY;
        return $this->request($refundQuery->all());
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
    public function request(array $params, $method = 'post', array $options = [], $returnResponse = false)
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
