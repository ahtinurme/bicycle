<?php
require 'config.php';
$bicycles = $pdo->query('
  SELECT b.*, 
  IFNULL((SELECT SUM(bp.weight) FROM bicycle_parts bp WHERE bp.bicycle_id = b.id), 0) weight, 
  IFNULL((SELECT SUM(bp2.price) FROM bicycle_parts bp2 WHERE bp2.bicycle_id = b.id), 0) price 
  FROM bicycles b ORDER by b.added DESC');
?>
<html>
<head>
    <title>Bycycles</title>
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