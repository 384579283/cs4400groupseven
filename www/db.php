<?php

$db = new Database();

class Database {

    private $dblink;

    function __construct() {

        $host = "localhost";
        $user = "CareerWorks";
        $pass = "pineapple";
        $db_name = "CareerWorks";

        // Connect to the DB server
        $dblink = @mysql_connect($host, $user, $pass);

        // Select the database
        mysql_select_db($db_name, $dblink);

        // Hold a reference to the DB server resource
        $this->dblink = $dblink;

    }

    private function doQuery($query) {

        // Execute the query
        $result = mysql_query($query, $this->dblink);

        // If the result is false, the query was bad
        if (!$result) {
            $message  = 'Invalid query: ' . mysql_error();
            die($message);
        }

        // Return the result
        return $result;

    }

    private function get_lookup_table($table) {

        // Fetch the entire table
        $result = $this->doQuery(sprintf("
              SELECT  ID,
                      NAME
                FROM  %s LU
            ORDER BY  ID ASC",
            $table
        ));

        // Pull the results into an array (map from ID to NAME)
        $arr = array();
        while ($row = mysql_fetch_assoc($result)) {
            $arr[$row['ID']] = $row['NAME'];
        }

        // Return the array
        return $arr;

    }

    public function lookup_application_status() {

        return $this->get_lookup_table("APPLICATION_STATUS_LU");

    }

    public function lookup_citizenship() {

        return $this->get_lookup_table("CITIZENSHIP_LU");

    }

    public function lookup_degree() {

        return $this->get_lookup_table("DEGREE_LU");

    }

    public function lookup_industry() {

        return $this->get_lookup_table("INDUSTRY_LU");

    }

    public function lookup_position_type() {

        return $this->get_lookup_table("POSITION_TYPE_LU");

    }

    public function lookup_test_type() {

        return $this->get_lookup_table("TEST_TYPE_LU");

    }

    public function verify_applicant_login($email, $password) {

        // Fetch the number of applicants which have this email and password
        $result = $this->doQuery(sprintf("
              SELECT  COUNT(*) AS SUCCESS
                FROM  CUSTOMER C,
                      APPLICANT A
               WHERE  C.USER_ID = A.USER_ID
                 AND  C.EMAIL = '%s'
                 AND  C.PASSWORD = '%s';",
            mysql_real_escape_string($email),
            mysql_real_escape_string($password)
        ));
        $row = mysql_fetch_assoc($result);

        // Successful login if one user has this email and password
        return ($row['SUCCESS'] == 1);

    }

}

?>

