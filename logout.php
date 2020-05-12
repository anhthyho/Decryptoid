<?php    // A handy function to destroy a session and its data
    session_start();
    $_SESSION = array();
    // Delete all the information in the array
    setcookie(session_name(), '', time() - 2592000, '/');
    session_destroy();
    unset($_SESSION['username']);
    unset($_SESSION['password']);
    echo "Please <a href='main.php'>click here</a> to log in.";
?>