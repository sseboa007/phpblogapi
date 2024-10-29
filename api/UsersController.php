<?php

    class UsersController {
        private UserService $service;

        public function __construct(private Userservice $userService) {
            $this->service = $userService;
        }

        public function processRequest(string $method, ?string $id): void {
            if ($id) {
                $this->processResourceRequest($method, $id);
            }
            else {
                $this->processCollectionRequest($method);
            }
        }

        private function processResourceRequest(string $method, ?string $id): void {
            $user = $this->service->getUser($id);

            if (!$user) {
                http_response_code(404);
                echo json_encode(["message" => "User not found"]);
                return;
            }

            switch ($method) {
                case "GET":
                    echo json_encode($user);
                    break;

                case "PATCH":
                    /*$data = (array) json_decode(file_get_contents("php://input"), true);

                    $errors = $this->getValidationErrors($data);

                    if(!empty($errors)) {
                        http_response_code(400);
                        echo json_encode(["errors" => $errors]);
                        break;
                    }

                    $id = $this->service->create($data);

                    echo json_encode(["id" => $id]);*/
                    break;

                case "DELETE":
                    break;

                default:
                    http_response_code(405);
                    header("Allow: GET, PATCH, DELETE");
            }

            
        }

        private function processCollectionRequest(string $method): void {
            switch($method) {
                case 'GET':
                    echo json_encode($this->service->getUsers());
                    break;

                case 'POST':
                    $data = (array) json_decode(file_get_contents("php://input"), true);

                    $errors = $this->getValidationErrors($data);

                    if(!empty($errors)) {
                        http_response_code(400);
                        echo json_encode(["errors" => $errors]);
                        break;
                    }

                    $id = $this->service->create($data);

                    echo json_encode(["id" => $id]);
                break;

                default:
                    http_response_code(405);
                    header("Allow: GET, POST");
            }
        }

        private function getValidationErrors(array $data): array {
            $errors = [];

            if (empty($data["firstName"])) {
                $errors[] = "FirstName is required";
            }

            if (empty($data["lastName"])) {
                $errors[] = "LastName is required";
            }

            if (empty($data["email"])) {
                $errors[] = "Email is required";
            }
            else {
                if (filter_var($data["email"], FILTER_VALIDATE_EMAIL) === false) {
                    $errors[] = "Please provide a valid email";
                }
            }

            if (empty($data["roleId"])) {
                $errors[] = "RoleId is required";
            }
            else {
                if (filter_var($data["roleId"], FILTER_VALIDATE_INT) === false) {
                    $errors[] = "Please provide a valid roleId";
                }
            }

            return $errors;
        }
    }