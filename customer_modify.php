<?php
  include 'common.php';
include 'side_bar.php';




if (isset($_GET['c_no'])) {
    $c_no = $_GET['c_no'];

    // Fetch the customer data from the database
    $sql = "SELECT * FROM customer WHERE c_no = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $c_no);
    $stmt->execute();
    $result = $stmt->get_result();
    $customer = $result->fetch_assoc();

    if ($customer) {
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Modify Customer</title>
            <link rel="stylesheet" href="./css/customer_view.css">
            <style>
                .container {
                    width: 60%;
                    margin: 50px;
                    margin-left: 280px;
                    padding: 20px;
                    background-color: #ffffff;
                    border: 1px solid #ccc;
                    border-radius: 10px;
                    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                }
                h1 {
                    text-align: center;
                    font-size: 40px;
                    color: #22584b;
                }
                form {
                    display: flex;
                    flex-direction: column;
                }
                label {
                    margin-bottom: 10px;
                    color: #22584b;
                    font-weight: bold;
                }
                input[type="text"], input[type="date"], textarea {
                    width: 100%;
                    padding: 10px;
                    border: 1px solid #ccc;
                    border-radius: 5px;
                    margin-bottom: 20px;
                    box-sizing: border-box;
                }
                textarea {
                    resize: vertical;
                    height: 100px;
                }
                .form-buttons {
                    display: flex;
                    justify-content: flex-end;
                    gap: 10px;
                }
                .form-buttons button {
                    padding: 10px 20px;
                    border: none;
                    border-radius: 5px;
                    cursor: pointer;
                    background-color: #22584b;
                    color: #ffffff;
                    font-weight: bold;
                    transition: background-color 0.3s ease;
                }
                .form-buttons button:hover {
                    background-color: #1a4a3f;
                }
                .form-buttons button.cancel {
                    background-color: #22584b;
                }
                .form-buttons button.cancel:hover {
                    background-color: #22584b;
                }
                .radio-group {
                    display: flex;
                    gap: 10px;
                }
                .radio-group label {
                    margin-right: 10px;
                }
                @media (max-width: 768px) {
                    .container {
                        width: 70%;
                        padding: 10px;
                    }
                    .form-buttons {
                        flex-direction: column;
                        align-items: flex-end;
                    }
                    .form-buttons button {
                        width: 100%;
                        padding: 10px;
                    }
                }
                @media (min-width: 768px) and (max-width: 1024px) {
                    .container {
                        width: 80%;
                    }
                    .form-buttons button {
                        width: auto;
                        padding: 10px;
                        font-size: 14px;
                    }
                }
                @media (min-width: 1024px) {
                    .container {
                        width: 70%;
                    }
                }
            </style>
        </head>
        <body>
        <div class="container">
            <h1>Modify Customer</h1>
            <br>
            <form action="customer_update.php" method="POST">
                <input type="hidden" name="c_no" value="<?php echo htmlspecialchars($customer['c_no']); ?>">
                <label>Name:</label>
                <input type="text" name="c_name" maxlength="30" pattern="[A-Za-z\s]+" value="<?php echo htmlspecialchars($customer['c_name']); ?>">
                
                <label>Mobile:</label>
                <input type="text" name="c_mobile" pattern="\d{11}" maxlength="11" value="<?php echo htmlspecialchars($customer['c_mobile']); ?>">
                
                <label>Birthday:</label>
                <input type="date" name="c_birthday" value="<?php echo htmlspecialchars($customer['c_birthday']); ?>">
                
                <label>Note:</label>
                <textarea name="c_note"><?php echo htmlspecialchars($customer['c_note']); ?></textarea>
                
                <label>Category:</label>
                <div class="radio-group">
                    <label><input type="radio" name="c_cat" value="F" <?php echo ($customer['c_cat'] === 'F') ? 'checked' : ''; ?>> F</label>
                    <label><input type="radio" name="c_cat" value="N" <?php echo ($customer['c_cat'] === 'N') ? 'checked' : ''; ?>> N</label>
                    <label><input type="radio" name="c_cat" value="S" <?php echo ($customer['c_cat'] === 'S') ? 'checked' : ''; ?>> S</label>
                    <label><input type="radio" name="c_cat" value="R" <?php echo ($customer['c_cat'] === 'R') ? 'checked' : ''; ?>> R</label>
                    <label><input type="radio" name="c_cat" value="B" <?php echo ($customer['c_cat'] === 'B') ? 'checked' : ''; ?>> B</label>
                </div>

                <div class="form-buttons">
                    <button type="submit">Update</button>
                    <button type="button" class="cancel" onclick="window.location.href='customer_list.php'">Cancel</button>
                </div>
            </form>
        </div>
        </body>
        </html>
        <?php
    } else {
        echo "Customer not found.";
    }

    $stmt->close();
} else {
    echo "Invalid request.";
}

$conn->close();
?>
