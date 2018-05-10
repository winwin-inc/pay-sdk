<?php
namespace winwin\pay\sdk\requests;

use winwin\pay\sdk\support\Attribute;

class RefundQuery extends Attribute
{
    protected $attributes = [
        'out_trade_no',
        'transaction_id',
        'out_refund_no',
        'refund_id',
    ];

    protected $requirements = [
    ];
}
