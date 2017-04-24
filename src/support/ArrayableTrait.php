<?php

namespace winwin\pay\sdk\support;

use function winwin\pay\sdk\support\snake_case;

trait ArrayableTrait
{
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    public function toArray($keySnakeCase = false)
    {
        $values = [];
        foreach (get_object_vars($this) as $key => $val) {
            if (isset($val)) {
                if ($keySnakeCase) {
                    $key = snake_case($key);
                }
                if ($val instanceof Arrayable) {
                    $values[$key] = $val->toArray($keySnakeCase);
                } else {
                    $values[$key] = $val;
                }
            }
        }

        return $values;
    }
}
