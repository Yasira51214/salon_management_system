<?php
include 'common.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Get POST data
$o_no = $_POST['o_no'] ?? '';
$paymentMethod = $_POST['payment-method'] ?? '';
$note = $_POST['note'] ?? '';
$services = $_POST['services'] ?? [];
$quantities = $_POST['quantities'] ?? [];

// Validate input
if (empty($o_no)) {
    die("Order number is required.");
}

// Begin transaction
$conn->begin_transaction();

try {
    // Update order memo and payment method
    $totalAmount = 0;

    foreach ($services as $serviceId) {
        $quantity = (int)($quantities[$serviceId] ?? 1);
        
        // Fetch service details
        $sql_service = "SELECT si_price, si_cat FROM service_item WHERE si_no = ?";
        $stmt_service = $conn->prepare($sql_service);
        if (!$stmt_service) {
            throw new Exception("Error preparing service statement: " . $conn->error);
        }
        $stmt_service->bind_param("i", $serviceId);
        $stmt_service->execute();
        $result_service = $stmt_service->get_result();
        $service = $result_service->fetch_assoc();
        
        $price = $service['si_price'] ?? 0;
        $category = $service['si_cat'] ?? '';

        $totalAmount += $price * $quantity;

        // Insert or update service records in `orderservice` table
        $sql_service_insert = "INSERT INTO `orderservice` (s_order_no, s_cat, s_si_no, s_price, s_qty) 
                               VALUES (?, ?, ?, ?, ?) 
                               ON DUPLICATE KEY UPDATE s_qty = VALUES(s_qty), s_price = VALUES(s_price)";
        $stmt_service_insert = $conn->prepare($sql_service_insert);
        if (!$stmt_service_insert) {
            throw new Exception("Error preparing service insert statement: " . $conn->error);
        }
        $stmt_service_insert->bind_param("issii", $o_no, $category, $serviceId, $price, $quantity);
        $stmt_service_insert->execute();
    }

    // Update order memo and total amount
    $sql_update_order = "UPDATE `order` SET o_memo = ?, o_amount = ?, o_pymt_method = ? WHERE o_no = ?";
    $stmt_update_order = $conn->prepare($sql_update_order);
    if (!$stmt_update_order) {
        throw new Exception("Error preparing order update statement: " . $conn->error);
    }
    $stmt_update_order->bind_param("sisi", $note, $totalAmount, $paymentMethod, $o_no);
    $stmt_update_order->execute();

    // Commit transaction
    $conn->commit();

    // Redirect or show a success message
    header("Location: order_main.php");
    exit();

} catch (Exception $e) {
    $conn->rollback();
    die("Error: " . $e->getMessage());
}

$conn->close();
?>
