<?php
include 'common.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Setting</title>
    <link rel="stylesheet" href="./css/currency_setting.css">
    <style>
        /* Responsive Design */
        @media (max-width: 768px) {
            .container {
                padding: 10px;
                width: 95%;
            }
            h1 {
                font-size: 24px;
            }
            table {
                font-size: 14px;
            }
            select {
                width: 50%;
                padding: 10px;
                font-size: 14px;
            }
            .form-buttons {
                flex-direction: column;
                align-items: stretch;
            }
            .form-buttons button {
                width: 100%;
                margin-bottom: 10px;
            }
        }
        @media (min-width: 768px) and (max-width: 1024px) {
            .container {
                padding: 15px;
                width: 90%;
            }
            table {
                font-size: 16px;
            }
            select {
                width: auto;
                padding: 8px;
                font-size: 14px;
            }
            .form-buttons {
                flex-direction: row;
                justify-content: flex-end;
            }
            .form-buttons button {
                padding: 12px 20px;
            }
        }
        @media (min-width: 1024px) {
            .container {
                padding: 20px;
                width: 70%;
            }
            table {
                font-size: 18px;
            }
        }
    </style>
</head>
<body>
    <?php include 'side_bar.php'; ?>
    <div class="container">
        <h1>Currency Setting</h1>
        <br>
        <form id="settingsForm" action="save_currency.php" method="post">
            <table>
                <tr>
                    <th>Kind of Currency </th>
                    <td>
                        <select name="currency" required value="Select">

                            <option value="RS">RS</option>
                            <option value="USD">USD</option>
                        </select>
                    </td>
                </tr>
            </table>
            <div class="form-buttons">
                <button type="submit">Save</button>
                <button type="button" onclick="cancel()">Cancel</button>
            </div>
        </form>
    </div>
    <script>
        function cancel() {
            window.location.href = './main.php';
        }
    </script>
</body>
</html>
