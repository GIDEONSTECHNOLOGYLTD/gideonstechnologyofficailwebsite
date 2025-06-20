<?php
class Response {
    private static function send($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    public static function success($data = null, $message = 'Success', $code = 200) {
        self::send([
            'status' => 'success',
            'message' => $message,
            'data' => $data
        ], $code);
    }

    public static function error($message = 'Error', $code = 400, $errors = null) {
        self::send([
            'status' => 'error',
            'message' => $message,
            'errors' => $errors
        ], $code);
    }

    public static function unauthorized($message = 'Unauthorized') {
        self::error($message, 401);
    }

    public static function forbidden($message = 'Forbidden') {
        self::error($message, 403);
    }

    public static function notFound($message = 'Not Found') {
        self::error($message, 404);
    }

    public static function validation($errors, $message = 'Validation Error') {
        self::error($message, 422, $errors);
    }

    public static function serverError($message = 'Internal Server Error') {
        self::error($message, 500);
    }

    public static function created($data = null, $message = 'Resource Created') {
        self::success($data, $message, 201);
    }

    public static function accepted($message = 'Request Accepted') {
        self::success(null, $message, 202);
    }

    public static function noContent($message = 'No Content') {
        self::success(null, $message, 204);
    }
}