<?php
     // Status Codes used by this endpoint 
     const STATUS_SUCCESS = 200;
     const STATUS_BAD_REQUEST = 400;
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
?>