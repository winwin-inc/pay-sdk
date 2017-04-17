<?php

namespace winwin\pay\sdk\payment;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use GuzzleHttp\Psr7\Response;
use winwin\pay\sdk\core\Util;
use winwin\pay\sdk\Config;
use winwin\pay\sdk\core\FaultException;
use winwin\support\XML;

class Payment
{
    /**
     * @var API
     */
    protected $api;

    /**
     * @var ServerRequestInterface
     */
    protected $request;

    /**
     * Constructor.
     *
     * @param API      $api
     */
    public function __construct(Config $config)
    {
        $this->api = new API($config);
    }

    public function getRequest()
    {
        if ($this->request === null) {
            $this->request = Util::createRequestFromGlobals();
        }
        return $this->request;
    }

    /**
     * Sets request
     *
     * @param ServerRequestInterface $request
     *
     * @return self
     */
    public function setRequest(ServerRequestInterface $request)
    {
        $this->request = $request;

        return $this;
    }

    /**
     * Return API instance.
     *
     * @return API
     */
    public function getAPI()
    {
        return $this->api;
    }

    public function __call($method, $args)
    {
        if (is_callable([$this->getAPI(), $method])) {
            return call_user_func_array([$this->api, $method], $args);
        }
    }

    public function handleNotify(callable $callback)
    {
        $notify = new Notify($this->getAPI()->getConfig(), $this->getRequest());

        if (!$notify->isValid()) {
            throw new FaultException('Invalid request payloads.', 400);
        }

        $notify = $notify->getNotify();
        $successful = $notify->get('result_code') === 'SUCCESS';

        $handleResult = call_user_func_array($callback, [$notify, $successful]);
        $result = [
            'version' => Config::VERSION,
        ];

        if ($handleResult === true) {
            $result['return_code'] = 'SUCCESS';
        } else {
            $result['return_code'] = 'FAIL';
            $result['return_msg'] = $handleResult;
        }

        return new Response(200, [], XML::build($result));
    }
}
