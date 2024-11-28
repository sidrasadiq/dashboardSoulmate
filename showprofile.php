<?php
session_start();
include 'layouts/config.php';
include 'layouts/session.php';
include 'layouts/main.php';
include 'layouts/functions.php';

// Check if the connection is established
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Initialize $profile
$profile = [];

if (isset($_SESSION['user_id']) && isset($_GET['id']) && is_numeric($_GET['id'])) {
    $userId = (int)$_SESSION['user_id']; // Logged-in user's ID
    $profileId = (int)$_GET['id'];       // Profile ID passed via URL

    // Check if the logged-in user is viewing their own profile
    if ($userId === $profileId) {
        // Fetch the logged-in user's profile
        $queryCondition = "profiles.user_id = $userId";
    } else {
        // Fetch the specific profile from the URL
        $queryCondition = "profiles.id = $profileId";
    }
} else {
    // Redirect to login page if no user is logged in or invalid 'id'
    $_SESSION['message'] = ['type' => 'error', 'content' => 'User not logged in'];
    header("Location: login.php");
    exit();
}


// Query to fetch profile data
$query = "
    SELECT 
        profiles.*, 
        countries.country_name, 
        cities.city_name,
        states.state_name,
        occupation.occupation_name,
        users.username,
        users.email,
        nationality.nationality_name,
        religion.religion_name,
        qualifications.qualification_name,
        user_cast.cast_name
    FROM 
        profiles
    JOIN users ON profiles.user_id = users.id
    LEFT JOIN countries ON profiles.country_id = countries.id
    LEFT JOIN cities ON profiles.city_id = cities.id
    LEFT JOIN states ON profiles.state_id = states.id
    LEFT JOIN occupation ON profiles.occupation_id = occupation.id
    LEFT JOIN nationality ON profiles.nationality_id = nationality.id
    LEFT JOIN religion ON profiles.religion_id = religion.id
    LEFT JOIN qualifications ON profiles.qualification_id = qualifications.id
    LEFT JOIN user_cast ON profiles.cast_id = user_cast.id
    WHERE $queryCondition"; // Use the determined query condition

// Execute the query
$result = mysqli_query($conn, $query);

if ($result && $result->num_rows > 0) {
    // Fetch the profile data
    $profile = $result->fetch_assoc();
} else {
    // Handle the case when no profile is found
    echo 'No profile found for this user.';
    if (isset($_SESSION['user_id'])) {
        echo ' User ID from session: ' . $_SESSION['user_id'];
    }
    exit();
}

