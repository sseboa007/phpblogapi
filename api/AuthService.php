<?php

  class AuthService {
    private UserService $service;

    public function __construct(UserService $service) {
      $this->service = $service;
    }

    public function signin(string $email, string $password) {
      $user = $this->service->getUserForLogin($email, $password);

      if (empty($user)) {
        return null;
      }

      $jwtGenerator = new JwtGenerator("&eiKn1_VP}y3");

      $jwt = $jwtGenerator->generateToken(["sub" => $user["UserId"], "role" => $user["RoleId"]], 60 * 20); // Valid for 20 minutes

      /*$valid = $jwtGenerator->validateToken($jwt);

      var_dump($valid);*/

      return $jwt;
    }

    public function signup(string $firstName, string $lastName, string $email, string $password, int $roleId) {

    }
  }

?>