class ApiResponse {
    private static $statusCodes = [
        'success' => 200,
        'created' => 201,
        'accepted' => 202,
        'no_content' => 204,
        'bad_request' => 400,
        'unauthorized' => 401,
        'forbidden' => 403,
        'not_found' => 404,
        'validation_error' => 422,
        'server_error' => 500
    ];

    public static function send($data = null, $status = 'success', $message = null, $meta = []) {
        $statusCode = self::$statusCodes[$status] ?? 200;
        
        $response = [
            'status' => $status,
            'code' => $statusCode
        ];

        if ($message !== null) {
            $response['message'] = $message;
        }

        if ($data !== null) {
            $response['data'] = $data;
        }

        if (!empty($meta)) {
            $response['meta'] = $meta;
        }

        header('Content-Type: application/json');
        http_response_code($statusCode);
        echo json_encode($response, JSON_PRETTY_PRINT);
        exit;
    }

    public static function success($data = null, $message = 'Success', $meta = []) {
        return self::send($data, 'success', $message, $meta);
    }

    public static function created($data = null, $message = 'Resource created successfully') {
        return self::send($data, 'created', $message);
    }

    public static function error($message, $status = 'server_error', $errors = [], $code = null) {
        $response = [
            'status' => $status,
            'code' => $code ?? self::$statusCodes[$status],
            'message' => $message
        ];

        if (!empty($errors)) {
            $response['errors'] = $errors;
        }

        header('Content-Type: application/json');
        http_response_code($response['code']);
        echo json_encode($response, JSON_PRETTY_PRINT);
        exit;
    }

    public static function validation($errors, $message = 'Validation failed') {
        return self::error($message, 'validation_error', $errors);
    }

    public static function notFound($message = 'Resource not found') {
        return self::error($message, 'not_found');
    }

    public static function unauthorized($message = 'Unauthorized access') {
        return self::error($message, 'unauthorized');
    }

    public static function forbidden($message = 'Access forbidden') {
        return self::error($message, 'forbidden');
    }

    public static function collection($data, $total, $page, $perPage) {
        $lastPage = ceil($total / $perPage);
        
        return self::success($data, null, [
            'pagination' => [
                'total' => $total,
                'per_page' => $perPage,
                'current_page' => $page,
                'last_page' => $lastPage,
                'from' => ($page - 1) * $perPage + 1,
                'to' => min($page * $perPage, $total)
            ]
        ]);
    }

    public static function resource($data, $includes = []) {
        if (!empty($includes)) {
            $data = self::loadIncludes($data, $includes);
        }
        return self::success($data);
    }

    private static function loadIncludes($data, $includes) {
        foreach ($includes as $include) {
            if (method_exists($data, $include)) {
                $data[$include] = $data->$include();
            }
        }
        return $data;
    }
}