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
    return register_keys($_GET, func_get_args());
}

?>
