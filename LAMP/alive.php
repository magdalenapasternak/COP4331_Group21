<?php
    include 'commons.php';

    send_json_response(STATUS_SUCCESS, (object)array(
        'data' => 'https://www.youtube.com/embed/dQw4w9WgXcQ?autoplay=1',
        'error' => '',
    ));
?>