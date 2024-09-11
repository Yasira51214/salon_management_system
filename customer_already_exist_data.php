<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Already Exists</title>
    <link rel="stylesheet" href="./css/register.css">
    <style>
        @media (max-width: 768px) {
            .container {
                width: 90%;
                padding: 10px;
            }
            .form-table th, .form-table td {
                padding: 10px;
                font-size: 14px;
            }
        }

        @media (min-width: 768px) and (max-width: 1024px) {
            .container {
                width: 80%;
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
<?php include_once "side_bar.php" ?>

<div class="container">
    <h1>Customer Already Exists</h1>
    <br>
    <p class="error-message">This mobile number is already registered. Here is the existing customer data:</p>
    <table class="form-table">
        <tr>
            <th>Full Name</th>
            <td><?php echo htmlspecialchars($_GET['c_name']); ?></td>
        </tr>
        <tr>
            <th>Mobile No.</th>
            <td><?php echo htmlspecialchars($_GET['c_mobile']); ?></td>
        </tr>
        <tr>
            <th>Birthday</th>
            <td><?php echo htmlspecialchars($_GET['c_birthday']); ?></td>
        </tr>
        <tr>
            <th>Note</th>
            <td><?php echo htmlspecialchars($_GET['c_note']); ?></td>
        </tr>
        <tr>
            <th>Category</th>
            <td><?php echo htmlspecialchars($_GET['c_cat']); ?></td>
        </tr>
    </table>
    <div class="form-buttons">
        <button type="button" onclick="window.location.href='register.php'">Back</button>
    </div>
</div>

</body>
</html>
