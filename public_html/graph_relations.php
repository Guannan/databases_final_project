<?php
    include 'includes/config.php';
    include 'includes/open.php';
    include 'includes/header.php'; 
    include 'includes/constants.php';


/**
    //$join_conditions = array("User" => array("Has_skill" => "User.UserID=Has_skill.UserID", "Knows_Language" ));

    // Create and execute the query
    $x_name = $_POST['xvar'];
    $x = $fields[$x_name];

    $y_name = $_POST['yvar'];
    $y = $fields[$y_name];

    echo '<h1>' . $y_name . ' vs. ' . $x_name . '</h1>';
    
    $attr_string = $x[1] . ' AS ' . $x[2] . ',' . $y[1] . ' AS ' . $y[2];
    
    $table_string = $x[0];
    $condition = '';
    if ($x[0] != $y[0]) {
        $condition = ' WHERE ' . $x[0] . '.UserID=' . $y[0] . '.UserID';

        $table_string = $x[0] . ',' . $y[0];

        //if (array_key_exists($x[0], $join_conditions) and array_key_exists($y[0], $join_conditions[$x[0]]))
        //    $condition = ' WHERE ' . $join_conditions[$x[0]][$y[0]];
        //else
        //    $condition = ' WHERE ' . $join_conditions[$y[0]][$x[0]];
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
**/
?>

<h1 id="title"></h1>
<div id="canvas"></div>
<div id="inputForm">
    <form name="ajaxform" id="ajaxform" action="get_relation_data.php" method="POST">
        <?php echo 'Plot <select name="yvar">';
            foreach ($fields as $f=>$g) {
                echo '<option value="' . $f . '">' . $f . '</option>';
            }
            echo '</select>';

            echo ' as a function of <select name="xvar">';
            foreach ($fields as $f=>$g) {
                echo '<option value="' . $f . '">' . $f . '</option>';
            }
            echo '</select><br/><br/>';
        ?>
        <input type="submit" value="Update"/>
    </form>
</div>

<script>
    function drawTitle(result) {
        var title = result.ylabel + ' vs. ' + result.xlabel;
        $("#title").text(title);
    }

    function drawGraph(result) {
        // Remove previous graph
        $("#canvas").empty();

        /// Parse new dataset
        var dataset = result.data;
        var xname = result.xname;
        var yname = result.yname;
        var xlabel = result.xlabel;
        var ylabel = result.ylabel;

        var w = $("#canvas").width();
        var h = $("#canvas").height();

        // top, bottom, left, right
        var padding = [30, 50, 60, 30];

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
            .attr("y", h-10)
            .text(xlabel);
        svg.append("text")
            .attr("class", "ylabel")
            .attr("text-anchor", "middle")
            .attr("x", -h/2)
            .attr("dy", "1.2em")
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
    } 
    
    // callback handler for form submit
    $("#ajaxform").submit(function(e)
    {
        console.log($(this));

        var postData = $(this).serializeArray();
        var formURL = $(this).attr("action");
        
        console.log(postData);
        console.log(formURL);

        $.ajax(
        {
            url: formURL,
            type: "POST",
            data: postData,
            success: function(result)
            {
                resultParsed = JSON.parse(result);
                drawTitle(resultParsed);
                drawGraph(resultParsed);
            },
            error: function(jqXHR, status, error)
            {
                console.log(jqXHR);
                console.log(error);
            }
        });

        console.log('Done');
        e.preventDefault();
        //e.unbind();
    });

    $("#ajaxform").submit();

</script>

<?php
    // flush
    mysql_free_result($result);
    
    include 'includes/footer.php';
?>

