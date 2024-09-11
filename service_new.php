<?php
include 'common.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Service Form</title>
    <link rel="stylesheet" href="./css/service_new.css">
    <link rel="stylesheet" href="./css/customer_list.css">
    <style>
        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.4);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 100%;
            max-width: 600px;
            border-radius: 10px;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

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
            width: 150;
            margin-bottom: 5px;
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

        .button-group {
            display: flex;
            gap: 5px;
        }

        .delete-button,
        .replace-button {
            display: none;
            padding: 5px 10px;
            cursor: pointer;
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
        <h1>Service New</h1>
        <br>
        <form id="service-form" action="submit_service.php" method="POST" enctype="multipart/form-data">
        <label for="service-category" id="lable">Service Category</label>    
        <div class="form-group">
                <select id="service-category" name="si_cat" onchange="updateCategoryName()">
                    <option value="">Select</option>
                    <option value="N">Nail</option>
                    <option value="P">Pedicure</option>
                    <option value="E">Eyelashes</option>
                    <option value="M">Massage</option>
                    <option value="T">Training</option>
                    <option value="S">Sales item</option>
                </select>
            </div>
            <label for="service-name" id="lable">Service Name</label>
            <div class="form-group">
                <input type="text" id="service-name" name="si_service_name">
            </div>
            <label for="price" id="lable">Price</label>
            <div class="form-group">    
                <input type="number" id="price" name="si_price">
            </div>
            <label for="promotion-price" id="lable">Promotion Price</label>
            <div class="form-group">
                <input type="number" id="promotion-price" name="si_promotion_price">
            </div>
            <label for="image" id="lable">Image</label>
            <div class="form-group">
                <div class="image-upload">
                    <label for="image1" class="img">Attach 1</label>
                    <div class="image-preview" id="preview1"></div>
                    <label for="image2">Attach 2</label>
                    <div class="image-preview" id="preview2"></div>
                    <label for="image3">Attach 3</label>
                    <div class="image-preview" id="preview3"></div>
                    <input type="file" id="image1" name="si_image1" onchange="previewImage(event, 'preview1', 'delete1', 'replace1')">
                    <input type="file" id="image2" name="si_image2" onchange="previewImage(event, 'preview2', 'delete2', 'replace2')">
                    <input type="file" id="image3" name="si_image3" onchange="previewImage(event, 'preview3', 'delete3', 'replace3')">
                </div>
                <div class="image-buttons">
                    <div class="button-group">
                        <button type="button" class="delete-button" id="delete1" onclick="deleteImage('image1', 'preview1', 'delete1', 'replace1')">Delete 1</button>
                        <button type="button" class="replace-button" id="replace1" onclick="replaceImage('image1')">Replace 1</button>
                    </div>
                    <div class="button-group">
                        <button type="button" class="delete-button" id="delete2" onclick="deleteImage('image2', 'preview2', 'delete2', 'replace2')">Delete 2</button>
                        <button type="button" class="replace-button" id="replace2" onclick="replaceImage('image2')">Replace 2</button>
                    </div>
                    <div class="button-group">
                        <button type="button" class="delete-button" id="delete3" onclick="deleteImage('image3', 'preview3', 'delete3', 'replace3')">Delete 3</button>
                        <button type="button" class="replace-button" id="replace3" onclick="replaceImage('image3')">Replace 3</button>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                
                <textarea  style="width: 100%; box-sizing: border-box; resize: none;"id="description" name="si_description" rows="5" placeholder="Type Note here"></textarea>
            </div>
            <div class="form-buttons">
                <button type="button" class="page-btn">View Order Form</button>
                <button type="submit" class="page-btn">Save</button>
                <a href="./service_list.php"><button type="button" class="page-btn">Cancel</button></a>
            </div>
        </form>
    </div>

    <!-- Modal for Order Form -->
    <div id="orderModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <div id="orderContent">Loading...</div>
        </div>
    </div>

    <script>
        // Function to update category name (as before)
        function updateCategoryName() {
            const categoryMap = {
                'S': 'Sales item',
                'N': 'Nail',
                'P': 'Pedicure',
                'E': 'Eyelashes',
                'M': 'Massage',
                'T': 'Training'
            };
            const categorySelect = document.getElementById('service-category');
            const selectedCategory = categorySelect.value;
            const fullCategoryName = categoryMap[selectedCategory] || 'Select';
            
            // Update the text of the selected option
            const options = categorySelect.options;
            for (let i = 0; i < options.length; i++) {
                if (options[i].value === selectedCategory) {
                    options[i].innerText = fullCategoryName;
                } else {
                    // Reset the option text if the value does not match
                    options[i].innerText = categoryMap[options[i].value] || 'Select';
                }
            }
        }

        // Event listener for form submission
        document.getElementById('service-form').addEventListener('submit', function(event) {
            event.preventDefault();
            const formData = new FormData(this);
            fetch('submit_service.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                alert('Form submitted');
                window.location.href = 'service_list.php'; // Redirect after successful submission
            })
            .catch(error => console.error('Error:', error));
        });

        // Event listener for View Order Form button
        document.getElementById('view-order').addEventListener('click', function() {
            const modal = document.getElementById('orderModal');
            const span = document.getElementsByClassName('close')[0];

            // Load content into the modal
            fetch('order_add_popup.php')
                .then(response => response.text())
                .then(data => {
                    document.getElementById('orderContent').innerHTML = data;
                    modal.style.display = 'block'; // Show the modal
                })
                .catch(error => console.error('Error:', error));

            // When the user clicks on <span> (x), close the modal
            span.onclick = function() {
                modal.style.display = 'none';
            }

            // When the user clicks anywhere outside of the modal, close it
            window.onclick = function(event) {
                if (event.target == modal) {
                    modal.style.display = 'none';
                }
            }
        });

        // Event listener for Cancel button
        document.getElementById('cancel').addEventListener('click', function() {
            document.getElementById('service-form').reset();
        });

        // Function to preview image
        function previewImage(event, previewId, deleteButtonId, replaceButtonId) {
            const preview = document.getElementById(previewId);
            const file = event.target.files[0];
            const reader = new FileReader();

            reader.onload = function() {
                preview.style.backgroundImage = 'url(' + reader.result + ')';
                document.getElementById(deleteButtonId).style.display = 'block'; // Show delete button
                document.getElementById(replaceButtonId).style.display = 'block'; // Show replace button
            };

            if (file) {
                reader.readAsDataURL(file);
            }
        }

        // Function to delete image
        function deleteImage(inputId, previewId, deleteButtonId, replaceButtonId) {
            const input = document.getElementById(inputId);
            const preview = document.getElementById(previewId);
            input.value = "";
            preview.style.backgroundImage = "";
            document.getElementById(deleteButtonId).style.display = 'none';
            document.getElementById(replaceButtonId).style.display = 'none';
        }

        // Function to replace image
        function replaceImage(inputId) {
            const input = document.getElementById(inputId);
            input.click();
        }

        // Initialize category name on page load
        document.addEventListener('DOMContentLoaded', updateCategoryName);
    </script>
</body>
</html>
