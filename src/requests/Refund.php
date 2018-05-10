<?php
namespace winwin\pay\sdk\requests;

use winwin\pay\sdk\support\Attribute;

class Refund extends Attribute
{
    protected $attributes = [
        'out_refund_no',
        'total_fee',
        'refund_fee',
        'op_user_id',
    ];

    protected $requirements = [
        'out_refund_no',
        'total_fee',
        'refund_fee',
        'op_user_id',
    ];
}
