<?php
session_start();
include 'layouts/config.php';
include 'layouts/session.php';
include 'layouts/main.php';
include 'layouts/functions.php';

// Initialize $profile
$profile = [];

// Check if user is logged in by ensuring session ID is set
if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id']; // Use session ID for the logged-in user

    // Check if the connection is established
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    try {
        // Query to fetch user details with associated information
        $query = "
            SELECT 
                profiles.*, 
                countries.country_name, 
                cities.city_name,
                users.username,
                users.email,
                users.password,
                nationality.nationality_name,
                religion.religion_name,
                qualifications.qualification_name
            FROM 
                profiles
            JOIN users ON profiles.user_id = users.id
            LEFT JOIN countries ON profiles.country_id = countries.id
            LEFT JOIN cities ON profiles.city_id = cities.id
            LEFT JOIN nationality ON profiles.nationality_id = nationality.id
            LEFT JOIN religion ON profiles.religion_id = religion.id
            LEFT JOIN qualifications ON profiles.qualification_id = qualifications.id
            WHERE profiles.user_id = ?;"; // Changed condition to fetch profile based on user_id

        // Prepare and execute the query
        $stmtUser = $conn->prepare($query);
        if (!$stmtUser) {
            die("Query preparation failed: " . $conn->error);
        }

        // Bind the user ID parameter
        $stmtUser->bind_param('i', $userId);
        $stmtUser->execute();
        $resultUser = $stmtUser->get_result();

        // Check if user data exists
        if ($resultUser->num_rows > 0) {
            $profile = $resultUser->fetch_assoc();
        } else {
            $_SESSION['message'] = ['type' => 'error', 'content' => 'Profile not found'];
            header("Location: showprofile.php");
            exit();
        }

        // Close the statement
        $stmtUser->close();
    } catch (Exception $e) {
        // Handle exceptions and set the session message
        $_SESSION['message'] = ['type' => 'error', 'content' => $e->getMessage()];
        header("Location: showprofile.php");
        exit();
    }
} else {
    // If session ID is not set, redirect to login page or show an error
    $_SESSION['message'] = ['type' => 'error', 'content' => 'User not logged in'];
    header("Location: login.php");
    exit();
}
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <title>Show Profile Panel</title>
    <style>
        .customclr {
            color: #4CA8F0;
        }
    </style>
</head>

