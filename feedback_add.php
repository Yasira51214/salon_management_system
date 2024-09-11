<?php
include 'common.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback Form</title>
    <link rel="stylesheet" href="./css/feedback_add.css">
    <style>
        @media (max-width: 768px) {
            h1 {
                font-size: 30px;
            }
            .icons img {
                width: 40px;
                height: 40px;
            }
            .icons p {
                font-size: 1em;
            }
            .feedback-table th, .feedback-table td {
                padding: 8px;
                font-size: 14px;
            }
            .actions-feedback button {
                font-size: 14px;
                padding: 8px 16px;
            }
        }
        @media (max-width: 480px) {
            .container {
                width: 95%;
            }
            .icons {
                flex-direction: column;
                align-items: center;
            }
            .icons img {
                margin: 10px 0;
            }
            .feedback-table th, .feedback-table td {
                font-size: 12px;
                padding: 6px;
            }
            .actions-feedback button {
                font-size: 12px;
                padding: 6px 12px;
            }
            .feedback-table {
                overflow-x: auto;
                display: block;
            }
            .feedback-table th, .feedback-table td {
                white-space: nowrap;
            }
        }
    </style>
</head>
<body>

<?php include_once "side_bar.php"; ?>

<?php
// Fetch parameters
$customerName = isset($_GET['customerName']) ? $_GET['customerName'] : '';
$customerName = isset($_GET['c_no']) ? $_GET['c_no'] : '';
$mobile = isset($_GET['mobile']) ? $_GET['mobile'] : '';
$selectedDate = isset($_GET['selectedDate']) ? $_GET['selectedDate'] : '';

$services = []; // Initialize services array

if ($customerName && $mobile && $selectedDate) {
    // Decode parameters
    $customerName = urldecode($customerName);
    $mobile = urldecode($mobile);
    $selectedDate = urldecode($selectedDate);

    // Fetch customer ID based on name and mobile
    $customer_sql = "SELECT c_no FROM customer WHERE c_name = ? AND c_mobile = ?";
    $stmt = $conn->prepare($customer_sql);
    if (!$stmt) {
        die('Error preparing customer SQL statement: ' . $conn->error);
    }
    $stmt->bind_param('ss', $customerName, $mobile);
    $stmt->execute();
    $customer_result = $stmt->get_result();

    if ($customer_result) {
        $customer = $customer_result->fetch_assoc();
        if ($customer) {
            $customerID = $customer['c_no'];

            // Fetch services based on customer ID and date
            $order_sql = "
            SELECT si.si_cat AS category, si.si_service_name AS service, os.s_si_no AS si_no
            FROM orderservice os
            JOIN service_item si ON os.s_si_no = si.si_no
            JOIN `order` o ON os.s_o_no = o.o_no
            WHERE o.o_c_no = ? AND o.o_date = ?
            ";
            $stmt = $conn->prepare($order_sql);
            if (!$stmt) {
                die('Error preparing order SQL statement: ' . $conn->error);
            }
            $stmt->bind_param('ss', $customerID, $selectedDate);
            $stmt->execute();
            $order_result = $stmt->get_result();

            if ($order_result) {
                while ($row = $order_result->fetch_assoc()) {
                    $services[] = [
                        'category' => $row['category'],
                        'service' => $row['service'],
                        'si_no' => $row['si_no']
                    ];
                }
            } else {
                die('Error fetching order results: ' . $conn->error);
            }
        } else {
            die('Customer not found');
        }
    } else {
        die('Error fetching customer results: ' . $conn->error);
    }
} else {
    die('Missing required parameters');
}
?>

<div class="container">
    <div class="feedback-container">
        <h1>Feedback</h1>
        <div class="icons">
            <img src="./Images/smile.png" alt="Happy" class="icon">
            <p>We're all ears! Tell us what you think and how we can serve you better</p>
            <img src="./Images/smile.png" alt="Happy" class="icon">
        </div>
        <div class="error">
            <p class="message"><span style="color:Black; font-weight: bold;">Dear: <span style="color:#22584b; font-weight: bold;"><?php echo htmlspecialchars($customerName); ?></span></p>
            <p class="message"><span style="color:Black; font-weight: bold;">Date: <span style="color:#22584b; font-weight: bold;"><?php echo htmlspecialchars($selectedDate); ?></span></p>
        </div>
        <form action="feedback_save.php" method="POST">
            <input type="hidden" name="customerName" value="<?php echo htmlspecialchars($customerName); ?>">
            <input type="hidden" name="date" value="<?php echo htmlspecialchars($selectedDate); ?>">
            <table class="feedback-table">
                <thead>
                    <tr>
                        <th>Category</th>
                        <th>Service</th>
                        <th>Satisfaction</th>
                        <th>Review</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($services as $index => $service): ?>
                    <tr>
                        <td>
                            <?php echo htmlspecialchars($service['category']); ?>
                            <input type="hidden" name="f_cat[<?php echo $index; ?>]" value="<?php echo htmlspecialchars($service['category']); ?>">
                        </td>
                        <td>
                            <?php echo htmlspecialchars($service['service']); ?>
                            <input type="hidden" name="f_si_no[<?php echo $index; ?>]" value="<?php echo htmlspecialchars($service['si_no']); ?>">
                        </td>
                        <td>
                            <div class="star-rating">
                                <span class="star" data-value="1">&#9733;</span>
                                <span class="star" data-value="2">&#9733;</span>
                                <span class="star" data-value="3">&#9733;</span>
                                <span class="star" data-value="4">&#9733;</span>
                                <span class="star" data-value="5">&#9733;</span>
                                <input type="hidden" name="rating[<?php echo $index; ?>]" class="rating-input" value="">
                            </div>
                        </td>
                        <td><input type="text" name="review[<?php echo $index; ?>]"></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div class="actions-feedback">
                <button type="submit">Save</button>
                <button type="button" onclick="window.location.href='feedback_main.php'">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    $('.star').on('click', function() {
        var rating = $(this).data('value');
        $(this).siblings('.star').removeClass('selected');
        $(this).addClass('selected').prevAll('.star').addClass('selected');
        $(this).siblings('.rating-input').val(rating);
    });
});
</script>
</body>
</html>
