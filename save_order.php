<?php
// Database connection
include "db_connection.php";

// Fetch data from POST
$c_no = $_POST['c_no'] ?? ''; // Get c_no from POST data
$name = $_POST['name'] ?? '';
$date = $_POST['selectedDate'] ?? ''; // Correctly retrieve the date from POST data
$memo = $_POST['memo'] ?? '';
$pro_no = $_POST['p_no'] ?? '';
$paymentMethod = $_POST['payment-method'] ?? ''; // Get payment method from POST data
$services = [];
$totalAmount = 0; // Initialize total amount

// Collect service data from POST
for ($i = 1; isset($_POST["s_no$i"]); $i++) {
    $si_no = $_POST["s_no$i"]; // Get si_no from POST data
    $s_cat = $_POST["s_cat$i"];
    $s_qty = $_POST["s_qty$i"];
    $s_price = $_POST["s_price$i"];

    // Validate if quantity is greater than 0
    if ($s_qty > 0) {
        $services[] = [
            'si_no' => $si_no,
            's_cat' => $s_cat,
            's_qty' => $s_qty,
            's_price' => $s_price
        ];
        // Calculate total amount
        $totalAmount += $s_qty * $s_price;
    }
}

// Insert into order table
$sql = "INSERT INTO `order` (o_c_no, o_date, o_memo, o_amount, o_pymt_method, o_pro_no) VALUES (?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("issdsi", $c_no, $date, $memo, $totalAmount, $paymentMethod, $pro_no);

if ($stmt->execute()) {
    $order_id = $stmt->insert_id;

    // Insert into orderservice table
    foreach ($services as $service) {
        $sql = "INSERT INTO orderservice (s_o_no, s_order_no, s_cat, s_si_no, s_qty, s_price) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iiisii", $order_id, $c_no, $service['s_cat'], $service['si_no'], $service['s_qty'], $service['s_price']);
        $stmt->execute(); 
    }
}
$stmt->close();
$conn->close();
?>
