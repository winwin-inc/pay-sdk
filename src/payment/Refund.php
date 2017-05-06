<?php
namespace winwin\pay\sdk\payment;

use winwin\pay\sdk\support\Attribute;

class Refund extends Attribute
{
    protected $attributes = [
        'method',
        'mch_id',
        'out_trade_no',
        'transaction_id',
        'out_refund_no',
        'total_fee',
        'refund_fee',
        'op_user_id',
    ];

    protected $requirements = [
        'method',
        'mch_id',
        'out_refund_no',
        'total_fee',
        'refund_fee',
        'op_user_id',
    ];
}
