<?php
    //TODO Should login return anything except a session/user_id?
    //     Or will the other pages fetch this information independently?

    include 'commons.php';

    // Perform the SQL query to find all users with the username provided
    function perform_delete_query($conn, $contact_id) {
        $statement = $conn->prepare("DELETE FROM `contacts` WHERE `contact_id`=?");
        $statement->bind_param("i", $contact_id);
        $statement->execute();
        $rowsAffected = $conn->affected_rows;
        $statement->close();
        return $rowsAffected;
    }

    verify_request_type('POST');

    $request = decode_JSON_request();
    verify_request_field($request, 'contact_id');

    $conn = connect_to_db();
    $rowsAffected = perform_delete_query($conn, $request['contact_id']);
    if ($rowsAffected > 0) {
        send_json_response(STATUS_SUCCESS, (object)array(
            'result' => 'SUCCESS',
            'error'=> '',
        ));
    } else {
        send_json_response(STATUS_SUCCESS, (object)array(
            'result' => 'DOES_NOT_EXIST',
            'error'=> '',
        ));
    }

    $conn->close();
?>