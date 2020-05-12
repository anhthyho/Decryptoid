<?php  //logout function // session destroy - anhthy 174 final
session_start();
$_SESSION = array();
// Delete all the information in the array
 setcookie(session_name(), '', time() - 2592000, '/');
session_destroy();
session_unset();
echo <<<_END
    <html><head><link rel='stylesheet' type='text/css' href='css.php'><title>Decrypt/Encrypt</title></head><body>
    <div class="central">
        <form method='POST' action='main.php' enctype='multipart/form-data'>
            <h5>Decryptoid</h5>
            <div class="allcenter">
                <div class="box"><h3>Please <a href='main.php'>click here</a> to log in.</h3></div>
            </div>
        </form>
    </div>
    
_END;
echo "</body></html>";
?>