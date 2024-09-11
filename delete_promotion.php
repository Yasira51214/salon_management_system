<?php
include 'common.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the promotion number from the form submission
    $p_no = isset($_POST['p_no']) ? (int)$_POST['p_no'] : 0;

    if ($p_no > 0) {
        // Prepare and execute the delete statements
        $conn->begin_transaction();
        try {
            // Delete from pro_service table first
            $stmt1 = $conn->prepare("DELETE FROM pro_service WHERE pro_p_no = ?");
            $stmt1->bind_param('i', $p_no);
            $stmt1->execute();

            // Delete from promotion table
            $stmt2 = $conn->prepare("DELETE FROM promotion WHERE p_no = ?");
            $stmt2->bind_param('i', $p_no);
            $stmt2->execute();

            // Check if any rows were affected
            if ($stmt1->affected_rows > 0 || $stmt2->affected_rows > 0) {
                $conn->commit();
                echo "<script>alert('Promotion and related services deleted successfully.');</script>";
            } else {
                $conn->rollback();
                echo "<script>alert('Error deleting promotion.');</script>";
            }
        } catch (Exception $e) {
            $conn->rollback();
            echo "<script>alert('Error: " . $e->getMessage() . "');</script>";
        }
        
        // Close statements
        $stmt1->close();
        $stmt2->close();
    }
}

// Redirect back to the promotion list after deletion
header("Location: promotion_list.php");
exit;
?>
