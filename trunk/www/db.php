<?php

$db = connect();

function connect() {
    $host = "localhost";
    $user = "CareerWorks";
    $pass = "pineapple";
    $db_name = "CareerWorks";
    $db = @mysql_connect($host, $user, $pass);
    mysql_select_db($db_name, $db);
    return $db;
}

function verify_login($username, $password) {
    $query = sprintf("
      SELECT  COUNT(*) AS SUCCESS
        FROM  CUSTOMER C,
              APPLICANT A
       WHERE  C.USER_ID = A.USER_ID
         AND  C.EMAIL = '<email>'
         AND  C.PASSWORD = '<password>';",
    );
}

?>

