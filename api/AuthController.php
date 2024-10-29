<?php
    class AuthController {
        private AuthService $service;

        public function __construct(AuthService $service) {
            $this->service = $service;
        }

        public function processRequest(string $method, ?string $id): void {
            echo "AUTH CONTROLLER";
        }
    }
?>