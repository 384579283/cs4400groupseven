<?

function redirect($url) {
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
