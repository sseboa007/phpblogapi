<?php
    class BlogsController {
        private BlogService $service;
        private AuthService $authService;

        public function __construct(BlogService $service, AuthService $authService) {
            $this->service = $service;
            $this->authService = $authService;
        }

        public function processRequest(string $method, ?string $id): void { 
            $headers = getallheaders();
            
            if (!$this->isAuthorized($headers)){
                return;
            }

            $decodedToken = $this->authService->decodeToken($headers['Authorization']);

            if ($id) {
                $this->processResourceRequest($method, $id, $decodedToken);
            }
            else {
                $this->processCollectionRequest($method, $decodedToken);
            }
        }

        private function processResourceRequest(string $method, string $id, object $decodedToken) {
            $blog = $this->service->getBlog($id);

            if (!$blog) {
                http_response_code(404);
                echo json_encode(["message" => "Blog not found"]);
                return;
            }

            if ($decodedToken->role != 1 && $decodedToken->sub != $blog->userId) {
                http_response_code(403);
                echo json_encode(["message" => "UnAuthorized"]);
                return;
            }

            switch ($method) {
                case "GET":
                    echo json_encode($blog);
                    break;
                
                case "PUT":
                    $data = (array) json_decode(file_get_contents("php://input"), true);
                    
                    $errors = $this->getValidationErrors($data);

                    if(!empty($errors)) {
                        http_response_code(400);
                        echo json_encode(["errors" => $errors]);
                        break;
                    }

                    $id = $this->service->update($data, $blog, $decodedToken->sub);

                    echo json_encode(["id" => $blog["BlogId"]]);
                    break;

                case "DELETE":
                    break;

                default:
                    http_response_code(405);
                    header("Allow: GET, PUT, DELETE");
                    break;
            }
        }

        private function processCollectionRequest(string $method, object $decodedToken): void {
            switch($method) {
                case 'GET':
                    echo json_encode($this->service->getBlogs($decodedToken));
                    break;

                case 'POST':
                    $data = (array) json_decode(file_get_contents("php://input"), true);

                    $errors = $this->getValidationErrors($data);

                    if(!empty($errors)) {
                        http_response_code(400);
                        echo json_encode(["errors" => $errors]);
                        break;
                    }

                    $id = $this->service->create($data, $decodedToken->sub);

                    echo json_encode(["id" => $id]);
                break;

                default:
                    http_response_code(405);
                    header("Allow: GET, POST");
                    break;
            }
        }

        private function isAuthorized($headers) {
            if (!array_key_exists('Authorization', $headers)) {
                http_response_code(403);
                echo json_encode(["message" => "Unauthorized"]);
                return false;
            }

            if (empty($headers['Authorization'])) {
                http_response_code(403);
                echo json_encode(["message" => "Unauthorized"]);
                return false;
            }

            if (!$this->startsWith(strtolower($headers['Authorization']), 'bearer')) {
                http_response_code(403);
                echo json_encode(["message" => "Unauthorized"]);
                return false;
            }

            return true;
        }

        private function startsWith($haystack, $needle) {
            $length = strlen( $needle );
            return substr( $haystack, 0, $length ) === $needle;
        }

        private function getValidationErrors(array $data): array {
            $errors = [];

            if (empty($data["title"])) {
                $errors[] = "Title is required";
            }

            if (empty($data["content"])) {
                $errors[] = "Content is required";
            }

            return $errors;
        }
    }
?>