<?php  //landing page - anhthy 174 final
require_once 'login.php';
echo <<<_END
    <html><head><link rel='stylesheet' type='text/css' href='css.php'><title>Decrypt/Encrypt</title></head><body>
    <div class="central">
        <form method='POST' action='main.php' enctype='multipart/form-data'>
            <h5>Decryptoid</h5>
            <div class="allcenter">
                <div class="box"><h3><a href="http://localhost/final/signup.php">Sign Up</a></h3></div>
                <div class="box"><h3><a href="http://localhost/final/auth.php">Login</a></h3></div>
            </div>
        </form>
    </div>
    
    
_END;
echo "</body></html>";

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

// function add_admin($conn, $username, $token){
// $query = "INSERT INTO admin VALUES('$username', '$token')";
// $result = $conn->query($query);
// if (!$result) die($conn->error);
// }
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