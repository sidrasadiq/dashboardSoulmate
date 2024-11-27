<?php
include 'userlayout/header.php';

session_start();
include 'layouts/config.php';
include 'layouts/session.php';
include 'layouts/main.php';
include 'layouts/functions.php';

// Check if user is logged in
if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];

    // Check if the connection is established
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    try {
        // Query to fetch user details
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
            WHERE profiles.user_id = ?";

        $stmtUser = $conn->prepare($query);
        if (!$stmtUser) {
            throw new Exception("Query preparation failed: " . $conn->error);
        }

        $stmtUser->bind_param('i', $userId);
        $stmtUser->execute();
        $resultUser = $stmtUser->get_result();

        if ($resultUser->num_rows > 0) {
            $profile = $resultUser->fetch_assoc();
        } else {
            $_SESSION['message'] = ['type' => 'error', 'content' => 'Profile not found'];
            header("Location: showprofile.php");
            exit();
        }

        $stmtUser->close();
    } catch (Exception $e) {
        $_SESSION['message'] = ['type' => 'error', 'content' => 'Error: ' . $e->getMessage()];
        header("Location: showprofile.php");
        exit();
    }
} else {
    $_SESSION['message'] = ['type' => 'error', 'content' => 'User not logged in'];
    header("Location: login.php");
    exit();
}

// Fetch data for dropdowns
$countries = $cities = $states = $nationalities = $religions = $qualifications = $occupations = $casts = [];

