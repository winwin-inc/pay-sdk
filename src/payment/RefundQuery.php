<?php
namespace winwin\pay\sdk\payment;

use winwin\pay\sdk\support\Attribute;

class RefundQuery extends Attribute
{
    protected $attributes = [
        'method',
        'mch_id',
        'out_trade_no',
        'transaction_id',
        'out_refund_no',
        'refund_id',
    ];

    protected $requirements = [
        'method',
        'mch_id',
    ];
}
