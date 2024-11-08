<?php
include 'layouts/session.php';
include 'layouts/config.php';
// Check if the ID is present in the URL
if (isset($_GET['id'])) {
    // Retrieve and sanitize the nationality ID from the URL
    $nationalityId = intval($_GET['id']);

    // Verify that the ID is valid
    if ($nationalityId <= 0) {
        $_SESSION['message'][] = array("type" => "danger", "content" => "Invalid nationality ID.");
        header("location: manage-nationality.php");
        exit();
    }

    try {
        // Step 1: Begin a database transaction
        if (!$conn->begin_transaction()) {
            throw new Exception("Transaction initiation failed: " . $conn->error);
        }

        // Step 2: Prepare the DELETE SQL statement
        $sql = "DELETE FROM nationality WHERE id = ?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Statement preparation failed: " . $conn->error);
        }

        // Step 3: Bind the nationality ID to the prepared statement
        $stmt->bind_param("i", $nationalityId);

        // Step 4: Execute the deletion
        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                // Commit the transaction on success
                $conn->commit();
                $_SESSION['message'][] = array("type" => "success", "content" => "Nationality deleted successfully!");
            } else {
                throw new Exception("No nationality found with the specified ID.");
            }
        } else {
            throw new Exception("Deletion failed: " . $stmt->error);
        }

        // Step 5: Close the statement
        $stmt->close();
    } catch (Exception $e) {
        // Step 6: Roll back the transaction on error
        $conn->rollback();

        // Log the error message in session
        $_SESSION['message'][] = array("type" => "danger", "content" => "Error: " . $e->getMessage());
    } finally {
        // Step 7: Redirect back to manage nationality page
        header("location: manage-nationality.php");
        exit();
    }
} else {
    // Redirect if no ID is specified in the URL
    $_SESSION['message'][] = array("type" => "danger", "content" => "No ID specified for deletion.");
    header("location: manage-nationality.php");
    exit();
}
