<?php

session_start();
include "../inc/dbinfo.inc";

// Unset all session variables
$_SESSION = array();
// Destroy the session
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}
// Finally, destroy the session
session_destroy();
// Close the database connection
$connection = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD);
if ($connection) {
    mysqli_close($connection);
}
// Redirect to the home page
header("Location: elearning.php");
exit();

?>