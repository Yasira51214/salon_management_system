<?php
include 'common.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $service_id = $_POST['service_id'];
    $service_category = $_POST['service-category'] ?? '';
    $service_name = $_POST['service-name'] ?? '';
    $price = $_POST['price'] ?? '';
    $promotion_price = $_POST['promotion-price'] ?? '';
    $description = $_POST['si_description'] ?? '';

    // Validate required fields
    $errors = [];
    if (empty($service_category)) $errors[] = 'Service category is required.';
    if (empty($service_name)) $errors[] = 'Service name is required.';
    if (empty($price)) $errors[] = 'Price is required.';
    if (empty($description)) $errors[] = 'Description is required.';

    if (!empty($errors)) {
        // Display errors and stop the script
        echo 'Error: The following fields are required: ' . implode(', ', $errors);
        exit();
    }

    // Handle image uploads
    $image1 = $_FILES['si_image1']['name'] ? 'uploads/' . basename($_FILES['si_image1']['name']) : '';
    $image2 = $_FILES['si_image2']['name'] ? 'uploads/' . basename($_FILES['si_image2']['name']) : '';
    $image3 = $_FILES['si_image3']['name'] ? 'uploads/' . basename($_FILES['si_image3']['name']) : '';

    if ($image1) {
        move_uploaded_file($_FILES['si_image1']['tmp_name'], $image1);
    }
    if ($image2) {
        move_uploaded_file($_FILES['si_image2']['tmp_name'], $image2);
    }
    if ($image3) {
        move_uploaded_file($_FILES['si_image3']['tmp_name'], $image3);
    }

    // Build the SQL query
    $sql = "UPDATE service_item SET 
            si_cat = ?, 
            si_service_name = ?, 
            si_price = ?, 
            si_description = ?";

    $params = [$service_category, $service_name, $price, $description];

    // Append promotion price if it's not empty
    if (!empty($promotion_price)) {
        $sql .= ", si_promotion_price = ?";
        $params[] = $promotion_price;
    }

    // Append image fields if they are not empty
    if ($image1) {
        $sql .= ", si_image1 = ?";
        $params[] = $image1;
    }
    if ($image2) {
        $sql .= ", si_image2 = ?";
        $params[] = $image2;
    }
    if ($image3) {
        $sql .= ", si_image3 = ?";
        $params[] = $image3;
    }

    $sql .= " WHERE si_no = ?";
    $params[] = $service_id;

    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        die('Error preparing SQL statement: ' . $conn->error);
    }

    // Generate the type definition string based on the number of parameters
    $types = str_repeat('s', count($params) - 1) . 'i'; // all strings except the last one, which is an integer
    $stmt->bind_param($types, ...$params);

    if ($stmt->execute()) {
        echo "Service updated successfully.";
        header("Location: service_list.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