// Display the profile information
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
            color: #4CA8F0 !important;
        }

        .btn-pro {
            background-color: #E9E9E9;
        }

        .btn-pro:hover {
            background-color: #E9E9E9;

        }

        .custom-avatar {
            width: 350px;
            height: 400px;
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
                                    <?php if (!empty($profile['profile_picture'])): ?>
                                        <img src="uploads/<?php echo htmlspecialchars($profile['profile_picture']); ?>" alt="Profile Picture" width="200" class="avatar-lg img-thumbnail custom-avatar"
                                            alt="profile-image">
                                    <?php else: ?>
                                        <p>No profile picture available.</p>
                                    <?php endif; ?>

                                    <!-- <img src="assets/images/profile.jpeg" class=" avatar-lg img-thumbnail" alt="profile-image"> -->
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
                                        <p class="text-muted mb-1"><strong>Gender:</strong> <span class="ms-2"><?php echo htmlspecialchars($profile['gender'] ?? 'N/A'); ?> / <strong>ID: <?php echo htmlspecialchars($profileId ?? 'Not Set'); ?></strong></span></p>
                                        <p class="text-muted mb-1"><strong>Seeking:</strong> <span class="ms-2"><?php echo htmlspecialchars($profile['looking_for'] ?? 'No Answer') . '/' . htmlspecialchars($profile['prefer_age_from'] ?? '') . '-' . htmlspecialchars($profile['prefer_age_to'] ?? '') . ' For: ' . htmlspecialchars($profile['relationship_looking'] ?? ''); ?></span> </p>
                                        <p class="text-muted mb-1"><strong>Last Active:</strong> <span class="ms-2">0 min ago</span></p>
                                    </div>
                                    <hr>

                                    <table class="table table-striped table-hover">
                                        <tr>
                                            <th class="text-muted mb-1">Overview</th>
                                            <th class="text-muted mb-1"><?php echo htmlspecialchars($profile['first_name'] ?? 'N/A'); ?></th>
                                            <th class="text-muted mb-1">
                                                <?php
                                                echo ($profile['gender'] ?? '') === 'female' ? "She's Looking For" : "He's Looking For";
                                                ?>
                                            </th>

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
                                            <td class="text-muted mb-1"><?php echo htmlspecialchars($profile['occupation_name'] ?? 'No Answer'); ?></td>
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
                                            <th class="w-25 customclr text-muted mb-1 text-start">Basic</th>
                                            <th class="w-50 customclr text-muted  mb-1 text-start"><?php echo htmlspecialchars($profile['first_name'] ?? 'N/A'); ?></th>
                                            <th class="w-25 customclr text-muted mb-1 text-start">Looking For</th>
                                        </tr>

                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="w-25 text-muted text-start">Gender:</td>
                                            <td class="w-50 text-muted text-start"><?php echo htmlspecialchars($profile['gender'] ?? 'No Answer'); ?></td>
                                            <td class="w-25 text-muted text-start"><?php echo htmlspecialchars($profile['looking_for'] ?? 'No Answer'); ?></td>
                                        </tr>
                                        <tr>
                                            <td class="w-25 text-muted text-start">Age:</td>
                                            <td class="w-50 text-muted text-start"><?php
                                                                                    echo !empty($profile['date_of_birth'])
                                                                                        ? htmlspecialchars((new DateTime())->diff(new DateTime($profile['date_of_birth']))->y . ' years')
                                                                                        : 'No Answer';
                                                                                    ?></td>
                                            <td class="w-25 text-muted text-start">
                                                <strong><?php echo htmlspecialchars($profile['prefer_age_from'] ?? '') . '-' . htmlspecialchars($profile['prefer_age_to'] ?? ''); ?></strong>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="w-25 text-muted text-start">Bio:</td>
                                            <td class="w-50 text-muted text-start"><?php echo htmlspecialchars($profile['bio']  ?? 'No Answer'); ?></td>
                                            <td class="w-25 text-muted text-start">No Answer</td>
                                        </tr>
                                        <tr>
                                            <td class="w-25 text-muted text-start">Lives In:</td>
                                            <td class="w-50 text-muted text-start"><?php echo htmlspecialchars($profile['city_name'] . "," . $profile['state_name'] . ", " . $profile['country_name'] ?? 'No Answer'); ?></td>
                                            <td class="w-25 text-muted text-start">No Answer</td>
                                        </tr>
                                        <tr>
                                            <td class="w-25 text-muted text-start">Relocate:</td>
                                            <td class="w-50 text-muted text-start">No Answer</td>
                                            <td class="w-25 text-muted text-start">No Answer</td>
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
                                            <td class="w-25 text-muted text-start">Height:</td>
                                            <td class="w-50 text-muted text-start"><?php echo htmlspecialchars($profile['height'] ?? 'No Answer'); ?></td>
                                            <td class="w-25 text-muted text-start">No Answer</td>
                                        </tr>
                                        <tr>
                                            <td class="w-25 text-muted text-start">Weight:</td>
                                            <td class="w-50 text-muted text-start"><?php echo htmlspecialchars($profile['weight'] ?? 'No Answer'); ?></td>
                                            <td class="w-25 text-muted text-start">No Answer</td>
                                        </tr>
                                        <tr>
                                            <td class="w-25 text-muted text-start">Body Style:</td>
                                            <td class="w-50 text-muted text-start"><?php echo htmlspecialchars($profile['body_type'] ?? 'No Answer'); ?></td>
                                            <td class="w-25 text-muted text-start">No Answer</td>
                                        </tr>
                                        <tr>
                                            <td class="w-25 text-muted text-start">Ethnicity:</td>
                                            <td class="w-50 text-muted text-start"><?php echo htmlspecialchars($profile['ethnicity'] ?? 'No Answer'); ?></td>
                                            <td class="w-25 text-muted text-start">No Answer</td>
                                        </tr>
                                        <tr>
                                            <td class="w-25 text-muted text-start">Appearance:</td>
                                            <td class="w-50 text-muted text-start"><?php echo htmlspecialchars($profile['my_appearance'] ?? 'No Answer'); ?></td>
                                            <td class="w-25 text-muted text-start">No Answer</td>
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
                                            <td class="w-25 text-muted text-start">Drink:</td>
                                            <td class="w-50 text-muted text-start"><?php echo htmlspecialchars($profile['drink_alcohol'] ?? 'No Answer'); ?></td>
                                            <td class="w-25 text-muted text-start">No Answer</td>
                                        </tr>
                                        <tr>
                                            <td class="w-25 text-muted text-start">Smoke:</td>
                                            <td class="w-50 text-muted text-start"><?php echo htmlspecialchars($profile['smoking'] ?? 'No Answer'); ?></td>
                                            <td class="w-25 text-muted text-start">No Answer</td>
                                        </tr>
                                        <tr>
                                            <td class="w-25 text-muted text-start">Marital Status:</td>
                                            <td class="w-50 text-muted text-start"><?php echo htmlspecialchars($profile['marital_status'] ?? 'No Answer'); ?></td>
                                            <td class="w-25 text-muted text-start">No Answer</td>
                                        </tr>
                                        <tr>
                                            <td class="w-25 text-muted text-start">Want (more) children:</td>
                                            <td class="w-50 text-muted text-start"><?php echo htmlspecialchars($profile['children'] ?? 'No Answer'); ?></td>
                                            <td class="w-25 text-muted text-start">No Answer</td>
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
                                            <td class="w-25 text-muted text-start">Nationality:</td>
                                            <td class="w-50 text-muted text-start"><?php echo htmlspecialchars($profile['nationality_name'] ?? 'No Answer'); ?></td>
                                            <td class="w-25 text-muted text-start">No Answer</td>
                                        </tr>
                                        <tr>
                                            <td class="w-25 text-muted text-start">Education:</td>
                                            <td class="w-50 text-muted text-start"><?php echo htmlspecialchars($profile['qualification_name'] ?? 'No Answer'); ?></td>
                                            <td class="w-25 text-muted text-start">No Answer</td>
                                        </tr>
                                        <tr>
                                            <td class="w-25 text-muted text-start">Religion:</td>
                                            <td class="w-50 text-muted text-start"><?php echo htmlspecialchars($profile['religion_name'] ?? 'No Answer'); ?></td>
                                            <td class="w-25 text-muted text-start">No Answer</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <h5 class="customclr text-muted mt-5 text-start">Safety Tip - Always meet in a public place for your first date
        </h5>
        <p class=" text-muted mb-1 text-start">If you choose to have a face-to-face meeting with another member, always meet in a public place with many <br> people around. Organize your own transportation to and from the date and never agree to be picked up at <br> your home. Always tell someone in your family or a friend where you are going and when you will return and <br> stay connected with your cell phone switched on at all times.</p>
        <button type="button" class="btn  btn-pro">For more safety tips click here</button>

    </div>
    <?php include 'userlayout/footer.php'; ?>
</body>

</html>