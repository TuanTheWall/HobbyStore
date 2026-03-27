<?php
// baocao_detail.php — trả về JSON với thông tin để redirect đến trang chi tiết
header('Content-Type: application/json');
$conn = mysqli_connect("localhost","root","","hobbystore");
$conn->set_charset("utf8");

$loai       = $_GET['loai'] ?? '';
$product_id = $conn->real_escape_string($_GET['product_id'] ?? '');

if ($loai === 'nhap') {
    $sql = "
        SELECT pr.receipt_code
        FROM purchase_receipt_items pri
        JOIN purchase_receipts pr ON pri.receipt_code = pr.receipt_code
        WHERE pri.product_id = '$product_id'
        ORDER BY pr.import_date DESC
        LIMIT 1
    ";
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo json_encode(['redirect' => 'receipt_detail_page.php?code=' . urlencode($row['receipt_code']) . '&from=baocao']);
    } else {
        echo json_encode(['error' => 'Không tìm thấy phiếu nhập']);
    }

} elseif ($loai === 'xuat') {
    $sql = "
        SELECT o.id_order
        FROM order_item oi
        JOIN orders o ON oi.id_order = o.id_order
        WHERE oi.ProductID = '$product_id' AND o.status = 'Đã giao'
        ORDER BY o.order_date DESC
        LIMIT 1
    ";
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo json_encode(['redirect' => 'order_detail_page.php?id=' . urlencode($row['id_order']) . '&from=baocao']);
    } else {
        echo json_encode(['error' => 'Không tìm thấy đơn hàng']);
    }

} else {
    echo json_encode(['error' => 'Yêu cầu không hợp lệ']);
}