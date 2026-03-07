<?php
require_once "config.php";

$id = $_POST['id'];

mysqli_query($conn,"
DELETE FROM cart_item
WHERE cart_item_id='$id'
");