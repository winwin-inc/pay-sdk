<?php

namespace winwin\pay\sdk\support;

interface Arrayable extends \JsonSerializable
{
    /**
     * 对象转换成数组.
     *
     * @param bool $keySnakeCase
     *
     * @return array
     */
    public function toArray($keySnakeCase = false);
}