<body class="bg-light">
    <?php include 'userlayout/header.php'; ?>

    <!-- Start Page Content here -->
    <div class="container">
        <div class="content-page">
            <div class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-xl-4 col-lg-5">
                            <div class="card text-center border-0">
                                <div class="card-body">
                                    <img src="<?php echo htmlspecialchars($profile['profile_picture']); ?>" class="avatar-lg img-thumbnail" alt="profile-image">
                                </div> <!-- end card-body -->
                            </div> <!-- end card -->
                        </div> <!-- end col-->

                        <!-- second section start -->
                        <div class="col-xl-8 col-lg-7">
                            <div class="card">
                                <!-- card Body start  -->
                                <div class="card-body">
                                    <h4 class="mb-1 mt-2"><?php echo htmlspecialchars($profile['first_name'] ?? 'N/A') . ' ' . htmlspecialchars($profile['last_name'] ?? ''); ?></h4>
                                    <p class="text-muted"><?php echo htmlspecialchars($profile['qualification_name'] ?? 'N/A'); ?></p>
                                    <div class="text-start mt-3">
                                        <p class="text-muted mb-2"><strong>Location:</strong> <span class="ms-2"><?php echo htmlspecialchars($profile['city_name'] ?? 'Unknown') . ', ' . htmlspecialchars($profile['country_name'] ?? 'Unknown'); ?></span></p>
                                        <p class="text-muted mb-1"><strong>Gender:</strong> <span class="ms-2"><?php echo htmlspecialchars($profile['gender'] ?? 'N/A'); ?> / <strong>ID: <?php echo htmlspecialchars($userId ?? 'Not Set'); ?></strong></span></p>
                                        <p class="text-muted mb-1"><strong>Seeking:</strong> <span class="ms-2"><?php echo htmlspecialchars($profile['looking_for'] ?? 'Any') . '/' . htmlspecialchars($profile['prefer_age_from'] ?? '') . '-' . htmlspecialchars($profile['prefer_age_to'] ?? '') . ' For: ' . htmlspecialchars($profile['relationship_looking'] ?? ''); ?></span> </p>
                                        <p class="text-muted mb-1"><strong>Last Active:</strong> <span class="ms-2">0 min ago</span></p>
                                    </div>
                                    <hr>

                                    <table class="table table-striped table-hover">
                                        <tr>
                                            <th class="text-muted mb-1">Overview</th>
                                            <th class="text-muted mb-1"><?php echo htmlspecialchars($profile['first_name'] ?? 'N/A'); ?></th>
                                            <th class="text-muted mb-1">She's Looking For</th>
                                        </tr>
                                        <tr>
                                            <td class="text-muted mb-1">Education:</td>
                                            <td class="text-muted mb-1"><?php echo htmlspecialchars($profile['qualification_name'] ?? 'No Answer'); ?></td>
                                            <td class="text-muted mb-1">Any</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted mb-1">Have children:</td>
                                            <td class="text-muted mb-1"><?php echo htmlspecialchars($profile['children'] ?? 'No Answer'); ?></td>
                                            <td class="text-muted mb-1">Any</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted mb-1">Drink:</td>
                                            <td class="text-muted mb-1"><?php echo htmlspecialchars($profile['drink_alcohol'] ?? 'No Answer'); ?></td>
                                            <td class="text-muted mb-1">Any</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted mb-1">Smoke:</td>
                                            <td class="text-muted mb-1"><?php echo htmlspecialchars($profile['smoking'] ?? 'No Answer'); ?></td>
                                            <td class="text-muted mb-1">Any</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted mb-1">Religion:</td>
                                            <td class="text-muted mb-1"><?php echo htmlspecialchars($profile['religion_name'] ?? 'No Answer'); ?></td>
                                            <td class="text-muted mb-1">Any</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted mb-1">Occupation:</td>
                                            <td class="text-muted mb-1">No Answer</td>
                                            <td class="text-muted mb-1">Any</td>
                                        </tr>
                                    </table>

                                </div>
                            </div>
                        </div> <!-- end card body -->
                    </div> <!-- end card -->
                </div> <!-- end col -->
                <br>
                <div class="col-xl-12 mt-5">
                    <div class="card">
                        <!-- Card Body Start -->
                        <div class="card-body">
                            <!-- More About Me Section -->
                            <h4 class="customclr">More About Me</h4>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover mt-5">
                                    <thead>
                                        <tr>

                                            <th class=" customclr text-muted mb-1 ">Basic</th>
                                            <th class="customclr text-muted mb-1 "><?php echo htmlspecialchars($profile['first_name'] ?? 'N/A'); ?></th>
                                            <th class="customclrr text-muted mb-1 "> Looking For</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="text-muted">Gender:</td>
                                            <td class="text-muted"><?php echo htmlspecialchars($profile['gender'] ?? 'No Answer'); ?></td>
                                            <td class="text-muted"><?php echo htmlspecialchars($profile['looking_for'] ?? 'No Answer'); ?></td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Age:</td>
                                            <td class="text-muted"><?php echo htmlspecialchars($profile['date_of_birth'] ?? 'No Answer'); ?></td>
                                            <td class="text-muted">
                                                <strong><?php echo htmlspecialchars($profile['prefer_age_from'] ?? '') . '-' . htmlspecialchars($profile['prefer_age_to'] ?? ''); ?></strong>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Lives In:</td>
                                            <td class="text-muted"><?php echo htmlspecialchars($profile['lives_in'] ?? 'No Answer'); ?></td>
                                            <td class="text-muted">Any</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Relocate:</td>
                                            <td class="text-muted">Any</td>
                                            <td class="text-muted">Any</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Appearance Section -->
                            <h5 class="customclr mt-4">Appearance</h5>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover mt-5">
                                    <thead>

                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="text-muted">Height:</td>
                                            <td class="text-muted"><?php echo htmlspecialchars($profile['height'] ?? 'No Answer'); ?></td>
                                            <td class="text-muted">Any</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Body Style:</td>
                                            <td class="text-muted"><?php echo htmlspecialchars($profile['body_type'] ?? 'No Answer'); ?></td>
                                            <td class="text-muted">Any</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Ethnicity:</td>
                                            <td class="text-muted"><?php echo htmlspecialchars($profile['ethnicity'] ?? 'No Answer'); ?></td>
                                            <td class="text-muted">Any</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Appearance:</td>
                                            <td class="text-muted"><?php echo htmlspecialchars($profile['my_appearance'] ?? 'No Answer'); ?></td>
                                            <td class="text-muted">Any</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Lifestyle Section -->
                            <h5 class="customclr mt-4">Lifestyle</h5>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover mt-5">
                                    <thead>

                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="text-muted">Drink:</td>
                                            <td class="text-muted"><?php echo htmlspecialchars($profile['drink_alcohol'] ?? 'No Answer'); ?></td>
                                            <td class="text-muted">Any</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Smoke:</td>
                                            <td class="text-muted"><?php echo htmlspecialchars($profile['smoke'] ?? 'No Answer'); ?></td>
                                            <td class="text-muted">Any</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Marital Status:</td>
                                            <td class="text-muted"><?php echo htmlspecialchars($profile['marital_status'] ?? 'No Answer'); ?></td>
                                            <td class="text-muted">Any</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Want (more) children:</td>
                                            <td class="text-muted"><?php echo htmlspecialchars($profile['children'] ?? 'No Answer'); ?></td>
                                            <td class="text-muted">Any</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Background / Cultural Values Section -->
                            <h5 class="customclr mt-4">Background / Cultural Values</h5>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover mt-5">
                                    <thead>

                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="text-muted">Nationality:</td>
                                            <td class="text-muted"><?php echo htmlspecialchars($profile['nationality_name'] ?? 'No Answer'); ?></td>
                                            <td class="text-muted">Any</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Education:</td>
                                            <td class="text-muted"><?php echo htmlspecialchars($profile['qualification_name'] ?? 'No Answer'); ?></td>
                                            <td class="text-muted">Any</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Religion:</td>
                                            <td class="text-muted"><?php echo htmlspecialchars($profile['religion_name'] ?? 'No Answer'); ?></td>
                                            <td class="text-muted">Any</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>


            </div>
        </div>
    </div>

    <?php include 'userlayout/footer.php'; ?>

</body>

</html>