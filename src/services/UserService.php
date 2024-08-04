<?php

namespace Src\Services;

use Src\Models\Authentication;
use Src\Models\Users;
use Src\Config\DatabaseConnector;
use Src\Utils\Checker;
use Src\Utils\Response;
use Src\Utils\Filter;

class UserService
{
    private $authenticationModel;
    private $pdo;
    private $userModel;
    private $tokenService;
    private $filter;

    function __construct()
    {
        $this->pdo = (new DatabaseConnector())->getConnection();
        $this->authenticationModel = new Authentication($this->pdo);
        $this->userModel = new Users($this->pdo);
        $this->tokenService = new TokenService();
        $this->filter = new Filter("username", "email", "password");
    }

    function register($user)
    {
        if (!(Checker::isFieldExist($user, ["username", "email", "password", "confirm_password"]))) {
            return Response::payload(
                400,
                false,
                "username, email password and confirm_password is required"
            );
        }

        $errors = $this->validate($user);

        if (count($errors) > 0) {
            return Response::payload(
                400,
                false,
                "registration unsuccessful",
                errors: $errors
            );
        }

        $creation = $this->userModel->create($user);
        return $creation ? Response::payload(
            201,
            true,
            "registration successful",
            array("user_id" => $this->userModel->get($creation)),
            $errors
        ) : Response::payload(
            400,
            False,
            message: "Contact administrator (belenkentharold@gmail.com)",
        );
    }

    function getInformation($id)
    {
        $token = $this->tokenService->readEncodedToken();

        if (!$token) {
            return Response::payload(404, false, "unauthorized access");
        }

        $user = $this->userModel->get($id);

        if (!$user) {
            return Response::payload(404, false, "user not found");
        }

        return Response::payload(200, true, "found user", array("user" => $user));
    }

    function getAllUser()
    {
        $token = $this->tokenService->readEncodedToken();

        if (!$token) {
            return Response::payload(404, false, "unauthorized access");
        }

        $filterStr = $this->filter->getFilterStr();

        if (str_contains($filterStr, "unavailable") || str_contains($filterStr, "empty")) {
            return Response::payload(400, false, $filterStr);
        }

        $user = $this->userModel->getAll($filterStr);

        if (!$user) {
            return Response::payload(404, false, "user not found");
        }

        return Response::payload(200, true, "found user", array("user" => $user));
    }
    function deleteUser($id)
    {
        $matches = $this->tokenService->isTokenMatch($id);
        if (!$matches) {
            return Response::payload(401, false, "unauthorized access");
        }

        $isDeleted = $this->userModel->delete($id);

        if (!$isDeleted) {
            return Response::payload(500, false, "Deletion Unsuccessful");
        }

        return Response::payload(200, true, "Deletion successful");
    }

    function updateUser($id, $newUserInfo)
{
    $matches = $this->tokenService->isTokenMatch($id);
    if (!$matches) {
        return Response::payload(401, false, "Unauthorized access");
    }

    if (empty($newUserInfo)) {
        return Response::payload(400, false, "No fields found");
    }

    $errors = $this->validate($newUserInfo);

    if (!empty($errors)) {
        return Response::payload(400, false, "Update Unsuccessful", errors: $errors);
    }

    if (!$this->userModel->get($id)) {
        return Response::payload(404, false, "User not found");
    }

    $updated_user = $this->userModel->update($id, $newUserInfo);
    return $updated_user ? Response::payload(200, true, "Update successful", array("user" => $this->userModel->get($id)))
        : Response::payload(400, false, "Contact administrator (belenkentharold@gmail.com)");
}



    function uploadImage($id, $files)
    {

        $this->userModel->uploadUserAvatar($id, $files);
        return Response::payload(200, true, "Image uploaded successfully", array("user" => $this->userModel->get($id)));
    }

    function validate($user)
    {
        $errors = array();

        if (Checker::isFieldExist($user, ["username"])) {
            $isUsernameExist = $this->UsernameExist($user["username"]);

            if ($isUsernameExist) $errors["username"] = $isUsernameExist;
        }

        if (Checker::isFieldExist($user, ["email"])) {
            $isemailExist = $this->emailExist($user["email"]);
            $validateemail = $this->validateemailFormat($user["email"]);

            if ($isemailExist) $errors["email"] = $isemailExist;
            if ($validateemail) $errors["email"] = $validateemail;
        }

        if (Checker::isFieldExist($user, ["password"])) {
            $isConfirmPasswordMatch = $this->confirmPasswordDoesNotMatch($user["password"], $user["confirm_password"]);

            if ($isConfirmPasswordMatch) $errors["password1"] = $isConfirmPasswordMatch;
        }

        return $errors;
    }

    function UsernameExist($username)
    {
        $username = $this->authenticationModel->get("username", $username);
        return $username == true ? "username already exist" : false;
    }

    function emailExist($email)
    {
        $email = $this->authenticationModel->get("email", $email);
        return $email == true ? "email already exist" : false;
    }

    function validateEmailFormat($email)
    {
        return preg_match('/^[\w.-]+@[a-zA-Z\d.-]+\.[a-zA-Z]{2,}$/', $email) ? null : "please enter a valid email address";
    }

    function confirmPasswordDoesNotMatch($password, $password2)
    {
        return $password !== $password2 ? "password does not match" : false;
    }
    
}
