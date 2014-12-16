<?php

$conn = mysql_connect($dbhost, $dbuser, $dbpass); 
if (!$conn) {
    die ('Error connecting to mysql. :-( <br/>');
} 
else {
 //echo 'Yes, we have connected to MySQL! :-) <br/>';
}


if (!mysql_select_db($dbname, $conn)) {
    echo 'Sorry, could not select database. :-(';
    exit;
}
else {
    //echo 'We have selected the database too! :-)' . '<br/>';
}

?>
