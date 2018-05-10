<?php
namespace winwin\pay\sdk\requests;

use winwin\pay\sdk\support\Attribute;

class Order extends Attribute
{
    protected $attributes = [
        'out_trade_no',
        'body',
        'openid',
        'total_fee',
        'notify_url',
        'device_info',
        'attach',
        'time_start',
        'time_expire',
        'goods_tag',
        'limit_pay',
        'product_id',
    ];

    protected $requirements = [
        'out_trade_no',
        'body',
        'total_fee',
    ];
}
