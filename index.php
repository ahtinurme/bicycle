<?php
require 'config.php';
$bicycles = $pdo->query(' SELECT * FROM bicycles ORDER by added DESC');
?>
<html>
<head>
    <title>Bicycles</title>
</head>
<body>
<a href="edit-bicycle.php">Add a bicycle</a>
<table>
    <thead>
    <tr>
        <th>Name</th>
        <th>Brand</th>
        <th>Weight</th>
        <th>Price</th>
        <th>&nbsp;</th>
    </tr>
    <?php foreach ($bicycles as $bicycle) { ?>
        <tr>
            <td><?php echo $bicycle['name']; ?></td>
            <td><?php echo $bicycle['brand']; ?></td>
            <td><?php echo $bicycle['weight']; ?>kg</td>
            <td><?php echo $bicycle['price']; ?>€</td>
            <td><a href="delete-bicycle.php?id=<?php echo $bicycle['id']; ?>">Delete</a> | <a href="edit-bicycle.php?id=<?php echo $bicycle['id']; ?>">Edit</a></td>
        </tr>
    <?php } ?>
    </thead>
</table>
</body>
</html>