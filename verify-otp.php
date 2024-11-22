<?php
session_start();

include 'layouts/config.php';
include 'layouts/functions.php';

// Check if the email exists in the session
if (!isset($_SESSION['email'])) {
    $_SESSION['message'][] = array("type" => "error", "content" => "Session expired. Please sign up again.");
    header("Location: signup.php");
    exit();
}

$email = $_SESSION['email']; // Get email from session

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["otp"])) {
    // Retrieve the OTP entered by the user
    $otp = trim($_POST['otp']);

    try {
        // Check if the OTP and email match in the database
        $sql = "SELECT id FROM users WHERE email = ? AND otp = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $email, $otp);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            // If a match is found, mark the user as verified
            $update_sql = "UPDATE users SET is_verified = 1, otp = NULL WHERE email = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("s", $email);
            if ($update_stmt->execute()) {
                $_SESSION['message'][] = array(
                    "type" => "success",
                    "content" => "Your account has been successfully verified! You can now log in."
                );
                unset($_SESSION['email']); // Clear the email from the session
                header("Location: login.php");
                exit();
            } else {
                throw new Exception("Failed to update user verification status.");
            }
        } else {
            // Invalid OTP
            $_SESSION['message'][] = array("type" => "error", "content" => "Invalid OTP.");
        }
    } catch (Exception $e) {
        $_SESSION['message'][] = array("type" => "error", "content" => "Error: " . $e->getMessage());
    }

    header("Location: verify-otp.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <title>Verify OTP - Matrimony</title>
    <style>
        .otp-container {
            max-width: 500px;
            margin: 50px auto;
            background-color: #f3f4f6;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
        }

        .otp-container h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }

        .otp-container button {
            width: 100%;
            padding: 12px;
            margin-top: 10px;
            background: linear-gradient(135deg, #3987cc, #E63A7A);
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        .otp-container button:hover {
            background: linear-gradient(135deg, #E63A7A, #3987cc);
        }

        .otp-container .form-group {
            margin-bottom: 20px;
        }

        .otp-container .form-control {
            height: 50px;
            font-size: 16px;
        }

        .otp-container p {
            text-align: center;
            margin-top: 15px;
            color: #555;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="otp-container">
            <?php displaySessionMessage(); ?>
            <h2>Verify OTP</h2>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                <div class="form-group">
                    <input type="number" class="form-control" placeholder="Enter OTP" name="otp" required>
                </div>
                <button type="submit" class="btn">Verify OTP</button>
            </form>
            <p>Didn't receive the OTP? <a href="resend-otp.php">Resend OTP</a></p>
        </div>
    </div>
</body>

</html>