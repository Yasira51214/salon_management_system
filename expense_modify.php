<?php

include 'common.php';
// Fetch categories
$sql = "SELECT ex_no, ex_cat_name FROM expense_cat";
$result = $conn->query($sql);

$categories = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $categories[] = [
            'ex_no' => $row['ex_no'],
            'ex_cat_name' => $row['ex_cat_name']
        ];
    }
}

// Fetch expense data for the given expense number
$e_no = isset($_GET['id']) ? intval($_GET['id']) : 0;
$expenseData = [
    'e_ex_no' => '', // Default value
    'e_description' => '', // Default value
    'e_price' => 0, // Default value
    'e_qty' => 0, // Default value
    'e_memo' => '' // Default value
];

if ($e_no > 0) {
    $sql = "SELECT e.e_no, e.e_ex_no, ec.ex_cat_name, e.e_description, e.e_price, e.e_qty, e.e_memo
            FROM expense e
            JOIN expense_cat ec ON e.e_ex_no = ec.ex_no
            WHERE e.e_no = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $e_no);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $expenseData = $result->fetch_assoc();
    }
    $stmt->close();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modify Expense</title>
    <link rel="stylesheet" type="text/css" href="./css/expense_modify.css">
</head>
<body>
<?php include 'side_bar.php'; ?>

<div class="container">
    <h1>Modify Expense</h1>
    <form method="POST" action="update_expense.php" id="expenseForm">
        <input type="hidden" name="expense_id" value="<?php echo htmlspecialchars($expenseData['e_no']); ?>">
        <table class="form-table">
            <tr>
                <th>Category</th>
                <td>
                    <select id="e_ex_no" name="e_ex_no" required>
                        <option value="">Select</option>
                        <?php
                        foreach ($categories as $category) {
                            $selected = ($category['ex_no'] == $expenseData['e_ex_no']) ? 'selected' : '';
                            echo "<option value=\"" . htmlspecialchars($category['ex_no']) . "\" $selected>" . htmlspecialchars($category['ex_cat_name']) . "</option>";
                        }
                        ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th>Description</th>
                <td><input type="text" id="e_description" name="e_description" value="<?php echo htmlspecialchars($expenseData['e_description']); ?>" required></td>
            </tr>
            <tr>
                <th>Price</th>
                <td><input type="number" id="e_price" name="e_price" step="0.01" value="<?php echo htmlspecialchars($expenseData['e_price']); ?>" required></td>
            </tr>
            <tr>
                <th>Quantity</th>
                <td><input type="number" id="e_qty" name="e_qty" value="<?php echo htmlspecialchars($expenseData['e_qty']); ?>" required></td>
            </tr>
            <tr>
                <th>Memo</th>
                <td><textarea id="e_memo" name="e_memo"><?php echo htmlspecialchars($expenseData['e_memo']); ?></textarea></td>
            </tr>
            <tr>
                <th>Amount</th>
                <td id="amountDisplay"><?php echo number_format($expenseData['e_price'] * $expenseData['e_qty'], 2); ?> /-</td>
            </tr>
        </table>
        <div class="form-buttons">
            <button type="submit" name="saveNew">Save</button>
            <button type="button" id="cancelButton"><a href="./balance_sheet.php" style="text-decoration:none; color:white;">Cancel</a></button>
        </div>
    </form>
</div>

<script>
    document.getElementById('e_price').addEventListener('input', calculateAmount);
    document.getElementById('e_qty').addEventListener('input', calculateAmount);

    function calculateAmount() {
        const price = parseFloat(document.getElementById('e_price').value) || 0;
        const quantity = parseFloat(document.getElementById('e_qty').value) || 0;
        const amount = price * quantity;
        document.getElementById('amountDisplay').innerText = amount.toFixed(2) + " /-";
    }

    document.getElementById('cancelButton').addEventListener('click', function() {
        alert('Cancel button clicked');
    });
</script>

</body>
</html>
