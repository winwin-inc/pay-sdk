<?php
namespace winwin\pay\sdk\requests;

use winwin\pay\sdk\support\Attribute;

class OrderQuery extends Attribute
{
    protected $attributes = [
        'out_trade_no',
        'transaction_id',
    ];

    protected $requirements = [
    ];
}
