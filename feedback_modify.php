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
$selectedDate = isset($_GET['selectedDate']) ? $_GET['selectedDate'] : '';

$services = [];
$feedbacks = [];

if ($customerName && $selectedDate) {
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

    // Fetch feedbacks based on customer ID and date
    $feedback_sql = "SELECT * FROM feedback WHERE f_c_no = ? AND f_date = ?";
    $stmt = $conn->prepare($feedback_sql);
    if (!$stmt) {
        die("Error preparing feedback query: " . $conn->error);
    }
    $stmt->bind_param('is', $customerID, $selectedDate);
    $stmt->execute();
    $feedback_result = $stmt->get_result();
    $feedbacks = $feedback_result->fetch_assoc();

    // Fetch services, service names, and si_cats based on customer ID
    $order_sql = "
        SELECT 
            si.si_cat AS si_cat,
            si.si_service_name AS service_name,
            os.s_si_no
        FROM 
            orderservice os
        JOIN 
            service_item si ON os.s_si_no = si.si_no
        WHERE 
            os.s_order_no = ?";
    $stmt = $conn->prepare($order_sql);
    if (!$stmt) {
        die("Error preparing order query: " . $conn->error);
    }
    $stmt->bind_param('i', $customerID);
    $stmt->execute();
    $order_result = $stmt->get_result();

    while ($row = $order_result->fetch_assoc()) {
        for ($i = 1; $i <= 1; $i++) {
            $rateKey = "f_rate$i";
            $reviewKey = "f_review$i";
            
            $services[] = [
                'si_cat' => $row['si_cat'],
                'service_name' => $row['service_name'],
                'rate' => $feedbacks[$rateKey] ?? 0,
                'review' => $feedbacks[$reviewKey] ?? ''
            ];
        }
    }
    $stmt->close();
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

        <form action="feedback_update.php" method="POST">
            <input type="hidden" name="customerName" value="<?php echo htmlspecialchars($customerName); ?>">
            <input type="hidden" name="date" value="<?php echo htmlspecialchars($selectedDate); ?>">
            <table class="feedback-table">
                <thead>
                    <tr>
                        <th>Location</th>
                        <th>Service Name</th>
                        <th>Satisfaction</th>
                        <th>Review</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($services as $index => $service): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($service['si_cat']); ?></td>
                        <td><?php echo htmlspecialchars($service['service_name']); ?></td>
                        <td>
                            <div class="star-rating">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                <span class="star <?php echo ($i <= $service['rate']) ? 'selected' : ''; ?>" data-value="<?php echo $i; ?>">&#9733;</span>
                                <?php endfor; ?>
                                <input type="hidden" name="rating[<?php echo $index; ?>]" class="rating-input" value="<?php echo $service['rate']; ?>">
                            </div>
                        </td>
                        <td><input type="text" name="review[<?php echo $index; ?>]" value="<?php echo htmlspecialchars($service['review']); ?>"></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div class="actions-feedback">
                <button type="submit">Save</button>
              <a href="./feedback_main.php"><button type="button">Cancel</button></a>
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
        $(this).closest('.star-rating').find('.rating-input').val(rating);
    });
});
</script>

</body>
</html>