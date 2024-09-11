<?php
include 'common.php';

// Get the date from URL parameter
$date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');

// Fetch categories
$sql = "SELECT ex_cat_name FROM expense_cat";
$result = $conn->query($sql);

$categories = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $categories[] = $row['ex_cat_name'];
    }
} 

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expense Add</title>
    <link rel="stylesheet" type="text/css" href="./css/expense_add.css">
</head>
<body>
<?php include 'side_bar.php'; ?>
<div class="container">
<h1>Expense Add</h1>
<br>
    <form method="POST" action="save_expense.php" id="expenseForm">
        <input type="hidden" name="e_exp_date" value="<?php echo htmlspecialchars($date); ?>">
        <table class="form-table">
            <tr>
                <th>Category</th>
                <td>
                    <select id="category" name="category">
                        <option value="Select">Select</option>
                        <?php
                        foreach ($categories as $category) {
                            echo "<option value=\"" . htmlspecialchars($category) . "\">" . htmlspecialchars($category) . "</option>";
                        }
                        ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th>Description</th>
                <td><input type="text" id="description" name="description"></td>
            </tr>
            <tr>
                <th>Price</th>
                <td><input type="number" id="price" name="price" step="0.01"></td>
            </tr>
            <tr>
                <th>Quantity</th>
                <td><input type="number" id="quantity" name="quantity"></td>
            </tr>
            <tr>
                <th>Memo</th>
                <td><textarea id="memo" name="memo"></textarea></td>
            </tr>
            <tr>
                <th>Amount</th>
                <td id="amountDisplay">0.00 /-</td>
            </tr>
        </table>
        <div class="form-buttons">
            <button type="submit" name="saveNew">Save & New</button>
            <button type="button"><a href="./balance_sheet.php" style="text-decoration:none; color:white;">Cancel</a></button>
        </div>
    </form>
</div>

<script>
    document.getElementById('expenseForm').addEventListener('submit', function(event) {
        const category = document.getElementById('category').value;
        const description = document.getElementById('description').value.trim();
        const price = document.getElementById('price').value.trim();
        const quantity = document.getElementById('quantity').value.trim();

        if (category === 'Select' || !description || !price || !quantity) {
            alert('Please fill in all required fields.');
            event.preventDefault(); // Prevent form submission
            return false; // Optional: to explicitly return false
        }
        
        if (isNaN(price) || isNaN(quantity)) {
            alert('Price and Quantity must be numeric values.');
            event.preventDefault(); // Prevent form submission
            return false; // Optional: to explicitly return false
        }
    });

    document.getElementById('price').addEventListener('input', calculateAmount);
    document.getElementById('quantity').addEventListener('input', calculateAmount);

    function calculateAmount() {
        const price = parseFloat(document.getElementById('price').value) || 0;
        const quantity = parseFloat(document.getElementById('quantity').value) || 0;
        const amount = price * quantity;
        document.getElementById('amountDisplay').innerText = amount.toFixed(2) + " /-";
    }

    // Optionally handle the Cancel button click event
    document.querySelector('button[type="button"]').addEventListener('click', function() {
        window.location.href = './balance_sheet.php'; // Redirect to the balance sheet
    });
</script>

</body>
</html>
