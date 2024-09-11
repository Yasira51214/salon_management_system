<?php
include 'common.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expense Manage</title>
    <link rel="stylesheet" href="./css/expense_manage.css">
    <style>
        /* Responsive Design */
        @media (max-width: 768px) {
            h1 {
                font-size: 20px;
            }
            .search-container input[type="text"] {
                font-size: 14px;
                padding: 8px;
            }
            .search-container button, .search-container a.add-new {
                font-size: 14px;
                padding: 8px 12px;
            }
            table {
                font-size: 14px;
            }
            td button {
                font-size: 12px;
                padding: 6px;
            }
        }

        @media (max-width: 480px) {
            .search-container {
                flex-direction: column;
                align-items: stretch;
            }
            .search-container input[type="text"], .search-container button, .search-container a.add-new {
                font-size: 12px;
                padding: 6px;
                margin: 2px 0;
            }
            table {
                font-size: 12px;
                overflow-x: auto;
                display: block;
            }
            td button {
                font-size: 10px;
                padding: 2px;
                margin-top: 5px;
            }
        }
        @media (max-width: 480px) {
            .container {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <?php
    include "side_bar.php";
    // Handle delete request
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete'])) {
        $ex_no_to_delete = $_POST['ex_no'];

        // Prepare and execute delete statement
        $delete_stmt = $conn->prepare("DELETE FROM expense_cat WHERE ex_no = ?");
        if ($delete_stmt === false) {
            echo "<script>alert('Error preparing the delete statement: " . $conn->error . "');</script>";
        } else {
            $delete_stmt->bind_param("i", $ex_no_to_delete);
            if ($delete_stmt->execute()) {
                echo "<script>alert('Expense category deleted successfully!');</script>";
            } else {
                echo "<script>alert('Error deleting category: " . $delete_stmt->error . "');</script>";
            }
            $delete_stmt->close();
        }
    }

    // Query to fetch expense categories in descending order
    $sql = "SELECT ex_no, ex_cat_name FROM expense_cat ORDER BY ex_no DESC";
    $result = $conn->query($sql);
    ?>

    <div class="container">
        <h1>Expense Manage</h1>
        <br>
        <div class="search-container">
            <input type="text" id="searchInput" placeholder="Search">
            <button onclick="searchMember()">Search</button>
            <a href="exp_cat_add.php" class="add-new">Add New</a>
        </div>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Expense Category Name</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="memberTable">
                <?php
                // Initialize a counter variable starting with the highest number
                $counter = $result->num_rows;

                // Check if there are results
                if ($result->num_rows > 0) {
                    // Output data of each row
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $counter . "</td>"; // Output the row number
                        echo "<td>" . htmlspecialchars($row['ex_cat_name']) . "</td>";
                        echo "<td>";
                        echo '<button class="delete" onclick="confirmDelete(\'' . htmlspecialchars($row['ex_no']) . '\')">Delete</button>';
                        echo '<a href="exp_cat_modify.php?id=' . urlencode($row['ex_no']) . '"><button class="modify">Modify</button></a>';
                        echo "</td>";
                        echo "</tr>";

                        // Decrement the counter
                        $counter--;
                    }
                } else {
                    echo "<tr><td colspan='3'>No expense categories found</td></tr>";
                }

                // Close the database connection
                $conn->close();
                ?>
            </tbody>
        </table>
    </div>

    <form id="deleteForm" action="" method="post" style="display: none;">
        <input type="hidden" name="ex_no" id="deleteExCatName">
        <input type="hidden" name="delete" value="true">
    </form>

    <script>
        function searchMember() {
            const input = document.getElementById('searchInput').value.toLowerCase();
            const table = document.getElementById('memberTable');
            const rows = table.getElementsByTagName('tr');

            for (let i = 0; i < rows.length; i++) {
                const cells = rows[i].getElementsByTagName('td');
                let match = false;

                for (let j = 0; j < cells.length; j++) {
                    if (cells[j]) {
                        const cellValue = cells[j].textContent || cells[j].innerText;
                        if (cellValue.toLowerCase().indexOf(input) > -1) {
                            match = true;
                            break;
                        }
                    }
                }

                if (match) {
                    rows[i].style.display = '';
                } else {
                    rows[i].style.display = 'none';
                }
            }
        }

        function confirmDelete(ex_no) {
            const confirmDelete = confirm('Are you sure you want to delete the expense category?');
            if (confirmDelete) {
                document.getElementById('deleteExCatName').value = ex_no;
                document.getElementById('deleteForm').submit();
            }
        }
    </script>
</body>
</html>
