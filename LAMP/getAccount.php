<?php
    include 'commons.php';

    // Perform the SQL query to find all users with the username provided
    function perform_user_query($conn, $user_id) {
        $statement = $conn->prepare("SELECT `user_id`, `username`, `password`, `first_name`, `last_name`, `created_at` FROM `users` WHERE `user_id`=?");
        $statement->bind_param("i", $user_id);
        $statement->execute();
        $results = $statement->get_result()->fetch_all(MYSQLI_ASSOC);
        $statement->close();
        return $results;
    }

    verify_request_type('POST');

    $request = decode_JSON_request();
    verify_request_field($request, 'user_id');

    $conn = connect_to_db();
    $results = perform_user_query($conn, $request['user_id']);
    if (count($results) > 0) {
        $result = $results[0];
        unset($result['password']);
        send_json_response(STATUS_SUCCESS, (object)array(
            'data' => $result,
            'result' => 'SUCCESS',
            'error' => '',
        ));
    } else {
        send_json_response(STATUS_SUCCESS, (object)array(
            'data' => NULL,
            'result' => 'DOES_NOT_EXIST',
            'error' => '',
        ));
    }

    $conn->close();
?>