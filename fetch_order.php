<?php
// fetch_order.php
include 'db_connection.php';

$order_id = $_GET['order_id']; // Assume you get the order ID from a GET request

$sql = "SELECT o_c_no, o_cat1, o_name1, o_price1, o_qty1, 
               o_cat2, o_name2, o_price2, o_qty2, 
               o_cat3, o_name3, o_price3, o_qty3, 
               o_cat4, o_name4, o_price4, o_qty4, 
               o_cat5, o_name5, o_price5, o_qty5, 
               o_cat6, o_name6, o_price6, o_qty6, 
               o_cat7, o_name7, o_price7, o_qty7, 
               o_cat8, o_name8, o_price8, o_qty8, 
               o_cat9, o_name9, o_price9, o_qty9, 
               o_cat10, o_name10, o_price10, o_qty10, 
               o_memo, o_amount 
        FROM `order` 
        WHERE o_c_no = '$order_id'";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $order = $result->fetch_assoc();
} else {
    echo "No order found.";
}
$conn->close();
?>
