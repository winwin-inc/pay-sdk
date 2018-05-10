<?php
namespace winwin\pay\sdk\requests;

use winwin\pay\sdk\support\Attribute;

class OrderClose extends Attribute
{
    protected $attributes = [
        'out_trade_no',
    ];

    protected $requirements = [
        'out_trade_no',
    ];
}
