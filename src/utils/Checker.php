<?php

namespace Src\Utils;
class Checker
{
    static function isFieldExist($request, $args)
    {
        foreach ($args as $field) {

            if (!(isset($request[$field]))) {
                return false;
            }
        }
        return true;
    }
}
