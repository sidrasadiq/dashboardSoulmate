<?php
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
$countries = $cities = $states = $nationality = $religion = $qualifications = $occupations = $casts = [];

try {
    // fetch the country 
    $queryCountries = "SELECT id, country_name FROM countries ORDER BY id ASC";
    $stmtCountries = $conn->prepare($queryCountries);
    $stmtCountries->execute();
    $resultCountries = $stmtCountries->get_result();
    while ($row = $resultCountries->fetch_assoc()) {
        $countries[] = $row;
    }
    // fetch city 
    $queryCities = "SELECT id, city_name FROM cities ORDER BY id ASC";
    $stmtCities = $conn->prepare($queryCities);
    $stmtCities->execute();
    $resultCities = $stmtCities->get_result();
    while ($row = $resultCities->fetch_assoc()) {
        $cities[] = $row;
    }

    // fetch states
    $queryStates = "SELECT id, state_name FROM states ORDER BY id ASC";
    $stmtStates = $conn->prepare($queryStates);
    $stmtStates->execute();
    $resultStates = $stmtStates->get_result();
    while ($row = $resultStates->fetch_assoc()) {
        $states[] = $row;
    }
    // fetch nationalities
    $queryNationalities = "SELECT id,  nationality_name FROM  nationality ORDER BY id ASC";
    $stmtNationalities = $conn->prepare($queryNationalities);
    $stmtNationalities->execute();
    $resultNationalities = $stmtNationalities->get_result();
    while ($row = $resultNationalities->fetch_assoc()) {
        $nationalities[] = $row;
    }
    // fetch 	religion 
    $queryReligions = "SELECT id,  religion_name FROM  religion ORDER BY id ASC";
    $stmtReligions = $conn->prepare($queryReligions);
    $stmtReligions->execute();
    $resultReligions = $stmtReligions->get_result();
    while ($row = $resultReligions->fetch_assoc()) {
        $religions[] = $row;
    }
    // fetch 	qualification 
    $queryQualifications = "SELECT id,  qualification_name FROM  qualifications ORDER BY id ASC";
    $stmtQualifications = $conn->prepare($queryQualifications);
    $stmtQualifications->execute();
    $resultQualifications = $stmtQualifications->get_result();
    while ($row = $resultQualifications->fetch_assoc()) {
        $qualifications[] = $row;
    }
    // fetch 	occupation 
    $queryOccupations = "SELECT id,  occupation_name FROM  occupation ORDER BY id ASC";
    $stmtOccupations = $conn->prepare($queryOccupations);
    $stmtOccupations->execute();
    $resultOccupations = $stmtOccupations->get_result();
    while ($row = $resultOccupations->fetch_assoc()) {
        $occupations[] = $row;
    }
    // fetch 	user_cast 
    $queryCasts  = "SELECT id,  cast_name FROM  user_cast ORDER BY id ASC";
    $stmtCasts = $conn->prepare($queryCasts);
    $stmtCasts->execute();
    $resultCasts = $stmtCasts->get_result();
    while ($row = $resultCasts->fetch_assoc()) {
        $casts[] = $row;
    }

    $stmtCountries->close();
    $stmtCities->close();
    $stmtStates->close();
    $stmtNationalities->close();
    $stmtReligions->close();
    $stmtQualifications->close();
    $stmtOccupations->close();
    $stmtCasts->close();
} catch (Exception $e) {
    $_SESSION['message'] = ['type' => 'error', 'content' => 'Error fetching dropdown data'];
    header("Location: errorpage.php");
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
                                        <!-- Body  type -->
                                        <div class="col-lg-12 mt-3">
                                            <div class="mb-3">
                                                <p class="text-muted">Body type:</p>
                                                <hr>
                                                <div class="form-check form-check-inline me-5">
                                                    <input class="form-check-input fs-5 highlight-radio" type="radio" name="bodyType" value="petite" id="petite">
                                                    <label class="form-check-label text-muted  fs-6" for="petite">Petite</label>
                                                </div>

                                                <div class="form-check form-check-inline me-5">
                                                    <input class="form-check-input fs-5 highlight-radio" type="radio" name="bodyType" id="slim" value="slim">
                                                    <label class="form-check-label text-muted  fs-6" for="slim">Slim</label>
                                                </div>

                                                <div class="form-check form-check-inline me-5">
                                                    <input class="form-check-input fs-5 highlight-radio" type="radio" name="bodyType" id="athletic" value="athletic">
                                                    <label class="form-check-label text-muted  fs-6" for="athletic">Athletic</label>
                                                </div>
                                                <div class="form-check form-check-inline me-5">
                                                    <input class="form-check-input fs-5 highlight-radio" type="radio" name="bodyType" id="average" value="average">
                                                    <label class="form-check-label text-muted  fs-6" for="average">Average</label>
                                                </div>

                                                <div class="form-check form-check-inline me-5">
                                                    <input class="form-check-input fs-5 highlight-radio" type="radio" name="bodyType" id="few extra pounds" value="few extra pounds">
                                                    <label class="form-check-label text-muted  fs-6" for="few extra pounds">Few Extra Pounds</label>
                                                </div>

                                                <div class="form-check form-check-inline me-5">
                                                    <input class="form-check-input fs-5 highlight-radio" type="radio" name="bodyType" id="full figured" value="full figured">
                                                    <label class="form-check-label text-muted  fs-6" for="full figured">Full Figured</label>
                                                </div>

                                                <div class="form-check form-check-inline me-5">
                                                    <input class="form-check-input fs-5 highlight-radio" type="radio" name="bodyType" id="large and lovely" value="large and lovely">
                                                    <label class="form-check-label text-muted  fs-6" for="large and lovely">Large and Lovely</label>
                                                </div>

                                            </div>

                                        </div>
                                        <!-- ethnicity  type -->
                                        <div class="col-lg-12 mt-2">
                                            <div class="mb-3">
                                                <p class="text-muted">Your ethnicity is mostly:</p>
                                                <hr>
                                                <div class="form-check form-check-inline me-5">
                                                    <input class="form-check-input fs-5 highlight-radio" type="radio" name="ethnicity" id="arab(middle eastern)" value="arab(middle eastern)">
                                                    <label class="form-check-label text-muted  fs-6" for="arab(middle eastern)">
                                                        Arab (Middle Eastern) </label>
                                                </div>
                                                <div class="form-check form-check-inline me-5">
                                                    <input class="form-check-input fs-5 highlight-radio" type="radio" name="ethnicity" id="asian" value="asian">
                                                    <label class="form-check-label text-muted  fs-6" for="asian">
                                                        Asian </label>
                                                </div>
                                                <div class="form-check form-check-inline me-5">
                                                    <input class="form-check-input fs-5 highlight-radio" type="radio" name="ethnicity" id="black" value="black">
                                                    <label class="form-check-label text-muted  fs-6" for="black">
                                                        Black</label>
                                                </div>
                                                <div class="form-check form-check-inline me-5">
                                                    <input class="form-check-input fs-5 highlight-radio" type="radio" name="ethnicity" value="caucasian(white)" id="caucasian(white)">
                                                    <label class="form-check-label text-muted  fs-6" for="caucasian(white)">
                                                        Caucasian (White) </label>
                                                </div>
                                                <div class="form-check form-check-inline me-5">
                                                    <input class="form-check-input fs-5 highlight-radio" type="radio" name="ethnicity" value="hispanic/latino" id="hispanic/latino">
                                                    <label class="form-check-label text-muted  fs-6" for="hispanic/latino">
                                                        Hispanic/Latino </label>
                                                </div>
                                                <div class="form-check form-check-inline me-5">
                                                    <input class="form-check-input fs-5 highlight-radio" type="radio" name="ethnicity" value="indain" id="indain">
                                                    <label class="form-check-label text-muted  fs-6" for="indain">
                                                        Indain </label>
                                                </div>
                                                <div class="form-check form-check-inline me-5">
                                                    <input class="form-check-input fs-5 highlight-radio" type="radio" name="ethnicity" value="pacific islander" id="pacific islander">
                                                    <label class="form-check-label text-muted  fs-6" for="pacific islander">
                                                        Pacific Islander </label>
                                                </div>
                                                <div class="form-check form-check-inline me-5">
                                                    <input class="form-check-input fs-5 highlight-radio" type="radio" name="ethnicity" value="other" id="other">
                                                    <label class="form-check-label text-muted  fs-6" for="other">
                                                        Other </label>
                                                </div>
                                                <div class="form-check form-check-inline me-5">
                                                    <input class="form-check-input fs-5" type="radio" name="ethnicity" value="mixed" id="mixed">
                                                    <label class="form-check-label text-muted  fs-6" for="mixed">
                                                        Mixed </label>
                                                </div>
                                                <div class="form-check form-check-inline me-5">
                                                    <input class="form-check-input fs-5 highlight-radio" type="radio" name="ethnicity" value="prefer not to say" id="prefer not to say">
                                                    <label class="form-check-label text-muted  fs-6" for="prefer not to say">
                                                        Prefre not to say </label>
                                                </div>
                                            </div>

                                        </div>

                                        <!-- my appearance   -->
                                        <div class="col-lg-12 mt-2">
                                            <div class="mb-3">
                                                <p class="text-muted">I consider my appearance as:</p>
                                                <hr>
                                                <!--  -->
                                                <div class="form-check form-check-inline me-5">
                                                    <input class="form-check-input fs-5 highlight-radio" type="radio" name="appearance" value="below average" id="below average">
                                                    <label class="form-check-label text-muted  fs-6" for="below average">
                                                        Below average </label>
                                                </div>

                                                <div class="form-check form-check-inline me-5">
                                                    <input class="form-check-input fs-5 highlight-radio" type="radio" name="appearance" value="average" id="average">
                                                    <label class="form-check-label text-muted  fs-6" for="average">
                                                        Average</label>
                                                </div>

                                                <div class="form-check form-check-inline me-5">
                                                    <input class="form-check-input fs-5 highlight-radio" type="radio" name="appearance" value="attractive" id="attractive">
                                                    <label class="form-check-label text-muted  fs-6" for="attractive">
                                                        Attractive</label>
                                                </div>

                                                <div class="form-check form-check-inline me-5">
                                                    <input class="form-check-input fs-5 highlight-radio" type="radio" name="appearance" value="very attractive" id="very attractive">
                                                    <label class="form-check-label text-muted  fs-6" for="very attractive">
                                                        Very attractive</label>
                                                </div>
                                            </div>
                                            <!-- height -->
                                            <div class="col-lg-3 mt-2">
                                                <div class="mb-3">
                                                    <p class="text-muted">Height:</p>
                                                    <hr>
                                                    <select id="height" name="height" class="form-select text-muted" required>
                                                        <option selected disabled value=""> Please Select... </option>
                                                        <option value="4'7 (140 cm)">4'7" (140 cm)</option>
                                                        <option value="4'8 (143 cm)">4'8" (143 cm)</option>
                                                        <option value="4'9 (145 cm)">4'9" (145 cm)</option>
                                                        <option value="4'10 (148 cm)">4'10" (148 cm)</option>
                                                        <option value="4'11 (150 cm)">4'11" (150 cm)</option>
                                                        <option value="5' (153 cm)">5' (153 cm)</option>
                                                        <option value="5'1 (155 cm)">5'1" (155 cm)</option>
                                                        <option value="5'2 (158 cm)">5'2" (158 cm)</option>
                                                        <option value="5'3 (161 cm)">5'3" (161 cm)</option>
                                                        <option value="5'4 (163 cm)">5'4" (163 cm)</option>
                                                        <option value="5'5 (166 cm)">5'5" (166 cm)</option>
                                                        <option value="5'6 (168 cm)">5'6" (168 cm)</option>
                                                        <option value="5'7 (171 cm)">5'7" (171 cm)</option>
                                                        <option value="5'8 (173 cm)">5'8" (173 cm)</option>
                                                        <option value="5'9 (176 cm)">5'9" (176 cm)</option>
                                                        <option value="5'10 (178 cm)">5'10" (178 cm)</option>
                                                        <option value="5'11 (181 cm)">5'11" (181 cm)</option>
                                                        <option value="6 (183 cm)">6' (183 cm)</option>
                                                        <option value="6'1 (186 cm)">6'1" (186 cm)</option>
                                                        <option value="6'2  (188 cm)">6'2" (188 cm)</option>
                                                        <option value="6'3  (191 cm)">6'3" (191 cm)</option>
                                                        <option value="6'4  (194 cm)">6'4" (194 cm)</option>
                                                        <option value="6'5  (196 cm)">6'5" (196 cm)</option>
                                                        <option value="6'6  (199 cm)">6'6" (199 cm)</option>
                                                        <option value="6'7  (201 cm)">6'7" (2011 cm)</option>
                                                        <option value="6'8  (204 cm)">6'8" (204 cm)</option>
                                                        <option value="6'9  (206 cm)">6'9" (206 cm)</option>
                                                        <option value="6'10  (209 cm)">6'10" (209 cm)</option>
                                                        <option value="6'11  (211 cm)">6'11" (211 cm)</option>
                                                        <option value="7  (214 cm)">7' (214 cm)</option>
                                                        <option value="7'1  (216 cm)">7'1" (216 cm)</option>
                                                        <option value="7'2  (219 cm)">7'2" (219 cm)</option>
                                                    </select>

                                                    <div class="valid-feedback">Looks good!</div>
                                                    <div class="invalid-feedback">Please select a project.</div>
                                                </div>
                                            </div>
                                            <!-- Weight -->
                                            <div class="col-lg-3 mt-2">
                                                <div class="mb-3">
                                                    <p class="text-muted">Weight:</p>
                                                    <hr>
                                                    <select id="weight" name="occupation" class="form-select text-muted" required>
                                                        <option selected disabled value="">Please Select... </option>
                                                        <option value="40kg (88Ib)">40kg (88Ib)</option>
                                                        <option value="41kg (90Ib)">41kg (90Ib)</option>
                                                        <option value="42kg (93Ib)">42kg (93Ib)</option>
                                                        <option value="43kg (95Ib)">43kg (95Ib)</option>
                                                        <option value="44kg (97Ib)">44kg (97Ib)</option>
                                                        <option value="45kg (99Ib)">45kg (99Ib)</option>
                                                        <option value="46kg (101Ib)">46kg (101Ib)</option>
                                                        <option value="47kg (104Ib)">47kg (104Ib)</option>
                                                        <option value="48kg (106Ib)">48kg (106Ib)</option>
                                                        <option value="49kg (108Ib)">49kg (108Ib)</option>
                                                        <option value="50kg (110Ib)">50kg (110Ib)</option>
                                                        <option value="51kg (112Ib)">51kg (112Ib)</option>
                                                        <option value="52kg (115Ib)">52kg (115Ib)</option>
                                                        <option value="53kg (117Ib)">53kg (117Ib)</option>
                                                        <option value="54kg (119Ib)">54kg (119Ib)</option>
                                                        <option value="55kg (121Ib)">55kg (121Ib)</option>
                                                        <option value="56kg (123Ib)">56kg (123Ib)</option>
                                                        <option value="57kg (126Ib)">57kg (126Ib)</option>
                                                        <option value="58kg (128Ib)">58kg (128Ib)</option>
                                                        <option value="59kg (130Ib)">59kg (130Ib)</option>
                                                        <option value="60kg (132Ib)">60kg (132Ib)</option>
                                                        <option value="61kg (134Ib)">61kg (134Ib)</option>
                                                        <option value="62kg (137Ib)">62kg (137Ib)</option>
                                                        <option value="63kg (139Ib)">63kg (139Ib)</option>
                                                        <option value="64kg (141Ib)">64kg (141Ib)</option>
                                                        <option value="65kg (143Ib)">65kg (143Ib)</option>
                                                        <option value="66kg (146Ib)">66kg (146Ib)</option>
                                                        <option value="67kg (148Ib)">67kg (148Ib)</option>
                                                        <option value="68kg (150Ib)">68kg (150Ib)</option>
                                                        <option value="69kg (152Ib)">69kg (152Ib)</option>
                                                        <option value="70kg (154Ib)">70kg (154Ib)</option>
                                                        <option value="71kg (157Ib)">71kg (157Ib)</option>
                                                        <option value="72kg (159Ib)">72kg (159Ib)</option>
                                                        <option value="73kg (161Ib)">73kg (161Ib)</option>
                                                        <option value="74kg (163Ib)">74kg (163Ib)</option>
                                                        <option value="75kg (165Ib)">75kg (165Ib)</option>
                                                        <option value="76kg (168Ib)">76kg (168Ib)</option>
                                                        <option value="77kg (170Ib)">77kg (170Ib)</option>
                                                        <option value="78kg (172Ib)">78kg (172Ib)</option>
                                                        <option value="79kg (174Ib)">79kg (174Ib)</option>
                                                        <option value="80kg (176Ib)">80kg (176Ib)</option>
                                                        <option value="81kg (179Ib)">81kg (179Ib)</option>
                                                        <option value="82kg (181Ib)">82kg (181Ib)</option>
                                                        <option value="83kg (183Ib">83kg (183Ib)</option>
                                                        <option value="84kg (185Ib)">84kg (185Ib)</option>
                                                        <option value="85kg (187Ib)">85kg (187Ib)</option>
                                                        <option value="86kg (190Ib)">86kg (190Ib)</option>
                                                        <option value="87kg (192Ib)">87kg (192Ib)</option>
                                                        <option value="88kg (194Ib)">88kg (194Ib)</option>
                                                        <option value="89kg (196Ib)">89kg (196Ib)</option>
                                                        <option value="90kg (198Ib)">90kg (198Ib)</option>
                                                        <option value="91kg (201Ib)">91kg (201Ib)</option>
                                                        <option value="92kg (203Ib)">92kg (203Ib)</option>
                                                        <option value="93kg (205Ib)">93kg (205Ib)</option>
                                                        <option value="94kg (207Ib)">94kg (207Ib)</option>
                                                        <option value="95kg (209Ib)">95kg (209Ib)</option>
                                                        <option value="96kg (212Ib)">96kg (212Ib)</option>
                                                        <option value="97kg (214Ib)">97kg (214Ib)</option>
                                                        <option value="98kg (216Ib)">98kg (216Ib)</option>
                                                        <option value="99kg (218Ib)">99kg (218Ib)</option>
                                                        <option value="100kg (220Ib)">100kg (220Ib)</option>
                                                    </select>
                                                    <div class="valid-feedback">Looks good!</div>
                                                    <div class="invalid-feedback">Please select a project.</div>
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
                                                    <input class="form-check-input fs-5 highlight-radio" type="radio" name="drinkAlcohol" value="do drink" id="do drink">
                                                    <label class="form-check-label text-muted  fs-6" for="do drink">
                                                        Do drink </label>
                                                </div>

                                                <div class="form-check form-check-inline me-5">
                                                    <input class="form-check-input fs-5 highlight-radio" type="radio" name="drinkAlcohol" value="occasionally drink" id="occasionally drink">
                                                    <label class="form-check-label text-muted  fs-6" for="occasionally drink">
                                                        Occasionally drink </label>
                                                </div>
                                                <div class="form-check form-check-inline me-5">
                                                    <input class="form-check-input fs-5 highlight-radio" type="radio" name="drinkAlcohol" value="do not drink" id="do not drink">
                                                    <label class="form-check-label text-muted  fs-6" for="do not drink">
                                                        Don't drink</label>
                                                </div>
                                                <div class="form-check form-check-inline me-5">
                                                    <input class="form-check-input fs-5 highlight-radio" type="radio" name="drinkAlcohol" value="prefer not to say" id="prefer not to say">
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
                                                    <input class="form-check-input fs-5 highlight-radio" type="radio" name="smoking" value="do smoke" id="do smoke">
                                                    <label class="form-check-label  text-muted  fs-6" for="do smoke">
                                                        Do smoke </label>
                                                </div>
                                                <div class="form-check form-check-inline me-5">
                                                    <input class="form-check-input fs-5 highlight-radio" type="radio" name="smoking" value="occasionally smoke id=" occasionally smoke">
                                                    <label class="form-check-label  text-muted  fs-6" for="occasionally smoke">
                                                        Occasionally smoke</label>
                                                </div>
                                                <div class="form-check form-check-inline me-5">
                                                    <input class="form-check-input fs-5 highlight-radio" type="radio" name="smoking" value="do not smoke" id="do not smoke">
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
                                                    <input class="form-check-input fs-5 highlight-radio" type="radio" name="maritalStatus" value="single" id="single">
                                                    <label class="form-check-label text-muted  fs-6" for="single">
                                                        Single </label>
                                                </div>
                                                <div class="form-check form-check-inline me-5">
                                                    <input class="form-check-input fs-5 highlight-radio" type="radio" name="maritalStatus" value="separated" id="separated">
                                                    <label class="form-check-label text-muted  fs-6" for="separated">
                                                        Separated</label>
                                                </div>
                                                <div class="form-check form-check-inline me-5">
                                                    <input class="form-check-input fs-5 highlight-radio" type="radio" name="maritalStatus" value="widowed" id="widowed">
                                                    <label class="form-check-label text-muted  fs-6" for="widowed">
                                                        Widowed</label>
                                                </div>
                                                <div class="form-check form-check-inline me-5">
                                                    <input class="form-check-input fs-5 highlight-radio" type="radio" name="maritalStatus" value="divorced" id="divorced">
                                                    <label class="form-check-label text-muted  fs-6" for="divorced">
                                                        Divorced</label>
                                                </div>
                                                <div class="form-check form-check-inline me-5">
                                                    <input class="form-check-input fs-5 highlight-radio" type="radio" name="maritalStatus" value="other" id="other">
                                                    <label class="form-check-label text-muted  fs-6" for="other">
                                                        Other</label>
                                                </div>
                                                <div class="form-check form-check-inline me-5">
                                                    <input class="form-check-input fs-5 highlight-radio" type="radio" name="maritalStatus" value="prefer not to say" id="prefer not to say">
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
                                                    <input class="form-check-input fs-5 highlight-radio" type="radio" name="children" value="yes" id="yes">
                                                    <label class="form-check-label  text-muted  fs-6" for="yes">
                                                        Yes </label>
                                                </div>

                                                <div class="form-check form-check-inline me-5">
                                                    <input class="form-check-input fs-5 highlight-radio" type="radio" name="children" value="not sure" id="not sure">
                                                    <label class="form-check-label  text-muted  fs-6" for="not sure">
                                                        Not Sure</label>
                                                </div>

                                                <div class="form-check form-check-inline me-5">
                                                    <input class="form-check-input fs-5 highlight-radio" type="radio" name="children" value="no" id="no">
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
                                                        <input class="form-check-input fs-5 highlight-radio" name="relationshipLooking" type="checkbox" id="marriage" value="marriage">
                                                        <label class="form-check-label text-muted  fs-6" for="marriage">Marriage</label>
                                                    </div>
                                                    <div class="form-check form-check-inline me-5">
                                                        <input class="form-check-input fs-5 highlight-radio" type="checkbox" name="relationshipLooking" id="friendship" value="friendship">
                                                        <label class="form-check-label text-muted  fs-6" for="friendship">Friendship</label>
                                                    </div>
                                                </div>
                                            </div>


                                            <!-- Save Profile Button -->
                                            <div class="row mb-3">
                                                <div class="col-lg-12 text-center">
                                                    <button type="submit" id="btnSaveProfile" name="btnSaveProfile" class="btn btn-primary">Save Profile</button>
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
    </div>
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