<?php
  class AuthService {
    private UserService $service;

    public function __construct(UserService $service) {
      $this->service = $service;
    }

    public function signin(string $email, string $password) {

    }

    public function signup(string $firstName, string $lastName, string $email, string $password, int $roleId) {

    }
  }
?>