<?php
    include 'includes/config.php';
    include 'includes/open.php';
    include 'includes/constants.php';


    //$join_conditions = array("User" => array("Has_skill" => "User.UserID=Has_skill.UserID", "Knows_Language" ));

    // Create and execute the query
    $x_name = $_POST['xvar'];
    $x = $fields[$x_name];

    $y_name = $_POST['yvar'];
    $y = $fields[$y_name];

    
    $attr_string = $x[1] . ' AS ' . $x[2] . ',' . $y[1] . ' AS ' . $y[2];
    
    $table_string = $x[0];
    $condition = '';
    if ($x[0] != $y[0]) {
        $condition = ' WHERE ' . $x[0] . '.UserID=' . $y[0] . '.UserID';

        $table_string = $x[0] . ',' . $y[0];

        /**
        if (array_key_exists($x[0], $join_conditions) and array_key_exists($y[0], $join_conditions[$x[0]]))
            $condition = ' WHERE ' . $join_conditions[$x[0]][$y[0]];
        else
            $condition = ' WHERE ' . $join_conditions[$y[0]][$x[0]];
        **/
    }

    $sql    = 'SELECT ' . $attr_string . ' FROM ' . $table_string . $condition;
    if (count($x) > 3) {
        $sql = $sql . ' GROUP BY ' . $x[0] . '.' . $x[3];
        if (count($y) > 3) { 
            $sql = $sql . ',' . $y[0] . '.' . $y[3];
        }
    }
    elseif (count($y) > 3) {
        $sql = $sql . ' GROUP BY ' . $y[0] . '.' . $y[3];
    }

    $result = mysql_query($sql, $conn);

    // check if the query successfully executed
    if (!$result) {
        echo "DB Error, could not query the database. :-( <br/>";
        echo 'MySQL Error: ' . mysql_error() . '<br/>';
        exit;
    }


    
    
    $data = array();
    while($row = mysql_fetch_assoc($result)) {
        $data[] = array($x[2] => intval($row[$x[2]]), $y[2] => intval($row[$y[2]]));
    }
    
    $result = array("data"=>$data, "xname"=>$x[2], "yname"=>$y[2], "xlabel"=>$x_name, "ylabel"=>$y_name);;
    echo json_encode($result); 

    // flush
    mysql_free_result($result);
?>

