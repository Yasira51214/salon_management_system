<?php
include 'common.php';

// Initialize variables
$ex_cat_name = '';
$ex_no = isset($_GET['id']) ? $_GET['id'] : '';
$message = '';

// Fetch the current expense category name if ex_no is provided
if ($ex_no) {
    $stmt = $conn->prepare("SELECT ex_cat_name FROM expense_cat WHERE ex_no = ?");
    $stmt->bind_param("s", $ex_no);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $ex_cat_name = $row['ex_cat_name'];
    } else {
        $message = "Expense category not found.";
    }
    $stmt->close();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && $ex_no) {
    $new_ex_cat_name = $_POST['new_ex_cat_name'];

    // Update the expense category name in the database
    $update_stmt = $conn->prepare("UPDATE expense_cat SET ex_cat_name = ? WHERE ex_no = ?");
    if ($update_stmt === false) {
        $message = "Error preparing the update statement: " . $conn->error;
    } else {
        $update_stmt->bind_param("ss", $new_ex_cat_name, $ex_no);
        if ($update_stmt->execute()) {
            // Redirect to expense_manage.php with a success message
            header("Location: expense_manage.php?message=" . urlencode("Expense category updated successfully!"));
            exit();
        } else {
            $message = "Error updating category: " . $update_stmt->error;
        }
        $update_stmt->close();
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expense Category Modify</title>
    <link rel="stylesheet" href="./css/exp_cat_modify.css">
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
            .actions-feedback {
            display: flex;
            justify-content: flex-end; /* Aligns the buttons to the right */
            padding: 10px;
        }
    }
    </style>
</head>
<body>
<?php
include_once "side_bar.php";
?>

<div class="container">
    <h1>Expense Category Modify</h1>
    <br>
    <div class="feedback-container">
        <?php if ($message) { echo "<p>$message</p>"; } ?>
        <form action="" method="post">
            <table class="feedback-table">
                <thead>
                    <tr>
                        <th>Expense Category Name</th>
                        <td><input type="text" name="new_ex_cat_name" value="<?php echo htmlspecialchars($ex_cat_name); ?>" required></td>
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
