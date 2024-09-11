<?php
include 'common.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Form</title>
    <link rel="stylesheet" href="./css/register.css">
    <link rel="stylesheet" href="./css/customer_list.css">
    <style>
        /* Additional styles */
        @media (max-width: 768px) {
            .container {
                width: 90%;
                padding: 10px;
            }
            .form-table th, .form-table td {
                padding: 10px;
                font-size: 14px;
            }
            .form-buttons {
                flex-direction: column;
                align-items: center;
            }
            .form-buttons button {
                width: 100%;
                margin-bottom: 10px;
            }
        }

        @media (min-width: 768px) and (max-width: 1024px) {
            .container {
                width: 80%;
            }
            .form-buttons button {
                width: 30%;
                margin-bottom: 0;
                padding: 10px;
                font-size: 14px;
            }
        }

        @media (min-width: 1024px) {
            .container {
                width: 70%;
            }
        }

        .error-message {
            color: red;
            font-weight: bold;
        }

        textarea {
            width: 100%;
            box-sizing: border-box;
            resize: vertical; /* Allows resizing vertically */
        }

        .form-buttons {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }
    </style>
</head>
<body>
<?php include_once "side_bar.php"; ?>

<div class="container">
    <h1>Customer Add</h1>
    <br>
    <?php
    if (isset($_GET['error'])) {
        if ($_GET['error'] == 'duplicate_customer') {
            echo "<script>alert('This name and mobile number combination already exists in the database.');</script>";
        } elseif ($_GET['error'] == 'invalid_name') {
            echo "<p class='error-message'>Invalid name. Only characters are allowed and it must be under 30 characters.</p>";
        } elseif ($_GET['error'] == 'invalid_mobile') {
            echo "<p class='error-message'>Invalid mobile number. It must be in the format 0300-0000000.</p>";
        }
    }
    ?>
    <form action="customer_save.php" method="post" id="customerForm">
        <table class="form-table">
            <tr>
                <th>Full Name</th>
                <td><input type="text" id="c_name" name="c_name" required maxlength="30" pattern="[A-Za-z\s]+" title="Only letters and spaces are allowed"></td>
            </tr>
            <tr>
                <th>Mobile No.</th>
                <td><input type="text" id="c_mobile" name="c_mobile" required pattern="\d{4}-\d{7}" maxlength="12" title="Mobile number must be in the format 0300-0000000"></td>
            </tr>
            <tr>
                <th>Birthday</th>
                <td><input type="date" id="c_birthday" name="c_birthday" required></td>
            </tr>
            <tr>
                <th>Note</th>
                <td><textarea name="c_note" rows="5" style="width: 100%; box-sizing: border-box; resize: none;">
                </textarea></td>
            </tr>
            <tr>
                <th>Category</th>
                <td>
                    <label><input type="radio" name="c_cat" value="F" checked required>F</label>&nbsp;&nbsp;
                    <label><input type="radio" name="c_cat" value="N">N</label>&nbsp;&nbsp;
                    <label><input type="radio" name="c_cat" value="S">S</label>&nbsp;&nbsp;
                    <label><input type="radio" name="c_cat" value="R">R</label>&nbsp;&nbsp;
                    <label><input type="radio" name="c_cat" value="B">B</label>&nbsp;&nbsp;
                </td>
            </tr>
        </table>
        <input type="hidden" name="action" id="action" value="save">
        <div class="form-buttons">
            <button type="submit" id="saveButton">Save</button>
            <button type="button" id="saveNewButton">Save & New</button>
            <button type="button" id="cancelButton">Cancel</button>
        </div>
    </form>
</div>

<script>
    document.getElementById('cancelButton').addEventListener('click', function() {
        window.location.href = 'customer_list.php'; // Change to your desired URL
    });

    document.getElementById('saveNewButton').addEventListener('click', function() {
        document.getElementById('action').value = 'save_new';
        document.getElementById('customerForm').submit();
    });

    document.getElementById('saveButton').addEventListener('click', function() {
        document.getElementById('action').value = 'save';
    });

    document.getElementById('c_mobile').addEventListener('input', function (event) {
        const input = event.target.value;
        const formattedInput = input.replace(/\D/g, '').replace(/^(\d{4})(\d{0,7})$/, '$1-$2');
        event.target.value = formattedInput;
    });
</script>

</body>
</html>
