<?php
    include 'includes/config.php';
    include 'includes/open.php';
    include 'includes/header.php'; 
    include 'includes/constants.php';
?>

<h1 id="title"></h1>
<div id="canvas"></div>
<div id="categorical" style="display:none">
    <?php echo json_encode(array_keys($categorical_vars)) ?>
</div>

<div id="inputForm">
    <form name="ajaxform" id="ajaxform" action="get_relation_data.php" method="POST">
        Plot <select id="aggregation" name="aggregation">
            <option value="ALL" disabled>All</option>
            <option value="AVG" selected="selected">Average</option>
            <option value="MAX">Maximum</option>
            <option value="MIN">Minimum</option>
        </select>

        <?php echo ' <select name="yvar">';
            foreach ($continuous_vars as $f=>$g) {
                echo '<option value="' . $f . '">' . $f . '</option>';
            }
            echo '</select>';

            echo ' as a function of <select name="xvar" onchange="updateForm(this)">';
            foreach (array_merge($categorical_vars, $continuous_vars) as $f=>$g) {
                echo '<option value="' . $f . '">' . $f . '</option>';
            }
            echo '</select>';
        ?>
        <span id="order">in <select name="order">
            <option value="DESC">Descending</option>
            <option value="ASC">Ascending</option>
        </select> order (max 40). </span><br/><br/>

        <div id="filterPanel">
            <button onclick="addFilter()">Add Filter</button>
            <div id="filters"></div>
        </div>
        <br>
        <input type="submit" value="Update"/>
    </form>
</div>

