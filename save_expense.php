<?php
include 'db_connection.php';

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data and trim whitespace
    $e_exp_date = trim($_POST['e_exp_date']);
    $e_description = trim($_POST['description']);
    $e_price = trim($_POST['price']);
    $e_qty = trim($_POST['quantity']);
    $e_memo = trim($_POST['memo']);
    $category = trim($_POST['category']);

    // Validate form data
    if (empty($e_exp_date) || empty($e_description) || empty($e_price) || empty($e_qty) || $category === 'Select') {
        $message = "Please fill in all required fields.";
    } else if (!is_numeric($e_price) || !is_numeric($e_qty)) {
        $message = "Price and Quantity must be numeric values.";
    } else {
        // Calculate amount
        $e_price = (float) $e_price; // Ensure price is treated as a float
        $e_qty = (int) $e_qty;       // Ensure quantity is treated as an integer
        $e_amount = $e_price * $e_qty;

        // Get ex_no from expense_cat table
        $stmt = $conn->prepare("SELECT ex_no FROM expense_cat WHERE ex_cat_name = ?");
        if (!$stmt) {
            die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
        }
        $stmt->bind_param("s", $category);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $ex_no = $row['ex_no'];
        } else {
            $message = "No category found with name: $category";
            $stmt->close();
            $conn->close();
            echo "<script>alert('$message'); window.history.back();</script>";
            exit();
        }
        $stmt->close();

        // Insert data into expense table
        $stmt = $conn->prepare("INSERT INTO expense (e_exp_date, e_description, e_price, e_qty, e_memo, e_amount, e_ex_no) VALUES (?, ?, ?, ?, ?, ?, ?)");
        if (!$stmt) {
            die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
        }
        $stmt->bind_param("ssddssi", $e_exp_date, $e_description, $e_price, $e_qty, $e_memo, $e_amount, $ex_no);

        if ($stmt->execute()) {
            $message = "Expense saved successfully!";
            if (isset($_POST['saveNew'])) {
                echo "<script>alert('$message'); window.location.href = 'balance_sheet.php';</script>";
                exit();
            }
        } else {
            $message = "Error: " . $stmt->error;
            echo "<script>alert('$message'); window.history.back();</script>";
        }
        $stmt->close();
        $conn->close();
    }
}
?>