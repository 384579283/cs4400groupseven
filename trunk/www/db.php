<?php

$db = connect();

function connect() {

    $host = "localhost";
    $user = "CareerWorks";
    $pass = "pineapple";
    $db_name = "CareerWorks";

    // Connect to the DB server
    $db = @mysql_connect($host, $user, $pass);

    // Select the database
    mysql_select_db($db_name, $db);

    // Return a reference to the DB server resource
    return $db;

}

function doQuery($query) {

    // Execute the query
    $result = mysql_query($query);

    // If the result is false, the query was bad
    if (!$result) {
        $message  = 'Invalid query: ' . mysql_error();
        die($message);
    }

    // Return the result
    return $result;

}

function verify_applicant_login($email, $password) {

    $result = doQuery("
          SELECT  COUNT(*) AS SUCCESS
            FROM  CUSTOMER C,
                  APPLICANT A
           WHERE  C.USER_ID = A.USER_ID
             AND  C.EMAIL = '%s'
             AND  C.PASSWORD = '%s';",
        mysql_real_escape_string($email),
        mysql_real_escape_string($password)
    );

    $row = mysql_fetch_assoc($result);

    return ($row['SUCCESS'] == 1);

}

?>

