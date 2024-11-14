<?php
session_start();
include 'layouts/config.php';
include 'layouts/functions.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $_SESSION['message'] = array("type" => "error", "content" => "Email and Password are required.");
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }

    try {
        // Enable error reporting
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

        // Prepare the SQL statement
        $stmt = mysqli_prepare($conn, "SELECT id, username, password, role_id FROM users WHERE email = ?");
        if ($stmt === false) {
            throw new Exception("Prepare statement failed: " . mysqli_error($conn));
        }

        // Bind parameters
        mysqli_stmt_bind_param($stmt, "s", $email);

        // Execute the statement
        mysqli_stmt_execute($stmt);

        // Bind the result
        mysqli_stmt_bind_result($stmt, $id, $username, $hashed_password, $role);

        // Fetch the result
        if (mysqli_stmt_fetch($stmt)) {
            // Verify the password
            if (password_verify($password, $hashed_password)) {
                // if ($password === $hashed_password) {
                // Store data in session variables
                $_SESSION["loggedin"] = true;
                $_SESSION["user_id"] = $id;
                // $_SESSION["first_name"] = $first_name;
                // $_SESSION["last_name"] = $last_name;
                $_SESSION["username"] = $username;
                $_SESSION["role_id"] = $role;
                $_SESSION["email"] = $email;
                // Check user role and redirect accordingly
                if ($role == 1) {
                    header("Location: index.php");
                } else {

                    // Set success message
                    // $_SESSION['message'][] = ["type" => "success", "content" => "Login successful!"];
                    header("Location: complete-profile.php");
                    exit();
                }
            } else {
                $_SESSION['message'][] = ["type" => "danger", "content" => "Invalid email or password."];
                header("Location: " . $_SERVER['PHP_SELF']);
                exit();
            }
        } else {
            $_SESSION['message'][] = ["type" => "danger", "content" => "Invalid email or password."];
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        }
    } catch (Exception $e) {
        $_SESSION['message'][] = ["type" => "danger", "content" => "Error: " . $e->getMessage()];
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } finally {
        // Close the statement and the connection
        if (isset($stmt)) {
            mysqli_stmt_close($stmt);
        }
        // if (isset($conn)) {
        //     mysqli_close($conn);
        // }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include 'layouts/title-meta.php'; ?>
    <?php include 'layouts/head-css.php'; ?>
    <title>Login - Matrimony</title>
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

        /* welcome section image */
        .welcome-section img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            position: absolute;
            top: 0;
            left: 0;
            opacity: 0.5;
        }

        /* welcome section text */
        .welcome-text {
            position: relative;
            z-index: 1;
            color: #fff;
            text-align: center;
        }

        /* welcome section heading */
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

        /* form section Button */
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

        /* hover form section */
        .form-section button:hover {
            background: linear-gradient(135deg, #E63A7A, #3987cc);
        }

        /* form fields */
        .input-group {
            margin-bottom: 20px;
        }

        /* social icons */
        .social-icons-lg a {
            font-size: 24px;
            color: #E63A7A;
            margin: 0 10px;
        }

        /* social icons hover */
        .social-icons-lg a:hover {
            color: #007bff;
        }

        /**
        google button sign in    */
        .login-with-google-btn {
            /* transition: background-color .3s, box-shadow .3s; */
            text-decoration: none;
            padding: 12px 16px 12px 42px;
            border: none;
            border-radius: 3px;
            box-shadow: 0 -1px 0 rgba(0, 0, 0, .04), 0 1px 1px rgba(0, 0, 0, .25);
            color: #757575;
            background-image: url(data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTgiIGhlaWdodD0iMTgiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGcgZmlsbD0ibm9uZSIgZmlsbC1ydWxlPSJldmVub2RkIj48cGF0aCBkPSJNMTcuNiA5LjJsLS4xLTEuOEg5djMuNGg0LjhDMTMuNiAxMiAxMyAxMyAxMiAxMy42djIuMmgzYTguOCA4LjggMCAwIDAgMi42LTYuNnoiIGZpbGw9IiM0Mjg1RjQiIGZpbGwtcnVsZT0ibm9uemVybyIvPjxwYXRoIGQ9Ik05IDE4YzIuNCAwIDQuNS0uOCA2LTIuMmwtMy0yLjJhNS40IDUuNCAwIDAgMS04LTIuOUgxVjEzYTkgOSAwIDAgMCA4IDV6IiBmaWxsPSIjMzRBODUzIiBmaWxsLXJ1bGU9Im5vbnplcm8iLz48cGF0aCBkPSJNNCAxMC43YTUuNCA1LjQgMCAwIDEgMC0zLjRWNUgxYTkgOSAwIDAgMCAwIDhsMy0yLjN6IiBmaWxsPSIjRkJCQzA1IiBmaWxsLXJ1bGU9Im5vbnplcm8iLz48cGF0aCBkPSJNOSAzLjZjMS4zIDAgMi41LjQgMy40IDEuM0wxNSAyLjNBOSA5IDAgMCAwIDEgNWwzIDIuNGE1LjQgNS40IDAgMCAxIDUtMy43eiIgZmlsbD0iI0VBNDMzNSIgZmlsbC1ydWxlPSJub256ZXJvIi8+PHBhdGggZD0iTTAgMGgxOHYxOEgweiIvPjwvZz48L3N2Zz4=);
            background-color: white;
            background-repeat: no-repeat;
            background-position: 12px 11px;

            /* hover of buttton */
            &:hover {
                box-shadow: 0 -1px 0 rgba(0, 0, 0, .04), 0 2px 4px rgba(0, 0, 0, .25);
            }


        }

        /* responsive for mobile screen */
        @media (max-width: 768px) {

            /* main container */
            .main-container {
                flex-direction: column;
                box-shadow: none;
                width: 100%;
                margin-top: -30px;
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
    <div class="container-fluid d-flex justify-content-center align-items-center mt-1 pt-5 mb-5 ">
        <div class="main-container">
            <!-- Welcome Section -->
            <div class="welcome-section">
                <img src="assets/images/Login page image.webp" alt="Welcome Image">
                <div class="welcome-text">
                    <h2>Welcome Back!</h2>
                    <p>Login to Continue Your Journey</p>
                </div>
            </div>
            <!-- Form Section -->
            <div class="form-section ">
                <?php displaySessionMessage(); ?>
                <form class="login-form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                    <h2 class="text-center mb-4">Members Log In</h2>
                    <div class="input-group mt-5">
                        <input type="email" name="email" class="form-control" placeholder="Your Email Address" required>
                    </div>
                    <div class="input-group">
                        <input type="password" name="password" class="form-control" placeholder="Your Password" required>
                    </div>

                    <!-- <button type="submit" onclick="window.location.href='complete-profile.php';">Continue</button> -->
                    <button type="submit">Continue</button>
                    <div class="text-center mt-4">
                        <p>or</p>
                        <!-- Google Sign-In Button -->
                        <a href="#" class="login-with-google-btn ">
                            Sign in with Google
                        </a>
                        <p class="mt-5">Let Others Know About Soulmate!</p>
                    </div>
                    <!-- Social Icons Section -->
                    <div class="social-icons-lg text-center mt-3">
                        <a href="https://www.facebook.com/soulmatemetrimony/" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
                        <a href="#" aria-label="Twitter"><i class="bi bi-twitter"></i></a>
                        <a href="https://www.instagram.com/soulmatemetrimonypakistan/" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
                        <a href="#" aria-label="LinkedIn"><i class="bi bi-linkedin"></i></a>
                    </div>
                    <p class="text-center mt-3">Don't have an account? <a href="signup.php">Sign Up</a></p>
                </form>
            </div>
        </div>
    </div>

</body>

</html>