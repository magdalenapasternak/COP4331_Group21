<?php
  include 'Commons.php';
  verify_request_type('POST');

  $searchResults = "";
	$searchCount = 0;
  $i = 0;

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

  $conn = connect_to_db();

  $sql = create_sql_query($user_id, $first_name, $last_name, $email, $phone_number);

  $result = $conn->query($sql);
  if($result->num_rows > 0) {
    $results = $result->fetch_all(MYSQLI_ASSOC);
    send_json_response($STATUS_SUCCESS, (object)array(
        'data' => $results,
        'error' => '',
    ));
  } else {
    send_json_response($STATUS_SUCCESS, (object)array(
        'data' => NULL,
        'error' => 'No results matching',
    ));
  }

  ///////////////////////////
  // Function Declarations //
  ///////////////////////////

  // formulates the sql search query
  function create_sql_query($user_id, $first_name, $last_name, $email, $phone_number) {
    $sql = "SELECT `contact_id`, `user_id`, `first_name`, `last_name`, `email`, `phone_number`, `created_at` FROM `contacts` WHERE ";
    if($first_name != "") {
      $sql .= "first_name like '%" . $first_name . "%' and ";
    }
    if($last_name != "") {
      $sql .= "last_name like '%" . $last_name . "%' and ";
    }
    if($email != "") {
      $sql .= "email like '%" . $email . "%' and ";
    }
    if($phone_number != "") {
      $sql .= "phone_number like '%" . $phone_number . "%' and ";
    }
    $sql .= "user_id = " . $user_id;
    return $sql;
  }


  // Ignore me
  /*
  while($row = $result->fetch_assoc()) {
    if($searchCount > 0) {
      $searchResults .= ",";
    }
    $searchCount++;
    $searchResults .= 'contact_id: ' . '"' . $row["contact_id"] . '"' . ',' .
                      '"user_id": ' . "'" . $row["user_id"] . "'" . ',' .
                      'first_name: ' . '"' . $row["first_name"] . '"' . ',' .
                      'last_name: ' . '"' . $row["last_name"] . '"' . ',' .
                      'email: ' . '"' . $row["email"] . '"' . ',' .
                      'phone_number: ' . '"' . $row["phone_number"] . '"';
  }
  send_json_response($STATUS_SUCCESS, (object)array(
      'data' => $searchResults,
      'error' => '',
  ));
  */

?>
