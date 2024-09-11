<?php
include 'common.php';
include "side_bar.php";

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

// Check if the success parameter is set in the URL
if (isset($_GET['success']) && $_GET['success'] == 1) {
    echo "<script>alert('Member data saved successfully!');</script>";
}

// Check if the update_success parameter is set in the URL
if (isset($_GET['update_success']) && $_GET['update_success'] == 1) {
    echo "<script>alert('Member data updated successfully!');</script>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Member List</title>
    <link rel="stylesheet" href="./css/member_list.css">
    <style>
       /* Responsive Design */
       @media (max-width: 768px) {
           .container {
               padding: 10px;
           }
           h1 {
               font-size: 20px;
           }
           .search-container {
               flex-direction: column;
               align-items: stretch;
           }
           .search-container input[type="text"],
           .search-container button {
               width: 100%;
               margin-bottom: 5px;
           }
           table {
               font-size: 11px;
               overflow-x: auto; /* Enable horizontal scrolling */
               display: block;   /* Ensure the table takes block layout */
           }
           table th, table td {
               padding: 10px;
           }
           .pagination {
               flex-direction: row;
               justify-content: center;
           }
           .pagination a {
               width: auto;
               margin: 5px;
           }
       }
       @media (min-width: 768px) and (max-width: 1024px) {
           .container {
               width: 100%;
           }
           table {
               font-size: 11px;
               overflow-x: auto; /* Enable horizontal scrolling */
               display: block;   /* Ensure the table takes block layout */
           }
           table th, table td {
               padding: 10px;
           }
           .search-container input[type="text"] {
               flex-grow: 0;
           }
           .form-buttons button {
               width: 30%;
               margin-bottom: 5px;
               padding: 10px;
               font-size: 14px;
           }
       }
       @media (min-width: 1024px) {
           .container {
               width: 70%;
           }
       }
   </style>
</head>
<body>
<?php
$items_per_page = 10; // Number of items per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Get current page number from URL
$offset = ($page - 1) * $items_per_page; // Calculate the offset for the SQL query
$search_query = isset($_GET['search']) ? $_GET['search'] : ''; // Get search query

// Fetch data
$sql = "SELECT m_fullname, m_name, m_password, m_role, m_mail FROM member 
        WHERE m_name LIKE ? 
        ORDER BY m_no DESC 
        LIMIT ?, ?";
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    die("Error preparing statement: " . $conn->error);
}

$search_param = "%$search_query%";
$stmt->bind_param('sii', $search_param, $offset, $items_per_page);
if ($stmt->execute() === false) {
    die("Error executing statement: " . $stmt->error);
}
$result = $stmt->get_result();

// Get total number of records for pagination
$sql_total = "SELECT COUNT(*) FROM member WHERE m_name LIKE ?";
$stmt_total = $conn->prepare($sql_total);
if ($stmt_total === false) {
    die("Error preparing statement: " . $conn->error);
}

$stmt_total->bind_param('s', $search_param);
if ($stmt_total->execute() === false) {
    die("Error executing statement: " . $stmt_total->error);
}
$stmt_total->bind_result($total_items);
$stmt_total->fetch();
$total_pages = ceil($total_items / $items_per_page);

$stmt_total->close();
?>

<div class="container">
<h1>MEMBER LIST</h1>
<br>

    <div class="search-container">
        <input type="text" id="searchInput" placeholder="Search" value="<?php echo htmlspecialchars($search_query); ?>">
        <button onclick="searchMember()">Search</button>
        <a href="member_add.php" class="add-new">Add New</a>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Full Name</th>
                <th>User Name</th>
                <th>Password</th>
                <th>Type</th>
                <th>Email</th>
                <th>Function</th>
            </tr>
        </thead>
        <tbody id="memberTable">
        <?php
              $counter = $offset + 1;

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $counter . "</td>";
                echo "<td>" . htmlspecialchars($row['m_fullname']) . "</td>";
                echo "<td>" . htmlspecialchars($row['m_name']) . "</td>";
                // Decrypt the password but display it as asterisks
                $decryptedPassword = decryptPassword($row['m_password'], $key);
                echo "<td>" . str_repeat('*', strlen($decryptedPassword)) . "</td>"; // Display as asterisks
                echo "<td>" . ($row['m_role'] == 0 ? 'Admin' : 'Operator') . "</td>";
                echo "<td>" . htmlspecialchars($row['m_mail']) . "</td>";
                echo "<td>
                <button class='delete' onclick='confirmDelete(\"" . htmlspecialchars($row['m_name']) . "\")'>Delete</button>
                <a href='member_modify.php?user=" . htmlspecialchars($row['m_name']) . "'><button class='modify'>Modify</button></a>
              </td>";
                    echo "</tr>";
                    $counter++;
                }
            } else {
                echo "<tr><td colspan='6'>No members found</td></tr>";
            }
            ?>
      
        
        </tbody>
    </table>

    <!-- Pagination -->
    <div class="pagination">
        <?php if ($page > 1): ?>
            <a href="?page=1&search=<?php echo urlencode($search_query); ?>">First</a>
            <a href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search_query); ?>">Prev</a>
        <?php endif; ?>

        <?php if ($page < $total_pages): ?>
            <a href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search_query); ?>">Next</a>
            <a href="?page=<?php echo $total_pages; ?>&search=<?php echo urlencode($search_query); ?>">Last</a>
        <?php endif; ?>
    </div>
</div>

<script>
function searchMember() {
    var searchQuery = document.getElementById('searchInput').value;
    window.location.href = '?search=' + encodeURIComponent(searchQuery);
}

function confirmDelete(userName) {
    if (confirm("Are you sure you want to delete this member?")) {
        window.location.href = "member_delete.php?user=" + encodeURIComponent(userName);
    }
}
</script>

</body>
</html>

<?php
// Close connection
$conn->close();
?>







