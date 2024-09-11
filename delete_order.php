<?php
// Include database connection
include "db_connection.php";

// Check if o_no is set in POST data
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $o_no = $_POST['o_no'] ?? '';

    if (empty($o_no)) {
        echo "<script>alert('Order Number is required!'); window.history.back();</script>";
        exit;
    }

    // Delete from orderservice
    $sql = "DELETE FROM orderservice WHERE s_o_no = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $o_no);
    if (!$stmt->execute()) {
        echo "<script>alert('Error deleting order services: " . $stmt->error . "'); window.history.back();</script>";
        exit;
    }

    // Delete from order
    $sql = "DELETE FROM `order` WHERE o_no = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $o_no);
    if (!$stmt->execute()) {
        echo "<script>alert('Error deleting order: " . $stmt->error . "'); window.history.back();</script>";
        exit;
    }

    echo "<script>
            alert('Order and services deleted successfully!');
            window.location.href = 'order_main.php';
          </script>";
} else {
    echo "<script>alert('Invalid request method!'); window.history.back();</script>";
}

$stmt->close();
$conn->close();
?>
