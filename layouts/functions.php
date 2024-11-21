<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Load PHPMailer files
require 'assets/vendor/PHPMailer/src/Exception.php';
require 'assets/vendor/PHPMailer/src/PHPMailer.php';
require 'assets/vendor/PHPMailer/src/SMTP.php';

// include 'config.php';
function get_home_url()
{
    // Determine the protocol
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    // Get the host
    $host = $_SERVER['HTTP_HOST'];
    // Get the base directory
    $baseDir = str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);
    // Construct the home URL
    $homeUrl = $protocol . $host . $baseDir;
    echo $homeUrl;
}

function logMessage($message)
{
    error_log($message, 3, 'debug.log'); // Change 'debug.log' to the desired log file path
}
function getUserRole()
{
    global $conn;
    $role_name = '';

    // Ensure the session is started
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    // Check if role_id exists in the session
    if (isset($_SESSION['role_id'])) {
        $role_id = $_SESSION['role_id'];

        // Prepare the SQL query to get the role name based on the role_id
        $query = "SELECT role_name FROM roles WHERE id = ?";
        if ($stmt = $conn->prepare($query)) {
            // Bind the parameter
            $stmt->bind_param("i", $role_id);

            // Execute the query
            $stmt->execute();

            // Bind the result variable
            $stmt->bind_result($role_name);

            // Fetch the result
            if ($stmt->fetch()) {
                $stmt->close();
                return $role_name;  // Return the role name
            } else {
                $stmt->close();
                return "Unknown Role";  // Handle case where no role is found
            }
        } else {
            return "Query Preparation Failed";  // Handle case where statement preparation fails
        }
    } else {
        return "Role not set";  // Handle case where role_id is not in the session
    }
}

function displaySessionMessage()
{
    // Ensure $_SESSION['message'] is an array before accessing it
    if (isset($_SESSION['message']) && is_array($_SESSION['message'])) {
        foreach ($_SESSION['message'] as $index => $message) {
            // Ensure message type and content are valid
            if (is_array($message) && isset($message['type'], $message['content'])) {
                $alertType = htmlspecialchars($message['type']);
                $content = htmlspecialchars($message['content']);

                // Output the Bootstrap alert with a unique class and data attribute for indexing
                echo "<div class='alert alert-{$alertType} alert-dismissible fade show session-alert' role='alert' data-index='{$index}'>";
                echo "{$content}";
                echo "<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>";
                echo "</div>";
            }
        }

        // Unset the session message after displaying it
        unset($_SESSION['message']);

        // Add JavaScript to dismiss each alert after 3 seconds
        echo "<script>
                setTimeout(function() {
                    var alerts = document.querySelectorAll('.session-alert');
                    alerts.forEach(function(alert) {
                        alert.classList.remove('show');
                        alert.classList.add('fade');
                        setTimeout(function() {
                            alert.remove();
                        }, 500); // Wait for the fade effect to finish
                    });
                }, 5000); // 5 seconds
              </script>";
    }
}
// Function to fetch a specific column value from any table based on column name and ID
function rowInfo($conn, $tableName, $columnName, $id)
{
    // Start a transaction
    $conn->begin_transaction();

    try {
        // Use prepared statement to prevent SQL injection
        $query = "SELECT {$columnName} FROM {$tableName} WHERE id = ?";

        // Prepare the SQL query
        $stmt = $conn->prepare($query);

        // Bind the parameter (assuming ID is an integer)
        $stmt->bind_param("i", $id); // Adjust type based on ID datatype

        // Execute the query
        $stmt->execute();

        // Fetch result
        $result = $stmt->get_result();

        // Check if a row was found
        if ($result->num_rows > 0) {
            // Fetch the row as an associative array
            $row = $result->fetch_assoc();
            $value = $row[$columnName]; // Return the specific column value

            // Commit the transaction
            $conn->commit();

            return $value;
        } else {
            // Return null if no row was found
            $conn->commit(); // Commit if no errors occurred
            return null;
        }
    } catch (Exception $e) {
        // Rollback the transaction on error
        $conn->rollback();

        // Handle the error (optional: log it or rethrow)
        $_SESSION['message'][] = ["type" => "danger", "content" => "Error fetching data: " . $e->getMessage()];

        return null; // Or handle error appropriately
    } finally {
        // Close the statement
        if (isset($stmt) && $stmt) {
            $stmt->close();
        }
    }
}

function sendVerificationEmail($mailHost, $mailUsername, $mailPassword, $mailPort, $to, $username, $verificationToken)
{

    // Create PHPMailer instance
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();                          // Use SMTP
        $mail->Host = $mailHost;                  // SMTP server
        $mail->SMTPAuth = true;                   // Enable SMTP authentication
        $mail->Username = $mailUsername;          // SMTP username
        $mail->Password = $mailPassword;          // SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // Enable TLS encryption
        $mail->Port = $mailPort;                  // SMTP port

        // Email settings
        $mail->setFrom($mailUsername, 'Soulmate'); // Sender email and name
        $mail->addAddress($to);                   // Recipient email

        $mail->isHTML(true);                      // Set email format to HTML
        $mail->Subject = 'Email Verification - Soulmate';

        // Generate email body
        $verificationLink = "https://soulmate.com.pk/verify.php?token=$verificationToken";
        $mail->Body = "
            <h1>Welcome to Soulmate, $username!</h1>
            <p>Thank you for signing up. Please verify your email address to activate your account.</p>
            <p>Click the link below to verify your email:</p>
            <p><a href='$verificationLink' style='color: #007BFF; text-decoration: none;'>Verify Email</a></p>
            <p>If you did not sign up for Soulmate, please ignore this email.</p>
        ";

        // Optional plain text alternative
        $mail->AltBody = "Welcome to Soulmate, $username!\n\nPlease verify your email address to activate your account:\n$verificationLink\n\nIf you did not sign up for Soulmate, please ignore this email.";

        // Send email
        $mail->send();
        return true; // Email sent successfully
    } catch (Exception $e) {
        return "Mailer Error: {$mail->ErrorInfo}";
    }
}
