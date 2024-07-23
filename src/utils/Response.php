<?php
namespace Src\Utils;

class Response{
    static function payload($code, $success, $message, $data = "No Data Found", $errors = array()){

        
        return array(
            "code" => $code,
            "success" => $success,
            "message" => $message,
            "data" => $data,
            "errors" => $errors
        );
    } 
    
}