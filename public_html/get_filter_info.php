<?php
    include 'includes/config.php';
    include 'includes/open.php';
    include 'includes/constants.php';

    $name = $_POST['name'];
    $info = $filters[$name];
    $table = $tables[$info[1][0]];

    // Numeric: min and max
    if ($info[0] == 0) {
        $query = 'SELECT MIN(a) AS min FROM (SELECT ' . $info[2] . 'AS a FROM ' . $table . $info[3] . ') as s';
//'SELECT MIN(' . $info[2] . ') AS min FROM ' . $table;
        $result = mysql_query($query, $conn);
        $min = 0;
        while ($row = mysql_fetch_assoc($result))
            $min = $row['min'];

        $query = 'SELECT MAX(a) AS max FROM (SELECT ' . $info[2] . 'AS a FROM ' . $table . $info[3] . ') as s';
        //$query = 'SELECT MAX(' . $info[2] . ') AS max FROM ' . $table;
        $result = mysql_query($query, $conn);
        $max = 0;
        while ($row = mysql_fetch_assoc($result))
            $max = $row['max'];

        echo json_encode(array("min"=>$min, "max"=>$max));
    }
   
    // Categorical: Filter by name
    else {
        $query = 'SELECT DISTINCT ' . $info[2] . ' FROM ' . $table . ' ORDER BY ' . $info[2];
        $result = mysql_query($query, $conn);
        $values = array();
        while ($row = mysql_fetch_assoc($result)) {
            $val = $row[$info[2]];
            if (strlen($val) > 0)
                $values[] = $val;
        }
        echo json_encode($values);
    }

?>
