<?php
include 'common.php';
// Retrieve form data
$expense_id = isset($_POST['expense_id']) ? intval($_POST['expense_id']) : 0;
$description = isset($_POST['e_description']) ? $_POST['e_description'] : '';
$price = isset($_POST['e_price']) ? floatval($_POST['e_price']) : 0.0;
$quantity = isset($_POST['e_qty']) ? intval($_POST['e_qty']) : 0;
$memo = isset($_POST['e_memo']) ? $_POST['e_memo'] : '';
$e_amount = $price * $quantity;
$selected_date = isset($_POST['date']) ? $_POST['date'] : '';

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Prepare the SQL query to update the expense
$sql_update_expense = "
    UPDATE `expense`
    SET e_description = ?, e_price = ?, e_qty = ?, e_memo = ?, e_amount = ?
    WHERE e_no = ?
";

// Prepare and bind the statement
$stmt = $conn->prepare($sql_update_expense);
if (!$stmt) {
    die("Error preparing query: " . $conn->error);
}

// Bind parameters
$stmt->bind_param('sdisdi', $description, $price, $quantity, $memo, $e_amount, $expense_id);

// Execute the query
if ($stmt->execute()) {
    echo "Expense updated successfully.";
    header("Location: balance_sheet.php");
} else {
    echo "Error updating expense: " . $stmt->error;
}

// Close the statement
$stmt->close();
$conn->close();
?>
