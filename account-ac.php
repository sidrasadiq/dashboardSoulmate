<?php
session_start();
include 'layouts/config.php';
include 'layouts/functions.php';

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['token'])) {
    $verification_token = $_GET['token'];
    $updatedAt = date("Y-m-d H:i:s");

    try {
        // Check if the token exists in the database
        $sql = "SELECT id, is_verified FROM users WHERE verification_token = ?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Failed to prepare statement: " . $conn->error);
        }

        $stmt->bind_param("s", $verification_token);
        $stmt->execute();
        $stmt->bind_result($user_id, $is_verified);

        if ($stmt->fetch()) {
            if ($is_verified) {
                // If already verified
                $_SESSION['message'][] = array(
                    "type" => "info",
                    "content" => "Your account is already verified. You can log in."
                );
            } else {
                // Update user's verification status
                $stmt->close();

                $update_sql = "UPDATE users SET is_verified = 1, verification_token = NULL, updated_at = ? WHERE id = ?";
                $update_stmt = $conn->prepare($update_sql);
                if (!$update_stmt) {
                    throw new Exception("Failed to prepare update statement: " . $conn->error);
                }

                $update_stmt->bind_param("si", $updatedAt, $user_id);
                if ($update_stmt->execute()) {
                    $_SESSION['message'][] = array(
                        "type" => "success",
                        "content" => "Your account has been successfully verified. You can now log in."
                    );
                } else {
                    throw new Exception("Failed to update user verification status: " . $update_stmt->error);
                }

                $update_stmt->close();
            }
        } else {
            // Invalid token
            $_SESSION['message'][] = array(
                "type" => "error",
                "content" => "Invalid or expired verification link."
            );
        }

        // Close the statement
        $stmt->close();
    } catch (Exception $e) {
        $_SESSION['message'][] = array("type" => "error", "content" => "Error: " . $e->getMessage());
    } finally {
        // Redirect to login page
        header("Location: login.php");
        exit();
    }
} else {
    // If no token is provided
    $_SESSION['message'][] = array(
        "type" => "error",
        "content" => "Invalid access. Verification token is required."
    );
    header("Location: login.php");
    exit();
}
