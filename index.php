<?php
    declare(strict_types=1);

    spl_autoload_register(function ($class) {
        require __DIR__ . "/api/$class.php";
    });

    set_error_handler("ErrorHandler::handleError");
    set_exception_handler("ErrorHandler::handleException");

    header('Access-Control-Allow-Origin: *'); 
    header("Access-Control-Allow-Credentials: true");
    header('Access-Control-Max-Age: 1000');
    header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token , Authorization');

    if($_SERVER["REQUEST_METHOD"] == "OPTIONS")
    {
        if (isset($_SERVER["HTTP_ACCESS_CONTROL_REQUEST_METHOD"]))
            header("Access-Control-Allow-Methods: POST, GET, OPTIONS, DELETE, PUT"); //Make sure you remove those you do not want to support

        if (isset($_SERVER["HTTP_ACCESS_CONTROL_REQUEST_HEADERS"]))
            header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

        //Just exit with 200 OK with the above headers for OPTIONS method
        exit(0);
    }

    header('Content-Type: application/json; charset=UTF-8');

    $parts = explode("/", $_SERVER["REQUEST_URI"]);

    if ($parts[2] != "users" && $parts[2] != "blogs" && $parts[2] != "roles" && $parts[2] != "auth" && $parts[2] != "publicblogs") {
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

        case 'auth':
            $service = new Userservice($conn);
            $authService = new AuthService($service);
            $controller = new AuthController($authService);            
            break;

        case 'blogs':
            $service = new BlogService($conn);
            $userService = new Userservice($conn);
            $authService = new AuthService($userService);
            $controller = new BlogsController($service, $authService);
            break;

        case 'publicblogs':
            $service = new BlogService($conn);
            $userService = new Userservice($conn);
            $controller = new PublicBlogsController($service, $userService);
            break;
    }

    if(!$controller) {
        http_response_code(404);
        echo json_encode(["message" => "Path Not Found"]);
        return;
    }

    $controller->processRequest($_SERVER["REQUEST_METHOD"], $id);