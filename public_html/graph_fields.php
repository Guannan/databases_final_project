<?php include 'includes/header.php'; ?>

<?php
    # Available fields for graphing, along with the table they are stored in
    $numeric_fields = array("Num Connections", "Age", "Num Skills", "Num Languages", "Num Jobs");
?>


<h1>Explore Relationships</h1>
<form action="graph_relations.php" method="post"> 
    <?php
        echo 'Plot <select name="yvar">';
        foreach ($numeric_fields as $f) {
            echo '<option value="' . $f . '">' . $f . '</option>';
        }
        echo '</select>';

        echo ' as a function of <select name="xvar">';
        foreach ($numeric_fields as $f) {
            echo '<option value="' . $f . '">' . $f . '</option>';
        }
        echo '</select><br><br>';
    ?>

    <input type="submit" value="See graph" /> 
</form>

<?php include 'includes/footer.php' ?>
