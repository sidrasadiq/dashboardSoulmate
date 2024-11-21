<?php
// include 'layouts/session.php';
// include 'layouts/config.php';
// include 'layouts/functions.php';
session_start();

// Check if the user is logged in and role_id is set in session
if (!isset($_SESSION['role_id'])) {
    // $_SESSION['message'][] = array("type" => "danger", "content" => "Unauthorized access.");
    header("location: login.php");
    exit();
}

try {
    // Redirect based on role ID
    switch ($_SESSION['role_id']) {
        case 1: // Admin role
            // header("location: admin-dashboard.php");
            include 'admin-dashboard.php';
            break;

        case 2: // User role
            // header("location: user-dashboard.php");
            include 'user_index.php';
            break;

        default: // Invalid role, redirect to logout
            $_SESSION['message'][] = array("type" => "danger", "content" => "Invalid role detected. Logging out.");
            header("location: logout.php");
            break;
    }
    exit(); // Ensure no further code is executed after redirection
} catch (Exception $e) {
    // Log any unexpected errors
    $_SESSION['message'][] = array("type" => "danger", "content" => "Error: " . $e->getMessage());
    header("location: logout.php");
    exit();
}
