<?php
// baocao_detail.php — trả về nội dung HTML cho modal
$conn = mysqli_connect("localhost","root","","hobbystore");
$conn->set_charset("utf8");

$loai       = $_GET['loai'] ?? '';
$product_id = $conn->real_escape_string($_GET['product_id'] ?? '');

if ($loai === 'nhap') {
    $sql = "
        SELECT pr.receipt_code, pr.import_date,
               pri.quantity, pri.price
        FROM purchase_receipt_items pri
        JOIN purchase_receipts pr ON pri.receipt_code = pr.receipt_code
        WHERE pri.product_id = '$product_id'
        ORDER BY pr.import_date DESC
    ";
    $result = $conn->query($sql);

    if (!$result || $result->num_rows === 0) {
        echo '<div class="modal-empty">Không có phiếu nhập nào.</div>';
        exit;
    }

    echo '<table class="modal-table">
            <tr>
              <th>Mã phiếu nhập</th>
              <th>Ngày nhập</th>
              <th>Số lượng</th>
              <th>Giá nhập</th>
            </tr>';

    while ($row = $result->fetch_assoc()) {
        $gia = number_format($row['price'], 0, ',', '.') . ' VNĐ';
        echo "<tr>
                <td>{$row['receipt_code']}</td>
                <td>{$row['import_date']}</td>
                <td>{$row['quantity']}</td>
                <td>{$gia}</td>
              </tr>";
    }
    echo '</table>';

} elseif ($loai === 'xuat') {
    $sql = "
        SELECT o.id_order, o.order_date, o.status,
               oi.quantity
        FROM order_item oi
        JOIN orders o ON oi.id_order = o.id_order
        WHERE oi.ProductID = '$product_id'
          AND o.status = 'Đã giao'
        ORDER BY o.order_date DESC
    ";
    $result = $conn->query($sql);

    if (!$result || $result->num_rows === 0) {
        echo '<div class="modal-empty">Không có đơn hàng nào.</div>';
        exit;
    }

    echo '<table class="modal-table">
            <tr>
              <th>Mã đơn hàng</th>
              <th>Ngày đặt</th>
              <th>Trạng thái</th>
              <th>Số lượng</th>
            </tr>';

    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>{$row['id_order']}</td>
                <td>{$row['order_date']}</td>
                <td>{$row['status']}</td>
                <td>{$row['quantity']}</td>
              </tr>";
    }
    echo '</table>';

} else {
    echo '<div class="modal-empty">Yêu cầu không hợp lệ.</div>';
}