<?php
namespace winwin\pay\sdk\payment;

use winwin\pay\sdk\support\Attribute;

class Order extends Attribute
{
    protected $attributes = [
        'method',
        'mch_id',
        'out_trade_no',
        'body',
        'openid',
        'total_fee',
        'spbill_create_ip',
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
        'method',
        'mch_id',
        'out_trade_no',
        'body',
        'total_fee',
        'spbill_create_ip',
        'notify_url',
    ];
}
