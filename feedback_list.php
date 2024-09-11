<?php
include 'common.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback List</title>
    <link rel="stylesheet" href="./css/feedback_list.css">
</head>
<body>
<?php include_once "side_bar.php"; ?>

<div class="container">
    <h1>Feedback List</h1>
    <table class="feedback-table">
        <thead>
            <tr>
                <th>Customer Name</th>
                <th>Date</th>
                <th>Category</th>
                <th>Service</th>
                <th>Satisfaction</th>
                <th>Review</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $sql = "SELECT * FROM feedback";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['customer_name']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['date']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['category']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['service']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['satisfaction']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['review']) . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='6'>No feedback found</td></tr>";
            }

            $conn->close();
            ?>
        </tbody>
    </table>
</div>
</body>
</html>
