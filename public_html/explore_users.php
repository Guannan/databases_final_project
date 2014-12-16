<?php include 'includes/header.php'; ?>

<h1>Explore Top Users</h1>
<form action="top_users.php" method="post"> 
    Attribute to sort on: <select name="attr">
        <option value="Fname">First Name</option>
        <option value="Lname">Last Name</option>
        <option value="NumConnection">Number of Connections</option>
        <option value="Age">Age</option>
    </select>
    <br><br>

    Maximum rows to return:<input type="number" name="limit" value="10"/>
    <br><br>

    <input type="radio" name="order" value="DESC" checked="checked">Decreasing Order
    <br>
    <input type="radio" name="order" value="ASC">Increasing Order
    <br><br>

    <input type="submit" value="See top users" /> 
</form>

<?php include 'includes/footer.php'; ?>
