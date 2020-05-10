<?php
require_once 'login.php';

echo <<<_END
    <html><head><title>Sign Up</title></head><body>
    <form method='POST' action='signup.php' enctype='multipart/form-data'>
        User Name: <input type='text' name='username' size='10'><br><br>
        Password: <input type='password' name='password' size='10'><br><br>
        Email: <input type='email' name='email' size='10'><br><br>
        <input type='submit' value='Submit' name='signup'><br><br>
    </form>
    
_END;

$conn = new mysqli($hn, $un, $pw, $db);
if ($conn->connect_error)
    die($conn->connect_error);

// check all entry fields are valid / not null -> trigger if button 'Add' is clicked
if (isset($_POST['username']) && isset($_POST['password']) && isset($_POST['email']) && $_POST['username'] != "" && $_POST['password'] != "" && $_POST['email'] != "" && isset($_POST['signup'])) {

    $user = sanitizeMySQL($conn, $_POST['username']);
    $password = sanitizeMySQL($conn, $_POST['password']);
    $email = sanitizeMySQL($conn, $_POST['email']);
    // $result = add_info($conn, $advisor, $student, $studentID, $classcode);
    add_user($conn, $user, $password, $email);
}

$conn->close();
echo "</body></html>";

function add_user($conn, $user, $password, $email)
{
    $stmt = $conn->prepare('INSERT INTO admin (user, password, email) VALUES(?,?,?)');
    $stmt->bind_param('sss', $user, $password, $email);
    $stmt->execute();
    $stmt->close();

    $salt1 = get_salt($conn, 1, $user);
    $salt2 = get_salt($conn, 2, $user);

    $token = hash('ripemd128', "$salt1$password$salt2");
    $stmt = $conn->prepare("UPDATE admin set password='$token' WHERE user='$user'");
    if ($stmt->execute()){
        header("Location: admin.php");
    }else {
        echo "User could not be created";
    }
    $stmt->close();
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

function get_post($conn, $var)
{
    return sanitize($conn->real_escape_string($_POST[$var]));
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