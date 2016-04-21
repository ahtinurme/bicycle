<?php
require 'config.php';
if ($_POST) {

}
$sth = $pdo->prepare('SELECT * FROM bicycles WHERE id = :id');
$sth->execute([':id' => $_REQUEST['id']]);
$bicycle = $sth->fetchAll();
$bicycleParts = [];
if (isset($bicycle[0])) {
    $partsSth = $pdo->prepare('SELECT * FROM bicyle_parts WHERE bicycle_id = :id');
    $sth->execute([':id' => $_REQUEST['id']]);
    $bicycleParts = $sth->fetchAll();
}
?>

<html>
<head>
    <title>Add a bicycle</title>
</head>
<body>
<form action="" method="post">
    <?php if (isset($bicycle[0]['id'])) { ?>
        <input type="hidden" name="id" value="<?php echo $bicycle[0]['id']; ?>">
    <?php } ?>
    <div>
        <label for="name">Name</label>
        <input name="name" id="name" value="<?php echo isset($bicycle[0]['name']) ? $bicycle[0]['name'] : ''; ?>">
    </div>
    <div>
        <label for="brand">Brand</label>
        <input name="brand" id="brand" value="<?php echo isset($bicycle[0]['brand']) ? $bicycle[0]['brand'] : ''; ?>">
    </div>
    <div clas

    <button type="submit">Save bicycle</button>
</form>
</body>
</html>
