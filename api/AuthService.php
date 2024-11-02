<?php

  class AuthService {
    private UserService $service;
    private JwtGenerator $jwtGenerator;

    public function __construct(UserService $service) {
      $this->service = $service;
      $this->jwtGenerator = new JwtGenerator("&eiKn1_VP}y3");
    }

    public function signin(string $email, string $password) {
      $user = $this->service->getUserForLogin($email, $password);

      if (empty($user)) {
        return null;
      }

      $jwt = $this->jwtGenerator->generateToken(["sub" => $user["UserId"], "role" => $user["RoleId"]], 60 * 120); // Valid for 2 hours

      return $jwt;
    }

    public function signup(string $firstName, string $lastName, string $email, string $password, int $roleId) {

    }

    public function decodeToken(string $token) {
      return $this->jwtGenerator->decodeToken($token);
    }
  }

?>