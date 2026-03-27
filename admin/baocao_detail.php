<?php
// baocao_detail.php — trả về nội dung HTML cho modal
$conn = mysqli_connect("localhost","root","","hobbystore");
$conn->set_charset("utf8");

$loai       = $_GET['loai'] ?? '';
$product_id = $conn->real_escape_string($_GET['product_id'] ?? '');

// CSS cho modal con (chỉ cần 1 lần, baocao.php đã có modal-table)
echo '<style>
.btn-xemct {
  padding: 4px 10px;
  font-size: 12px;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  font-weight: 600;
  background: #e0eaff;
  color: #1a3a8f;
  transition: 0.2s;
}
.btn-xemct:hover { background: #c0d4ff; }

/* Modal con */
.modal2-overlay {
  display: none;
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,0.6);
  z-index: 2000;
  align-items: center;
  justify-content: center;
}
.modal2-overlay.active { display: flex; }
.modal2-box {
  background: #fff;
  border-radius: 12px;
  padding: 24px 28px;
  width: 600px;
  max-width: 95vw;
  max-height: 75vh;
  overflow-y: auto;
  box-shadow: 0 8px 30px rgba(0,0,0,0.3);
  position: relative;
}
.modal2-box h4 {
  font-size: 18px;
  color: #22314e;
  margin-bottom: 14px;
  border-bottom: 2px solid #eee;
  padding-bottom: 8px;
}
.modal2-close {
  position: absolute;
  top: 12px; right: 14px;
  background: none; border: none;
  font-size: 22px; cursor: pointer;
  color: #888;
}
.modal2-close:hover { color: red; }
.info-row { display: flex; gap: 8px; margin-bottom: 8px; font-size: 15px; }
.info-row .lbl { font-weight: 700; color: #444; min-width: 130px; }
</style>

<!-- Modal con (chi tiết phiếu/đơn) -->
<div class="modal2-overlay" id="modal2Overlay" onclick="closeModal2(event)">
  <div class="modal2-box">
    <button class="modal2-close" onclick="document.getElementById(\'modal2Overlay\').classList.remove(\'active\')">&times;</button>
    <h4 id="modal2Title">Chi tiết</h4>
    <div id="modal2Body"></div>
  </div>
</div>

<script>
function closeModal2(e) {
  const overlay = document.getElementById("modal2Overlay");
  if (e.target === overlay) overlay.classList.remove("active");
}
document.addEventListener("keydown", function(e){
  if(e.key === "Escape") document.getElementById("modal2Overlay").classList.remove("active");
});
</script>
';

if ($loai === 'nhap') {
    $sql = "
        SELECT pr.receipt_code, pr.import_date, pr.total_quantity, pr.total_value,
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
              <th></th>
            </tr>';

    while ($row = $result->fetch_assoc()) {
        $gia      = number_format($row['price'], 0, ',', '.') . ' VNĐ';
        $tong     = number_format($row['total_value'], 0, ',', '.') . ' VNĐ';
        $tong_qty = $row['total_quantity'];
        $code     = htmlspecialchars($row['receipt_code']);
        $date     = $row['import_date'];

        // Encode data cho modal con
        $data = htmlspecialchars(json_encode([
            'receipt_code'   => $row['receipt_code'],
            'import_date'    => $date,
            'quantity'       => $row['quantity'],
            'price'          => number_format($row['price'], 0, ',', '.') . ' VNĐ',
            'total_quantity' => $tong_qty,
            'total_value'    => $tong,
        ], JSON_UNESCAPED_UNICODE), ENT_QUOTES);

        echo "<tr>
                <td>{$code}</td>
                <td>{$date}</td>
                <td>{$row['quantity']}</td>
                <td>{$gia}</td>
                <td><button class='btn-xemct' onclick='xemChiTietPhieu({$data})'>Chi tiết</button></td>
              </tr>";
    }
    echo '</table>';

    echo "<script>
function xemChiTietPhieu(d) {
  document.getElementById('modal2Title').textContent = 'Chi tiết phiếu nhập — ' + d.receipt_code;
  document.getElementById('modal2Body').innerHTML = \`
    <div class='info-row'><span class='lbl'>Mã phiếu nhập:</span><span>\${d.receipt_code}</span></div>
    <div class='info-row'><span class='lbl'>Ngày nhập:</span><span>\${d.import_date}</span></div>
    <div class='info-row'><span class='lbl'>Số lượng SP này:</span><span>\${d.quantity}</span></div>
    <div class='info-row'><span class='lbl'>Giá nhập SP này:</span><span>\${d.price}</span></div>
    <hr style='margin:12px 0;border-color:#eee;'>
    <div class='info-row'><span class='lbl'>Tổng SL cả phiếu:</span><span>\${d.total_quantity}</span></div>
    <div class='info-row'><span class='lbl'>Tổng giá trị phiếu:</span><span>\${d.total_value}</span></div>
  \`;
  document.getElementById('modal2Overlay').classList.add('active');
}
</script>";

} elseif ($loai === 'xuat') {
    $sql = "
        SELECT o.id_order, o.order_date, o.status, o.total,
               o.receiver_name, o.receiver_phone, o.receiver_address,
               o.payment_method, o.note,
               c.username, c.email,
               oi.quantity
        FROM order_item oi
        JOIN orders o ON oi.id_order = o.id_order
        JOIN customers c ON o.customer_id = c.customer_id
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
              <th></th>
            </tr>';

    while ($row = $result->fetch_assoc()) {
        $order_id = htmlspecialchars($row['id_order']);
        $total    = $row['total'] ? number_format($row['total'], 0, ',', '.') . ' VNĐ' : '—';

        $data = htmlspecialchars(json_encode([
            'id_order'         => $row['id_order'],
            'order_date'       => $row['order_date'],
            'status'           => $row['status'],
            'quantity'         => $row['quantity'],
            'total'            => $total,
            'receiver_name'    => $row['receiver_name'] ?? $row['username'],
            'receiver_phone'   => $row['receiver_phone'] ?? '—',
            'receiver_address' => $row['receiver_address'] ?? '—',
            'payment_method'   => $row['payment_method'] ?? '—',
            'note'             => $row['note'] ?? '—',
            'email'            => $row['email'],
        ], JSON_UNESCAPED_UNICODE), ENT_QUOTES);

        echo "<tr>
                <td>{$order_id}</td>
                <td>{$row['order_date']}</td>
                <td>{$row['status']}</td>
                <td>{$row['quantity']}</td>
                <td><button class='btn-xemct' onclick='xemChiTietDon({$data})'>Chi tiết</button></td>
              </tr>";
    }
    echo '</table>';

    echo "<script>
function xemChiTietDon(d) {
  document.getElementById('modal2Title').textContent = 'Chi tiết đơn hàng — ' + d.id_order;
  document.getElementById('modal2Body').innerHTML = \`
    <div class='info-row'><span class='lbl'>Mã đơn hàng:</span><span>\${d.id_order}</span></div>
    <div class='info-row'><span class='lbl'>Ngày đặt:</span><span>\${d.order_date}</span></div>
    <div class='info-row'><span class='lbl'>Trạng thái:</span><span>\${d.status}</span></div>
    <div class='info-row'><span class='lbl'>Số lượng SP này:</span><span>\${d.quantity}</span></div>
    <div class='info-row'><span class='lbl'>Tổng đơn hàng:</span><span>\${d.total}</span></div>
    <hr style='margin:12px 0;border-color:#eee;'>
    <div class='info-row'><span class='lbl'>Người nhận:</span><span>\${d.receiver_name}</span></div>
    <div class='info-row'><span class='lbl'>SĐT:</span><span>\${d.receiver_phone}</span></div>
    <div class='info-row'><span class='lbl'>Địa chỉ:</span><span>\${d.receiver_address}</span></div>
    <div class='info-row'><span class='lbl'>Thanh toán:</span><span>\${d.payment_method}</span></div>
    <div class='info-row'><span class='lbl'>Email:</span><span>\${d.email}</span></div>
    <div class='info-row'><span class='lbl'>Ghi chú:</span><span>\${d.note}</span></div>
  \`;
  document.getElementById('modal2Overlay').classList.add('active');
}
</script>";

} else {
    echo '<div class="modal-empty">Yêu cầu không hợp lệ.</div>';
}