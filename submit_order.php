<?php
// Database connection
require "db_connection.php";

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'] ?? '';
    $mobile = $_POST['mobile'] ?? '';
    $note = $_POST['note'] ?? '';
    $selectedServices = $_POST['selected_services'] ?? [];
    $quantities = $_POST['quantities'] ?? [];

    // Fetch customer number (c_no) from the customer table
    $sql = "SELECT c_no FROM customer WHERE c_name = ? AND c_mobile = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $name, $mobile);
    $stmt->execute();
    $result = $stmt->get_result();
    $customer = $result->fetch_assoc();

    if (!$customer) {
        echo json_encode(['status' => 'error', 'message' => 'Customer not found']);
        exit;
    }

    $o_c_no = $customer['c_no'];

    // Prepare to insert order
    $orderData = array_fill(0, 41, "NULL"); // 41 fields in total, including all o_cat, o_name, o_price, o_qty, o_memo, and o_amount
    $orderData[0] = $o_c_no; // o_c_no is at index 0
    $totalAmount = 0;

    // Fill orderData array with services
    foreach ($selectedServices as $index => $service) {
        $catIndex = $index * 4 + 1; // calculate the starting index for the category fields

        $orderData[$catIndex] = isset($service['cat']) ? "'" . $conn->real_escape_string($service['cat']) . "'" : "NULL";
        $orderData[$catIndex + 1] = isset($service['name']) ? "'" . $conn->real_escape_string($service['name']) . "'" : "NULL";
        $orderData[$catIndex + 2] = isset($service['price']) ? $service['price'] : "NULL";
        $orderData[$catIndex + 3] = isset($quantities[$index]) ? $quantities[$index] : "NULL";

        if (isset($service['price']) && isset($quantities[$index])) {
            $totalAmount += $service['price'] * $quantities[$index];
        }
    }

    // Add note and total amount
    $orderData[37] = "'" . $conn->real_escape_string($note) . "'"; // o_memo is at index 37
    $orderData[38] = $totalAmount; // o_amount is at index 38

    // Convert orderData array to a string for the SQL query
    $orderDataString = implode(", ", $orderData);

    $sql = "INSERT INTO `order` 
            (`o_c_no`, `o_cat1`, `o_name1`, `o_price1`, `o_qty1`, 
             `o_cat2`, `o_name2`, `o_price2`, `o_qty2`, `o_cat3`, `o_name3`, `o_price3`, `o_qty3`, 
             `o_cat4`, `o_name4`, `o_price4`, `o_qty4`, `o_cat5`, `o_name5`, `o_price5`, `o_qty5`, 
             `o_cat6`, `o_name6`, `o_price6`, `o_qty6`, 
             `o_cat7`, `o_name7`, `o_price7`, `o_qty7`,
             `o_cat8`, `o_name8`, `o_price8`, `o_qty8`,
             `o_cat9`, `o_name9`, `o_price9`, `o_qty9`,
             `o_cat10`, `o_name10`, `o_price10`, `o_qty10`,
             `o_memo`, `o_amount`) 
            VALUES ($orderDataString)";

    if ($conn->query($sql) === TRUE) {
        echo json_encode(['status' => 'success', 'message' => 'New order created successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error: ' . $sql . '<br>' . $conn->error]);
    }
}

$conn->close();
?>
