<?php
  // Status Codes used by this endpoint //
  $STATUS_SUCCESS = 200;
  $STATUS_BAD_REQUEST = 400;
  $STATUS_UNAUTHORIZED = 401;
  $STATUS_INTERNAL_ERROR = 500;
  ////////////////////////////////////////

  $user_id = 0;
  $first_name = "";
  $last_name = "";
  $email = "";
  $phone_number = "";

  $indata = getRequestInfo();

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

  // Check to make sure data is legit
  function check_data($user_id, $first_name, $last_name, $email, $phone_number) {
    if ($user_id == 0 || $first_name == "" || $last_name == "" || $email == "" || $phone_number == "") {
      return true;
    } else {
      return false;
    }
  }

  // Attempts to connect to the database. On failure, it sends an error response and quits
  function connect_to_db() {
      $conn = new mysqli("10.0.0.4", "contacts_app", "#oigCH10*oq^", "contacts_app");
      if($conn->connect_error) {
          send_json_response($STATUS_INTERNAL_ERROR, (object)array(
              'data' => NULL,
              'error' => 'Could not connect to database',
          ));
          exit();
      }
      return $conn;
  }

  // decodes json
  function getRequestInfo() {
		return json_decode(file_get_contents('php://input'), true);
	}

  // Utility for sending back a json response
  function send_json_response($status, $data) {
      header('Content-Type: application/json');
      http_response_code($status);
      echo(json_encode($data));
  }
?>
