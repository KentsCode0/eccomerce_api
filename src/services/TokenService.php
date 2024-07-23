<?php

namespace Src\Services;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Src\Config\DatabaseConnector;
use Src\Models\Authentication;
use Src\Utils\Checker;
use Src\Utils\Response;
use Exception;
use DateTimeImmutable;

class TokenService
{
    private $authenticationModel;
    private $pdo;
    private $issuedAt;
    function __construct()
    {
        $this->pdo = (new DatabaseConnector())->getConnection();
        $this->authenticationModel = new Authentication($this->pdo);
        $this->issuedAt = new DateTimeImmutable();
    }
    function login($user)
    {
        if (!(Checker::isFieldExist($user, ["email", "password"]))) {
            return Response::payload(
                400,
                false,
                "email and password is required"
            );
        }

        $email = filter_var($user['email'], FILTER_SANITIZE_EMAIL);
        $password = $user['password'];

        $user = $this->authenticationModel->get("email", $email);
        $token = $this->create($user, $password);

        if (!$token) {
            return Response::payload(
                401,
                false,
                "Login failed",
                errors:"Incorrect email or password"
            );
        }

        unset($user['password']);
        return Response::payload(
            201,
            true,
            "Login successful",
            array("user_id" => $user["user_id"], "token" => $token)
        );
    }
    function create($user, $password)
    {

        if (!($user && password_verify($password, $user['password']))) {
            return false;
        }


        $payload = array(
            'iat'  => $this->issuedAt->getTimestamp(),
            'iss'  => "https://anuwrap.vercel.app/",     
            'nbf'  => $this->issuedAt->getTimestamp(),
            'exp'  => $this->issuedAt->modify('+1 day')->getTimestamp(),
            "user_id" => $user["user_id"],
        );

        $token = JWT::encode(
            $payload,
            $_ENV['SECRET_API_KEY'],
            "HS256"
        );
        

        return $token;
    }

    function readEncodedToken()
    {
        $headers = apache_request_headers();
        if (isset($headers['Authorization'])) {
            $token = str_replace('Bearer ', '',  $headers['Authorization']);

            $key = $_ENV['SECRET_API_KEY'];

            try {
                $token = JWT::decode($token, new Key($key, 'HS256'));
                $token = json_decode(json_encode(($token)), true);
                return $token;
            } catch (Exception $e) {
                error_log($e->getMessage());
            }
        } else {
            return null;
        }
    }
    function isTokenMatch($id)
    {
        $tokenService = new TokenService();
        $token = $tokenService->readEncodedToken();

        return $token && $token['user_id'] == $id;
    }
}
