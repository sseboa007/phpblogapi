<?php
    class PublicBlogsController {
        private BlogService $service;
        private UserService $userService;

        public function __construct(BlogService $service, UserService $userService) {
            $this->service = $service;
            $this->userService = $userService;
        }

        public function processRequest(string $method, ?string $id): void { 
            if ($id) {
                $this->processResourceRequest($method, $id);
            }
            else {
                $this->processCollectionRequest($method);
            }
        }

        private function processResourceRequest(string $method, string $id) {
            $blog = $this->service->getBlog($id);

            switch ($method) {
                case "GET":
                    echo json_encode($blog);
                    break;
                
                default:
                    http_response_code(405);
                    header("Allow: GET");
                    break;
            }
        }

        private function processCollectionRequest(string $method): void { 
            switch ($method) {
                case "GET":
                    echo json_encode($this->service->getBlogs());
                    break;

                default:
                    http_response_code(405);
                    header("Allow: GET");
                    break;
            }
        }
    }
?>