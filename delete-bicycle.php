<?php
require 'config.php';
$sth = $pdo->prepare('DELETE FROM bicycles WHERE id = :id');
$sth->execute([':id' => isset($_REQUEST['id']) ? $_REQUEST['id'] : 0]);
header('Location: ./');
