<?php
    class AuthController {
        private AuthService $service;

        public function __construct(AuthService $service) {
            $this->service = $service;
        }

        public function processRequest(string $method, string $path): void {
            if(empty($path)){
                http_response_code(404);
                echo json_encode(["message" => "Path not found"]);
                return;
            }

            switch($method) {
                case 'GET':
                    http_response_code(405);
                    header("Allow: POST");
                    break;

                case 'POST':
                    $this->processPost($path);
                    break;
            }
        }

        private function processPost(string $path) {
            $data = (array) json_decode(file_get_contents("php://input"), true);

            if (empty($data)) {
                http_response_code(400);
                return;
            }

            switch(strtolower($path)) {
                case 'login':
                    $this->processLogin($data["email"], $data["password"]);

                    break;

                case 'register':
                    break;

                default:
                    http_response_code(404);
                    echo json_encode(["message" => "Path not found"]);
                    break;
            }
        }

        private function processLogin(string $email, string $password) {
            $response = $this->service->signin($email, $password);

            if (empty($response) || $response == null) {
                http_response_code(400);
                echo json_encode(["message" => "Bad credentials provided"]);
                return;
            }

            echo json_encode(["token" => $response]);
        }
    }
?>