<?

function stripslashes_deep($value) {
    $value = is_array($value)
              ? array_map('stripslashes_deep', $value)
              : stripslashes($value);

    return $value;
}

function kill_magic_quotes() {

    $magic_quotes =
    (
        (
              function_exists("get_magic_quotes_gpc")
           && get_magic_quotes_gpc()
        ) || (
              ini_get('magic_quotes_sybase')
           && strtolower(ini_get('magic_quotes_sybase')) != "off"
        )
    );

    if ($magic_quotes) {
        $_GET = stripslashes_deep($_GET);
        $_POST = stripslashes_deep($_POST);
        $_COOKIE = stripslashes_deep($_COOKIE);
    }

}

function months_from($month_str) {

    $months = array();

    $d = explode('-', $month_str);
    $year = intval($d[0]);
    $month = intval($d[1]);

    while ($year != date('Y') || $month != date('n')) {
        if ($month == 13) {
            $month = 1;
            $year++;
        }
        $m = $year . '-' . str_pad($month, 2, "0", STR_PAD_LEFT);
        $months[] = $m;
        $month++;
    }
    $months[] = current_month();

    return $months;

}

function current_month() {
    return date('Y-m');
}

function format_date($date) {
    return date("M j, Y", $date);
}

function get_login_type() {
    foreach (array('applicant', 'recruiter', 'admin') as $type) {
        if ($_SESSION[$type]) {
            return $type;
        }
    }
    return false;
}

function access($type) {

    session_start();

    $current_type = get_login_type();
    if ($current_type != $type) {
        echo "This page is for " . $type . "s only.<br/><br/>";
        if ($current_type) {
            echo "You are: " . $current_type;
        } else {
            echo "You are not logged in.";
        }
        exit();
    }

}


function access_applicant() {
    access('applicant');
}

function access_recruiter() {
    access('recruiter');
}

function access_admin() {
    access('admin');
}

function goto_continue($message, $url) {
    $GLOBALS['message'] = $message;
    $GLOBALS['url'] = $url;
    require('continue.php');
    session_write_close();
    exit();
}

function logout() {
    foreach (array('applicant', 'recruiter', 'admin', 'user_id') as $var) {
        $_SESSION[$var] = null;
    }
}

function login_applicant($user_id) {
    logout();
    $_SESSION['applicant'] = true;
    $_SESSION['user_id'] = $user_id;
    goto_continue('Login successful.', 'job_search.php');
}

function login_recruiter($user_id) {
    logout();
    $_SESSION['recruiter'] = true;
    $_SESSION['user_id'] = $user_id;
    goto_continue('Login successful.', 'recruiter_status.php');
}

function login_admin($user_id) {
    logout();
    $_SESSION['admin'] = true;
    $_SESSION['user_id'] = $user_id;
    goto_continue('Login successful.', 'industry_report.php');
}

function redirect($url) {
    session_write_close();
    header("Location: " . $url);
    exit();
}

function not_null_keys($array, $keys) {
    foreach ($keys as $k) {
        if (!isset($array[$k])) {
            return false;
        }
    }
    return true;
}

function register_keys($array, $keys) {
    if (!not_null_keys($array, $keys)) {
        return false;
    }
    foreach ($keys as $k) {
        $GLOBALS[$k] = $array[$k];
    }
    return true;
}

function register_post_keys() {
    $args = func_get_args();
    return register_keys($_POST, $args);
}

function register_get_keys() {
    $args = func_get_args();
    return register_keys($_GET, $args);
}

function register_optional_keys($array, $keys) {
    foreach ($keys as $k) {
        if (array_key_exists($k, $array)) {
            $GLOBALS[$k] = $array[$k];
        } else {
            $GLOBALS[$k] = null;
        }
    }
}

function register_optional_post_keys() {
    $args = func_get_args();
    return register_optional_keys($_POST, $args);
}

function register_optional_get_keys() {
    $args = func_get_args();
    return register_optional_keys($_GET, $args);
}

?>
