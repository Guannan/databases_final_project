<?php include 'header.php'; ?>

<?php
    # Available fields for graphing, along with the table they are stored in
    $numeric_fields = array("Num Connections", "Age", "Num Skills");
?>


<h1>Explore Relationships</h1>
<form action="graph_relations.php" method="post"> 
    <?php
        echo 'X Variable: <select name="xvar">';
        foreach ($numeric_fields as $f) {
            echo '<option value="' . $f . '">' . $f . '</option>';
        }
        echo '</select><br><br>';

        echo 'Y Variable: <select name="yvar">';
        foreach ($numeric_fields as $f) {
            echo '<option value="' . $f . '">' . $f . '</option>';
        }
        echo '</select><br><br>';
    ?>

    <input type="submit" value="See graph" /> 
</form>

<?php include 'footer.php' ?>
