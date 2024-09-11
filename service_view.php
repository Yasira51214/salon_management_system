<?php
include 'common.php';

// Check if 'id' parameter is present in the URL
if (isset($_GET['id'])) {
    $service_id = $_GET['id'];

    // Prepare and execute the SQL query to fetch service details
    $sql = "SELECT si_no, si_cat, si_service_name, si_price, si_promotion_price, si_image1, si_image2, si_image3, si_description FROM service_item WHERE si_no = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $service_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if a service was found
    if ($result->num_rows > 0) {
        $service = $result->fetch_assoc();
    } else {
        echo "Service not found.";
        exit();
    }
} else {
    echo "No service ID specified.";
    exit();
}

// Map category codes to full names
$category_map = [
    'N' => 'Nail',
    'P' => 'Pedicure',
    'E' => 'Eyelashes',
    'M' => 'Massage',
    'T' => 'Training',
    'S' => 'Sales item'
];

// Get the full category name
$category_full_name = isset($category_map[$service['si_cat']]) ? $category_map[$service['si_cat']] : 'Unknown';

// Close the database connection
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Service</title>
    <link rel="stylesheet" href="./css/service_new.css">
    <style>
        h1 {
            text-align: center;
            font-size: 40px;
            color: #22584b;
        }

        .form-container {
            width: 50%;
            margin: 50px auto;
            padding: 20px;
            background-color: #ffffff;
            border: 1px solid #ccc;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: inline-block;
            width: 80;
            margin-right: 120px;
            font-weight: bold;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: calc(70% - 110px);
            padding: 6px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .form-group input[type="file"] {
            display: none;
        }

        .image-upload {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }

        .image-upload label {
            display: inline-block;
            padding: 6px 10px;
            margin-right: 5px;
            background-color: #ddd;
            border: 1px solid #ccc;
            border-radius: 4px;
            cursor: pointer;
        }

        .image-preview {
            width: 40px;
            height: 30px;
            border: 1px solid #ccc;
            border-radius: 4px;
            background-size: cover;
            background-position: center;
            margin-left: 5px;
            margin-right: 10px;
        }

        .image-buttons {
            display: flex;
            flex-direction: row;
            align-items: flex-start;
            margin-left: 3px;
            margin-right: 5px;
        }

        .button-group {
            display: flex;
            gap: 3px;
            margin-top: 0px;
            float: left;
            margin-left: 12px;
        }

        .delete-button,
        .replace-button {
            padding: 5px 10px;
            background-color: #f44336;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 8px;
            display: none;
        }

        .replace-button {
            background-color: #4caf50;
        }

        .form-buttons {
            display: flex;
            justify-content: flex-end; /* Aligns buttons to the right */
            gap: 10px; /* Maintains the spacing between buttons */
        }

        .form-buttons button {
            padding: 10px 20px;
            cursor: pointer;
        }

        .form-buttons a {
            text-decoration: none;
            color: #000;
        }

        .page-btn {
            background-color: #22584b;
            color: white;
            border: none;
            padding: 10px 20px;
            margin: 0 5px;
            cursor: pointer;
        }
        #lable{
            font-weight: bold;
        }
        
    </style>
</head>
<body>
    <?php include "side_bar.php"; ?>

    <div class="form-container">
        <h1>View Service</h1>
        <label for="si_no" id="lable">Service ID:</label>
        <div class="form-group">    
            <input type="text" id="si_no" value="<?php echo $service['si_no']; ?>" readonly>
        </div>
        <label for="si_cat">Category:</label>
        <div class="form-group">
            <input type="text" id="si_cat" value="<?php echo $category_full_name; ?>" readonly>
        </div>
        <label for="si_service_name" id="lable">Service Name:</label>
        <div class="form-group">
            <input type="text" id="si_service_name" value="<?php echo $service['si_service_name']; ?>" readonly>
        </div>
        <label for="si_price" id="lable">Price:</label>
        <div class="form-group">
            <input type="text" id="si_price" value="<?php echo $service['si_price']; ?>" readonly>
        </div>
        <label for="si_promotion_price" id="lable">Promotion Price:</label>
        <div class="form-group">
            <input type="text" id="si_promotion_price" value="<?php echo $service['si_promotion_price']; ?>" readonly>
        </div>
        <label for="si_description" id="lable">Description:</label>
        <div class="form-group">
            <textarea id="si_description" rows="5" style="width: 100%; box-sizing: border-box; resize: none;" readonly><?php echo $service['si_description']; ?></textarea>
        </div>
        <div class="form-group">
            <label>Images:</label>
            <div class="image-upload">
                <label for="image1">Image 1</label>
                <img class="image-preview" src="<?php echo $service['si_image1']; ?>" alt="Service Image 1">
                <?php if (!empty($service['si_image2'])): ?>
                    <img class="image-preview" src="<?php echo $service['si_image2']; ?>" alt="Service Image 2">
                <?php endif; ?>
                <?php if (!empty($service['si_image3'])): ?>
                    <img class="image-preview" src="<?php echo $service['si_image3']; ?>" alt="Service Image 3">
                <?php endif; ?>
            </div>
        </div>
        <div class="form-buttons">
            <button class="page-btn" onclick="window.location.href='service_list.php'">Back to List</button>
        </div>
    </div>
</body>
</html>
