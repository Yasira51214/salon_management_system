<?php
include 'db_connection.php';
session_start();

// Function to decrypt passwords
function decryptPassword($encryptedPassword, $key) {
    // Decode the base64 encoded password
    $decoded = base64_decode($encryptedPassword);
    
    // Check if the decoded data contains the delimiter
    if (strpos($decoded, '::') === false) {
        return "Invalid password format"; // or handle it as needed
    }
    
    // Split the data into encryptedData and iv
    list($encryptedData, $iv) = explode('::', $decoded, 2);
    
    // Decrypt the data
    return openssl_decrypt($encryptedData, 'aes-256-cbc', $key, 0, $iv);
}

// Generate or retrieve your encryption key
$key = 'your-encryption-key-here'; // Replace with your actual key, ensure it is securely stored

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Query to get the encrypted password from the database
    $sql = "SELECT * FROM member WHERE m_name='$username'";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // Decrypt the stored password
        $decryptedPassword = decryptPassword($row['m_password'], $key);

        // Verify the entered password with the decrypted password in the database
        if ($password === $decryptedPassword) {
            $_SESSION['username'] = $row['m_name'];
            $_SESSION['role'] = $row['m_role'];

            // Hash the entered password for storing in the login table
            $hashedLoginPassword = password_hash($password, PASSWORD_DEFAULT);
            
            // Check if the user login data already exists in the login table
            $checkLoginSQL = "SELECT * FROM login WHERE l_username='$username'";
            $checkLoginResult = $conn->query($checkLoginSQL);
            
            if ($checkLoginResult->num_rows == 0) {
                // Insert login data into the login table with the hashed password
                $insertLoginSQL = "INSERT INTO login (l_username, l_password) VALUES ('$username', '$hashedLoginPassword')";
                $conn->query($insertLoginSQL);
            }
            
            // Redirect based on user role
            if ($row['m_role'] == '0') {
                header("Location: main.php?page=dashboard");
            } elseif ($row['m_role'] == '1') {
                header("Location: main.php?page=dashboard");
            }
            exit(); // Always exit after a header redirection
        } else {
            echo "<script>showError('Invalid username or password');</script>";
        }
    } else {
        echo "<script>showError('Invalid username or password');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login Page</title>
  <link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet">
  <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
  <link rel="stylesheet" href="./css/style.css">
  <script>
    function showError(message) {
      const errorDiv = document.getElementById('error-message');
      errorDiv.textContent = message;
      errorDiv.style.display = 'block';
    }
  </script>
</head>
<body>
<div class="box-form">
  <div class="left">
    <div class="overlay">
      <img class="box-form" src="./images/Picture1.png" alt="">
    </div>
  </div>
  <div class="right">
    <h5>Login</h5>
    <p>Don't have an account? Contact Admin!</p>
    <form method="POST" action="">
      <div class="inputs">
        <input type="text" name="username" placeholder="User name" required>
        <br>
        <input type="password" name="password" placeholder="Password" required>
      </div>
      <br><br>
      <div class="remember-me--forget-password">
        <label>
          <input type="checkbox" name="remember_me" checked/>
          <span class="text-checkbox">Remember me</span>
        </label>
      </div>
      <br>
      <button type="submit" name="login">Login</button>
      <div id="error-message" style="display: none; color: red; margin-top: 10px;"></div>
    </form>
  </div>
</div>
</body>
</html>