try {
    // Fetch countries
    $queryCountries = "SELECT id, country_name FROM countries ORDER BY id ASC";
    $stmtCountries = $conn->prepare($queryCountries);
    $stmtCountries->execute();
    $resultCountries = $stmtCountries->get_result();
    while ($row = $resultCountries->fetch_assoc()) {
        $countries[] = $row;
    }

    // Fetch cities
    $queryCities = "SELECT id, city_name FROM cities ORDER BY id ASC";
    $stmtCities = $conn->prepare($queryCities);
    $stmtCities->execute();
    $resultCities = $stmtCities->get_result();
    while ($row = $resultCities->fetch_assoc()) {
        $cities[] = $row;
    }

    // Fetch states
    $queryStates = "SELECT id, state_name FROM states ORDER BY id ASC";
    $stmtStates = $conn->prepare($queryStates);
    $stmtStates->execute();
    $resultStates = $stmtStates->get_result();
    while ($row = $resultStates->fetch_assoc()) {
        $states[] = $row;
    }

    // Fetch nationalities
    $queryNationalities = "SELECT id, nationality_name FROM nationality ORDER BY id ASC";
    $stmtNationalities = $conn->prepare($queryNationalities);
    $stmtNationalities->execute();
    $resultNationalities = $stmtNationalities->get_result();
    while ($row = $resultNationalities->fetch_assoc()) {
        $nationalities[] = $row;
    }

    // Fetch religions
    $queryReligions = "SELECT id, religion_name FROM religion ORDER BY id ASC";
    $stmtReligions = $conn->prepare($queryReligions);
    $stmtReligions->execute();
    $resultReligions = $stmtReligions->get_result();
    while ($row = $resultReligions->fetch_assoc()) {
        $religions[] = $row;
    }

    // Fetch qualifications
    $queryQualifications = "SELECT id, qualification_name FROM qualifications ORDER BY id ASC";
    $stmtQualifications = $conn->prepare($queryQualifications);
    $stmtQualifications->execute();
    $resultQualifications = $stmtQualifications->get_result();
    while ($row = $resultQualifications->fetch_assoc()) {
        $qualifications[] = $row;
    }

    // Fetch occupations
    $queryOccupations = "SELECT id, occupation_name FROM occupation ORDER BY id ASC";
    $stmtOccupations = $conn->prepare($queryOccupations);
    $stmtOccupations->execute();
    $resultOccupations = $stmtOccupations->get_result();
    while ($row = $resultOccupations->fetch_assoc()) {
        $occupations[] = $row;
    }

    // Fetch casts
    $queryCasts = "SELECT id, cast_name FROM user_cast ORDER BY id ASC";
    $stmtCasts = $conn->prepare($queryCasts);
    $stmtCasts->execute();
    $resultCasts = $stmtCasts->get_result();
    while ($row = $resultCasts->fetch_assoc()) {
        $casts[] = $row;
    }

    // Close all statements
    $stmtCountries->close();
    $stmtCities->close();
    $stmtStates->close();
    $stmtNationalities->close();
    $stmtReligions->close();
    $stmtQualifications->close();
    $stmtOccupations->close();
    $stmtCasts->close();
} catch (Exception $e) {
    $_SESSION['message'] = ['type' => 'error', 'content' => 'Error fetching dropdown data: ' . $e->getMessage()];
    header("Location: editprofile.php");
    exit();
}

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['btnUpdateProfile'])) {
    // Process form submission logic
    $firstName = isset($_POST['firstName']) ? intval($_POST['firstName']) : '';
    $lastName = isset($_POST['lastName']) ? intval($_POST['lastName']) : '';
    $userName = isset($_POST['userName']) ? intval($_POST['userName']) : '';
    $dob = isset($_POST['dob']) ? intval($_POST['dob']) : '';
    $gender = isset($_POST['gender']) ? intval($_POST['gender']) : '';
    $contactNum = isset($_POST['contactNum']) ? intval($_POST['contactNum']) : '';
    $WhatsNum = isset($_POST['WhatsNum']) ? intval($_POST['WhatsNum']) : '';
    $cnicNum = isset($_POST['cnicNum']) ? intval($_POST['cnicNum']) : '';
    $cast = isset($_POST['cast']) ? intval($_POST['cast']) : '';
    $nationality = isset($_POST['nationality']) ? intval($_POST['nationality']) : '';
    $religion = isset($_POST['religion']) ? intval($_POST['religion']) : '';
    $qualification = isset($_POST['qualification']) ? intval($_POST['qualification']) : '';
    $interests = isset($_POST['interests']) ? intval($_POST['interests']) : '';
    $country = isset($_POST['country']) ? intval($_POST['country']) : '';
    $state = isset($_POST['state']) ? intval($_POST['state']) : '';
    $city = isset($_POST['city']) ? intval($_POST['city']) : '';
    $userProfilePic = isset($_POST['userProfilePic']) ? intval($_POST['userProfilePic']) : '';
    $occupation = isset($_POST['occupation']) ? intval($_POST['occupation']) : '';
    $BioDetails = isset($_POST['BioDetails']) ? intval($_POST['BioDetails']) : '';
    $bodyType = isset($_POST['bodyType']) ? intval($_POST['bodyType']) : '';
    $ethnicity = isset($_POST['ethnicity']) ? intval($_POST['ethnicity']) : '';
    $appearance = isset($_POST['appearance']) ? intval($_POST['appearance']) : '';
    $height = isset($_POST['height']) ? intval($_POST['height']) : '';
    $weight = isset($_POST['weight']) ? intval($_POST['weight']) : '';
    $drinkAlcohol = isset($_POST['drinkAlcohol']) ? intval($_POST['drinkAlcohol']) : '';
    $smoking = isset($_POST['smoking']) ? intval($_POST['smoking']) : '';
    $maritalStatus = isset($_POST['maritalStatus']) ? intval($_POST['maritalStatus']) : '';
    $children = isset($_POST['children']) ? intval($_POST['children']) : '';
    $relationshipLooking = isset($_POST['relationshipLooking']) ? intval($_POST['relationshipLooking']) : '';
    $profileId = isset($_GET['id']) ? intval($_GET['id']) : ''; // Profile ID
    try {
        $conn->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);

        // Prepare and execute the update query here
        // ... (update logic is untouched for brevity)
        $query = "
UPDATE profiles
SET
first_name = ?, last_name = ?, user_id = ?, date_of_birth = ?, gender = ?,
contact_number = ?, whatsapp_contact = ?, cnic= ?, cast_id = ?,
nationality_id = ?, religion_id = ?, qualification_id = ?, interests = ?,
country_id = ?, state_id = ?, city_id = ?, profile_picture = ?, occupation_id = ?,
bio = ?, body_type = ?, ethnicity = ?, my_appearance = ?, height = ?,
weight = ?, drink_alcohol = ?, smoking = ?, marital_status = ?, children = ?,
relationship_looking = ?
WHERE id = ? AND user_id = ?";


        $stmt = $conn->prepare($query);

        if ($stmt === false) {
            die('MySQL prepare error: ' . $conn->error);
        }

        // Bind parameters and execute statement
        $stmt->bind_param(
            "ssissssssssssssssssssssssssiiii",
            $firstName,
            $lastName,
            $userName,
            $dob,
            $gender,
            $contactNum,
            $WhatsNum,
            $cnicNum,
            $cast,
            $nationality,
            $religion,
            $qualification,
            $interests,
            $country,
            $state,
            $city,
            $userProfilePic,
            $occupation,
            $BioDetails,
            $bodyType,
            $ethnicity,
            $appearance,
            $height,
            $weight,
            $drinkAlcohol,
            $smoking,
            $maritalStatus,
            $children,
            $relationshipLooking,
            $profileId,
            $userId
        );

        if (!$stmt->execute()) {
            throw new Exception('Execute statement failed: ' . $stmt->error);
        }

        // Commit transaction
        $conn->commit();

        $_SESSION['message'] = ['type' => 'success', 'content' => 'Profile updated successfully.'];
        header("Location: showprofile.php");
        exit();
    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['message'] = ['type' => 'error', 'content' => 'Error updating profile: ' . $e->getMessage()];
        header("Location: editprofile.php");
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <title>Edit Profile | Soulmate </title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        /* Highlight the radio button on focus and when checked */
        .highlight-radio:focus,
        .highlight-radio:checked {
            box-shadow: 0 0 0 0.25rem rgba(0, 123, 255, 0.25);
            /* Bootstrap focus box-shadow */
            border-color: #F5367B !important;
            /* Change border color on selection */
        }

        /* Custom color for checked radio buttons */
        .highlight-radio:checked {
            background-color: #F5367B !important;
            /* Highlight color when checked */
            border-color: #F5367B !important;
        }

        .form-check {
            justify-content: space-between;
            padding: 20px 40px;
        }

        .headCustom {
            color: #4CA8F0 !important;
        }

        .container {
            margin-left: 13px;
        }
    </style>

</head>

<body class="bg-light w-100">
    <div class="container">
        <h3 class=" text-muted mt-5">Edit Profile</h3>
        <div class="max-width-3">
            Answering these profile questions will help other users find you in search results and help us to find you <br> more accurate matches.
            <em>Answer all questions below to complete this step.</em>

        </div>
        <br>
        <h4 class="headCustom ">Your Basics</h4>
        <hr>
        <!-- row start -->
        <div class="row ">
            <div class="col-12">

                <div class="card bg-light border-0">
                    <div class="card-body">
                        <p class="text-muted fs-14"> </p>
                        <div class="row">
                            <div>
                                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="needs-validation" novalidate enctype="multipart/form-data">
                                    <input type="hidden" name="userId" value="<?php echo htmlspecialchars($userId); ?>">

                                    <div class="row mb-3">


                                        <!-- First Name Input -->
                                        <div class="col-lg-4">
                                            <div class="mb-3">
                                                <label for="firstName" class="form-label text-muted">First Name:</label>
                                                <input type="text" value=" <?php echo htmlspecialchars($profile['first_name']); ?>" id="firstName" name="firstName" class="form-control" required placeholder="Enter  Your First Name">
                                                <div class="valid-feedback">Looks good!</div>
                                                <div class="invalid-feedback">Please fill this field.</div>
                                            </div>
                                        </div>
                                        <!-- last Name Input -->
                                        <div class="col-lg-4">
                                            <div class="mb-3">
                                                <label for="lastName" class="form-label text-muted">Last Name:</label>
                                                <input type="text" value=" <?php echo htmlspecialchars($profile['last_name']); ?>" id="lastName" name="lastName" class="form-control" required placeholder="Enter  Your Last Name">
                                                <div class="valid-feedback">Looks good!</div>
                                                <div class="invalid-feedback">Please fill this field.</div>
                                            </div>
                                        </div>
                                        <!-- User Name Input -->
                                        <div class="col-lg-4">
                                            <div class="mb-3">
                                                <label for="userName" class="form-label text-muted">User Name:</label>
                                                <input type="text" value=" <?php echo htmlspecialchars($profile['username']); ?>" id="userName" name="userName" class="form-control" required placeholder="Enter  Your User Name">
                                                <div class="valid-feedback">Looks good!</div>
                                                <div class="invalid-feedback">Please fill this field.</div>
                                            </div>
                                        </div>
                                        <!-- Date Input -->
                                        <div class="col-lg-4">
                                            <div class="mb-3">
                                                <label for="dob" class="form-label text-muted">Date Of Birth:</label>
                                                <input type="date"
                                                    value="<?php echo isset($profile['date_of_birth']) ? htmlspecialchars(date('Y-m-d', strtotime($profile['date_of_birth']))) : ''; ?>"
                                                    id="dob"
                                                    name="dob"
                                                    class="form-control text-muted"
                                                    required disabled>
                                                <div class="valid-feedback">Looks good!</div>
                                                <div class="invalid-feedback">Please fill this field.</div>
                                            </div>
                                        </div>

                                        <!-- Gender Dropdown -->
                                        <div class="col-lg-4">
                                            <div class="mb-3">
                                                <label for="gender" class="form-label text-muted">I'm a:</label>
                                                <input type="text" value=" <?php echo htmlspecialchars($profile['gender']); ?>" id="gender" name="gender" class="form-control" required disabled>
                                                <div class="valid-feedback">Looks good!</div>
                                                <div class="invalid-feedback">Please select a project.</div>
                                            </div>
                                        </div>

                                        <!-- Contact Number -->
                                        <div class="col-lg-4">
                                            <div class="mb-3">
                                                <label for="contactNum" class="form-label text-muted">Contact Number:</label>
                                                <input type="number" value="<?php echo htmlspecialchars($profile['contact_number']); ?>" id="contactNum" name="contactNum" class="form-control" required placeholder="Enter Your Contact Number">
                                                <div class="valid-feedback">Looks good!</div>
                                                <div class="invalid-feedback">Please fill this field.</div>
                                            </div>
                                        </div>

                                        <!-- WhatsApp Number -->
                                        <div class="col-lg-4">
                                            <div class="mb-3">
                                                <label for="WhatsNum" class="form-label text-muted">WhatsApp Number:</label>
                                                <input type="number" value="<?php echo htmlspecialchars($profile['whatsapp_contact']); ?>" id="WhatsNum" name="WhatsNum" class="form-control" required placeholder="Enter  Your  WhatsApp Number">
                                                <div class="valid-feedback">Looks good!</div>
                                                <div class="invalid-feedback">Please fill this field.</div>
                                            </div>
                                        </div>
                                        <!-- CNIC Number -->
                                        <div class="col-lg-4">
                                            <div class="mb-3">
                                                <label for="cnicNum" class="form-label text-muted">CNIC Number:</label>
                                                <input type="number" value="<?php echo htmlspecialchars($profile['cnic']); ?>" id="cnicNum" name="cnicNum" class="form-control" required placeholder="Enter  Your  CNIC Number">
                                                <div class="valid-feedback">Looks good!</div>
                                                <div class="invalid-feedback">Please fill this field.</div>
                                            </div>
                                        </div>

                                        <!-- Cast Dropdown -->
                                        <div class="col-lg-4">
                                            <div class="mb-3">
                                                <label for="cast" class="form-label text-muted">Cast:</label>
                                                <select id="cast" name="cast" class="form-select text-muted" required>
                                                    <option selected disabled value="">Select Cast</option>
                                                    <?php foreach ($casts as $cast): ?>
                                                        <option value="<?php echo htmlspecialchars($cast['id']); ?>"
                                                            <?php echo (isset($profile['cast_id']) && $cast['id'] == $profile['cast_id']) ? 'selected' : ''; ?>>
                                                            <?php echo htmlspecialchars($cast['cast_name']); ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <div class="valid-feedback">Looks good!</div>
                                                <div class="invalid-feedback">Please select a cast.</div>
                                            </div>
                                        </div>



                                        <!-- Nationality Dropdown -->
                                        <div class="col-lg-4">
                                            <div class="mb-3">
                                                <label for="nationality" class="form-label text-muted">Nationality:</label>
                                                <select id="nationality" name="nationality" class="form-select text-muted" required>
                                                    <?php foreach ($nationalities as $nationality): ?>
                                                        <option value="<?php echo htmlspecialchars($nationality['id']); ?>"
                                                            <?php echo (isset($profile['nationality_id']) && $nationality['id'] == $profile['nationality_id']) ? 'selected' : ''; ?>>
                                                            <?php echo htmlspecialchars($nationality['nationality_name']); ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <div class="valid-feedback">Looks good!</div>
                                                <div class="invalid-feedback">Please select a project.</div>
                                            </div>
                                        </div>
                                        <!-- Religion Dropdown -->
                                        <div class="col-lg-4">
                                            <div class="mb-3">
                                                <label for="religion" class="form-label text-muted">Religion:</label>
                                                <select id="religion" name="religion" class="form-select text-muted" required>
                                                    <?php foreach ($religions as $religion): ?>
                                                        <option value="<?php echo htmlspecialchars($religion['id']); ?>"
                                                            <?php echo (isset($profile['religion_id']) && $religion['id'] == $profile['religion_id']) ? 'selected' : ''; ?>>
                                                            <?php echo htmlspecialchars($religion['religion_name']); ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <div class="valid-feedback">Looks good!</div>
                                                <div class="invalid-feedback">Please select a project.</div>
                                            </div>
                                        </div>
                                        <!-- qualification Dropdown -->
                                        <div class="col-lg-4">
                                            <div class="mb-3">
                                                <label for="qualification" class="form-label text-muted">Qualification:</label>
                                                <select id="qualification" name="qualification" class="form-select text-muted" required>
                                                    <option selected disabled value="">Select Qualification</option>
                                                    <?php foreach ($qualifications as $qualification): ?>
                                                        <option value="<?php echo htmlspecialchars($qualification['id']); ?>"
                                                            <?php echo (isset($profile['qualification_id']) && $qualification['id'] == $profile['qualification_id']) ? 'selected' : ''; ?>>
                                                            <?php echo htmlspecialchars($qualification['qualification_name']); ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <div class="valid-feedback">Looks good!</div>
                                                <div class="invalid-feedback">Please select a project.</div>
                                            </div>
                                        </div>
                                        <!-- interests -->
                                        <div class="col-lg-4">
                                            <div class="mb-3">
                                                <label for="interests" class="form-label text-muted">Interests:</label>
                                                <input type="text" value=" <?php echo htmlspecialchars($profile['interests']); ?>" id="interests" name="interests" class="form-control" required placeholder="Enter  Your Interests">
                                                <div class="valid-feedback">Looks good!</div>
                                                <div class="invalid-feedback">Please select a project.</div>
                                            </div>
                                        </div>
                                        <!-- Country Dropdown -->
                                        <div class="col-lg-4">
                                            <div class="mb-3">
                                                <label for="country" class="form-label text-muted">Country:</label>
                                                <select id="country" name="country" class="form-select text-muted" required>
                                                    <option selected disabled value="">Select Country</option>
                                                    <?php foreach ($countries as $country): ?>
                                                        <option value="<?php echo htmlspecialchars($country['id']); ?>"
                                                            <?php echo (isset($profile['country_id']) && $country['id'] == $profile['country_id']) ? 'selected' : ''; ?>>
                                                            <?php echo htmlspecialchars($country['country_name']); ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <div class="valid-feedback">Looks good!</div>
                                                <div class="invalid-feedback">Please select a project.</div>
                                            </div>
                                        </div>
                                        <!-- State Dropdown -->
                                        <div class="col-lg-4">
                                            <div class="mb-3">
                                                <label for="state" class="form-label text-muted">State:</label>
                                                <select id="state" name="state" class="form-select text-muted" required>
                                                    <option selected disabled value="">Select State</option>
                                                    <?php foreach ($states as $state): ?>
                                                        <option value="<?php echo htmlspecialchars($state['id']); ?>"
                                                            <?php echo (isset($profile['state_id']) && $state['id'] == $profile['state_id']) ? 'selected' : ''; ?>>
                                                            <?php echo htmlspecialchars($state['state_name']); ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <div class="valid-feedback">Looks good!</div>
                                                <div class="invalid-feedback">Please select a project.</div>
                                            </div>
                                        </div>
                                        <!-- City Dropdown -->
                                        <div class="col-lg-4">
                                            <div class="mb-3">
                                                <label for="city" class="form-label text-muted">City:</label>
                                                <select id="city" name="city" class="form-select text-muted" required>
                                                    <option selected disabled value="">Select City</option>
                                                    <?php foreach ($cities as $city): ?>
                                                        <option value="<?php echo htmlspecialchars($city['id']); ?>"
                                                            <?php echo (isset($profile['city_id']) && $city['id'] == $profile['city_id']) ? 'selected' : ''; ?>>
                                                            <?php echo htmlspecialchars($city['city_name']); ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <div class="valid-feedback">Looks good!</div>
                                                <div class="invalid-feedback">Please select a project.</div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="mb-2">
                                                <label for="userProfilePic" class="form-label text-muted">Profile Picture *</label>
                                                <input type="file" id="userProfilePic" name="userProfilePic" class="form-control" accept="image/*" onchange="displayImage(this)" required>

                                                <?php
                                                // Handle default image if the profile_picture field is empty or null
                                                $profilePicturePath = !empty($profile['profile_picture']) ? htmlspecialchars($profile['profile_picture']) : 'path/to/default-profile.png';
                                                ?>

                                                <img id="profilePicPreview"
                                                    src="uploads/<?php echo $profilePicturePath; ?>"
                                                    alt="Profile Picture"
                                                    class="img-thumbnail mt-2"
                                                    style="max-width: 150px; width: 100%;">

                                                <div class="valid-feedback">Looks good!</div>
                                                <div class="invalid-feedback" id="imageError">Please upload a profile picture.</div>
                                            </div>
                                        </div>

                                        <!-- preferences -->
                                        <div class="col-lg-4">
                                            <div class="mb-2">
                                                <label for="preferences" class="form-label text-muted">Preferences</label>
                                                <input type="text" value=" <?php echo htmlspecialchars($profile['preferences']); ?>" id="preferences" name="preferences" class="form-control">
                                                <div class="valid-feedback">Looks good!</div>
                                                <div class="invalid-feedback" id="imageError">Please fill this field.</div>
                                            </div>
                                        </div>
                                        <!-- Occupation Dropdown -->
                                        <div class="col-lg-4">
                                            <div class="mb-3">
                                                <label for="occupation" class="form-label text-muted">Occupation:</label>
                                                <select id="occupation" name="occupation" class="form-select text-muted" required>
                                                    <option selected disabled value="">Select Occupation</option>
                                                    <?php foreach ($occupations as $occupation): ?>
                                                        <option value="<?php echo htmlspecialchars($occupation['id']); ?>"
                                                            <?php echo (isset($profile['occupation_id']) && $occupation['id'] == $profile['occupation_id']) ? 'selected' : ''; ?>>
                                                            <?php echo htmlspecialchars($occupation['occupation_name']); ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <div class="valid-feedback">Looks good!</div>
                                                <div class="invalid-feedback">Please select a project.</div>
                                            </div>
                                        </div>
                                        <!-- Bio Details Input -->
                                        <div class="col-lg-12">
                                            <div class="mb-3">
                                                <textarea id="taskDetails" name="taskDetails" class="form-control" rows="3" placeholder="A little about yourself" required> <?php echo htmlspecialchars($profile['bio']); ?></textarea>
                                                <div class="valid-feedback">Looks good!</div>
                                                <div class="invalid-feedback">Please fill this field.</div>
                                            </div>
                                        </div>
                                        <h4 class="mt-5 headCustom">Your Appearance</h4>
                                        <hr>
                                        <div class="col-lg-12 mt-3">
                                            <div class="mb-3">
                                                <p class="text-muted">Body type:</p>
                                                <hr>
                                                <?php
                                                // Fetch the user's body type from the database
                                                $bodyType = isset($profile['body_type']) ? htmlspecialchars($profile['body_type']) : '';
                                                ?>

                                                <div class="form-check form-check-inline me-5">
                                                    <input class="form-check-input fs-5 highlight-radio"
                                                        type="radio"
                                                        name="bodyType"
                                                        value="petite"
                                                        id="petite"
                                                        <?php echo $bodyType === 'petite' ? 'checked' : ''; ?>>
                                                    <label class="form-check-label text-muted fs-6" for="petite">Petite</label>
                                                </div>

                                                <div class="form-check form-check-inline me-5">
                                                    <input class="form-check-input fs-5 highlight-radio"
                                                        type="radio"
                                                        name="bodyType"
                                                        id="slim"
                                                        value="slim"
                                                        <?php echo $bodyType === 'slim' ? 'checked' : ''; ?>>
                                                    <label class="form-check-label text-muted fs-6" for="slim">Slim</label>
                                                </div>

                                                <div class="form-check form-check-inline me-5">
                                                    <input class="form-check-input fs-5 highlight-radio"
                                                        type="radio"
                                                        name="bodyType"
                                                        id="athletic"
                                                        value="athletic"
                                                        <?php echo $bodyType === 'athletic' ? 'checked' : ''; ?>>
                                                    <label class="form-check-label text-muted fs-6" for="athletic">Athletic</label>
                                                </div>

                                                <div class="form-check form-check-inline me-5">
                                                    <input class="form-check-input fs-5 highlight-radio"
                                                        type="radio"
                                                        name="bodyType"
                                                        id="average"
                                                        value="average"
                                                        <?php echo $bodyType === 'average' ? 'checked' : ''; ?>>
                                                    <label class="form-check-label text-muted fs-6" for="average">Average</label>
                                                </div>

                                                <div class="form-check form-check-inline me-5">
                                                    <input class="form-check-input fs-5 highlight-radio"
                                                        type="radio"
                                                        name="bodyType"
                                                        id="few extra pounds"
                                                        value="few extra pounds"
                                                        <?php echo $bodyType === 'few extra pounds' ? 'checked' : ''; ?>>
                                                    <label class="form-check-label text-muted fs-6" for="few extra pounds">Few Extra Pounds</label>
                                                </div>

                                                <div class="form-check form-check-inline me-5">
                                                    <input class="form-check-input fs-5 highlight-radio"
                                                        type="radio"
                                                        name="bodyType"
                                                        id="full figured"
                                                        value="full figured"
                                                        <?php echo $bodyType === 'full figured' ? 'checked' : ''; ?>>
                                                    <label class="form-check-label text-muted fs-6" for="full figured">Full Figured</label>
                                                </div>

                                                <div class="form-check form-check-inline me-5">
                                                    <input class="form-check-input fs-5 highlight-radio"
                                                        type="radio"
                                                        name="bodyType"
                                                        id="large and lovely"
                                                        value="large and lovely"
                                                        <?php echo $bodyType === 'large and lovely' ? 'checked' : ''; ?>>
                                                    <label class="form-check-label text-muted fs-6" for="large and lovely">Large and Lovely</label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-lg-12 mt-2">
                                            <div class="mb-3">
                                                <p class="text-muted">Your ethnicity is mostly:</p>
                                                <hr>
                                                <div class="form-check form-check-inline me-5">
                                                    <input class="form-check-input fs-5 highlight-radio" type="radio" name="ethnicity" id="arab(middle eastern)" value="arab(middle eastern)"
                                                        <?php echo ($profile['ethnicity'] == 'arab(middle eastern)') ? 'checked' : ''; ?>>
                                                    <label class="form-check-label text-muted fs-6" for="arab(middle eastern)">Arab (Middle Eastern)</label>
                                                </div>

                                                <div class="form-check form-check-inline me-5">
                                                    <input class="form-check-input fs-5 highlight-radio" type="radio" name="ethnicity" id="asian" value="asian"
                                                        <?php echo ($profile['ethnicity'] == 'asian') ? 'checked' : ''; ?>>
                                                    <label class="form-check-label text-muted fs-6" for="asian">Asian</label>
                                                </div>

                                                <div class="form-check form-check-inline me-5">
                                                    <input class="form-check-input fs-5 highlight-radio" type="radio" name="ethnicity" id="black" value="black"
                                                        <?php echo ($profile['ethnicity'] == 'black') ? 'checked' : ''; ?>>
                                                    <label class="form-check-label text-muted fs-6" for="black">Black</label>
                                                </div>

                                                <div class="form-check form-check-inline me-5">
                                                    <input class="form-check-input fs-5 highlight-radio" type="radio" name="ethnicity" id="caucasian(white)" value="caucasian(white)"
                                                        <?php echo ($profile['ethnicity'] == 'caucasian(white)') ? 'checked' : ''; ?>>
                                                    <label class="form-check-label text-muted fs-6" for="caucasian(white)">Caucasian (White)</label>
                                                </div>

                                                <div class="form-check form-check-inline me-5">
                                                    <input class="form-check-input fs-5 highlight-radio" type="radio" name="ethnicity" id="hispanic/latino" value="hispanic/latino"
                                                        <?php echo ($profile['ethnicity'] == 'hispanic/latino') ? 'checked' : ''; ?>>
                                                    <label class="form-check-label text-muted fs-6" for="hispanic/latino">Hispanic/Latino</label>
                                                </div>

                                                <div class="form-check form-check-inline me-5">
                                                    <input class="form-check-input fs-5 highlight-radio" type="radio" name="ethnicity" id="indain" value="indain"
                                                        <?php echo ($profile['ethnicity'] == 'indain') ? 'checked' : ''; ?>>
                                                    <label class="form-check-label text-muted fs-6" for="indain">Indian</label>
                                                </div>

                                                <div class="form-check form-check-inline me-5">
                                                    <input class="form-check-input fs-5 highlight-radio" type="radio" name="ethnicity" id="pacific islander" value="pacific islander"
                                                        <?php echo ($profile['ethnicity'] == 'pacific islander') ? 'checked' : ''; ?>>
                                                    <label class="form-check-label text-muted fs-6" for="pacific islander">Pacific Islander</label>
                                                </div>

                                                <div class="form-check form-check-inline me-5">
                                                    <input class="form-check-input fs-5 highlight-radio" type="radio" name="ethnicity" id="other" value="other"
                                                        <?php echo ($profile['ethnicity'] == 'other') ? 'checked' : ''; ?>>
                                                    <label class="form-check-label text-muted fs-6" for="other">Other</label>
                                                </div>

                                                <div class="form-check form-check-inline me-5">
                                                    <input class="form-check-input fs-5" type="radio" name="ethnicity" id="mixed" value="mixed"
                                                        <?php echo ($profile['ethnicity'] == 'mixed') ? 'checked' : ''; ?>>
                                                    <label class="form-check-label text-muted fs-6" for="mixed">Mixed</label>
                                                </div>

                                                <div class="form-check form-check-inline me-5">
                                                    <input class="form-check-input fs-5 highlight-radio" type="radio" name="ethnicity" id="prefer not to say" value="prefer not to say"
                                                        <?php echo ($profile['ethnicity'] == 'prefer not to say') ? 'checked' : ''; ?>>
                                                    <label class="form-check-label text-muted fs-6" for="prefer not to say">Prefer not to say</label>
                                                </div>
                                            </div>
                                        </div>


                                        <!-- my appearance   -->
                                        <div class="col-lg-12 mt-2">
                                            <div class="mb-3">
                                                <p class="text-muted">I consider my appearance as:</p>
                                                <hr>

                                                <!-- Below Average -->
                                                <div class="form-check form-check-inline me-5">
                                                    <input class="form-check-input fs-5 highlight-radio" type="radio" name="appearance" value="below average" id="below average"

                                                        <?php echo ($profile['my_appearance'] == 'below average') ? 'checked' : ''; ?>>
                                                    <label class="form-check-label text-muted fs-6" for="below average">Below average</label>
                                                </div>

                                                <!-- Average -->
                                                <div class="form-check form-check-inline me-5">
                                                    <input class="form-check-input fs-5 highlight-radio" type="radio" name="appearance" value="average" id="average"
                                                        <?php echo ($profile['my_appearance'] == ' average') ? 'checked' : ''; ?>>
                                                    <label class="form-check-label text-muted fs-6" for="average">Average</label>
                                                </div>

                                                <!-- Attractive -->
                                                <div class="form-check form-check-inline me-5">
                                                    <input class="form-check-input fs-5 highlight-radio" type="radio" name="appearance" value="attractive" id="attractive"
                                                        <?php echo ($profile['my_appearance'] == ' attractive') ? 'checked' : ''; ?>>
                                                    <label class="form-check-label text-muted fs-6" for="attractive">Attractive</label>
                                                </div>

                                                <!-- Very Attractive -->
                                                <div class="form-check form-check-inline me-5">
                                                    <input class="form-check-input fs-5 highlight-radio" type="radio" name="appearance" value="very attractive" id="very attractive"
                                                        <?php echo ($profile['my_appearance'] == ' very attractive') ? 'checked' : ''; ?>>
                                                    <label class="form-check-label text-muted fs-6" for="very attractive">Very attractive</label>
                                                </div>
                                            </div>
                                        </div>


                                        <!-- height -->
                                        <div class="row">
                                            <div class="col-lg-3 mt-2">
                                                <div class="mb-3">
                                                    <p class="text-muted">Height:</p>
                                                    <hr>
                                                    <select id="height" name="height" class="form-select text-muted" required>
                                                        <option selected disabled value=""> Please Select... </option>
                                                        <option value="4'7 (140 cm)" <?php echo (isset($profile['height']) && $profile['height'] == "4'7 (140 cm)") ? 'selected' : ''; ?>>4'7" (140 cm)</option>
                                                        <option value="4'8 (143 cm)" <?php echo (isset($profile['height']) && $profile['height'] == "4'8 (143 cm)") ? 'selected' : ''; ?>>4'8" (143 cm)</option>
                                                        <option value="4'9 (145 cm)" <?php echo (isset($profile['height']) && $profile['height'] == "4'9 (145 cm)") ? 'selected' : ''; ?>>4'9" (145 cm)</option>
                                                        <option value="4'10 (148 cm)" <?php echo (isset($profile['height']) && $profile['height'] == "4'10 (148 cm)") ? 'selected' : ''; ?>>4'10" (148 cm)</option>
                                                        <option value="4'11 (150 cm)" <?php echo (isset($profile['height']) && $profile['height'] == "4'11 (150 cm)") ? 'selected' : ''; ?>>4'11" (150 cm)</option>
                                                        <option value="5' (153 cm)" <?php echo (isset($profile['height']) && $profile['height'] == "5' (153 cm)") ? 'selected' : ''; ?>>5' (153 cm)</option>
                                                        <option value="5'1 (155 cm)" <?php echo (isset($profile['height']) && $profile['height'] == "5'1 (155 cm)") ? 'selected' : ''; ?>>5'1" (155 cm)</option>
                                                        <option value="5'2 (158 cm)" <?php echo (isset($profile['height']) && $profile['height'] == "5'2 (158 cm)") ? 'selected' : ''; ?>>5'2" (158 cm)</option>
                                                        <option value="5'3 (161 cm)" <?php echo (isset($profile['height']) && $profile['height'] == "5'3 (161 cm)") ? 'selected' : ''; ?>>5'3" (161 cm)</option>
                                                        <option value="5'4 (163 cm)" <?php echo (isset($profile['height']) && $profile['height'] == "5'4 (163 cm)") ? 'selected' : ''; ?>>5'4" (163 cm)</option>
                                                        <option value="5'5 (166 cm)" <?php echo (isset($profile['height']) && $profile['height'] == "5'5 (166 cm)") ? 'selected' : ''; ?>>5'5" (166 cm)</option>
                                                        <option value="5'6 (168 cm)" <?php echo (isset($profile['height']) && $profile['height'] == "5'6 (168 cm)") ? 'selected' : ''; ?>>5'6" (168 cm)</option>
                                                        <option value="5'7 (171 cm)" <?php echo (isset($profile['height']) && $profile['height'] == "5'7 (171 cm)") ? 'selected' : ''; ?>>5'7" (171 cm)</option>
                                                        <option value="5'8 (173 cm)" <?php echo (isset($profile['height']) && $profile['height'] == "5'8 (173 cm)") ? 'selected' : ''; ?>>5'8" (173 cm)</option>
                                                        <option value="5'9 (176 cm)" <?php echo (isset($profile['height']) && $profile['height'] == "5'9 (176 cm)") ? 'selected' : ''; ?>>5'9" (176 cm)</option>
                                                        <option value="5'10 (178 cm)" <?php echo (isset($profile['height']) && $profile['height'] == "5'10 (178 cm)") ? 'selected' : ''; ?>>5'10" (178 cm)</option>
                                                        <option value="5'11 (181 cm)" <?php echo (isset($profile['height']) && $profile['height'] == "5'11 (181 cm)") ? 'selected' : ''; ?>>5'11" (181 cm)</option>
                                                        <option value="6' (183 cm)" <?php echo (isset($profile['height']) && $profile['height'] == "6' (183 cm)") ? 'selected' : ''; ?>>6' (183 cm)</option>
                                                        <option value="6'1 (186 cm)" <?php echo (isset($profile['height']) && $profile['height'] == "6'1 (186 cm)") ? 'selected' : ''; ?>>6'1" (186 cm)</option>
                                                        <option value="6'2 (188 cm)" <?php echo (isset($profile['height']) && $profile['height'] == "6'2 (188 cm)") ? 'selected' : ''; ?>>6'2" (188 cm)</option>
                                                        <option value="6'3 (191 cm)" <?php echo (isset($profile['height']) && $profile['height'] == "6'3 (191 cm)") ? 'selected' : ''; ?>>6'3" (191 cm)</option>
                                                        <option value="6'4 (194 cm)" <?php echo (isset($profile['height']) && $profile['height'] == "6'4 (194 cm)") ? 'selected' : ''; ?>>6'4" (194 cm)</option>
                                                        <option value="6'5 (196 cm)" <?php echo (isset($profile['height']) && $profile['height'] == "6'5 (196 cm)") ? 'selected' : ''; ?>>6'5" (196 cm)</option>
                                                        <option value="6'6 (199 cm)" <?php echo (isset($profile['height']) && $profile['height'] == "6'6 (199 cm)") ? 'selected' : ''; ?>>6'6" (199 cm)</option>
                                                        <option value="6'7 (201 cm)" <?php echo (isset($profile['height']) && $profile['height'] == "6'7 (201 cm)") ? 'selected' : ''; ?>>6'7" (201 cm)</option>
                                                        <option value="6'8 (204 cm)" <?php echo (isset($profile['height']) && $profile['height'] == "6'8 (204 cm)") ? 'selected' : ''; ?>>6'8" (204 cm)</option>
                                                        <option value="6'9 (206 cm)" <?php echo (isset($profile['height']) && $profile['height'] == "6'9 (206 cm)") ? 'selected' : ''; ?>>6'9" (206 cm)</option>
                                                        <option value="6'10 (209 cm)" <?php echo (isset($profile['height']) && $profile['height'] == "6'10 (209 cm)") ? 'selected' : ''; ?>>6'10" (209 cm)</option>
                                                        <option value="6'11 (211 cm)" <?php echo (isset($profile['height']) && $profile['height'] == "6'11 (211 cm)") ? 'selected' : ''; ?>>6'11" (211 cm)</option>
                                                        <option value="7' (214 cm)" <?php echo (isset($profile['height']) && $profile['height'] == "7' (214 cm)") ? 'selected' : ''; ?>>7' (214 cm)</option>
                                                        <option value="7'1 (216 cm)" <?php echo (isset($profile['height']) && $profile['height'] == "7'1 (216 cm)") ? 'selected' : ''; ?>>7'1" (216 cm)</option>
                                                        <option value="7'2 (219 cm)" <?php echo (isset($profile['height']) && $profile['height'] == "7'2 (219 cm)") ? 'selected' : ''; ?>>7'2" (219 cm)</option>

                                                    </select>

                                                    <div class="valid-feedback">Looks good!</div>
                                                    <div class="invalid-feedback">Please select a project.</div>
                                                </div>
                                            </div>

                                        </div>

                                        <!-- Weight -->
                                        <div class="row">
                                            <div class="col-lg-3 mt-2">
                                                <div class="mb-3">
                                                    <p class="text-muted">Weight:</p>
                                                    <hr>
                                                    <select id="weight" name="occupation" class="form-select text-muted" required>
                                                        <option selected disabled value="">Please Select... </option>
                                                        <option value="40kg (88Ib)" <?php echo (isset($profile['weight']) && $profile['weight'] == "40kg (88Ib)") ? 'selected' : ''; ?>>40kg (88Ib)</option>
                                                        <option value="41kg (90Ib)" <?php echo (isset($profile['weight']) && $profile['weight'] == "41kg (90Ib)") ? 'selected' : ''; ?>>41kg (90Ib)</option>
                                                        <option value="42kg (93Ib)" <?php echo (isset($profile['weight']) && $profile['weight'] == "42kg (93Ib)") ? 'selected' : ''; ?>>42kg (93Ib)</option>
                                                        <option value="43kg (95Ib)" <?php echo (isset($profile['weight']) && $profile['weight'] == "43kg (95Ib)") ? 'selected' : ''; ?>>43kg (95Ib)</option>
                                                        <option value="44kg (97Ib)" <?php echo (isset($profile['weight']) && $profile['weight'] == "44kg (97Ib)") ? 'selected' : ''; ?>>44kg (97Ib)</option>
                                                        <option value="45kg (99Ib)" <?php echo (isset($profile['weight']) && $profile['weight'] == "45kg (99Ib)") ? 'selected' : ''; ?>>45kg (99Ib)</option>
                                                        <option value="46kg (101Ib)" <?php echo (isset($profile['weight']) && $profile['weight'] == "46kg (101Ib)") ? 'selected' : ''; ?>>46kg (101Ib)</option>
                                                        <option value="47kg (104Ib)" <?php echo (isset($profile['weight']) && $profile['weight'] == "47kg (104Ib)") ? 'selected' : ''; ?>>47kg (104Ib)</option>
                                                        <option value="48kg (106Ib)" <?php echo (isset($profile['weight']) && $profile['weight'] == "48kg (106Ib)") ? 'selected' : ''; ?>>48kg (106Ib)</option>
                                                        <option value="49kg (108Ib)" <?php echo (isset($profile['weight']) && $profile['weight'] == "49kg (108Ib)") ? 'selected' : ''; ?>>49kg (108Ib)</option>
                                                        <option value="50kg (110Ib)" <?php echo (isset($profile['weight']) && $profile['weight'] == "50kg (110Ib)") ? 'selected' : ''; ?>>50kg (110Ib)</option>
                                                        <option value="51kg (112Ib)" <?php echo (isset($profile['weight']) && $profile['weight'] == "51kg (112Ib)") ? 'selected' : ''; ?>>51kg (112Ib)</option>
                                                        <option value="52kg (115Ib)" <?php echo (isset($profile['weight']) && $profile['weight'] == "52kg (115Ib)") ? 'selected' : ''; ?>>52kg (115Ib)</option>
                                                        <option value="53kg (117Ib)" <?php echo (isset($profile['weight']) && $profile['weight'] == "53kg (117Ib)") ? 'selected' : ''; ?>>53kg (117Ib)</option>
                                                        <option value="54kg (119Ib)" <?php echo (isset($profile['weight']) && $profile['weight'] == "54kg (119Ib)") ? 'selected' : ''; ?>>54kg (119Ib)</option>
                                                        <option value="55kg (121Ib)" <?php echo (isset($profile['weight']) && $profile['weight'] == "55kg (121Ib)") ? 'selected' : ''; ?>>55kg (121Ib)</option>
                                                        <option value="56kg (123Ib)" <?php echo (isset($profile['weight']) && $profile['weight'] == "56kg (123Ib)") ? 'selected' : ''; ?>>56kg (123Ib)</option>
                                                        <option value="57kg (126Ib)" <?php echo (isset($profile['weight']) && $profile['weight'] == "57kg (126Ib)") ? 'selected' : ''; ?>>57kg (126Ib)</option>
                                                        <option value="58kg (128Ib)" <?php echo (isset($profile['weight']) && $profile['weight'] == "58kg (128Ib)") ? 'selected' : ''; ?>>58kg (128Ib)</option>
                                                        <option value="59kg (130Ib)" <?php echo (isset($profile['weight']) && $profile['weight'] == "59kg (130Ib)") ? 'selected' : ''; ?>>59kg (130Ib)</option>
                                                        <option value="60kg (132Ib)" <?php echo (isset($profile['weight']) && $profile['weight'] == "60kg (132Ib)") ? 'selected' : ''; ?>>60kg (132Ib)</option>
                                                        <option value="61kg (134Ib)" <?php echo (isset($profile['weight']) && $profile['weight'] == "61kg (134Ib)") ? 'selected' : ''; ?>>61kg (134Ib)</option>
                                                        <option value="62kg (137Ib)" <?php echo (isset($profile['weight']) && $profile['weight'] == "62kg (137Ib)") ? 'selected' : ''; ?>>62kg (137Ib)</option>
                                                        <option value="63kg (139Ib)" <?php echo (isset($profile['weight']) && $profile['weight'] == "63kg (139Ib)") ? 'selected' : ''; ?>>63kg (139Ib)</option>
                                                        <option value="64kg (141Ib)" <?php echo (isset($profile['weight']) && $profile['weight'] == "64kg (141Ib)") ? 'selected' : ''; ?>>64kg (141Ib)</option>
                                                        <option value="65kg (143Ib)" <?php echo (isset($profile['weight']) && $profile['weight'] == "65kg (143Ib)") ? 'selected' : ''; ?>>65kg (143Ib)</option>
                                                        <option value="66kg (146Ib)" <?php echo (isset($profile['weight']) && $profile['weight'] == "66kg (146Ib)") ? 'selected' : ''; ?>>66kg (146Ib)</option>
                                                        <option value="67kg (148Ib)" <?php echo (isset($profile['weight']) && $profile['weight'] == "67kg (148Ib)") ? 'selected' : ''; ?>>67kg (148Ib)</option>
                                                        <option value="68kg (150Ib)" <?php echo (isset($profile['weight']) && $profile['weight'] == "68kg (150Ib)") ? 'selected' : ''; ?>>68kg (150Ib)</option>
                                                        <option value="69kg (152Ib)" <?php echo (isset($profile['weight']) && $profile['weight'] == "69kg (152Ib)") ? 'selected' : ''; ?>>69kg (152Ib)</option>
                                                        <option value="70kg (154Ib)" <?php echo (isset($profile['weight']) && $profile['weight'] == "70kg (154Ib)") ? 'selected' : ''; ?>>70kg (154Ib)</option>
                                                        <option value="71kg (157Ib)" <?php echo (isset($profile['weight']) && $profile['weight'] == "71kg (157Ib)") ? 'selected' : ''; ?>>71kg (157Ib)</option>
                                                        <option value="72kg (159Ib)" <?php echo (isset($profile['weight']) && $profile['weight'] == "72kg (159Ib)") ? 'selected' : ''; ?>>72kg (159Ib)</option>
                                                        <option value="73kg (161Ib)" <?php echo (isset($profile['weight']) && $profile['weight'] == "73kg (161Ib)") ? 'selected' : ''; ?>>73kg (161Ib)</option>
                                                        <option value="74kg (163Ib)" <?php echo (isset($profile['weight']) && $profile['weight'] == "74kg (163Ib)") ? 'selected' : ''; ?>>74kg (163Ib)</option>
                                                        <option value="75kg (165Ib)" <?php echo (isset($profile['weight']) && $profile['weight'] == "75kg (165Ib)") ? 'selected' : ''; ?>>75kg (165Ib)</option>
                                                        <option value="76kg (168Ib)" <?php echo (isset($profile['weight']) && $profile['weight'] == "76kg (168Ib)") ? 'selected' : ''; ?>>76kg (168Ib)</option>
                                                        <option value="77kg (170Ib)" <?php echo (isset($profile['weight']) && $profile['weight'] == "77kg (170Ib)") ? 'selected' : ''; ?>>77kg (170Ib)</option>
                                                        <option value="78kg (172Ib)" <?php echo (isset($profile['weight']) && $profile['weight'] == "78kg (172Ib)") ? 'selected' : ''; ?>>78kg (172Ib)</option>
                                                        <option value="79kg (175Ib)" <?php echo (isset($profile['weight']) && $profile['weight'] == "79kg (175Ib)") ? 'selected' : ''; ?>>79kg (175Ib)</option>
                                                        <option value="80kg (176Ib)" <?php echo (isset($profile['weight']) && $profile['weight'] == "80kg (176Ib)") ? 'selected' : ''; ?>>80kg (176Ib)</option>
                                                        <option value="81kg (179Ib)" <?php echo (isset($profile['weight']) && $profile['weight'] == "81kg (179Ib)") ? 'selected' : ''; ?>>81kg (179Ib)</option>
                                                        <option value="82kg (181Ib)" <?php echo (isset($profile['weight']) && $profile['weight'] == "82kg (181Ib)") ? 'selected' : ''; ?>>82kg (181Ib)</option>
                                                        <option value="83kg (183Ib)" <?php echo (isset($profile['weight']) && $profile['weight'] == "83kg (183Ib)") ? 'selected' : ''; ?>>83kg (183Ib)</option>
                                                        <option value="84kg (185Ib)" <?php echo (isset($profile['weight']) && $profile['weight'] == "84kg (185Ib)") ? 'selected' : ''; ?>>84kg (185Ib)</option>
                                                        <option value="85kg (187Ib)" <?php echo (isset($profile['weight']) && $profile['weight'] == "85kg (187Ib)") ? 'selected' : ''; ?>>85kg (187Ib)</option>
                                                        <option value="86kg (190Ib)" <?php echo (isset($profile['weight']) && $profile['weight'] == "86kg (190Ib)") ? 'selected' : ''; ?>>86kg (190Ib)</option>
                                                        <option value="87kg (192Ib)" <?php echo (isset($profile['weight']) && $profile['weight'] == "87kg (192Ib)") ? 'selected' : ''; ?>>87kg (192Ib)</option>
                                                        <option value="88kg (194Ib)" <?php echo (isset($profile['weight']) && $profile['weight'] == "88kg (194Ib)") ? 'selected' : ''; ?>>88kg (194Ib)</option>
                                                        <option value="89kg (196Ib)" <?php echo (isset($profile['weight']) && $profile['weight'] == "89kg (196Ib)") ? 'selected' : ''; ?>>89kg (196Ib)</option>
                                                        <option value="90kg (198Ib)" <?php echo (isset($profile['weight']) && $profile['weight'] == "90kg (198Ib)") ? 'selected' : ''; ?>>90kg (198Ib)</option>
                                                        <option value="91kg (201Ib)" <?php echo (isset($profile['weight']) && $profile['weight'] == "91kg (201Ib)") ? 'selected' : ''; ?>>91kg (201Ib)</option>
                                                        <option value="92kg (203Ib)" <?php echo (isset($profile['weight']) && $profile['weight'] == "92kg (203Ib)") ? 'selected' : ''; ?>>92kg (203Ib)</option>
                                                        <option value="93kg (205Ib)" <?php echo (isset($profile['weight']) && $profile['weight'] == "93kg (205Ib)") ? 'selected' : ''; ?>>93kg (205Ib)</option>
                                                        <option value="94kg (207Ib)" <?php echo (isset($profile['weight']) && $profile['weight'] == "94kg (207Ib)") ? 'selected' : ''; ?>>94kg (207Ib)</option>
                                                        <option value="95kg (209Ib)" <?php echo (isset($profile['weight']) && $profile['weight'] == "95kg (209Ib)") ? 'selected' : ''; ?>>95kg (209Ib)</option>
                                                        <option value="96kg (212Ib)" <?php echo (isset($profile['weight']) && $profile['weight'] == "96kg (212Ib)") ? 'selected' : ''; ?>>96kg (212Ib)</option>
                                                        <option value="97kg (214Ib)" <?php echo (isset($profile['weight']) && $profile['weight'] == "97kg (214Ib)") ? 'selected' : ''; ?>>97kg (214Ib)</option>
                                                        <option value="98kg (216Ib)" <?php echo (isset($profile['weight']) && $profile['weight'] == "98kg (216Ib)") ? 'selected' : ''; ?>>98kg (216Ib)</option>
                                                        <option value="99kg (218Ib)" <?php echo (isset($profile['weight']) && $profile['weight'] == "99kg (218Ib)") ? 'selected' : ''; ?>>99kg (218Ib)</option>
                                                        <option value="100kg (220Ib)" <?php echo (isset($profile['weight']) && $profile['weight'] == "100kg (220Ib)") ? 'selected' : ''; ?>>100kg (220Ib)</option>
                                                    </select>
                                                    <div class="valid-feedback">Looks good!</div>
                                                    <div class="invalid-feedback">Please select a project.</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!--  -->
                                    <h4 class="mt-5 headCustom">Your Lifestyle</h4>
                                    <hr>
                                    <!-- drink alchohal   -->
                                    <div class="col-lg-12 mt-2">
                                        <div class="mb-3">
                                            <p class="text-muted">Do you drink?</p>
                                            <hr>
                                            <div class="form-check form-check-inline me-5">
                                                <input class="form-check-input fs-5 highlight-radio" type="radio" name="drinkAlcohol" value="do drink" id="do drink"
                                                    <?php echo ($profile['drink_alcohol'] == 'do drink') ? 'checked' : ''; ?>>
                                                <label class="form-check-label text-muted  fs-6" for="do drink">
                                                    Do drink </label>
                                            </div>

                                            <div class="form-check form-check-inline me-5">
                                                <input class="form-check-input fs-5 highlight-radio" type="radio" name="drinkAlcohol" value="occasionally drink" id="occasionally drink"
                                                    <?php echo ($profile['drink_alcohol'] == 'occasionally drink') ? 'checked' : ''; ?>>
                                                <label class="form-check-label text-muted  fs-6" for="occasionally drink">
                                                    Occasionally drink </label>
                                            </div>
                                            <div class="form-check form-check-inline me-5">
                                                <input class="form-check-input fs-5 highlight-radio" type="radio" name="drinkAlcohol" value="do not drink" id="do not drink" <?php echo ($profile['drink_alcohol'] == 'do not drink') ? 'checked' : ''; ?>>
                                                <label class="form-check-label text-muted  fs-6" for="do not drink">
                                                    Don't drink</label>
                                            </div>
                                            <div class="form-check form-check-inline me-5">
                                                <input class="form-check-input fs-5 highlight-radio" type="radio" name="drinkAlcohol" value="prefer not to say" id="prefer not to say" <?php echo ($profile['drink_alcohol'] == 'prefer not to say') ? 'checked' : ''; ?>>
                                                <label class="form-check-label text-muted  fs-6" for="prefer not to say">
                                                    Prefre not to say </label>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- my smoke   -->
                                    <div class="col-lg-12 mt-2">
                                        <div class="mb-3">
                                            <p class="text-muted">Do you smoke?</p>
                                            <hr>
                                            <div class="form-check form-check-inline me-5">
                                                <input class="form-check-input fs-5 highlight-radio" type="radio" name="smoking" value="do smoke" id="do smoke" <?php echo ($profile['smoking'] == 'do smoke') ? 'checked' : ''; ?>>
                                                <label class="form-check-label  text-muted  fs-6" for="do smoke">
                                                    Do smoke </label>
                                            </div>
                                            <div class="form-check form-check-inline me-5">
                                                <input class="form-check-input fs-5 highlight-radio" type="radio" name="smoking" value="occasionally smoke" id=" occasionally smoke" <?php echo ($profile['smoking'] == 'occasionally smoke') ? 'checked' : ''; ?>>
                                                <label class="form-check-label  text-muted  fs-6" for="occasionally smoke">
                                                    Occasionally smoke</label>
                                            </div>
                                            <div class="form-check form-check-inline me-5">
                                                <input class="form-check-input fs-5 highlight-radio" type="radio" name="smoking" value="do not smoke" id="do not smoke" <?php echo ($profile['smoking'] == ' do not smoke') ? 'checked' : ''; ?>>
                                                <label class="form-check-label  text-muted  fs-6" for="do not smoke">
                                                    Don't smoke</label>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Marital Status:   -->
                                    <div class="col-lg-12 mt-2">
                                        <div class="mb-3">
                                            <p class="text-muted">Marital Status:
                                            </p>
                                            <hr>
                                            <!--  -->
                                            <div class="form-check form-check-inline me-5">
                                                <input class="form-check-input fs-5 highlight-radio" type="radio" name="maritalStatus" value="single" id="single" <?php echo ($profile['marital_status'] == 'single') ? 'checked' : ''; ?>>
                                                <label class="form-check-label text-muted  fs-6" for="single">
                                                    Single </label>
                                            </div>
                                            <div class="form-check form-check-inline me-5">
                                                <input class="form-check-input fs-5 highlight-radio" type="radio" name="maritalStatus" value="separated" id="separated" <?php echo ($profile['marital_status'] == 'separated') ? 'checked' : ''; ?>>
                                                <label class="form-check-label text-muted  fs-6" for="separated">
                                                    Separated</label>
                                            </div>
                                            <div class="form-check form-check-inline me-5">
                                                <input class="form-check-input fs-5 highlight-radio" type="radio" name="maritalStatus" value="widowed" id="widowed" <?php echo ($profile['marital_status'] == 'widowed') ? 'checked' : ''; ?>>
                                                <label class="form-check-label text-muted  fs-6" for="widowed">
                                                    Widowed</label>
                                            </div>
                                            <div class="form-check form-check-inline me-5">
                                                <input class="form-check-input fs-5 highlight-radio" type="radio" name="maritalStatus" value="divorced" id="divorced" <?php echo ($profile['marital_status'] == 'divorced') ? 'checked' : ''; ?>>
                                                <label class="form-check-label text-muted  fs-6" for="divorced">
                                                    Divorced</label>
                                            </div>
                                            <div class="form-check form-check-inline me-5">
                                                <input class="form-check-input fs-5 highlight-radio" type="radio" name="maritalStatus" value="other" id="other" <?php echo ($profile['marital_status'] == 'other') ? 'checked' : ''; ?>>
                                                <label class="form-check-label text-muted  fs-6" for="other">
                                                    Other</label>
                                            </div>
                                            <div class="form-check form-check-inline me-5">
                                                <input class="form-check-input fs-5 highlight-radio" type="radio" name="maritalStatus" value="prefer not to say" id="prefer not to say" <?php echo ($profile['marital_status'] == 'prefer not to say') ? 'checked' : ''; ?>>
                                                <label class="form-check-label text-muted  fs-6" for="prefer not to say">
                                                    Prefer not to say</label>
                                            </div>
                                        </div>

                                    </div>
                                    <!-- Do you want (more) children?  -->
                                    <div class="col-lg-12 mt-2">
                                        <div class="mb-3">
                                            <p class="text-muted">Do you want (more) children?
                                            </p>
                                            <hr>
                                            <div class="form-check form-check-inline me-5">
                                                <input class="form-check-input fs-5 highlight-radio" type="radio" name="children" value="yes" id="yes" <?php echo ($profile['children'] == 'yes') ? 'checked' : ''; ?>>
                                                <label class="form-check-label  text-muted  fs-6" for="yes">
                                                    Yes </label>
                                            </div>

                                            <div class="form-check form-check-inline me-5">
                                                <input class="form-check-input fs-5 highlight-radio" type="radio" name="children" value="not sure" id="not sure" <?php echo ($profile['children'] == 'not sure') ? 'checked' : ''; ?>>
                                                <label class="form-check-label  text-muted  fs-6" for="not sure">
                                                    Not Sure</label>
                                            </div>

                                            <div class="form-check form-check-inline me-5">
                                                <input class="form-check-input fs-5 highlight-radio" type="radio" name="children" value="no" id="no" <?php echo ($profile['children'] == 'not') ? 'checked' : ''; ?>>
                                                <label class="form-check-label  text-muted  fs-6" for="no">
                                                    No</label>
                                            </div>


                                        </div>
                                        <!--Relationship you're looking for:    -->
                                        <div class="col-lg-12 mt-2">
                                            <div class="mb-3">
                                                <p class="text-muted">Relationship you're looking for:</p>
                                                <hr>
                                                <div class="form-check form-check-inline me-5">
                                                    <input class="form-check-input fs-5 highlight-radio" name="relationshipLooking[]" type="checkbox" id="marriage" value="marriage" <?php echo (in_array('marriage', explode(',', $profile['relationship_looking']))) ? 'checked' : ''; ?>>
                                                    <label class="form-check-label text-muted fs-6" for="marriage">Marriage</label>
                                                </div>
                                                <div class="form-check form-check-inline me-5">
                                                    <input class="form-check-input fs-5 highlight-radio" name="relationshipLooking[]" type="checkbox" id="friendship" value="friendship" <?php echo (in_array('friendship', explode(',', $profile['relationship_looking']))) ? 'checked' : ''; ?>>
                                                    <label class="form-check-label text-muted fs-6" for="friendship">Friendship</label>
                                                </div>

                                            </div>
                                        </div>


                                        <!-- Save Profile Button -->
                                        <div class="row mb-3">
                                            <div class="col-lg-12 text-center">
                                                <button type="submit" id="btnUpdateProfile" name="btnUpdateProfile" class="btn btn-primary">Save Profile</button>
                                            </div>
                                        </div>
                                </form>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>

    </div><!-- row end -->
    </di>
    <?php include 'userlayout/footer.php'; ?>

    <!-- Add Bootstrap JavaScript bundle with Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>
    <script>
        function displayImage(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('profilePicPreview');
                    preview.src = e.target.result;
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>

</html>
</body>