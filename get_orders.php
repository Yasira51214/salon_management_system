<?php
header('Content-Type: application/json');

// Database connection 
include 'db_connection.php';

if ($conn->connect_error) {
    die(json_encode(['error' => $conn->connect_error]));
}

if (!isset($_GET['year']) || !isset($_GET['month'])) {
    die(json_encode(['error' => 'Year and month parameters are required']));
}

$year = intval($_GET['year']);
$month = intval($_GET['month']);

$start_date = "$year-$month-01";
$end_date = date("Y-m-t", strtotime($start_date));

// Fetch orders
$sql_orders = "SELECT DAY(o_date) as day, SUM(o_amount) as o_amount FROM `order` WHERE o_date BETWEEN '$start_date' AND '$end_date' GROUP BY DAY(o_date)";
$result_orders = $conn->query($sql_orders);

if ($result_orders === false) {
    die(json_encode(['error' => $conn->error]));
}

$orders = [];
while ($row = $result_orders->fetch_assoc()) {
    $orders[intval($row['day'])] = $row['o_amount'];
}

// Fetch expenses
$sql_expenses = "SELECT DAY(e_exp_date) as day, SUM(e_amount) as e_amount FROM expense WHERE e_exp_date BETWEEN '$start_date' AND '$end_date' GROUP BY DAY(e_exp_date)";
$result_expenses = $conn->query($sql_expenses);

if ($result_expenses === false) {
    die(json_encode(['error' => $conn->error]));
}

$expenses = [];
while ($row = $result_expenses->fetch_assoc()) {
    $expenses[intval($row['day'])] = $row['e_amount'];
}

$conn->close();

echo json_encode(['orders' => $orders, 'expenses' => $expenses]);
?>






