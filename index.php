<?php
    declare(strict_types=1);

    spl_autoload_register(function ($class) {
        require __DIR__ . "/api/$class.php";
    });

    set_error_handler("ErrorHandler::handleError");
    set_exception_handler("ErrorHandler::handleException");

    header('Content-Type: application/json; charset=UTF-8');

    $parts = explode("/", $_SERVER["REQUEST_URI"]);

    if ($parts[2] != "users" && $parts[2] != "blogs" && $parts[2] != "roles" && $parts[2] != "auth") {
        http_response_code(404);
        exit;
    }
    
    $id = $parts[3] ?? null;
    $path = $parts[2];
    $controller = null;
    $service = null;

    $database = new Database("localhost", "blogapi", "root", "");
    $conn = $database->getConnection();

    switch ($path) {
        case 'users':
            $service = new Userservice($conn);
            $controller = new UsersController($service);
            break;
        case 'blogs':
            break;

        case 'roles':
            break;

        case 'auth':
            $service = new Userservice($conn);
            $authService = new AuthService($service);
            $controller = new AuthController($authService);            
            break;
    }

    if(!$controller) {
        http_response_code(404);
        echo json_encode(["message" => "Path Not Found"]);
    }

    $controller->processRequest($_SERVER["REQUEST_METHOD"], $id);