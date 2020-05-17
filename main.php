<?php
// landing page - anhthy 174 final
require_once 'login.php';
require_once 'double_trans.php';
require_once 'rc4.php';

$conn = new mysqli($hn, $un, $pw, $db);
session_start();

if (array_key_exists('check', $_SESSION)){
    if ($_SESSION['check'] && $_SESSION['check'] != hash('ripemd128', $_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT'])){
        different_user();
    }
}

if (isset($_SESSION['username'])) {
    $user = $_SESSION['username'];
    echo <<<_END
        <h3>Hi $user, welcome to Decryptoid</h3>
    _END;
}

echo <<<_END
    <html><head><link rel='stylesheet' type='text/css' href='css.php'>
    <title>Decryptoid</title></head><body>
    <div class="central">
        <form method='POST' action='main.php' enctype='multipart/form-data'>
            <h5>Decryptoid</h5>
            <div class="allcenter">
                <div class="box"><a href="http://localhost/final/signup.php">Sign Up</a></div>
                <div class="box"><a href="http://localhost/final/auth.php">Login</a></div>
            </div>
        </form>
        <form id="form_1" method='POST' action='main.php' enctype='multipart/form-data'>
        File: <input type='file' name='cipher_file' size='10'><br><br>
        Input Text: <input type='text' name='input_text' size='10'><br><br>
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
        <div class="allcenter">
            <input type='submit' value='Encrypt' name='Encrypt'>
        <input type='submit' value='Decrypt' name='Decrypt'><br><br>
        </div>
    </div>
    </form>
_END;

if (isset($_FILES['cipher_file']) && ! empty($_FILES['cipher_file']['name']) && $_FILES['cipher_file']['type'] != 'text/plain' && (isset($_POST['Decrypt']) || isset($_POST['Encrypt']))) {
    echo <<<_END
        <h1>Please upload text file</h1>
    _END;
} elseif (isset($_POST['cipher']) && $_POST['cipher'] == 'Select' && (isset($_POST['Decrypt']) || isset($_POST['Encrypt']))) {
    echo <<<_END
        <h1>Please select a cipher</h1>
    _END;
} elseif (isset($_POST['cipher']) && ($_POST['cipher'] == 'Double Transposition') && ($_POST['key1'] == "" || $_POST['key2'] == "") && (isset($_POST['Decrypt']) || isset($_POST['Encrypt']))) {
    echo <<<_END
        <h1>Please enter key(s) for Double Transposition</h1>
    _END;
} elseif (isset($_POST['cipher']) && ($_POST['cipher'] == 'RC4') && $_POST['rc4_key'] == "" && (isset($_POST['Decrypt']) || isset($_POST['Encrypt']))) {
    echo <<<_END
        <h1>Please enter key for RC4</h1>
    _END;
} elseif (isset($_FILES['cipher_file']) && ! empty($_FILES['cipher_file']['name']) && isset($_POST['cipher']) && ($_POST['cipher'] != 'Select') && isset($_POST['Decrypt'])) {

    // if file upload -> file -> content
    $cipher_name = $_POST['cipher'];
    $cipher_name = sanitize($cipher_name);

    $uploaded_file = $_FILES['cipher_file']['name'];
    $content = $uploaded_file;
    move_uploaded_file($_FILES['cipher_file']['tmp_name'], $uploaded_file);
    // echo "Uploaded file '$name' as '$newname':<br>";
    $content = file_get_contents($uploaded_file);
    $content = sanitize($content);
    // echo "$content <br><br>";

    decrypt($cipher_name, $content, $conn);
} elseif (isset($_POST['input_text']) && $_POST['input_text'] != "" && isset($_POST['cipher']) && ($_POST['cipher'] != 'Select') && isset($_POST['Decrypt'])) {
    // if text box -> content

    $cipher_name = $_POST['cipher'];
    $cipher_name = sanitize($cipher_name);
    $content = $_POST['input_text'];
    $content = sanitize($content);

    decrypt($cipher_name, $content, $conn);
} elseif (isset($_FILES['cipher_file']) && ! empty($_FILES['cipher_file']['name']) && isset($_POST['cipher']) && $_POST['cipher'] != "Select" && isset($_POST['Encrypt'])) {

    // if file upload -> file -> content
    $cipher_name = $_POST['cipher'];
    $cipher_name = sanitize($cipher_name);

    $uploaded_file = $_FILES['cipher_file']['name'];
    $content = $uploaded_file;
    move_uploaded_file($_FILES['cipher_file']['tmp_name'], $uploaded_file);
    // echo "Uploaded file '$name' as '$newname':<br>";
    $content = file_get_contents($uploaded_file);
    $content = sanitize($content);

    encrypt($cipher_name, $content, $conn);
} elseif (isset($_POST['input_text']) && $_POST['input_text'] != "" && isset($_POST['cipher']) && ($_POST['cipher'] != 'Select') && isset($_POST['Encrypt'])) {
    // if text box -> content

    $cipher_name = $_POST['cipher'];
    $cipher_name = sanitize($cipher_name);
    $content = $_POST['input_text'];
    $content = sanitize($content);

    encrypt($cipher_name, $content, $conn);
} else {
    echo <<<_END
        <h1>Please enter inputs for selections above</h1>
    _END;
}

$conn->close();
echo "</body></html>";

function decrypt($cipher_name, $content, $conn)
{
    $dHeader = "Decrypt with $cipher_name <br><br>";
    add_text($conn, $content, $cipher_name);

    // if selects simple sub and decrypt
    $decryption = "";
    if ($_POST['cipher'] == 'Simple Substitution') {
        $decryption = simple_sub($content, "decrypt");
    } // double transposition flow
    else if ($_POST['cipher'] == 'Double Transposition' && isset($_POST['key1']) && $_POST['key1'] != "" && isset($_POST['key2']) && $_POST['key2'] != "") {
        $key = $_POST['key1'];
        $key = sanitize($key);
        $key2 = $_POST['key2'];
        $key2 = sanitize($key2);
        $decryption = double_trans($content, $key, $key2, "decrypt");
    } // rc4 flow
    else if ($_POST['cipher'] == 'RC4' && isset($_POST['rc4_key']) && $_POST['rc4_key'] != "") {
        $key = $_POST['rc4_key'];
        $key = sanitize($key);
        $decryption = "decrypt hex -> text";
        $decryption .= "<br>" . rc4($key, $content, "decrypt");
    }
    echo <<<_END
         <div class="answer">
             <h3>$dHeader</h3>
             <div class="box2"><h3>$content</h3></div>
             <h1>----> decrypted ----></h1>
             <div class="box2"><h3>$decryption</h3></div>
         </div>
    _END;
}

function encrypt($cipher_name, $content, $conn)
{
    $eHeader = "Encrypt with $cipher_name: <br><br>";
    add_text($conn, $content, $cipher_name);

    $encryption = "";
    // if selects simple sub and encrypt
    if ($_POST['cipher'] == 'Simple Substitution') {
        $encryption = simple_sub($content, "encrypt");
    } else if ($_POST['cipher'] == 'Double Transposition' && isset($_POST['key1']) && $_POST['key1'] != "" && isset($_POST['key2']) && $_POST['key2'] != "") {
        $key = $_POST['key1'];
        $key = sanitize($key);
        $key2 = $_POST['key2'];
        $key2 = sanitize($key2);
        $encryption = double_trans($content, $key, $key2, "encrypt");
    } else if ($_POST['cipher'] == 'RC4' && isset($_POST['rc4_key']) && $_POST['rc4_key'] != "") {
        $key = $_POST['rc4_key'];
        $key = sanitize($key);
        $encryption = "encrypt text -> hex<br><br>";
        $encryption .= rc4($key, $content, "encrypt");
    }
    echo <<<_END
         <div class="answer">
             <h3>$eHeader</h3>
             <div class="box2"><h3>$content</h3></div>
             <h1>----> encrypted ----></h1>
             <div class="box2"><h3>$encryption</h3></div>
         </div>
    _END;
}

// adds uploaded file contents, user, and cipher used to db
function add_text($conn, $content, $cipher_name)
{
    if (isset($_SESSION['username'])) {
        $user = $_SESSION['username'];
        $stmt = $conn->prepare('INSERT INTO text (text, user, cipher) VALUES(?,?,?)');
        $stmt->bind_param('sss', $content, $user, $cipher_name);
        $stmt->execute();
        $stmt->close();
    }
}

/**
 * simple substitution flow
 *
 * @param
 *            inputted string to be subbed $input
 * @param
 *            decrypt or encrypt $de
 * @return string of subbed input
 */
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
            return "invalid input: may only contain letters and spaces";
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

function different_user()
{
    destroy_session_and_data();
    die("<p><a href=main.php>Click here to sign in again</a></p>");
}

/**
 * gets salt from db
 *
 * @param
 *            db connection $conn
 * @param salt1/salt2 $saltnum
 * @param
 *            user for salt $username
 * @return string value of salt
 */
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

function display($result)
{

    // formatting code from https://www.siteground.com/tutorials/php-mysql/display-table-data/
    echo '<table border="1" cellspacing="2" cellpadding="2">
      <tr>
          <td> <font face="Comic Sans"> Bytes </font> </td>
      </tr>';
    if (! $result)
        die("Database access failed: ");
    else {
        while ($row = $result->fetch_assoc()) {
            $field1name = $row["bytes"];

            echo '<tr>
                  <td><font face="Comic Sans">' . $field1name . '</td>
              </tr>';
        }
        $result->close();
    }
}

?>