<?php

namespace Src\Controllers;

use Src\Services\UserService;

class UsersController
{
    private $userService;
    function __construct()
    {
        $this->userService = new UserService();
    }
    function getUser($request)
    {
        $payload = $this->userService->getInformation($request["userId"]);

        if (array_key_exists("code", $payload)) {
            http_response_code($payload["code"]);
            unset($payload["code"]);
        }

        echo json_encode($payload);
    }

    function getAllUser()
    {
        $payload = $this->userService->getAllUser();

        if (array_key_exists("code", $payload)) {
            http_response_code($payload["code"]);
            unset($payload["code"]);
        }

        echo json_encode($payload);
    }
    function postUser()
    {
        $postData = json_decode(file_get_contents("php://input"));
        $postData = json_decode(json_encode($postData), true);
        $payload = $this->userService->register($postData);

        if (array_key_exists("code", $payload)) {
            http_response_code($payload["code"]);
            unset($payload["code"]);
        }

        echo json_encode($payload);
    }

    function deleteUser($request)
    {
        $payload = $this->userService->deleteUser($request["userId"]);
        if (array_key_exists("code", $payload)) {
            http_response_code($payload["code"]);
            unset($payload["code"]);
        }
        echo json_encode($payload);
    }
    function updateUser($request)
{
    $postData = json_decode(file_get_contents("php://input"), true);
    if (!is_array($postData)) {
        $postData = [];
    }

    $payload = $this->userService->updateUser($request["userId"], $postData);

    if (array_key_exists("code", $payload)) {
        http_response_code($payload["code"]);
        unset($payload["code"]);
    }

    echo json_encode($payload);
}


    function uploadImage($request){
        $payload = $this->userService->uploadImage($request["userId"], $_FILES);


        if (array_key_exists("code", $payload)) {
            http_response_code($payload["code"]);
            unset($payload["code"]);
        }

        echo json_encode($payload);
    }

}
