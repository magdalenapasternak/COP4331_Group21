<?php
    // Status Codes used by this endpoint 
    const STATUS_SUCCESS = 200;
    const STATUS_BAD_REQUEST = 400;
    const STATUS_UNAUTHORIZED = 401;
    const STATUS_INTERNAL_ERROR = 500;

    // Utility for sending back a json response
    function send_json_response($status, $data) {
        header('Content-Type: application/json');
        http_response_code($status);
        echo(json_encode($data));
    }

    // Decodes the json request body. I hope one was sent.
    function decode_JSON_request() {
        return json_decode(file_get_contents("php://input"), true);
    }

    // If a parameter is missing, sends an error response and quits
    function verify_request_field($request, $fieldName) {
        if (!isset($request[$fieldName])) {
            send_json_response(STATUS_BAD_REQUEST, (object)array(
                'data' => NULL,
                'error' => 'Request JSON must include a ' . $fieldName . ' field',
            ));
            exit();
        }
    }

    // If the request type is not what is expected, sends an error response and quits
    function verify_request_type($type) {
        if ($_SERVER['REQUEST_METHOD'] !== $type) {
            send_json_response(STATUS_BAD_REQUEST, (object)array(
                'data' => NULL,
                'error' => 'Must be a ' . $type . ' request',
            ));
            exit();
        }
    }

    // Attempts to connect to the database. On failure, it sends an error response and quits
    function connect_to_db() {
        $conn = new mysqli("10.0.0.4", "contacts_app", "#oigCH10*oq^", "contacts_app");
        if($conn->connect_error) {
            send_json_response(STATUS_INTERNAL_ERROR, (object)array(
                'data' => NULL,
                'error' => 'Could not connect to database',
            ));
            exit(); 
        } 
        return $conn;
    }

    // Perform the SQL query to find all users with the username provided
    function perform_user_query($conn, $username) {
        $statement = $conn->prepare("SELECT `user_id`, `username`, `password`, `first_name`, `last_name`, `created_at` FROM `users` WHERE `username`=?");
        $statement->bind_param("s", $username);
        $statement->execute();
        $results = $statement->get_result()->fetch_all(MYSQLI_ASSOC);
        $statement->close();
        return $results;
    }

    // Authenticates a password. This will use salts later.
    function verify_password($request, $record) {
        return $request['password'] === $record['password'];
    }

    function send_invalid_credentials() {
        send_json_response(STATUS_UNAUTHORIZED, (object)array(
            'data' => NULL,
            'error'=> 'Incorrect credentials',
        ));
    }

    verify_request_type('POST');
    
    $request = decode_JSON_request();
    verify_request_field($request, 'username');
    verify_request_field($request, 'password');

    $conn = connect_to_db();
    $results = perform_user_query($conn, $request['username']);
    if (count($results) > 0) {
        $result = $results[0];
        if (verify_password($request, $result)) {
            unset($result['password']);
            send_json_response(STATUS_SUCCESS, (object)array(
                'data' => $result,
                'error' => '',
            ));
        } else {
            send_invalid_credentials();
        }
    } else {
        send_invalid_credentials();
    }

    $conn->close();
?>