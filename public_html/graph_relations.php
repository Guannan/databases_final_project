<?php
    include 'config.php';
    include 'open.php';
    include 'header.php'; 


    $fields = array("Num Connections" => array("User", "User.NumConnection", "NumConnection"),
        "Age" => array("User", "User.Age", "Age"),
        "Num Skills" => array("Has_skill", "COUNT(Has_skill.SkillID) as NumSkills", "NumSkills", "UserID"));

    $join_conditions = array("User" => array("Has_skill" => "User.UserID=Has_skill.UserID"));

    // Create and execute the query
    $x_name = $_POST['xvar'];
    $x = $fields[$x_name];

    $y_name = $_POST['yvar'];
    $y = $fields[$y_name];

    echo '<h1>' . $y_name . ' vs. ' . $x_name . '</h1>';
    
    $attr_string = $x[1] . ',' . $y[1];
    
    $table_string = $x[0];
    $condition = '';
    if ($x[0] != $y[0]) {
        $table_string = $x[0] . ',' . $y[0];

        if (array_key_exists($x[0], $join_conditions) and array_key_exists($y[0], $join_conditions[$x[0]]))
            $condition = ' WHERE ' . $join_conditions[$x[0]][$y[0]];
        else
            $condition = ' WHERE ' . $join_conditions[$y[0]][$x[0]];
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


    echo '<div id="data" style="display:none;">';
    //$prefix = '';
    //echo '[\n';
    //while ($row = mysql_fetch_assoc($result)) {
    //    echo $prefix . ' {\n';
    //    echo '  "' . $x_name . '": ' . $row[$x[2]] . ',\n';
    //    echo '  "' . $y_name . '": ' . $row[$y[2]] . '\n}';
    //    $prefix = ',\n';
    //}
    //echo '\n]';
    
    $data = array();
    while($row = mysql_fetch_assoc($result)) {
        $data[] = array($x[2] => intval($row[$x[2]]), $y[2] => intval($row[$y[2]]));
    }
    echo json_encode($data);
    echo '</div>';

    echo '<div id="xname" style="display:none;">' . $x[2] . '</div>';
    echo '<div id="yname" style="display:none;">' . $y[2] . '</div>';
    echo '<div id="xlabel" style="display:none;">' . $x_name . '</div>';
    echo '<div id="ylabel" style="display:none;">' . $y_name . '</div>';

 
    // show results
    //echo '<table class="results">';
    //echo '<tr> <th>' . $x_name . '</th> <th>' . $y_name . '</th></tr>';
    //while ($row = mysql_fetch_assoc($result)) {
    //    echo '<tr>' ;
    //    echo '<td class="numeric">' . $row[$x[2]] . '</td>';
    //    echo '<td class="numeric">' . $row[$y[2]] . '</td>';
    //    echo '</tr>';
    //}
    //echo '</table>';
?>

<div id="canvas"></div>

<script>
    var dataset = JSON.parse(document.getElementById("data").textContent);
    var xname = document.getElementById("xname").textContent;
    var yname = document.getElementById("yname").textContent;
    var xlabel = document.getElementById("xlabel").textContent;
    var ylabel = document.getElementById("ylabel").textContent;

    console.log(dataset);
    console.log(xname);

    var w = $("#canvas").width();
    var h = $("#canvas").height();

    // top, bottom, left, right
    var padding = [20, 40, 50, 20];

    // Create SVG element
    var svg = d3.select("#canvas")
                .append("svg:svg")
                .attr("width", w)
                .attr("height", h);

    // Compute axis scales
    var xScale = d3.scale.linear()
                   .domain([0, d3.max(dataset, function(d) { return d[xname]; })])
                   .range([padding[2], w-padding[3]]);
    var yScale = d3.scale.linear()
                   .domain([0, d3.max(dataset, function(d) { return d[yname]; })])
                   .range([h-padding[1], padding[0]]);

    var xAxis = d3.svg.axis()
                  .scale(xScale)
                  .orient("bottom")
                  .ticks(6);
    var yAxis = d3.svg.axis()
                  .scale(yScale)
                  .orient("left")
                  .ticks(8);

    // Draw axes
    svg.append("g")
        .attr("class", "axis")
        .attr("transform", "translate(0," + (h-padding[1]) + ")")
        .call(xAxis);
    svg.append("g")
        .attr("class", "axis")
        .attr("transform", "translate(" + padding[2] + ",0)")
        .call(yAxis);

    // Draw axis labels
    svg.append("text")
        .attr("class", "xlabel")
        .attr("text-anchor", "middle")
        .attr("x", w/2)
        .attr("y", h-6)
        .text(xlabel);
    svg.append("text")
        .attr("class", "ylabel")
        .attr("text-anchor", "middle")
        .attr("x", -h/2)
        .attr("dy", "1em")
        .attr("transform", "rotate(-90)")
        .text(ylabel);

    // Plot data points
    svg.selectAll("circle")
       .data(dataset)
       .enter()
       .append("circle")    
       .attr("cx", function(d) { return xScale(d[xname]); })
       .attr("cy", function(d) { return yScale(d[yname]); })
       .attr("r", 5);
    
    // Draw axes
    
    
</script>

<?php
    // flush
    mysql_free_result($result);
    
    include 'footer.php';
?>

