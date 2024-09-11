<?php
include 'common.php';

// Function to decrypt password
function decryptPassword($encryptedPassword, $key) {
    list($encryptedData, $iv) = explode('::', base64_decode($encryptedPassword), 2);
    return openssl_decrypt($encryptedData, 'aes-256-cbc', $key, 0, $iv);
}

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['user'])) {
    $userName = $_GET['user'];

    // Fetch data
    $stmt = $conn->prepare("SELECT m_fullname, m_name, m_password, m_role, m_mail FROM member WHERE m_name = ?");
    $stmt->bind_param("s", $userName);
    $stmt->execute();
    $stmt->bind_result($fullName, $userName, $password, $role, $email);
    $stmt->fetch();

    // Close statement and connection
    $stmt->close();
    $conn->close();

    // Generate or retrieve your encryption key
    $key = 'your-encryption-key-here'; // Replace with your actual key

    // Decrypt the password
    $decryptedPassword = decryptPassword($password, $key);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Member Edit</title>
    <link rel="stylesheet" href="./css/member_modify.css">
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
        }
        /* Eye icon styling */
        .eye-icon {
            cursor: pointer;
            margin-left: -40px;
            width:25px;
            height:25px;
        }
    </style>
</head>
<body>
    <?php include "side_bar.php"; ?>
    <div class="container">
        <h1>Member Modify</h1>
        <form action="member_modify_save.php" method="post">
            <table>
                <tr>
                    <th>Full Name</th>
                    <td><input type="text" name="fullName" value="<?php echo htmlspecialchars($fullName); ?>" required></td>
                </tr>
                <tr>
                    <th>User Name</th>
                    <td><input type="text" name="userName" value="<?php echo htmlspecialchars($userName); ?>" readonly></td>
                </tr>
                <tr>
                    <th>Password</th>
                    <td>
                        <input type="password" id="password" name="password" value="<?php echo htmlspecialchars($decryptedPassword); ?>" required>
                        <img src="./images/eye-icon.png" class="eye-icon" id="togglePassword">
                    </td>
                </tr>
                <tr>
                    <th>Role</th>
                    <td>
                        <select name="role" required>
                            <option value="Admin" <?php if ($role == 0) echo 'selected'; ?>>Admin</option>
                            <option value="Operator" <?php if ($role == 1) echo 'selected'; ?>>Operator</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th>Email</th>
                    <td><input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required></td>
                </tr>
            </table>
            <div class="form-buttons">
                <button type="submit">Update</button>
                <a href="./member_list.php"><button type="button">Cancel</button></a>
            </div>
        </form>
    </div>

    <script>
        document.getElementById('togglePassword').addEventListener('click', function () {
            const passwordField = document.getElementById('password');
            const type = passwordField.type === 'password' ? 'text' : 'password';
            passwordField.type = type;
            // Optionally, change the icon based on the field type
            this.src = type === 'password' ? './images/eye-icon.png' : './images/eye-icon-open.png'; // Change as needed
        });
    </script>
</body>
</html>
