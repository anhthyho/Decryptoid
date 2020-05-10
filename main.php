<?php //anhthy ho midterm  - cs174
require_once'login.php';

echo <<<_END
    <html><head><title>Decrypt/Encrypt</title></head><body>
    <form method='POST' action='main.php' enctype='multipart/form-data'>
        <a href="http://localhost/midterm/signup.php">Sign Up</a><br><br>
        <a href="http://localhost/midterm/admin.php">Login</a>
    </form>
    
_END;
 
$conn = new mysqli($hn, $un, $pw, $db);
if ($conn->connect_error) die ("cannot connect to database");

//file to check if infected
if (isset ($_FILES['file']) && !empty($_FILES['file']['name']) && isset($_POST['Submit_File'])){
    
    $uploaded_file = $_FILES['file']['name'];
    $content = $uploaded_file; 
    switch($_FILES['file']['type']) {
        case 'text/plain': $ext = 'txt'; break;
        case 'text/css': $ext = 'txt'; break;
        case 'text/html': $ext = 'txt'; break;
        default: $ext = ''; break;
    }
    
    if ($ext){
        move_uploaded_file($_FILES['file']['tmp_name'], $uploaded_file);
        //echo "Uploaded file '$name' as '$newname':<br>";
        $content = file_get_contents($uploaded_file);
        $content = sanitize($content);
        //echo $content;
        
        //send content to query and check if file contains malware bytes
        echo query_malware($conn, $content);
        
    }
}

$conn->close();
echo "</body></html>";


//dumps contents of file
//check if content contains any of the bytes in database
//returns if match found
function query_malware($conn, $contents){
    $stmt = $conn->prepare('SELECT bytes FROM hash');
    $stmt->execute(); 
    foreach ($stmt->get_result() as $row) {
        if (strpos($contents, $row['bytes'])!==false){
            return 'file is not safe, has been found in our database';
        }
    } 
    return 'file is safe';
}

function get_post($conn, $var){
    return sanitize($conn->real_escape_string($_POST[$var]));
}

//sanitize string
function sanitize($input){
    $input=strip_tags($input);
    $input=stripslashes($input);
    $input=htmlentities($input);
    return $input;
}

//sanitize mysql 
function sanitizeMySQL($connection, $var) {
    if (get_magic_quotes_gpc())
        $var = stripslashes($var);
    $var = $connection->real_escape_string($var);
    $var = sanitize($var);
    return $var;
}

// function add_admin($conn, $username, $token){
//     $query = "INSERT INTO admin VALUES('$username', '$token')";
//     $result = $conn->query($query);
//     if (!$result) die($conn->error);
// }



function display($result){
    
    //formatting code from https://www.siteground.com/tutorials/php-mysql/display-table-data/
    echo '<table border="1" cellspacing="2" cellpadding="2">
      <tr>
          <td> <font face="Comic Sans"> Bytes </font> </td>
      </tr>';
    if (!$result) die ("Database access failed: ");
    else {
        while ($row = $result->fetch_assoc()) {
            $field1name = $row["bytes"];
            
            echo '<tr>
                  <td><font face="Comic Sans">'.$field1name.'</td>
              </tr>';
        }
        $result->close();
    }
}

?>