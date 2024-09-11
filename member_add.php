<?php
include 'common.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Member Add</title>
    <link rel="stylesheet" href="./css/member_add.css">
    <style>
     /* Responsive Design */
     @media (max-width: 768px) {
            .container {
                width: 100%; /* Full width for mobile screens */
                padding: 20px; /* Adjusted padding */
            }
            h1 {
                font-size: 20px;
            }
            table {
                width: 100%; /* Full width for table */
                font-size: 14px;
                border-collapse: collapse;
            }
            th, td {
                padding: 12px;
                text-align: left;
                border: 1px solid #ddd;
            }
            .form-buttons {
                flex-direction: row; /* Stack buttons vertically */
                gap: 10px;
            }
            .form-buttons button,
            .form-buttons a button {
                width: 50%; /* Full width for buttons */
            }
        }
        @media (min-width: 768px) and (max-width: 1024px) {
            .container {
                width: 90%;
            }
            table {
                font-size: 14px;
            }
        }
        @media (min-width: 1024px) {
            .container {
                width: 70%;
            }
            .psd-char {
                color: red;
                font-size: 12px;
            }
        }
    </style>
</head> 
<body>
    <?php 
        include_once "side_bar.php"; 
    ?>

    <div class="container">
        <h1>Member Add</h1>
        <form action="member_save.php" method="post" onsubmit="return validateForm()">
            <table>
                <tr>
                    <th>Full Name</th>
                    <td><input type="text" name="fullName" required maxlength="30" pattern="[A-Za-z0-9 ]+" title="Only alphabets, numbers, and spaces are allowed"></td>
                </tr>
                <tr>
                    <th>User ID</th>
                    <td><input type="text" name="userName" required maxlength="15" pattern="[A-Za-z0-9 ]+" title="Only alphabets, numbers, and spaces are allowed"></td>
                </tr>
                <tr>
                    <th>Password</th>
                    <td>
                        <input type="password" id="password" name="password" required maxlength="30" minlength="5">
                        <p id="password-message" class="psd-char" style="display:none;">Minimum 5 one number and one uppercase letter</p>
                    </td>
                </tr>
                <tr>
                    <th>Role</th>
                    <td>
                        <select name="role" required>
                            <option value="Admin">Admin</option>
                            <option value="Operator" selected>Operator</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th>Email</th>
                    <td><input type="email" name="email" required maxlength="50"></td>
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
            window.location.href = './member_list.php';
        }

        document.addEventListener('DOMContentLoaded', function () {
            var passwordInput = document.getElementById('password');
            var passwordMessage = document.getElementById('password-message');

            passwordInput.addEventListener('input', function () {
                var valueLength = passwordInput.value.length;

                if (valueLength < 5 || valueLength > 30) {
                    passwordMessage.style.display = 'block';
                } else {
                    passwordMessage.style.display = 'none';
                }
            });
        });

        function validateForm() {
            var userName = document.querySelector('input[name="userName"]').value;
            var userNamePattern = /^[A-Za-z0-9 ]+$/;

            if (!userNamePattern.test(userName)) {
                alert("User ID can only contain alphabets, numbers, and spaces.");
                return false; // Prevent form submission
            }

            return true; // Allow form submission
        }
    </script>
</body>
</html>
