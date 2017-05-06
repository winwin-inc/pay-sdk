<?php
namespace winwin\pay\sdk\payment;

use winwin\pay\sdk\support\Attribute;

class OrderQuery extends Attribute
{
    protected $attributes = [
        'method',
        'mch_id',
        'out_trade_no',
        'transaction_id',
    ];

    protected $requirements = [
        'method',
        'mch_id',
    ];
}
