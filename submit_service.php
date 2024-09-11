<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $category = $_POST['si_cat'];
    $name = $_POST['si_service_name'];
    $price = $_POST['si_price'];
    $promotion_price = isset($_POST['si_promotion_price']) && $_POST['si_promotion_price'] !== '' ? $_POST['si_promotion_price'] : 0.00;
    $description = $_POST['si_description'];

    $image1 = $_FILES['si_image1']['name'];
    $image2 = isset($_FILES['si_image2']['name']) ? $_FILES['si_image2']['name'] : NULL;
    $image3 = isset($_FILES['si_image3']['name']) ? $_FILES['si_image3']['name'] : NULL;

    $target_dir = "uploads/";

    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0755, true);
    }

    $target_file1 = $target_dir . basename($image1);
    $target_file2 = $image2 ? $target_dir . basename($image2) : NULL;
    $target_file3 = $image3 ? $target_dir . basename($image3) : NULL;

    $uploadOk = true;

    if (!move_uploaded_file($_FILES['si_image1']['tmp_name'], $target_file1)) {
        echo "Sorry, there was an error uploading your file 1: " . $_FILES['si_image1']['error'];
        $uploadOk = false;
    }

    if ($image2 && !move_uploaded_file($_FILES['si_image2']['tmp_name'], $target_file2)) {
        echo "Sorry, there was an error uploading your file 2: " . $_FILES['si_image2']['error'];
        $uploadOk = false;
    }

    if ($image3 && !move_uploaded_file($_FILES['si_image3']['tmp_name'], $target_file3)) {
        echo "Sorry, there was an error uploading your file 3: " . $_FILES['si_image3']['error'];
        $uploadOk = false;
    }

    if ($uploadOk) {
        $sql = "INSERT INTO service_item (si_cat, si_service_name, si_price, si_promotion_price, si_image1, si_image2, si_image3, si_description) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("ssddssss", $category, $name, $price, $promotion_price, $target_file1, $target_file2, $target_file3, $description);

            if ($stmt->execute()) {
                echo "New service added successfully";
                header("Location: service_list.php");
                exit();
            } else {
                echo "Error executing statement: " . $stmt->error;
            }

            $stmt->close();
        } else {
            echo "Error preparing statement: " . $conn->error;
        }
    } else {
        echo "File upload failed, data not saved.";
    }

    $conn->close();
} else {
    echo "No POST data received.";
}
?>
