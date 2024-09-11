<?php

include 'common.php';

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $order_name1 = $_POST['service1_name'] ?? null;
    $order_price1 = $_POST['service1_price'] ?? null;
    $order_name2 = $_POST['service2_name'] ?? null;
    $order_price2 = $_POST['service2_price'] ?? null;
    $order_name3 = $_POST['service3_name'] ?? null;
    $order_price3 = $_POST['service3_price'] ?? null;
    $order_name4 = $_POST['service4_name'] ?? null;
    $order_price4 = $_POST['service4_price'] ?? null;

    // Ensure that empty fields are replaced with NULL for the SQL statement
    $order_name1 = $order_name1 ? "'" . $conn->real_escape_string($order_name1) . "'" : "NULL";
    $order_price1 = $order_price1 ? $order_price1 : "NULL";
    $order_name2 = $order_name2 ? "'" . $conn->real_escape_string($order_name2) . "'" : "NULL";
    $order_price2 = $order_price2 ? $order_price2 : "NULL";
    $order_name3 = $order_name3 ? "'" . $conn->real_escape_string($order_name3) . "'" : "NULL";
    $order_price3 = $order_price3 ? $order_price3 : "NULL";
    $order_name4 = $order_name4 ? "'" . $conn->real_escape_string($order_name4) . "'" : "NULL";
    $order_price4 = $order_price4 ? $order_price4 : "NULL";

    $sql = "INSERT INTO `order` (order_name1, order_price1, order_name2, order_price2, order_name3, order_price3, order_name4, order_price4)
            VALUES ($order_name1, $order_price1, $order_name2, $order_price2, $order_name3, $order_price3, $order_name4, $order_price4)";

    if ($conn->query($sql) === TRUE) {
        echo "New order created successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>



Danish code checking 

<?php
require "db_connection.php";

// print_r("agya bhai submit_order.hp py");
// exit();
// Set the content type to JSON
header('Content-Type: application/json');

try {
    if (isset($_POST['submit'])) {
        // Fetch data from POST request
        // $name = $_POST['name'];
        // $mobile = $_POST['mobile'];
        // $memo = $_POST['memo'] ?? '';
        $name = $_POST['name'];
    $mobile = $_POST['mobile'];
    $note = $_POST['note'];
    $selectedService = $_POST['selected_services'] ?? [];
    $quantities = $_POST['quantities'] ?? [];
    $totalAmount=0;
    // print_r($selectedService);
    // exit();
    
        // $services = json_decode($_POST['services'], true); // Decode JSON string to array
    
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

        // Prepare the insert statement for the order table
        $sql = "INSERT INTO `order` (`o_c_no`, `o_cat1`, `o_name1`, `o_price1`, `o_qty1`, 
                    `o_cat2`, `o_name2`, `o_price2`, `o_qty2`, `o_cat3`, `o_name3`, `o_price3`, `o_qty3`, 
                    `o_cat4`, `o_name4`, `o_price4`, `o_qty4`, `o_cat5`, `o_name5`, `o_price5`, `o_qty5`, 
                    `o_cat6`, `o_name6`, `o_price6`, `o_qty6`, 
                    `o_cat7`, `o_name7`, `o_price7`, `o_qty7`,
                    `o_cat8`, `o_name8`, `o_price8`, `o_qty8`,
                    `o_cat9`, `o_name9`, `o_price9`, `o_qty9`,
                    `o_cat10`, `o_name10`, `o_price10`, `o_qty10`,
                    `o_memo`, `o_amount`) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
// print_r($sql);

        // $stmt = $conn->prepare($sql);

        // Initialize an array with null values for all columns
        // $values = array_fill(0, 31, null); // 31 null values for 31 placeholders

        // $values[0] = $o_c_no; // o_c_no is at index 0
        // $total_amount = 0;

        // Iterate over services to fill in the values array
        // foreach ($services as $index => $service) {
        //     $cat_index = $index * 4 + 1; // calculate the starting index for the category fields
        //     $values[$cat_index] = $service['cat'];
        //     $values[$cat_index + 1] = $service['name'];
        //     $values[$cat_index + 2] = $service['price'];
        //     $values[$cat_index + 3] = $service['qty'];
        //     $total_amount += $service['price'] * $service['qty'];
        // }

        // $values[29] = $memo; // o_memo is at index 29
        // $values[30] = $total_amount; // o_amount is at index 30

        // Generate dynamic binding types string
        // $bind_types = "i"; // first value is an integer (o_c_no)
        // for ($i = 1; $i <= 28; $i++) {
        //     $bind_types .= "s"; // categories and names are strings
        //     $bind_types .= "i"; // prices and quantities are integers
        // }
        // $bind_types .= "s"; // memo is a string
        // $bind_types .= "d"; // amount is a double

        // Bind parameters and execute the statement
        // $stmt->bind_param($bind_types, ...$values);
        // $stmt->execute();

        // Check if the insertion was successful
//         if ($stmt->affected_rows > 0) {
//             // Redirect to order_add.php after successful insertion
//             header('Location: order_add.php');
//             exit;
//         } else {
//             echo json_encode(['status' => 'error', 'message' => 'Failed to insert data']);
//             exit;
//         }
//     }
// } catch (Exception $e) {
//     echo json_encode(['status' => 'error', 'message' => 'Execution error: ' . $e->getMessage()]);
// }
$stmt = $conn->prepare($sql);
        // $stmt->bind_param("iisdisdisdisdisdisdisdisdisdisdisdisdisdisdssd", ...$values); // Bind parameters dynamically
        // $stmt->execute();

        if ($stmt->affected_rows > 0) {
            echo json_encode(['status' => 'success', 'message' => 'Order successfully recorded']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to insert order data']);
        }
    }
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Execution error: ' . $e->getMessage()]);
}
?>
