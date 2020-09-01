<?php
  include 'commons.php';

  verify_request_type('POST');

  $user_id = 0;
  $contact_id = 0;
  $first_name = "";
  $last_name = "";
  $email = "";
  $phone_number = "";

  $indata = decode_JSON_request();

  $contact_id = $indata["contact_id"];
  $user_id = $indata["user_id"];
  $first_name = $indata["first_name"];
  $last_name = $indata["last_name"];
  $email = $indata["email"];
  $phone_number = $indata["phone_number"];

  $conn = connect_to_db();

  $sql = create_sql_query($contact_id, $user_id, $first_name, $last_name, $email, $phone_number);

  if($conn->multi_query($sql)) {
    send_json_response($STATUS_SUCCESS, (object)array(
        'data' => NULL,
        'error' => 'Contact successfully updated',
    ));
  } else {
    send_json_response($STATUS_INTERNAL_ERROR, (object)array(
        'data' => NULL,
        'error' => 'Error',
    ));
  }

  ///////////////////////////
  // Function Declarations //
  ///////////////////////////

  // formulates the sql search query
  function create_sql_query($contact_id, $user_id, $first_name, $last_name, $email, $phone_number) {
    $sqlCount = 0;
    $sql = "";
    if($first_name != "") {
      $sql .= "UPDATE `contacts` SET `first_name` = '" . $first_name . "' WHERE `user_id` = " . $user_id . " and `contact_id` = " . $contact_id;
      $sqlCount++;
    }
    if($last_name != "") {
      if($sqlCount > 0) {
        $sql .= ";";
      }
      $sql .= "UPDATE `contacts` SET `last_name` = '" . $last_name . "' WHERE `user_id` = " . $user_id . " and `contact_id` = " . $contact_id;
      $sqlCount++;
    }
    if($email != "") {
      if($sqlCount > 0) {
        $sql .= ";";
      }
      $sql .= "UPDATE `contacts` SET `email` = '" . $email . "' WHERE `user_id` = " . $user_id . " and `contact_id` = " . $contact_id;
      $sqlCount++;
    }
    if($phone_number != "") {
      if($sqlCount > 0) {
        $sql .= ";";
      }
      $sql .= "UPDATE `contacts` SET `phone_number` = '" . $phone_number . "' WHERE `user_id` = " . $user_id . " and `contact_id` = " . $contact_id;
      $sqlCount++;
    }
    return $sql;
  }
?>
