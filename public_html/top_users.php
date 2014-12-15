<?php
    include 'config.php';
    include 'open.php';
    include 'header.php'; 

    echo '<h1>Top Users</h1>';

    // Create and execute the query
    $attr = $_POST['attr'];
    $lim = $_POST['limit'];

    $sql    = 'SELECT CONCAT(FName, \' \' ,LName) as Name, Industry, NumConnection, Age FROM User ORDER BY ' . $attr;
    if ($_POST['order'] == 'DESC')
        $sql = $sql . ' DESC';
    $sql = $sql . ' LIMIT 0,' . $lim;
    
    $result = mysql_query($sql, $conn);

    // check if the query successfully executed
    if (!$result) {
        echo "DB Error, could not query the database. :-( <br/>";
        echo 'MySQL Error: ' . mysql_error() . '<br/>';
        exit;
    }
    
    // show results
    echo '<table class="results">';
    echo '<tr> <th>Name</th> <th>Industry</th> <th>Num Connections</th> <th>Age</th></tr>';
    while ($row = mysql_fetch_assoc($result)) {
        echo '<tr>' ;
        echo '<td>' . $row['Name'] .  '</td>' ;
        echo '<td>' . $row['Industry'] .  '</td>';
        echo '<td class="numeric">' . $row['NumConnection'] . '</td>';
        echo '<td class="numeric">' . $row['Age'] . '</td>';
        echo '</tr>';
    }
    echo '</table>';
    

    // flush
    mysql_free_result($result);
    
    include 'footer.php';    
?>

