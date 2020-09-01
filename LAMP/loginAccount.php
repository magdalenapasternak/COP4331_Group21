<?php
    //TODO Should login return anything except a session/user_id?
    //     Or will the other pages fetch this information independently?

    include 'commons.php';

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
        send_json_response(STATUS_SUCCESS, (object)array(
            'data' => NULL,
            'result' =>  'INVALID_CREDENTIALS',
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
                'result' => 'SUCCESS',
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