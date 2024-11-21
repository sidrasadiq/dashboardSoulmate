<?php
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Load PHPMailer files
require 'assets/vendor/PHPMailer/src/Exception.php';
require 'assets/vendor/PHPMailer/src/PHPMailer.php';
require 'assets/vendor/PHPMailer/src/SMTP.php';

include 'layouts/config.php';
include 'layouts/functions.php';

function sendVerificationEmail($to, $username, $verificationToken)
{
    global $mailHost, $mailUsername, $mailPassword, $mailPort; // Use global variables from config.php

    // Create PHPMailer instance
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();                          // Use SMTP
        $mail->Host = $mailHost;                  // SMTP server from config.php
        $mail->SMTPAuth = true;                   // Enable SMTP authentication
        $mail->Username = $mailUsername;          // SMTP username from config.php
        $mail->Password = $mailPassword;          // SMTP password from config.php
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // Enable TLS encryption
        $mail->Port = $mailPort;                  // SMTP port from config.php

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
        return "Mailer Error: {$mail->ErrorInfo}"; // Return error message for debugging
    }
}


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["username"])) {
    // Input data from form
    $username = trim($_POST['username']);
    $usergender = $_POST['usergender'];
    $userlooking = $_POST['userlooking'];
    $userdate = $_POST['userdate'];
    $useremail = trim($_POST['useremail']);
    $userpass = password_hash(trim($_POST['userpass']), PASSWORD_BCRYPT); // Secure password hashing
    $createdAt = date("Y-m-d H:i:s");
    $updatedAt = date("Y-m-d H:i:s");
    $role_id = 2; // Default role_id for new users
    $verification_token = bin2hex(random_bytes(16));

    // Check if user has confirmed terms
    if (!isset($_POST['usercheck'])) {
        $_SESSION['message'][] = array("type" => "error", "content" => "You must agree to the terms to sign up.");
        header("location: signup.php");
        exit();
    }

    // Calculate the user's age based on the date of birth
    $birthDate = new DateTime($userdate);
    $today = new DateTime();
    $age = $today->diff($birthDate)->y;

    if ($age < 18) {
        $_SESSION['message'][] = array("type" => "error", "content" => "You must be at least 18 years old to sign up.");
        header("location: signup.php");
        exit();
    }

    try {
        // Start the transaction
        $conn->begin_transaction();

        // Check if email is unique
        $sql_check = "SELECT id FROM users WHERE email = ?";
        $stmt_check = $conn->prepare($sql_check);
        $stmt_check->bind_param("s", $useremail);
        $stmt_check->execute();
        $stmt_check->store_result();
        if ($stmt_check->num_rows > 0) {
            $_SESSION['message'][] = array("type" => "error", "content" => "This email is already registered.");
            header("location: signup.php");
            exit();
        }

        // Insert into the user table with role_id
        $sql_user = "INSERT INTO users (username, email, password, role_id, is_verified, verification_token, created_at, updated_at) 
                     VALUES (?, ?, ?, ?, 0, ?, ?, ?)";
        $stmt_user = $conn->prepare($sql_user);
        if (!$stmt_user) {
            throw new Exception("Failed to prepare user statement: " . $conn->error);
        }

        $stmt_user->bind_param("sssssss", $username, $useremail, $userpass, $role_id, $verification_token, $createdAt, $updatedAt);
        if ($stmt_user->execute()) {
            $user_id = $stmt_user->insert_id;

            // Insert into the profile table using the user_id
            $sql_profile = "INSERT INTO profiles (user_id, gender, looking_for, date_of_birth, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt_profile = $conn->prepare($sql_profile);
            if (!$stmt_profile) {
                throw new Exception("Failed to prepare profile statement: " . $conn->error);
            }

            $stmt_profile->bind_param("isssss", $user_id, $usergender, $userlooking, $userdate, $createdAt, $updatedAt);
            if ($stmt_profile->execute()) {
                // Commit transaction if both inserts were successful
                $conn->commit();

                // Send verification email
                $emailSent = sendVerificationEmail($useremail, $username, $verification_token);
                if ($emailSent !== true) {
                    $_SESSION['message'][] = array("type" => "error", "content" => "Error sending verification email: $emailSent");
                }

                $_SESSION['message'][] = array(
                    "type" => "success",
                    "content" => "Signup successful! Please check your email for verification instructions."
                );
                header("Location: login.php");
                exit();
            } else {
                throw new Exception("Failed to save profile data: " . $stmt_profile->error);
            }
        } else {
            throw new Exception("Failed to save user data: " . $stmt_user->error);
        }
    } catch (Exception $e) {
        // Roll back transaction on error
        $conn->rollback();
        $_SESSION['message'][] = array("type" => "error", "content" => "Error: " . $e->getMessage());
    } finally {
        header("location: signup.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <title>Sign Up - Matrimony</title>
    <style>
        /* main container */
        .main-container {
            max-width: 1000px;
            width: 80%;
            margin: auto;
            display: flex;
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            background-color: #f3f4f6;
        }

        /* welcome section */
        .welcome-section {
            width: 50%;
            position: relative;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            background: linear-gradient(135deg, #8e2de2, #4a00e0);
        }

        /* welcom Section image */
        .welcome-section img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            position: absolute;
            top: 0;
            left: 0;
            opacity: 0.5;
        }

        /* welcome text */
        .welcome-text {
            position: relative;
            z-index: 1;
            color: #fff;
            text-align: center;
        }

        /* welcome heading */
        .welcome-text h2 {
            font-size: 36px;
            margin-bottom: 10px;
        }

        /* welcome section paragraph */
        .welcome-text p {
            font-size: 18px;
        }

        /* form section */
        .form-section {
            width: 50%;
            padding: 40px;
            background-color: #f3f4f6;
        }

        /* form section button */
        .form-section button {
            width: 100%;
            padding: 12px;
            margin-top: 15px;
            background: linear-gradient(135deg, #3987cc, #E63A7A);
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        .form-section button:hover {
            background: linear-gradient(135deg, #E63A7A, #3987cc);
        }

        /* form fields */
        .input-group {
            margin-bottom: 20px;
        }

        /* check button */
        .form-check-label {
            color: #333;
        }

        /* check input */
        .form-check-input {
            width: 18px;
            height: 18px;
        }

        /* responsive for the mobile screen */
        @media (max-width: 768px) {

            /* main container */
            .main-container {
                flex-direction: column;
                box-shadow: none;
                width: 100%;
                margin-top: -70px;
            }

            /* welcome section */

            .welcome-section {
                display: none;
            }

            /* form section */
            .form-section {
                width: 100%;
                padding: 20px;
            }
        }
    </style>
</head>

<body>

    <div class="container-fluid d-flex justify-content-center align-items-center mt-5 pt-5 mb-5">

        <div class="main-container">
            <!-- Welcome Section -->
            <div class="welcome-section">
                <img src="assets/images/Singuppageimage.jpg" alt="Welcome Image">
                <div class="welcome-text">
                    <h2>Join Us</h2>
                    <p>Your Perfect Match is Just a Click Away</p>
                </div>
            </div>
            <!-- Form Section -->


            <div class="form-section  ">
                <?php displaySessionMessage(); ?>
                <form class="signup-form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                    <h2 class="text-center mb-4">Sign Up</h2>
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Full Name" name="username" required>
                    </div>
                    <div class="input-group">
                        <select class="form-select" name="usergender" required>
                            <option value="" disabled selected>I'm a</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                        </select>
                    </div>
                    <div class="input-group">
                        <select class="form-select" name="userlooking" required>
                            <option value="" disabled selected>I'm looking for</option>
                            <option value="male">Groom (Boy)</option>
                            <option value="female">Bride (Girl)</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="input-group">
                        <input type="date" class="form-control" name="userdate" required>

                    </div>
                    <div class="input-group">
                        <input type="email" class="form-control" placeholder="Email Address" name="useremail" required>
                    </div>
                    <div class="input-group">
                        <input type="password" class="form-control" placeholder="Your Soulmate Password" name="userpass" required>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="defaultCheck1" name="usercheck" required>
                        <label class="form-check-label" for="defaultCheck1">
                            Yes, I confirm that I am over 18 and agree to the Terms of Use and Privacy Statement.
                        </label>
                    </div>
                    <button type="submit" class="btn">Submit</button>
                    <p class="text-center mt-3">Already have an account? <a href="login.php">Log In</a></p>
                </form>
            </div>
        </div>
    </div>
</body>

</html>