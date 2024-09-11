<?php
include 'common.php';
?>
<?php

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize variables
$customerName = '';
$selectedDate = '';
$feedbacks = [];
$orders = [];

// Get parameters from query string
if (isset($_GET['date']) && !empty($_GET['date'])) {
    $selectedDate = $_GET['date'];
}

// Fetch feedback details based on date
function getFeedbackByDate($date, $conn) {
    $feedbacks = [];
    $sql = "SELECT c.c_name, c.c_no AS f_c_no,
                f.f_rate1, f.f_rate2, f.f_rate3, f.f_rate4, f.f_rate5, f.f_rate6, f.f_rate7, f.f_rate8, f.f_rate9, f.f_rate10,
                f.f_review1, f.f_review2, f.f_review3, f.f_review4, f.f_review5, f.f_review6, f.f_review7, f.f_review8, f.f_review9, f.f_review10
            FROM feedback f
            JOIN customer c ON f.f_c_no = c.c_no
            WHERE f.f_date = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Error preparing statement: " . $conn->error);
    }
    $stmt->bind_param('s', $date);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $feedbacks[] = $row;
    }

    $stmt->close();
    return $feedbacks;
}

// Fetch order details based on customer number
    function getOrdersByCustomerNo($customerNo, $conn) {
        $orders = [];
        $sql = "
            SELECT si.si_cat, si.si_service_name
            FROM orderservice os
            JOIN service_item si ON os.s_si_no = si.si_no
            WHERE os.s_o_no IN (
                SELECT o_no 
                FROM `order` 
                WHERE o_c_no = ?
            )
        ";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            die("Error preparing statement: " . $conn->error);
        }
        $stmt->bind_param('i', $customerNo);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $orders[] = [
                'category' => $row['si_cat'], // Fetch si_cat from service_item table
                'service' => $row['si_service_name'], // Fetch si_service_name from service_item table
            ];
        }

    $stmt->close();
    return $orders;
}

// Get feedback and orders data
$feedbacks = getFeedbackByDate($selectedDate, $conn);
if (!empty($feedbacks)) {
    $customerNo = $feedbacks[0]['f_c_no'];
    $orders = getOrdersByCustomerNo($customerNo, $conn);
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback View</title>
    <link rel="stylesheet" href="./css/feedback_view.css">
    <script>
    function deleteFeedback(customerName, selectedDate) {
        if (confirm('Are you sure you want to delete this feedback?')) {
            window.location.href = 'feedback_delete.php?customerName=' + encodeURIComponent(customerName) + '&date=' + encodeURIComponent(selectedDate);
        }
    }
    </script>
</head>
<body>
            <?php include_once "side_bar.php" ?>
            <div class="container">
                <h1>FEEDBACK VIEW</h1>
                <?php if (!empty($feedbacks)): ?>
            <?php foreach ($feedbacks as $feedback): ?>
                <div class="error">
                    <p class="message"><span style="color:Black; font-weight: bold;">Dear: <span style="color:#22584b; font-weight: bold;"><?php echo htmlspecialchars($feedback['c_name']); ?></span></p>
                    <p class="message"><span style="color:Black; font-weight: bold;">Date: <span style="color:#22584b; font-weight: bold;"><?php echo htmlspecialchars($selectedDate); ?></span></p>
                </div>
                <table class="feedback-table">
                    <tr>
                        <th>Category</th>
                        <th>Service</th>
                        <th>Satisfaction</th>
                        <th>Review</th>
                    </tr>
                    <?php
                    for ($i = 1; $i <= 10; $i++):
                        $category = $orders[$i - 1]['category'] ?? ''; // Use updated category from service_item
                        $service = $orders[$i - 1]['service'] ?? ''; // Use updated service from service_item
                        $review = $feedback["f_review$i"] ?? '';
                        $rate = $feedback["f_rate$i"] ?? '';
                        if ($service || $review || $rate): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($category); ?></td>
                                <td><?php echo htmlspecialchars($service); ?></td>
                                <td><?php echo str_repeat('★', (int)$rate); ?></td>
                                <td><?php echo htmlspecialchars($review); ?></td>
                            </tr>
                        <?php endif;
                    endfor;
                    ?>
                </table>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No feedback found for the selected date.</p>
        <?php endif; ?>

        <div class="actions-feedback">
            <button onclick="window.location.href='feedback_modify.php?customerName=<?php echo urlencode($feedback['c_name']); ?>&selectedDate=<?php echo urlencode($selectedDate); ?>'">Modify</button>
            <button onclick="deleteFeedback('<?php echo urlencode($feedback['c_name']); ?>', '<?php echo urlencode($selectedDate); ?>')">Delete</button>
            <button><a href="./feedback_main.php">Cancel</a></button>
        </div>
    </div>
</body>
</html>