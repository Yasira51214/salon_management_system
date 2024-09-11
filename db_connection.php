<?php
// if (session_status() == PHP_SESSION_NONE) {
//     session_start();
// }
    $conn=mysqli_connect('localhost','root','123','salon_management_system');
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    
?>