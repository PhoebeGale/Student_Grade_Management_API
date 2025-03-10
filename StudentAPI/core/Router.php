<?php

namespace core;

use controller\StudentController;

class Router {
    public static function handleRequest() {
        
        header("Access-Control-Allow-Origin: *");
        header("Content-Type: application/json; charset=UTF-8");

        $controller = new StudentController();

        $method = $_SERVER['REQUEST_METHOD'];
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        $segments = explode('/', trim($path, '/'));
        $id = $segments[2] ?? null;

        switch ($method) {
            case 'GET':
                if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                    if (isset($_GET['id'])) {
                        $controller->getStudentById($_GET['id']);
                    } else {
                        $controller->getAllStudents();
                    }
                }
                break;

            case 'POST':
                $input = json_decode(file_get_contents("php://input"), true);
                if (!empty($input)) {
                    $controller->createStudent($input); 
                } else {
                    echo json_encode(["error" => "Missing student data."]);
                }
                break;

            case 'PUT':
                if ($id) {
                    $input = json_decode(file_get_contents("php://input"), true);
                    if (!empty($input)) {
                        $controller->updateStudent($id, $input); 
                    } else {
                        echo json_encode(["error" => "Missing update data."]);
                    }
                } else {
                    echo json_encode(["error" => "Student ID is required."]);
                }
                break;

            case 'DELETE':
                if ($id) {
                    $controller->deleteStudent($id); 
                } else {
                    echo json_encode(["error" => "Student ID is required."]);
                }
                break;

            default:
                echo json_encode(["error" => "Invalid request method."]);
                break;
        }
    }
}