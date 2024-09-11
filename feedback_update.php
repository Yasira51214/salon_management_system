<?php
include 'common.php';
?>
<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customerName = $_POST['customerName'];
    $date = $_POST['date'];
    $ratings = $_POST['rating'];
    $reviews = $_POST['review'];

    // Fetch customer ID based on name
    $customer_sql = "SELECT c_no FROM customer WHERE c_name = ?";
    $stmt = $conn->prepare($customer_sql);
    if (!$stmt) {
        die("Error preparing customer query: " . $conn->error);
    }
    $stmt->bind_param('s', $customerName);
    $stmt->execute();
    $customer_result = $stmt->get_result();
    $customer = $customer_result->fetch_assoc();
    $customerID = $customer['c_no'];

    // Update feedback entries
    $feedback_update_sql = "UPDATE feedback SET ";
    $feedback_updates = [];
    $feedback_types = "";
    $feedback_params = [];

    for ($i = 1; $i <= 10; $i++) {
        $feedback_updates[] = "f_rate$i = ?, f_review$i = ?";
        $feedback_types .= "is";
        $feedback_params[] = $ratings[$i - 1] ?? 0;
        $feedback_params[] = $reviews[$i - 1] ?? '';
    }

    $feedback_update_sql .= implode(", ", $feedback_updates);
    $feedback_update_sql .= " WHERE f_c_no = ? AND f_date = ?";
    $feedback_types .= "is";
    $feedback_params[] = $customerID;
    $feedback_params[] = $date;

    $stmt = $conn->prepare($feedback_update_sql);
    if (!$stmt) {
        die("Error preparing feedback update query: " . $conn->error);
    }

    $stmt->bind_param($feedback_types, ...$feedback_params);
    $stmt->execute();
    echo "<script>alert('Feedback saved successfully!'); window.location.href = 'feedback_main.php';</script>";
    // header("Location: feedback_main.php");
    exit();
}
?>
