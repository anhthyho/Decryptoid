<?php
// anhthy ho midterm - cs174
require_once 'login.php';
require_once 'double_trans.php';
require_once 'rc4.php';

$conn = new mysqli($hn, $un, $pw, $db);
if ($conn->connect_error)
    die("cannot connect to database");

session_start();
if (isset($_SESSION['username'])) {
    $user = $_SESSION['username'];
    echo <<<_END
        <html><head><title>Decryptoid</title></head><body>
        <h3>Hi $user, you are now logged in</h3>
        <form id="form_1" method='POST' action='admin.php' enctype='multipart/form-data'>
            File: <input type='file' name='cipher_file' size='10'><br><br>
            <select id="cipher" name='cipher'>
                <option value="Select">Select</option>
                <option value="Simple Substitution">Simple Substitution</option>
                <option value="Double Transposition">Double Transposition</option>
                <option value="RC4">RC4</option>
            </select><br><br>
            <h4>Double Transposition Inputs</h4>
            Key 1: <input type='text' name='key1' size='10'><br><br>
            Key 2: <input type='text' name='key2' size='10'><br><br>
            <h4>RC4 Input</h4>
            Key for RC4: <input type='text' name='rc4_key' size='10'><br><br>
            <input type='submit' value='Encrypt' name='Encrypt'>
            <input type='submit' value='Decrypt' name='Decrypt'><br><br>
            <a href="logout.php">Logout</a>
        </form>
    _END;
    if (isset($_FILES['cipher_file']) && ! empty($_FILES['cipher_file']['name']) && isset($_POST['cipher']) && ($_POST['cipher'] != 'Select') && isset($_POST['Decrypt'])) {

        $cipher_name = $_POST['cipher'];
        $cipher_name = sanitize($cipher_name);

        $uploaded_file = $_FILES['cipher_file']['name'];
        $content = $uploaded_file;
        switch ($_FILES['cipher_file']['type']) {
            case 'text/plain':
                $ext = 'txt';
                break;
            case 'text/css':
                $ext = 'txt';
                break;
            case 'text/html':
                $ext = 'txt';
                break;
            default:
                $ext = '';
                break;
        }

        if ($ext) {
            move_uploaded_file($_FILES['cipher_file']['tmp_name'], $uploaded_file);
            // echo "Uploaded file '$name' as '$newname':<br>";
            $content = file_get_contents($uploaded_file);
            $content = sanitize($content);
            echo "$content <br><br>";
        }

        echo "Decrypt with $cipher_name <br><br>";
        add_text($conn, $content, $user, $cipher_name);

        // if selects simple sub and decrypt
        if ($_POST['cipher'] == 'Simple Substitution') {
            echo simple_sub($content, "decrypt");
        } else if ($_POST['cipher'] == 'Double Transposition' && isset($_POST['key1']) && $_POST['key1'] != "" && isset($_POST['key2']) && $_POST['key2'] != "") {
            $key = $_POST['key1'];
            $key = sanitize($key);
            $key2 = $_POST['key2'];
            $key2 = sanitize($key2);
            echo double_trans($content, $key, $key2, "decrypt");
        } else if ($_POST['cipher'] == 'RC4' && isset($_POST['rc4_key']) && $_POST['rc4_key'] != "") {
            $key = $_POST['rc4_key'];
            $key = sanitize($key);
            echo "encrypt text -> hex, decrypt hex -> text";
            echo rc4($key, $content, "decrypt");
        }
    } elseif (isset($_FILES['cipher_file']) && ! empty($_FILES['cipher_file']['name']) && isset($_POST['cipher']) && $_POST['cipher'] != "Select" && isset($_POST['Encrypt'])) {

        $cipher_name = $_POST['cipher'];
        $cipher_name = sanitize($cipher_name);

        $uploaded_file = $_FILES['cipher_file']['name'];
        $content = $uploaded_file;
        switch ($_FILES['cipher_file']['type']) {
            case 'text/plain':
                $ext = 'txt';
                break;
            case 'text/css':
                $ext = 'txt';
                break;
            case 'text/html':
                $ext = 'txt';
                break;
            default:
                $ext = '';
                break;
        }

        if ($ext) {
            move_uploaded_file($_FILES['cipher_file']['tmp_name'], $uploaded_file);
            // echo "Uploaded file '$name' as '$newname':<br>";
            $content = file_get_contents($uploaded_file);
            $content = sanitize($content);
            echo "$content <br><br>";
        }

        print "Encrypt with $cipher_name: <br><br>";
        add_text($conn, $content, $user, $cipher_name);

        // if selects simple sub and encrypt
        if ($_POST['cipher'] == 'Simple Substitution') {
            echo simple_sub($content, "encrypt");
        } else if ($_POST['cipher'] == 'Double Transposition' && isset($_POST['key1']) && $_POST['key1'] != "" && isset($_POST['key2']) && $_POST['key2'] != "") {
            $key = $_POST['key1'];
            $key = sanitize($key);
            $key2 = $_POST['key2'];
            $key2 = sanitize($key2);
            echo double_trans($content, $key, $key2, "encrypt");
        } else if ($_POST['cipher'] == 'RC4' && isset($_POST['rc4_key']) && $_POST['rc4_key'] != "") {
            $key = $_POST['rc4_key'];
            $key = sanitize($key);
            echo "encrypt text -> hex, decrypt hex -> text";
            echo rc4($key, $content, "encrypt");
        }
    } else {
        echo "Please make your selections above";
    }
} else {
    header("Location: main.php");
    die;
}

$conn->close();
echo "</body></html>";

// adds uploaded file contents, user, and cipher used to db
function add_text($conn, $content, $user, $cipher_name)
{
    $stmt = $conn->prepare('INSERT INTO text (text, user, cipher) VALUES(?,?,?)');
    $stmt->bind_param('sss', $content, $user, $cipher_name);
    $stmt->execute();
    $stmt->close();
}

function simple_sub($input, $de)
{
    $new_alph = strtolower("OIFJCMGBNSPUXZEAWDLTRKQHVY ");
    $alph = "abcdefghijklmnopqrstuvwxyz ";

    if ($de == "decrypt") {
        $alph = strtolower("OIFJCMGBNSPUXZEAWDLTRKQHVY ");
        $new_alph = "abcdefghijklmnopqrstuvwxyz ";
    }

    $output = "";
    $inputLen = strlen($input);

    for ($i = 0; $i < $inputLen; $i ++) {
        $curr = $input[$i];
        $index = strpos($alph, strtolower($curr));
        if ($index == false) {
            echo "The string could not be encrypted";
        } else {
            if (ctype_upper($curr)) {
                $output = $output . strtoupper(substr($new_alph, $index, 1));
            } else {
                $output = $output . substr($new_alph, $index, 1);
            }
        }
    }
    return $output;
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

function display_all($conn)
{
    $query = "SELECT * FROM hash";
    $result = $conn->query($query);
    display($result);
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