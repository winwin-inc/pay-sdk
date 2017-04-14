<?php

namespace winwin\pay\sdk;

use winwin\support\Attribute;

class Config extends Attribute
{
    const VERSION = '1.0.0';
    const GATEWAY = 'https://api.17gaoda.com/pay/gateway';

    protected $attributes = [
        'gateway',
        'version',
        'appid',
        'secret',
        'charset',
        'sign_type',
    ];

    protected $requirements = [
        'gateway',
        'version',
        'appid',
        'secret'
    ];

    public function __construct(array $config)
    {
        parent::__construct(array_merge([
            'version' => self::VERSION,
            'gateway' => self::GATEWAY
        ], $config));
    }
}
