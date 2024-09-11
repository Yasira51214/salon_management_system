<?php
include 'db_connection.php';

// Check if form data is posted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get posted data
    $customerName = $_POST['customerName'] ?? '';
    $date = $_POST['date'] ?? '';
    $ratings = $_POST['rating'] ?? []; // Array of ratings
    $reviews = $_POST['review'] ?? []; // Array of reviews

    // Validate data
    if (empty($customerName) || empty($date) || !is_array($ratings) || !is_array($reviews)) {
        echo "Error: Missing or invalid data.";
        exit;
    }

    // Fetch customer ID based on name
    $customer_sql = "SELECT c_no FROM customer WHERE c_name = ?";
    $stmt = $conn->prepare($customer_sql);
    if (!$stmt) {
        echo "Error preparing customer query: " . $conn->error;
        exit;
    }
    $stmt->bind_param('s', $customerName);
    $stmt->execute();
    $customer_result = $stmt->get_result();
    $customer = $customer_result->fetch_assoc();
    $customerID = $customer['c_no'] ?? 0;

    // Assuming you have a way to get the order ID
    $order_sql = "SELECT o_no FROM `order` WHERE o_c_no = ? ORDER BY o_date DESC LIMIT 1";
    $stmt = $conn->prepare($order_sql);
    if (!$stmt) {
        echo "Error preparing order query: " . $conn->error;
        exit;
    }
    $stmt->bind_param('i', $customerID);
    $stmt->execute();
    $order_result = $stmt->get_result();
    $order = $order_result->fetch_assoc();
    $orderID = $order['o_no'] ?? 0;

    // Prepare the SQL to insert feedback
    $sql = "INSERT INTO feedback (
        f_c_no, f_o_no, f_rate1, f_rate2, f_rate3, f_rate4, f_rate5, f_rate6, f_rate7, f_rate8, f_rate9, f_rate10,
        f_review1, f_review2, f_review3, f_review4, f_review5, f_review6, f_review7, f_review8, f_review9, f_review10, f_date
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        echo "Error preparing feedback query: " . $conn->error;
        exit;
    }

    // Initialize variables for binding
    $rate1 = $ratings[0] ?? 0;
    $rate2 = $ratings[1] ?? 0;
    $rate3 = $ratings[2] ?? 0;
    $rate4 = $ratings[3] ?? 0;
    $rate5 = $ratings[4] ?? 0;
    $rate6 = $ratings[5] ?? 0;
    $rate7 = $ratings[6] ?? 0;
    $rate8 = $ratings[7] ?? 0;
    $rate9 = $ratings[8] ?? 0;
    $rate10 = $ratings[9] ?? 0;

    $review1 = $reviews[0] ?? '';
    $review2 = $reviews[1] ?? '';
    $review3 = $reviews[2] ?? '';
    $review4 = $reviews[3] ?? '';
    $review5 = $reviews[4] ?? '';
    $review6 = $reviews[5] ?? '';
    $review7 = $reviews[6] ?? '';
    $review8 = $reviews[7] ?? '';
    $review9 = $reviews[8] ?? '';
    $review10 = $reviews[9] ?? '';

    // Bind parameters
    $stmt->bind_param(
        'ii' . str_repeat('i', 10) . str_repeat('s', 10) . 's',
        $customerID,
        $orderID,
        $rate1, $rate2, $rate3, $rate4, $rate5, $rate6, $rate7, $rate8, $rate9, $rate10,
        $review1, $review2, $review3, $review4, $review5, $review6, $review7, $review8, $review9, $review10,
        $date
    );
    
    if ($stmt->execute()) {
        echo "<script>alert('Feedback saved successfully!'); window.location.href = 'feedback_main.php';</script>";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
