<?php
  include 'Commons.php';

  verify_request_type('POST');

  $user_id = 0;
  $first_name = "";
  $last_name = "";
  $email = "";
  $phone_number = "";

  $indata = decode_JSON_request();

  $user_id = $indata["user_id"];
  $first_name = $indata["first_name"];
  $last_name = $indata["last_name"];
  $email = $indata["email"];
  $phone_number = $indata["phone_number"];

  if (!check_data($user_id, $first_name, $last_name, $email, $phone_number)) {
    send_json_response($STATUS_BAD_REQUEST, (object)array(
        'data' => NULL,
        'error' => 'Inproper entry',
    ));
  }

  $conn = connect_to_db();

  $sql = "INSERT INTO contacts ". "(user_id,first_name,last_name,email,phone_number) ".
    "VALUES('$user_id','$first_name','$last_name','$email','$phone_number')";

  if (mysqli_query($conn, $sql)) {
    send_json_response($STATUS_SUCCESS, (object)array(
        'data' => NULL,
        'error' => 'Entry created successfully',
    ));
  } else {
    echo "Error: " . $sql . "" . mysqli_error($conn);
  }
  mysqli_close($conn);

  ///////////////////////////
  // Function Declarations //
  ///////////////////////////

  // Checks to make sure data is authentic
  function check_data($user_id, $first_name, $last_name, $email, $phone_number) {
    if ($user_id == 0 || $first_name == "" || $last_name == "" || $email == "" || $phone_number == "") {
      return true;
    } else {
      return false;
    }
  }
?>
