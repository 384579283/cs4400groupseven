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

    /**
     * Retrives every application status, indexed by id.
     */
    public function lookup_application_status() {

        return $this->get_lookup_table("APPLICATION_STATUS_LU");

    }

    /**
     * Retrives every citizenship option, indexed by id.
     */

    public function lookup_citizenship() {

        return $this->get_lookup_table("CITIZENSHIP_LU");

    }

    /**
     * Retrives every degree option, indexed by id.
     */
    public function lookup_degree() {

        return $this->get_lookup_table("DEGREE_LU");

    }

    /**
     * Retrives every industry, indexed by id.
     */
    public function lookup_industry() {

        return $this->get_lookup_table("INDUSTRY_LU");

    }

    /**
     * Retrives every job position type, indexed by id.
     */
    public function lookup_position_type() {

        return $this->get_lookup_table("POSITION_TYPE_LU");

    }

    /**
     * Retrives every test type, indexed by id.
     */
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

    /**
     * Validates the login for an applicant.
     *
     * If authenticated succeeds, return the applicant's id.
     * Otherwise, return false.
     */
    public function applicant_login($email, $password) {

        return $this->customer_login('APPLICANT', $email, $password);

    }

    /**
     * Validates the login for a recruiter.
     *
     * If authenticated succeeds, return the recruiter's id.
     * Otherwise, return false.
     */
    public function recruiter_login($email, $password) {

        return $this->customer_login('RECRUITER', $email, $password);

    }

    /**
     * Gets the name of a customer.
     *
     * @param $user_id
     *        the customer's id
     */
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

    /**
     * Gets the date of the earliest-posted job.
     * Used to determine how far back reports can be generated.
     */
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

    /**
     * Job search.
     *
     * @param $industry (optional)
     * @param $keywords array of keywords
     * @param $position_types array of position types
     * @param $minimum_salary (optional)
     */
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
                 AND  J.TITLE LIKE '%s' ",
            $keyword_string
        );

        if ($minimum_salary) {
            $query .= sprintf("
                     AND  J.MINIMUM_SALARY >= '%s'",
                    mysql_real_escape_string($minimum_salary)
            );

        }

        if ($industry) {
            $query .= sprintf("
                     AND  J.INDUSTRY = '%s'",
                    mysql_real_escape_string($industry)
            );
        }

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

    private function create_customer($password, $email, $name) {

        $this->doQuery(sprintf("
              INSERT  INTO  CUSTOMER (PASSWORD, EMAIL, NAME)
              VALUES  ('%s', '%s', '%s');",
            mysql_real_escape_string($password),
            mysql_real_escape_string($email),
            mysql_real_escape_string($name)
        ));

        return mysql_insert_id();

    }

    /**
     * Creates a new recruiter account.
     */
    public function create_recruiter($password, $email, $name,
            $company_name, $phone, $fax, $website, $description) {

        $this->transaction_start();

        // Insert the customer record
        $id = $this->create_customer($password, $email, $name);

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

    /**
     * Creates a new applicant account.
     */
    public function create_applicant($password, $email, $name,
            $phone, $degree, $experience, $citizenship, $birth, $description) {

        $this->transaction_start();

        // Insert the customer record
        $id = $this->create_customer($password, $email, $name);

        if (mysql_error()) {
            $this->transaction_rollback();
            return false;
        }

        // Insert the applicant record
        $this->doQuery(sprintf("
              INSERT  INTO APPLICANT (USER_ID, PHONE, HIGHEST_DEGREE,
                                      YEARS_EXPERIENCE, CITIZENSHIP,
                                      BIRTH_YEAR, DESCRIPTION)
              VALUES  ('%s', '%s', '%s', '%s', '%s', '%s', '%s');",
            $id,
            mysql_real_escape_string($phone),
            mysql_real_escape_string($degree),
            mysql_real_escape_string($experience),
            mysql_real_escape_string($citizenship),
            mysql_real_escape_string($birth),
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

    /**
     * Modifies an applicant account.
     */
    public function edit_applicant($user_id, $phone, $degree, $experience,
                                   $citizenship, $birth, $description) {

        $this->doQuery(sprintf("
              UPDATE  APPLICANT
                 SET  PHONE = '%s',
                      HIGHEST_DEGREE = '%s',
                      YEARS_EXPERIENCE = '%s',
                      CITIZENSHIP = '%s',
                      BIRTH_YEAR = '%s',
                      DESCRIPTION = '%s'
               WHERE  USER_ID = '%s';",
                mysql_real_escape_string($phone),
                mysql_real_escape_string($degree),
                mysql_real_escape_string($experience),
                mysql_real_escape_string($citizenship),
                mysql_real_escape_string($birth),
                mysql_real_escape_string($description),
                mysql_real_escape_string($user_id)
        ));

    }

    /**
     * Creates a new job.
     *
     * @param $posted_by the id of the recruiter posting the job
     * @param $title the jod title
     * @param $position_types [array]
     */
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
                mysql_real_escape_string($positions)
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
           WHERE  J.POSTED_BY = '%s'
             AND  J.ACTIVE = '1';",
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
                'requested' => $row['NUM_POSITIONS']
            );
        }

        return $jobs;

    }

    public function close_job($job_id) {

        $this->transaction_start();

        // TODO : fetch a list of application IDs for the job
        $applications = array();

        foreach ($applications as $application_id) {

            // TODO Decline the application

            if (mysql_error()) {
                $this->transaction_rollback();
                return false;
            }

        }

        // TODO Set job.active to false

        if (mysql_error()) {
            $this->transaction_rollback();
            return false;
        }

        $this->transaction_commit();

    }

    public function get_job($job_id) {

        $result = $this->doQuery(sprintf("
              SELECT  J.TITLE,
                      J.NUM_POSITIONS,
                      J.INDUSTRY,
                      J.MINIMUM_SALARY,
                      J.TEST_TYPE,
                      J.MIN_TEST_SCORE,
                      J.EMAIL,
                      J.FAX,
                      J.DESCRIPTION
                FROM  JOB J
               WHERE  J.JOB_ID = '%s';",
            mysql_real_escape_string($job_id)
        ));

        $row = mysql_fetch_assoc($result);

        if (!$row) {
            return false;
        }

        $job = array(
            'title' => $row['TITLE'],
            'positions' => $row['NUM_POSITIONS'],
            'industry' => $row['INDUSTRY'],
            'salary' => $row['MINIMUM_SALARY'],
            'test' => $row['TEST_TYPE'],
            'test_score' => $row['MIN_TEST_SCORE'],
            'email' => $row['EMAIL'],
            'fax' => $row['FAX'],
            'description' => $row['DESCRIPTION']
        );

        $result = $this->doQuery(sprintf("
              SELECT  T.POSITION_TYPE
                FROM  JOB_POSITION_TYPE T
               WHERE  T.JOB_ID = '%s'",
            mysql_real_escape_string($job_id)
        ));

        $position_types = array();
        while ($row = mysql_fetch_assoc($result)) {
            $position_types[] = $row['POSITION_TYPE'];
        }

        $job['position_types'] = $position_types;

        return $job;

    }

    public function update_test_score($application_id, $score) {

        $this->doQuery(sprintf("
              UPDATE  APPLICATION
                 SET  TEST_SCORE='%s'
               WHERE  APPLICATION_ID='%s';",
            mysql_real_escape_string($score),
            mysql_real_escape_string($application_id)
        ));

    }

    public function get_company($recruiter_id) {

        $result = $this->doQuery(sprintf("
              SELECT  R.COMPANY_NAME,
                      C.EMAIL,
                      C.PHONE,
                      R.FAX,
                      R.WEBSITE,
                      R.DESCRIPTION
                FROM  CUSTOMER C,
                      RECRUITER R
               WHERE  C.USER_ID = R.USER_ID
                 AND  R.USER_ID = '%s';",
            mysql_real_escape_string($recruiter_id)
        ));

        $row = mysql_fetch_assoc($result);

        if (!$row) {
            return false;
        }

        $company = array(
            'name' => $row['COMPANY_NAME'],
            'email' => $row['EMAIL'],
            'phone' => $row['PHONE'],
            'fax' => $row['FAX'],
            'website' => $row['WEBSITE'],
            'description' => $row['DESCRIPTION'],
        );

        return $company;

    }

    public function apply($applicant_id, $job_id) {

        $this->doQuery(sprintf("
              INSERT  INTO APPLICATION (APPLICANT_ID, JOB_ID, OPEN_DATE)
                      VALUES ('%s', '%s', '%s');",
                mysql_real_escape_string($applicant_id),
                mysql_real_escape_string($job_id),
                date("Y-m-d")
        ));

        $id = mysql_insert_id();

        return $id;

    }

    public function get_applications_for_applicant($applicant_id) {

        $result = $this->doQuery(sprintf("
              SELECT  J.TITLE,
                      R.COMPANY_NAME,
                      A.OPEN_DATE,
                      A.STATUS
                FROM  JOB J,
                      RECRUITER R,
                      APPLICATION A
               WHERE  J.POSTED_BY = R.USER_ID
                 AND  A.JOB_ID = J.JOB_ID
                 AND  A.APPLICANT_ID = '%s';",
                mysql_real_escape_string($applicant_id)
        ));

        $applications = array();

        while ($row = mysql_fetch_assoc($result)) {
            $applications[] = array(
                'title' => $row['TITLE'],
                'company' => $row['COMPANY_NAME'],
                'date_applied' => $row['OPEN_DATE'],
                'status' => $row['STATUS']
            );
        }

        return $applications;

    }



}

?>
