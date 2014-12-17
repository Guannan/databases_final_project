<?php
    include 'includes/config.php';
    include 'includes/open.php';
    include 'includes/constants.php';

    //$join_conditions = array("User" => array("Has_skill" => "User.UserID=Has_skill.UserID", "Knows_Language" ));

    // Create and execute the query
    $x_name = $_POST['xvar'];
    $x = $continuous_vars[$x_name];

    $y_name = $_POST['yvar'];
    $y = $continuous_vars[$y_name];

    $agg = $_POST['aggregation'];

    // mode 0 = scatter plot
    // mode 1 = bar chart
    // mode 2 = line chart
    $mode = 0;
    if (array_key_exists($x_name, $categorical_vars)) {
        $x = $categorical_vars[$x_name];
        $mode = 1;
    }
    elseif ($agg != 'ALL') {
        $mode = 2;
    }

    $attr_string = $x[0] . ' AS varA,' . $y[0] . ' AS varB';

    $table_ids = $x[1];
    $table_names = array();
    foreach ($table_ids as $t) {
        $table_names[] = $tables[$t]; 
    }

    $conditions = array_merge($x[2], $y[2]);
    for ($i = 0; $i < count($table_ids)-1; $i++) {
        for ($j = $i+1; $j < count($table_ids); $j++) {
            if (array_key_exists($table_ids[$j], $cross_refs[$table_ids[$i]])) {
                $conditions[] = $cross_refs[$table_ids[$i]][$table_ids[$j]];
            }
        }
    }

    $tablesY = $y[1];
    foreach ($tablesY as $t) {
        if (!in_array($t, $table_ids)) {
            foreach ($table_ids as $t2) {
                if (array_key_exists($t, $cross_refs[$t2])) {
                    $conditions[] = $cross_refs[$t2][$t];
                }
            }
            $table_ids[] = $t;
            $table_names[] = $tables[$t];
        }
    }

    $table_str = join(",", $table_names);
 
    $condition_str = '';

    if (count($conditions) > 0) {
        $condition_str = ' WHERE ' . join(' AND ', $conditions);
    }

    $sql    = 'SELECT ' . $attr_string . ' FROM ' . $table_str . $condition_str;
    if (strlen($x[3]) > 0) {
        $sql = $sql . ' GROUP BY ' . $x[3];
        if (strlen($y[3]) > 0) { 
            $sql = $sql . ',' . $y[3];
        }
    }
    elseif (strlen($y[3]) > 0) {
        $sql = $sql . ' GROUP BY ' . $y[3];
    }

    if ($mode != 0) {
        $sql = 'SELECT varA, ' . $agg . '(varB) AS varB FROM (' . $sql . ') as s GROUP BY varA';
    }

    //echo 'Query: ' .  $sql . '<br>';
    $result = mysql_query($sql, $conn);

    // check if the query successfully executed
    if (!$result) {
        echo "DB Error, could not query the database. :-( <br/>";
        echo 'MySQL Error: ' . mysql_error() . '<br/>';
        exit;
    }

    
    $data = array();
    while($row = mysql_fetch_assoc($result)) {
        if ($mode == 0 || $mode == 2)
            $data[] = array("varA" => intval($row["varA"]), "varB" => intval($row["varB"]));
        elseif ($mode == 1)
            if (strlen($row["varA"]) > 0)
                $data[] = array("varA" => $row["varA"], "varB" => intval($row["varB"]));
    }
    
    $dataset = array("data"=>$data, "xname"=>"varA", "yname"=>"varB", "xlabel"=>$x_name, "ylabel"=>$y_name, "mode"=>$mode);
    echo json_encode($dataset);

    // flush
    mysql_free_result($result);
?>
