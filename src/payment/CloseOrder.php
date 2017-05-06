<?php
namespace winwin\pay\sdk\payment;

use winwin\pay\sdk\support\Attribute;

class CloseOrder extends Attribute
{
    protected $attributes = [
        'method',
        'mch_id',
        'out_trade_no',
    ];

    protected $requirements = [
        'method',
        'mch_id',
        'out_trade_no',
    ];
}
