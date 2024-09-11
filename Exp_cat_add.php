<?php
include 'common.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expense Category</title>
    <link rel="stylesheet" href="./css/exp_cat_add.css">
    <style>
        /* Responsive Design */
        @media (max-width: 768px) {
            .container {
                padding: 15px;
            }
            h1 {
                font-size: 20px;
            }
            .feedback-table th, .feedback-table td {
                padding: 10px;
                font-size: 14px;
            }
            .actions-feedback button {
                font-size: 14px;
                padding: 8px 16px;
            }
        }

        @media (max-width: 480px) {
            .container {
                padding: 10px;
                width: 100%;
            }
            h1 {
                font-size: 18px;
            }
            .feedback-table {
                font-size: 12px;
                overflow-x: auto;
                display: block;
            }
            .feedback-table th, .feedback-table td {
                padding: 8px;
            }
            .actions-feedback button {
                font-size: 12px;
                padding: 6px 12px;
                margin-right: 5px;
            }
        }
    </style>
</head>
<body>
<?php include "side_bar.php"; ?>
<div class="container">
<h1>Expense Category Add</h1>
<br>
    <div class="feedback-container">

        <?php
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $expense_cat = $_POST['expense_cat'];

            // Check if connection was successful
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            // Prepare and bind
            $stmt = $conn->prepare("INSERT INTO expense_cat (ex_cat_name) VALUES (?)");
            if ($stmt === false) {
                echo "<script>alert('Error preparing the statement: " . $conn->error . "');</script>";
            } else {
                $stmt->bind_param("s", $expense_cat);

                // Execute and check
                if ($stmt->execute()) {
                    echo "<script>
                        alert('Expense category added successfully!');
                        window.location.href='expense_manage.php';
                    </script>";
                } else {
                    echo "<script>alert('Error: " . $stmt->error . "');</script>";
                }

                // Close connection
                $stmt->close();
            }
            $conn->close();
        }
        ?>

        <form action="" method="post">
            <table class="feedback-table">
                <thead>
                    <tr>
                        <th>Expense Category Name</th>
                        <td><input type="text" name="expense_cat" required></td>
                    </tr>
                </thead>
            </table>
            <div class="actions-feedback">
                <button type="submit">Save</button>
                <button type="button" onclick="window.location.href='expense_manage.php';">Cancel</button>
            </div>
        </form>
    </div>
</div>

</body>
</html>
