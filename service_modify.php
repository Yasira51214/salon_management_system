<?php
include 'common.php';
?>
<?php

if (isset($_GET['id'])) {
    $service_id = $_GET['id'];

    // Fetch the existing data
    $sql = "SELECT * FROM service_item WHERE si_no = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $service_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $service = $result->fetch_assoc();
    } else {
        echo "No service found with the given ID.";
        exit();
    }
} else {
    echo "No service ID provided.";
    exit();
}
?> 

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modify Service</title>
    <link rel="stylesheet" href="./css/service_new.css">
    <style>
        /* Other CSS styles for the form */
        .form-container {
            max-width: 800px;
            margin: auto;
            padding: 20px;
            background: #fff;
            box-shadow: 0px 0px 10px 0px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            width: 150px;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
        }

        .image-upload {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .image-preview {
            width: 80px;
            height: 80px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-size: cover;
            background-position: center;
            
        }

        .image-buttons {
            display: flex;
            gap: 10px;
            
        }

        .delete-button,
        .replace-button {
            display: inline-block;
            padding: 5px 10px;
            cursor: pointer;
            font-size: 14px;
        }

        .delete-button {
            background-color: #f44336;
            color: white;
            border: none;
            border-radius: 4px;
        }

        .replace-button {
            background-color: #4caf50;
            color: white;
            border: none;
            border-radius: 4px;
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
        <h1>Service Modify</h1>
        <form id="service-form" method="POST" action="update_service.php" enctype="multipart/form-data">
            <input type="hidden" name="service_id" value="<?php echo $service['si_no']; ?>">

            <div class="form-group">
                <label for="service-category">Service Category</label>
                <select id="service-category" name="service-category" onchange="updateCategoryName()">
                    <option value="">Select</option>
                    <option value="N" <?php if ($service['si_cat'] == 'N') echo 'selected'; ?>>Nail</option>
                    <option value="P" <?php if ($service['si_cat'] == 'P') echo 'selected'; ?>>Pedi</option>
                    <option value="E" <?php if ($service['si_cat'] == 'E') echo 'selected'; ?>>Eyelashes</option>
                    <option value="M" <?php if ($service['si_cat'] == 'M') echo 'selected'; ?>>Massage</option>
                    <option value="T" <?php if ($service['si_cat'] == 'T') echo 'selected'; ?>>Training</option>
                    <option value="S" <?php if ($service['si_cat'] == 'S') echo 'selected'; ?>>Sales item</option>
                </select>
            </div>

            <div class="form-group">
                <label for="service-name">Service Name</label>
                <input type="text" id="service-name" name="service-name" value="<?php echo $service['si_service_name']; ?>">
            </div>

            <div class="form-group">
                <label for="price">Price</label>
                <input type="number" id="price" name="price" value="<?php echo $service['si_price']; ?>">
            </div>

            <div class="form-group">
                <label for="promotion-price">Promotion Price</label>
                <input type="number" id="promotion-price" name="promotion-price" value="<?php echo $service['si_promotion_price']; ?>">
            </div>

            <div class="form-group">
                <label for="image">Image</label>
                <div class="image-upload">
                    <label for="image1">Attach 1</label>
                    <div class="image-preview" id="preview1">
                        <?php if ($service['si_image1']) echo '<img src="' . $service['si_image1'] . '" alt="Image 1" style="width:100%;height:100%;">'; ?>
                    </div>
                    <div class="image-buttons" id="buttons1" style="<?php echo $service['si_image1'] ? 'display: flex;' : 'display: none;'; ?>">
                        <button type="button" class="delete-button" onclick="deleteImage('si_image1', 'preview1', 'buttons1')">Delete</button>
                        <button type="button" class="replace-button" onclick="replaceImage('image1')">Replace</button>
                    </div>
                    <label for="image2">Attach 2</label>
                    <div class="image-preview" id="preview2">
                        <?php if ($service['si_image2']) echo '<img src="' . $service['si_image2'] . '" alt="Image 2" style="width:100%;height:100%;">'; ?>
                    </div>
                    <div class="image-buttons" id="buttons2" style="<?php echo $service['si_image2'] ? 'display: flex;' : 'display: none;'; ?>">
                        <button type="button" class="delete-button" onclick="deleteImage('si_image2', 'preview2', 'buttons2')">Delete</button>
                        <button type="button" class="replace-button" onclick="replaceImage('image2')">Replace</button>
                    </div>
                    <label for="image3">Attach 3</label>
                    <div class="image-preview" id="preview3">
                        <?php if ($service['si_image3']) echo '<img src="' . $service['si_image3'] . '" alt="Image 3" style="width:100%;height:100%;">'; ?>
                    </div>
                    <div class="image-buttons" id="buttons3" style="<?php echo $service['si_image3'] ? 'display: flex;' : 'display: none;'; ?>">
                        <button type="button" class="delete-button" onclick="deleteImage('si_image3', 'preview3', 'buttons3')">Delete</button>
                        <button type="button" class="replace-button" onclick="replaceImage('image3')">Replace</button>
                    </div>
                    <input type="file" id="image1" name="si_image1" style="display: none;" onchange="previewImage(event, 'preview1', 'buttons1')">
                    <input type="file" id="image2" name="si_image2" style="display: none;" onchange="previewImage(event, 'preview2', 'buttons2')">
                    <input type="file" id="image3" name="si_image3" style="display: none;" onchange="previewImage(event, 'preview3', 'buttons3')">
                </div>
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea style="width: 100%; box-sizing: border-box; resize: none;" rows="5" id="description" name="description"><?php echo $service['si_description']; ?></textarea>
            </div>

            <div class="form-buttons">
                <button type="submit" class="page-btn">Save</button>
                <button type="submit" class="page-btn">Save & New</button>
                <a href="service_list.php"><button type="button" class="page-btn">Cancel</button></a>
            </div>
        </form>
    </div>

    <script>
        function deleteImage(imageField, previewId, buttonsId) {
            if (confirm("Are you sure you want to delete this image?")) {
                var xhr = new XMLHttpRequest();
                xhr.open("POST", "delete_image.php", true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xhr.onreadystatechange = function () {
                    if (xhr.readyState === XMLHttpRequest.DONE) {
                        if (xhr.status === 200) {
                            document.getElementById(previewId).style.display = 'none';
                            document.getElementById(buttonsId).style.display = 'none';
                            document.getElementById(imageField).value = ''; // Clear the image input
                        } else {
                            alert("Failed to delete the image. Please try again.");
                        }
                    }
                };
                xhr.send("imageField=" + encodeURIComponent(imageField) + "&service_id=" + encodeURIComponent(document.querySelector('[name="service_id"]').value));
            }
        }

        function replaceImage(imageFieldId) {
            document.getElementById(imageFieldId).click();
        }

        function previewImage(event, previewId, buttonsId) {
            var input = event.target;
            var file = input.files[0];
            var reader = new FileReader();

            reader.onload = function (e) {
                var img = document.getElementById(previewId).querySelector('img');
                if (!img) {
                    img = document.createElement('img');
                    img.style.width = '100%';
                    img.style.height = '100%';
                    document.getElementById(previewId).appendChild(img);
                }
                img.src = e.target.result;
                document.getElementById(previewId).style.display = 'block';
                document.getElementById(buttonsId).style.display = 'flex';
            };
            reader.readAsDataURL(file);
        }

        function updateCategoryName() {
            var category = document.getElementById('service-category').value;
            // Logic to update category name based on selected category if needed
        }
    </script>
</body>
</html>
