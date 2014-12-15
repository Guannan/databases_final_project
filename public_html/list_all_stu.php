<html> 
<body>
    <!-- Show fancy title --> 
    <h1>JHU Student Explorer</h1>
    
<?php
    include 'config.php';
    include 'open.php';
    
    // Create and execute the query
    $sql    = 'SELECT StuID, CONCAT(FName, \' \' ,LName) as Name FROM Student';
    $result = mysql_query($sql, $conn);

    // check if the query successfully executed
    if (!$result) {
        echo "DB Error, could not query the database. :-( <br/>";
        echo 'MySQL Error: ' . mysql_error() . '<br/>';
        exit;
    }
    
    // show results
    echo '<h3>Students </h3>';
    
    /*
    while ($row = mysql_fetch_assoc($result)) {
        echo $row['StuID'] . ' ' . $row['Name'] . '<br/>' ;
    }
    */
    
    echo '<table border=1>';
    echo '<tr> <th>Student ID</th> <th>Name</th></tr>';
    while ($row = mysql_fetch_assoc($result)) {
        echo '<tr>' ;
        echo '<td>' . $row['StuID'] .  '</td>' ;
        echo '<td>' . $row['Name'] .  '</td>';
        echo '</tr>';
    }
    echo '</table>';
    

    // flush
    mysql_free_result($result);
    
    
    ?>
</html> 
</body> 
