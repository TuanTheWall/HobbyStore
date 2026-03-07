<?php
require_once "config.php";

$id = $_POST['id'];
$qty = $_POST['qty'];

mysqli_query($conn,"
UPDATE cart_item
SET quantity='$qty'
WHERE cart_item_id='$id'
");