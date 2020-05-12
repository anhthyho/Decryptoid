<?php //anhthy ho final  - cs174
require_once'login.php';
echo <<<_END
    <html><head><title>Decrypt/Encrypt</title></head><body>
    <form method='POST' action='main.php' enctype='multipart/form-data'>
        <a href="http://localhost/final/signup.php">Sign Up</a><br><br>
        <a href="http://localhost/final/auth.php">Login</a>
    </form>
    
_END;

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