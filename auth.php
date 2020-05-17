<?php 
// auth -> session - anhthy 174 final
require_once 'login.php';
echo "<link rel='stylesheet' type='text/css' href='css.php'>";
ini_set('session.use_only_cookies', 1);
$conn = new mysqli($hn, $un, $pw, $db);
if ($conn->connect_error)
    die("cannot connect to database");

// checks username/password saved from hashed and salted token from db
// created users already (separately)
if (isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW'])) {
    $un_temp = sanitizeMySQL($conn, $_SERVER['PHP_AUTH_USER']);
    $pw_temp = sanitizeMySQL($conn, $_SERVER['PHP_AUTH_PW']);
    $query = "SELECT * FROM admin WHERE user='$un_temp'";
    $result = $conn->query($query);

    if (! $result) {
        $result->close();
        die($conn->error);
    } elseif ($result->num_rows) {
        $row = $result->fetch_array(MYSQLI_NUM);
        $result->close();
        $salt1 = get_salt($conn, 1, $un_temp);
        $salt2 = get_salt($conn, 2, $un_temp);
        $token = hash('ripemd128', "$salt1$pw_temp$salt2");
        if ($token == $row[1]) {
            // setcookie('user', $un_temp, time() + 60 * 60 * 24 * 7, '/');
            ini_set('session.gc_maxlifetime', 60 * 60 * 24);
            session_start();
            $_SESSION['username'] = $un_temp;
            $_SESSION['password'] = $pw_temp;

            // secure session
            $_SESSION['check'] = hash('ripemd128', $_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT']);
            echo <<<_END
                <div class=central>
                    <h3>Hi $row[0], you are now logged in</h3> 
                    <h3><p><a href=main.php>Click here to continue</a></p></h3>
                </div>
            _END;
        } else {
            header('WWW-Authenticate: Basic realm="Restricted Section"');
            header('HTTP/1.0 401 Unauthorized');
            echo("Invalid username/password combination");
        }
    } else {
        header('WWW-Authenticate: Basic realm="Restricted Section"');
        header('HTTP/1.0 401 Unauthorized');
        echo("Invalid username/password combination");
    }
} else { // if ($_SERVER['PHP_AUTH_USER’]) and ($_SERVER['PHP_AUTH_PW’]) are not set\
    $_SESSION['errorMessage'] = "Please enter username and password";
    header('WWW-Authenticate: Basic realm="Restricted Section"');
    header('HTTP/1.0 401 Unauthorized');
    echo("Please enter your username and password");
}

$conn->close();
echo "</body></html>";

function different_user()
{
    destroy_session_and_data();
    die("<p><a href=main.php>Click here to sign in again</a></p>");
}

function get_salt($conn, $saltnum, $username)
{
    $salt = "";
    if ($saltnum == 1) {
        $stmt = $conn->prepare("SELECT salt1 FROM admin WHERE user = '$username'");
    } elseif ($saltnum == 2) {
        $stmt = $conn->prepare("SELECT salt2 FROM admin WHERE user = '$username'");
    }
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $rows = $result->num_rows;
        if ($rows == 0) {
            echo "nothing found";
        } else {
            $result->data_seek(0);
            $row = $result->fetch_array(MYSQLI_NUM);
            $salt = $row[0];
        }
    } else {
        $stmt->close();
        die("Username is incorect");
    }
    $stmt->close();
    return $salt;
}

// sanitize string
function sanitize($input)
{
    $input = strip_tags($input);
    $input = stripslashes($input);
    $input = htmlentities($input);
    return $input;
}

// sanitize mysql
function sanitizeMySQL($connection, $var)
{
    if (get_magic_quotes_gpc())
        $var = stripslashes($var);
    $var = $connection->real_escape_string($var);
    $var = sanitize($var);
    return $var;
}

?>