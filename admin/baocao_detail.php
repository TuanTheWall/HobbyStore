<?php
header('Content-Type: application/json; charset=utf-8');
$conn = mysqli_connect("localhost","root","","hobbystore");
mysqli_set_charset($conn, "utf8");

$loai       = $_GET['loai']       ?? '';
$product_id = mysqli_real_escape_string($conn, $_GET['product_id'] ?? '');
$from       = mysqli_real_escape_string($conn, $_GET['from'] ?? '');
$to         = mysqli_real_escape_string($conn, $_GET['to']   ?? '');

if($loai === 'nhap'){
    $where = "pri.product_id = '$product_id'";
    if($from != "") $where .= " AND pr.import_date >= '$from'";
    if($to   != "") $where .= " AND pr.import_date <= '$to'";

    $sql = "
        SELECT pr.receipt_code, pr.import_date,
               SUM(pri.quantity) as tong_sl,
               SUM(pri.quantity * pri.price) as tong_tien
        FROM purchase_receipt_items pri
        JOIN purchase_receipts pr ON pri.receipt_code = pr.receipt_code
        WHERE $where
        GROUP BY pr.receipt_code, pr.import_date
        ORDER BY pr.import_date DESC
    ";
    $result = mysqli_query($conn, $sql);
    $rows = [];
    while($r = mysqli_fetch_assoc($result)) $rows[] = $r;
    echo json_encode(['type' => 'nhap', 'data' => $rows], JSON_UNESCAPED_UNICODE);

} elseif($loai === 'xuat'){
    $where = "oi.ProductID = '$product_id' AND o.status = 'Đã giao'";
    if($from != "") $where .= " AND o.order_date >= '$from'";
    if($to   != "") $where .= " AND o.order_date <= '$to'";

    $sql = "
        SELECT o.id_order, o.order_date,
               o.receiver_name, o.receiver_phone,
               oi.quantity
        FROM order_item oi
        JOIN orders o ON oi.id_order = o.id_order
        WHERE $where
        ORDER BY o.order_date DESC
    ";
    $result = mysqli_query($conn, $sql);
    $rows = [];
    while($r = mysqli_fetch_assoc($result)) $rows[] = $r;
    echo json_encode(['type' => 'xuat', 'data' => $rows], JSON_UNESCAPED_UNICODE);

} else {
    echo json_encode(['error' => 'Yêu cầu không hợp lệ']);
}