<script>
/**
    var submit = false;
    $("#ajaxform").submit(function(e){ 
        if (!submit) {
            submit = true;
            return false; }
        else return true; });
**/

    function updateForm(select) {
        var categorical = JSON.parse($("#categorical").text());
        if (categorical.indexOf($(select).val()) > -1) {
            $("#order").show();
            $("#aggregation option[value='ALL']").attr('disabled', 'disabled');
       
            if ($("#aggregation option:selected").val() == 'ALL') {
                $("#aggregation option[value='ALL']").removeAttr('selected');
                $("#aggregation option[value='AVG']").attr('selected', 'selected');
            }
        }
        else {
            $("#order").hide();
            $("#aggregation option[value='ALL']").removeAttr('disabled');
        }
    }


    function removeFilter(filter) {
        $(filter).parent().remove();
    }


    var numFilters = 0;

    function addFilter() {
        $.ajax(
        {
            url: 'get_filter_list.php',
            success: function(result)
            {
                var attrs = JSON.parse(result);

                var filter = document.createElement("select");
                numFilters++;
                $(filter).attr("name", "filter"+numFilters)
                         .attr("onchange", "updateFilterInfo(this)");
                for (var i = 0; i < attrs.length; i++) {
                    var option = document.createElement("option");
                    $(option).attr("name", attrs[i]).text(attrs[i]);

                    $(filter).append(option);
                }
                var filterDiv = document.createElement("div");
                $(filterDiv).attr("class", "filter");
                $(filterDiv).append("Filter ");
                $(filterDiv).append(filter);
                $(filterDiv).append(document.createElement("span"));
                var removeButton = document.createElement("button");
                $(removeButton).attr("onclick", "removeFilter(this)")
                        .attr("class", "removeButton")
                        .text("Remove");
                $(filterDiv).append(removeButton);
                $(filterDiv).append("<br>");

                $("#filters").append(filterDiv);

                $(filter).attr("onchange", "updateFilterInfo(this)");
                updateFilterInfo(filter);
            },
            error: function(jqXHR, status, error)
            {
                console.log('Error!');
                console.log(jqXHR);
                console.log(status);
                console.log(error);
            }
        });

    }


    function updateFilterInfo(select) {
        //var postData = $(this).serializeArray();
        var postData = {name: $(select).val()};
        $.ajax(
        {
            url: 'get_filter_info.php',
            type: "POST",
            data: postData,
            success: function(result)
            {
                var info = JSON.parse(result);
                if ('min' in info) {
                    var p = $(select).next("span").empty();
                    p.append(' between ');
                    var minInput = document.createElement("input");
                    $(minInput).attr("type", "number")
                               .attr("value", info['min'])
                               .attr("style", "width:6em")
                               .attr("name", $(select).attr("name")+"min");
                    p.append(minInput);
                    p.append(" and ");
                    var maxInput = document.createElement("input");
                    $(maxInput).attr("type", "number")
                               .attr("value", info['max'])
                               .attr("style", "width:6em")
                               .attr("name", $(select).attr("name")+"max");
                    p.append(maxInput);
                }
                else {
                    var p = $(select).next("span").empty();
                    p.append(' = ');
                    var list = document.createElement("select");
                    $(list).attr("name", $(select).attr("name")+"val");
                    for (var i = 0; i < info.length; i++) {
                        var option = document.createElement("option");
                        $(option).attr("name", info[i]).text(info[i]);
                        $(list).append(option);
                    }
                    p.append(list);
                }
            },
            error: function(jqXHR, status, error)
            {
                console.log('Error!');
                console.log(jqXHR);
                console.log(status);
                console.log(error);
            }
        });
        
    }
    
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

        // mode 0 = scatterplot
        // mode 1 = bar chart
        // mode 2 = line chart
        var mode = result.mode;

        var w = $("#canvas").width();
        var h = $("#canvas").height();

        // top, bottom, left, right
        var padding = [30, 50, 60, 30];

        // Bar chart parameters
        var barPadding = 5;
        var plotW = w - padding[2] - padding[3] - barPadding;
        var barWidth = plotW / dataset.length;
        barPadding = Math.min(5, barWidth/4);

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



        // Draw axis labels
        if (mode == 0 || mode == 2) {
            svg.append("text")
                .attr("class", "xlabel")
                .attr("text-anchor", "middle")
                .attr("x", w/2)
                .attr("y", h-10)
                .text(xlabel);
        }
        svg.append("text")
            .attr("class", "ylabel")
            .attr("text-anchor", "middle")
            .attr("x", -h/2)
            .attr("dy", "1.2em")
            .attr("transform", "rotate(-90)")
            .text(ylabel);

        // Plot
        if (mode == 0) {
            // Plot data points
            svg.selectAll("circle")
                   .data(dataset)
                   .enter()
                   .append("circle")    
                   .attr("class", "dot")
                   .attr("cx", function(d) { return xScale(d[xname]); })
                   .attr("cy", function(d) { return yScale(d[yname]); })
                   .attr("r", 5);
        }
        else if (mode == 1) {
            // Draw bars
            var bar = svg.selectAll("g")
                   .data(dataset)
                   .enter().append("g")
                   .attr("transform", function(d, i) { return "translate(" + (barPadding + padding[2] + i * barWidth) + ",0)"; });

            bar.append("rect")
                   .attr("class", "bar")
                   .attr("y", function(d) { return yScale(d[yname]); })
                   .attr("width", barWidth - barPadding)
                   .attr("height", function(d) { return h-padding[1]-yScale(d[yname]); });
            
            // Draw labels
            var angle = Math.atan(12 / barWidth) * 180 / Math.PI;  // Shallowest angle to avoid overlapping
            bar.append("g")
                .attr("class", "barLabel")
                .attr("transform", function(d) { return "translate(" + (barWidth/8) + "," + (h-padding[1] + 3) + ")"; })
                .append("text")
                .attr("dy", "0.75em")
                .attr("transform", "rotate("+angle+")")
                .text(function(d) { return d[xname]; });
        }
        else if (mode == 2) {
            line = d3.svg.line()
                .x(function(d) { return xScale(d[xname]); })
                .y(function(d) { return yScale(d[yname]); });

            svg.append("path")
                .datum(dataset)
                .attr("class", "line")
                .attr("d", line);
        }
            
        // Draw axes
        svg.append("g")
            .attr("class", "axis")
            .attr("transform", "translate(0," + (h-padding[1]) + ")")
            .call(xAxis);
        svg.append("g")
            .attr("class", "axis")
            .attr("transform", "translate(" + padding[2] + ",0)")
            .call(yAxis);

    } 
   
    // callback handler for form submit
    $("#ajaxform").submit(function(e)
    {
        var postData = $(this).serializeArray();
        postData.push({"name": "numFilters", "value": numFilters.toString()});
        var formURL = $(this).attr("action");
        
        $.ajax(
        {
            url: formURL,
            type: "POST",
            data: postData,
            success: function(result)
            {
                resultParsed = JSON.parse(result);
                //console.log(resultParsed);
                drawTitle(resultParsed);
                drawGraph(resultParsed);
            },
            error: function(jqXHR, status, error)
            {
                console.log('Error!');
                console.log(jqXHR);
                console.log(status);
                console.log(error);
            }
        });

        e.preventDefault();
        //e.unbind();
    });

    $("#ajaxform").submit();
</script>

<?php
    include 'includes/footer.php';
?>

