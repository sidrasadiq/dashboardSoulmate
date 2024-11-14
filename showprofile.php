<?php
session_start();
include 'layouts/config.php';
include 'layouts/session.php';
include 'layouts/main.php';
include 'layouts/functions.php';

// Retrieve user_id from the session
$userId = $_SESSION['user_id'] ?? null;

// Check if the user is logged in and the user ID in the URL matches the logged-in user's ID
if (!$userId || !isset($_GET['id']) || intval($_GET['id']) !== intval($userId)) {
    // Redirect if user_id is not set in session or does not match the URL
    header("Location: user_index.php?user_id=$userId");
    exit();
}

$user_id = intval($_GET['id']); // Sanitize user input

try {
    // Query to fetch the user details along with role and other associated data
    $queryUser = "
        SELECT 
            profiles.*, 
            roles.role_name,
            countries.country_name, 
            cities.city_name,
            casts.cast_name, 
            nationalities.nationality_name,
            religions.religion_name,
            qualifications.qualification_name
        FROM 
            profiles
        JOIN roles ON profiles.role_id = roles.id
        LEFT JOIN countries ON profiles.country_id = countries.id
        LEFT JOIN cities ON profiles.city_id = cities.id
        LEFT JOIN casts ON profiles.cast_id = casts.id
        LEFT JOIN nationalities ON profiles.nationality_id = nationalities.id
        LEFT JOIN religions ON profiles.religion_id = religions.id
        LEFT JOIN qualifications ON profiles.qualification_id = qualifications.id
        WHERE profiles.id = ?;
    ";

    // Prepare and execute the user query
    $stmtUser = $conn->prepare($queryUser);
    $stmtUser->bind_param('i', $user_id);
    $stmtUser->execute();
    $resultUser = $stmtUser->get_result();

    if ($resultUser->num_rows > 0) {
        $profile = $resultUser->fetch_assoc();
    } else {
        $_SESSION['message'] = ['type' => 'error', 'content' => 'Profile not found'];
        header("Location: showprofile.php");
        exit();
    }
    $stmtUser->close(); // Close user statement
} catch (Exception $e) {
    // Handle exception and set the session message
    $_SESSION['message'] = ['type' => 'error', 'content' => $e->getMessage()];
    header("Location: showprofile.php");
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
</head>

<body class="bg-light">
    <?php include 'layouts/user_pannel/header.php'; ?>

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

                        <div class="col-xl-8 col-lg-7">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="mb-1 mt-2"><?php echo htmlspecialchars($profile['first_name'] . ' ' . $profile['last_name']); ?></h4>
                                    <p class="text-muted"><?php echo htmlspecialchars($profile['qualification_name']); ?></p>
                                    <div class="text-start mt-3">
                                        <p class="text-muted mb-2"><strong>Location:</strong> <span class="ms-2"><?php echo htmlspecialchars($profile['city_name']) . ', ' . htmlspecialchars($profile['country_name']); ?></span></p>
                                        <p class="text-muted mb-1"><strong>Gender: </strong><span class="ms-2"><?php echo htmlspecialchars($profile['gender']); ?> / <strong>ID: <?php echo htmlspecialchars($user_id); ?></strong></span></p>
                                        <p class="text-muted mb-1"><strong>Seeking:</strong> <span class="ms-2"><?php echo htmlspecialchars($profile['gender'] === 'Male' ? 'Female' : 'Male'); ?> 24-33 for Marriage</span></p>
                                        <p class="text-muted mb-1"><strong>Last Active:</strong> <span class="ms-2">0 min ago</span></p>
                                    </div>
                                    <hr>

                                    <table class="table table-striped table-hover">
                                        <tr>
                                            <th class="text-muted mb-1">Overview</th>
                                            <th class="text-muted mb-1"><?php echo htmlspecialchars($profile['first_name']); ?></th>
                                            <th class="text-muted mb-1">She's Looking For</th>
                                        </tr>
                                        <tr>
                                            <td class="text-muted mb-1">Education:</td>
                                            <td class="text-muted mb-1"><?php echo htmlspecialchars($profile['qualification_name'] ?: 'No Answer'); ?></td>
                                            <td class="text-muted mb-1">Any</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted mb-1">Have children:</td>
                                            <td class="text-muted mb-1"><?php echo htmlspecialchars($profile['children'] ?: 'No Answer'); ?></td>
                                            <td class="text-muted mb-1">Any</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted mb-1">Drink:</td>
                                            <td class="text-muted mb-1"><?php echo htmlspecialchars($profile['drink_alcohol'] ?: 'No Answer'); ?></td>
                                            <td class="text-muted mb-1">Any</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted mb-1">Smoke:</td>
                                            <td class="text-muted mb-1"><?php echo htmlspecialchars($profile['smoking'] ?: 'No Answer'); ?></td>
                                            <td class="text-muted mb-1">Any</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted mb-1">Religion:</td>
                                            <td class="text-muted mb-1"><?php echo htmlspecialchars($profile['religion_name'] ?: 'No Answer'); ?></td>
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
            </div>
        </div>
    </div>

    <?php include 'layouts/user_pannel/footer.php'; ?>

</body>

</html>