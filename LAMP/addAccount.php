<?php
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

    // Perform the SQL query to find all users with the username provided
    function perform_insert_query($conn, $username, $password, $firstName, $lastName) {
        $statement = $conn->prepare("INSERT INTO `users` (`username`, `password`, `first_name`, `last_name`) VALUES (?,?,?,?)");
        $statement->bind_param("ssss", $username, $password, $firstName, $lastName);
        $statement->execute();
        $statement->close();
    }

    verify_request_type('POST');

    $request = decode_JSON_request();
    verify_request_field($request, 'username');
    verify_request_field($request, 'password');
    verify_request_field($request, 'first_name');
    verify_request_field($request, 'last_name');

    $conn = connect_to_db();
    $existing = perform_user_query($conn, $request['username']);
    if (count($existing) > 0) {
        send_json_response(STATUS_SUCCESS, (object)array(
            'result' => 'EXISTING_ACCOUNT',
            'error'=> '',
        ));
    } else {
        perform_insert_query($conn, $request['username'], $request['password'], $request['first_name'], $request['last_name']);
        send_json_response(STATUS_SUCCESS, (object)array(
            'result' => 'SUCCESS',
            'error'=> '',
        ));
    }

    $conn->close();
?>