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

    private function transaction_start() {

        mysql_query("START TRANSACTION");

    }

    private function transaction_rollback() {

        mysql_query("ROLLBACK");

    }

    private function transaction_commit() {

        mysql_query("COMMIT");

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

    private function customer_login($table, $email, $password) {

        // Fetch the id of the user with this email and password
        $result = $this->doQuery(sprintf("
              SELECT  C.USER_ID
                FROM  CUSTOMER C,
                      %s A
               WHERE  C.USER_ID = A.USER_ID
                 AND  C.EMAIL = '%s'
                 AND  C.PASSWORD = '%s';",
            $table,
            mysql_real_escape_string($email),
            mysql_real_escape_string($password)
        ));
        $row = mysql_fetch_assoc($result);

        // If no such user exists, login fails
        if (!$row) {
            return false;
        }

        // Successful login, return the user id
        return $row['USER_ID'];

    }

    public function applicant_login($email, $password) {

        return $this->customer_login('APPLICANT', $email, $password);

    }

    public function recruiter_login($email, $password) {

        return $this->customer_login('RECRUITER', $email, $password);

    }

    public function get_customer_name($user_id) {

        $result = $this->doQuery(sprintf("
              SELECT  C.NAME
                FROM  CUSTOMER C
               WHERE  C.USER_ID = '%s';",
            mysql_real_escape_string($user_id)
        ));

        $row = mysql_fetch_assoc($result);

        return $row['NAME'];

    }

    public function earliest_job_date() {

        $result = $this->doQuery("
              SELECT  J.POST_DATE
                FROM  JOB J
            ORDER BY  POST_DATE ASC
               LIMIT  1");

        if ($row = mysql_fetch_assoc($result)) {
            return strtotime($row['POST_DATE']);
        } else {
            return time();
        }

    }

    public function search_jobs($industry, $keywords,
                                $position_types, $minimum_salary) {

        $keyword_string = '%';
        foreach($keywords as $word) {
            $keyword_string .= mysql_real_escape_string($word) . '%';
        }

        $query = sprintf("
              SELECT  J.TITLE,
                      R.COMPANY_NAME AS EMPLOYER,
                      I.INDUSTRY,
                      J.MINIMUM_SALARY
                FROM  JOB J,
                      RECRUITER R
               WHERE  J.POSTED_BY = R.USER_ID
                 AND  J.INDUSTRY = '%s'
                 AND  J.MINIMUM_SALARY >= '%s'
                 AND  J.TITLE LIKE '%s' ",
            mysql_real_escape_string($industry),
            mysql_real_escape_string($minimum_salary),
            $keyword_string
        );

        if (count($position_types) != 0) {
            $query .= " AND ( 0 = 1 ";
            foreach ($position_types as $type) {
                $query .= sprintf("
                     OR  J.JOB_ID IN (SELECT  T.JOB_ID
                                        FROM  JOB_POSITION_TYPE T
                                       WHERE  T.ID = '%s'",
                    mysql_real_escape_string($type)
                );
            }
            $query .= ")";
        }

        $query .= ';';

        $result = $this->doQuery($query);

        $search_results = array();

        while ($row = mysql_fetch_assoc($result)) {
            $search_results[] = array(
                'title' => $row['TITLE'],
                'employer' => $row['EMPLOYER'],
                'industry' => $row['INDUSTRY'],
                'minimum_salary' => $row['MINIMUM_SALARY']
            );
        }

        return $search_results;

    }

    public function create_recruiter($password, $email, $name,
            $company_name, $phone, $fax, $website, $description) {

        $this->transaction_start();

        // Insert the customer record
        $this->doQuery(sprintf("
              INSERT  INTO  CUSTOMER (PASSWORD, EMAIL, NAME)
              VALUES  ('%s', '%s', '%s');",
            mysql_real_escape_string($password),
            mysql_real_escape_string($email),
            mysql_real_escape_string($name)
        ));

        $id = mysql_insert_id();

        if (mysql_error()) {
            $this->transaction_rollback();
            return false;
        }

        // Insert the recruiter record
        $this->doQuery(sprintf("
              INSERT  INTO RECRUITER (USER_ID, COMPANY_NAME, PHONE,
                                      FAX, WEBSITE, DESCRIPTION)
              VALUES  ('%s', '%s', '%s', '%s', '%s', '%s');",
            $id,
            mysql_real_escape_string($company_name),
            mysql_real_escape_string($phone),
            mysql_real_escape_string($fax),
            mysql_real_escape_string($website),
            mysql_real_escape_string($description)
        ));

        if (mysql_error()) {
            $this->transaction_rollback();
            return false;
        }

        $this->transaction_commit();

        if (mysql_error()) {
            return false;
        }

        // Return the id of the inserted records
        return $id;

    }

    public function post_job($posted_by, $title, $description,
                             $industry, $minimum_salary, $test,
                             $minimum_score, $email, $phone,
                             $fax, $positions, $position_types) {

        $this->transaction_start();

        // Insert the job
        $this->doQuery(sprintf("
            INSERT  INTO JOB (POSTED_BY, POST_DATE,
                    TITLE, DESCRIPTION, INDUSTRY, MINIMUM_SALARY,
                    TEST_TYPE, MIN_TEST_SCORE, EMAIL, PHONE, FAX, 
                    NUM_POSITIONS)
          VALUES ('%s', '%s', '%s', '%s', '%s', '%s',
                  '%s', '%s', '%s', '%s', '%s', '%s');",
                mysql_real_escape_string($posted_by),
                date("Y-m-d"),
                mysql_real_escape_string($title),
                mysql_real_escape_string($description),
                mysql_real_escape_string($industry),
                mysql_real_escape_string($minimum_salary),
                mysql_real_escape_string($test),
                mysql_real_escape_string($minimum_score),
                mysql_real_escape_string($email),
                mysql_real_escape_string($phone),
                mysql_real_escape_string($fax),
                mysql_real_escape_string($num_positions)
        ));

        $id = mysql_insert_id();

        if ($err = mysql_error()) {
            $this->transaction_rollback();
            return false;
        }

        // Insert each position type
        foreach ($position_types as $type) {

            $this->doQuery(sprintf("
                INSERT INTO  JOB_POSITION_TYPE(JOB_ID, POSITION_TYPE)
                     VALUES  ('%s', '%s');",
                $id,
                mysql_real_escape_string($type)
            ));

            if (mysql_error()) {
                $this->transaction_rollback();
                return false;
            }

        }

        $this->transaction_commit();

        if (mysql_error()) {
            return false;
        }

        // Return the id of the inserted job
        return $id;

    }

    public function recruiter_status($user_id) {

        $result = $this->doQuery("

          SELECT  J.JOB_ID,
                  J.TITLE,
                  J.POST_DATE,
                  --# waiting for tests
                  (  SELECT COUNT(*)
                      FROM  APPLICATION NATURAL JOIN JOB
                     WHERE  STATUS = (  SELECT DISTINCT ID
                                         FROM  APPLICATION_STATUS_LU
                                        WHERE  UPPER(NAME) LIKE '%TEST%'))
                  AS WAITING_FOR_TESTS,
                  -- # waiting for interviews,
                  (  SELECT COUNT(*)
                      FROM  APPLICATION A NATURAL JOIN JOB
                     WHERE  A.STATUS = (  SELECT DISTINCT ID
                                         FROM  APPLICATION_STATUS_LU
                                        WHERE  UPPER(NAME) LIKE '%INTERVIEW%'))
                  AS WAITING_FOR_INTERVIEWS,
                  --# waiting for decisions,
                  (  SELECT COUNT(*)
                      FROM  APPLICATION A NATURAL JOIN JOB
                     WHERE  A.STATUS = (  SELECT DISTINCT ID
                                           FROM  APPLICATION_STATUS_LU
                                          WHERE  UPPER(NAME) LIKE '%DECISION%'))
                  AS WAITING_FOR_DECISIONS,
                  --# positions filled
                  (  SELECT COUNT(*)
                      FROM  APPLICATION A NATURAL JOIN JOB
                     WHERE  A.STATUS = (  SELECT DISTINCT ID
                                           FROM  APPLICATION_STATUS_LU
                                          WHERE  UPPER(NAME) LIKE '%ACCEPTED%'))
                  AS POSITIONS_FILLED,
                  J.NUM_POSITIONS,
                  J.POST_DATE
            FROM  JOB J" . sprintf("
           WHERE  J.POSTED_BY = '%s';",
            mysql_real_escape_string($user_id)
        ));

        $jobs = array();

        while ($row = mysql_fetch_assoc($result)) {
            $jobs[] = array(
                'id' => $row['JOB_ID'],
                'title' => $row['TITLE'],
                'date' => strtotime($row['POST_DATE']),
                'status_test' => $row['WAITING_FOR_TESTS'],
                'status_interview' => $row['WAITING_FOR_INTERVIEWS'],
                'status_decision' => $row['WAITING_FOR_DECISIONS'],
                'status_filled' => $row['POSITIONS_FILLED'],
            );
        }

        return $jobs;

    }

}

?>
