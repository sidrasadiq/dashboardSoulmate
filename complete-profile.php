<?php
session_start();
include 'layouts/config.php';
include 'layouts/functions.php';

// Ensure the user is logged in by checking the session for 'user_id'
if (!isset($_SESSION['user_id'])) {
    $_SESSION['message'][] = array("type" => "error", "content" => "You must be logged in to complete your profile.");
    header("Location: login.php");
    exit();
}
$userId = $_SESSION['user_id'];
// Check if the user profile is incomplete
$profileCheckQuery = "SELECT * FROM profiles WHERE user_id = ? AND (prefer_age_from IS NULL OR prefer_age_to IS NULL OR country_id IS NULL 
OR city_id IS NULL OR relationship_looking IS NULL OR ethnicity IS NULL OR beliefs IS NULL OR drink_alcohol IS NULL OR smoking IS NULL OR children IS NULL 
OR marital_status IS NULL OR my_appearance IS NULL OR body_type IS NULL OR profile_picture IS NULL)";
$stmtProfileCheck = $conn->prepare($profileCheckQuery);
if (!$stmtProfileCheck) {
    throw new Exception("Prepare statement failed: " . $conn->error);
}

$stmtProfileCheck->bind_param("i", $userId);
$stmtProfileCheck->execute();
$profileResult = $stmtProfileCheck->get_result();

if ($profileResult->num_rows > 0) {
    // Profile is incomplete; allow the user to complete it
    // Show the form (do not redirect to the same page)
} else {
    // If profile is complete, proceed to the user dashboard
    header("Location: user_index.php");
    exit();
}

// Initialize arrays for countries and cities
$countries = [];
$cities = [];
$states = [];

try {
    // Start a transaction for data fetching
    $conn->begin_transaction();

    // Fetch countries
    $queryCountries = "SELECT id, country_name FROM countries ORDER BY id ASC;";
    $stmtCountries = $conn->prepare($queryCountries);
    $stmtCountries->execute();
    $resultCountries = $stmtCountries->get_result();
    while ($row = $resultCountries->fetch_assoc()) {
        $countries[] = $row;
    }

    // Fetch states
    $queryStates = "SELECT id, state_name FROM states  ORDER BY id ASC;";
    $stmtStates = $conn->prepare($queryStates);
    $stmtStates->execute();
    $resultStates  = $stmtStates->get_result();
    while ($row = $resultStates->fetch_assoc()) {
        $states[] = $row;
    }
    // Fetch cities
    $queryCities = "SELECT id, city_name FROM cities ORDER BY id ASC;";
    $stmtCities = $conn->prepare($queryCities);
    $stmtCities->execute();
    $resultCities = $stmtCities->get_result();
    while ($row = $resultCities->fetch_assoc()) {
        $cities[] = $row;
    }

    $conn->commit();
} catch (Exception $e) {
    $conn->rollback();
    $_SESSION['message'][] = array("type" => "error", "content" => "Error: " . $e->getMessage());
    header("location: complete-profile.php");
    exit();
}

// Check if form is submitted via POST
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submit"])) {

    // Sanitize and retrieve form data
    $photo = isset($_FILES['uploadedFile']) ? $_FILES['uploadedFile']['name'] : null;

    $ageFrom = isset($_POST['ageFrom']) ? intval($_POST['ageFrom']) : null;
    $ageTo = isset($_POST['ageTo']) ? intval($_POST['ageTo']) : null;
    $country = isset($_POST['country']) ? intval($_POST['country']) : null;
    $city = isset($_POST['city']) ? intval($_POST['city']) : null;
    $state = isset($_POST['state']) ? intval($_POST['state']) : null;
    $relationshipLooking = isset($_POST['relationshipLooking']) ? (is_array($_POST['relationshipLooking']) ? implode(", ", $_POST['relationshipLooking']) : $_POST['relationshipLooking']) : null;
    $ethnicity = isset($_POST['ethnicity']) ? $_POST['ethnicity'] : null;
    $beliefs = isset($_POST['beliefs']) ? $_POST['beliefs'] : null;
    $drinkAlcohol = isset($_POST['drinkAlcohol']) ? $_POST['drinkAlcohol'] : 'No'; // Set default to 'No' if not selected
    $smoking = isset($_POST['smoking']) ? $_POST['smoking'] : 'No'; // Set default to 'No'
    $children = isset($_POST['children']) ? $_POST['children'] : 'No'; // Set default to 'No'
    $maritalStatus = isset($_POST['maritalStatus']) ? $_POST['maritalStatus'] : null;
    $appearance = isset($_POST['appearance']) ? $_POST['appearance'] : null;
    $bodyType = isset($_POST['bodyType']) ? $_POST['bodyType'] : null;
    var_dump($_POST);

    try {
        // Start the transaction for saving data
        $conn->begin_transaction();

        // Handle file upload for photo
        if (isset($_FILES['uploadedFile']) && $_FILES['uploadedFile']['error'] == 0) {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
            if (in_array($_FILES['uploadedFile']['type'], $allowedTypes)) {
                $uploadDir = 'uploads/';
                $photo = basename($_FILES['uploadedFile']['name']);
                $uploadFile = $uploadDir . $photo;
                if (!move_uploaded_file($_FILES['uploadedFile']['tmp_name'], $uploadFile)) {
                    throw new Exception("Failed to upload the file.");
                }
            } else {
                throw new Exception("Invalid file type. Only JPEG, PNG, and GIF are allowed.");
            }
        }

        // Update existing profile
        $sql_update_profile = "UPDATE profiles 
   SET prefer_age_from = ?, prefer_age_to = ?, country_id = ?, city_id = ?, state_id=?,
       relationship_looking = ?, ethnicity = ?, beliefs = ?, drink_alcohol = ?, smoking = ?, 
       children = ?, marital_status = ?, my_appearance = ?, body_type = ?, profile_picture = ? 
   WHERE user_id = ?";


        $stmt_update = $conn->prepare($sql_update_profile);
        if (!$stmt_update) {
            throw new Exception("Failed to prepare update statement: " . $conn->error);
        }

        // Bind parameters
        $stmt_update->bind_param(
            "iiiiissssssssssi",
            $ageFrom,
            $ageTo,
            $country,
            $city,
            $state,
            $relationshipLooking,
            $ethnicity,
            $beliefs,
            $drinkAlcohol,
            $smoking,
            $children,
            $maritalStatus,
            $appearance,
            $bodyType,
            $photo,
            $userId
        );

        // Execute and handle errors
        if (!$stmt_update->execute()) {
            throw new Exception("Failed to update profile: " . $stmt_update->error);
        }


        // Commit the transaction after successful insert
        $conn->commit();

        $_SESSION['message'][] = array("type" => "success", "content" => "Profile saved successfully!");
        header("Location: user_index.php?user_id=$userId"); // Redirect to the next page
        exit();
    } catch (Exception $e) {
        // Rollback if error occurs
        $conn->rollback();
        $_SESSION['message'][] = array("type" => "error", "content" => "Error: " . $e->getMessage());
        header("location: complete-profile.php");
        exit();
    }
}
?>









<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <title>Complete Profile - Matrimony</title>
    <style>
        /* not show all steps in one step */
        .step {
            display: none;
        }

        /* display individually by click */
        .step.active {
            display: block;
        }

        /* steps of all buttons  */
        .step-buttons {
            margin-top: 50px;
            margin-right: 490px;
            padding: 10px;

        }

        /* styling of input field field */
        .step-head {
            color: #E63A7A;
            font-size: 30px;
        }

        /* step digit styling  */
        .step-num {
            color: #E63A7A;
            font-size: 80px;
        }

        /* file upload styling contianer */
        .file-upload-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
        }

        /* file upload input */
        .file-upload-input {
            position: relative;
            display: inline-block;
            border: 2px dashed #4CAF50;
            border-radius: 8px;
            padding: 40px 20px;
            text-align: center;
            background-color: #fff;
            cursor: pointer;
            transition: border-color 0.3s ease, background-color 0.3s ease;
        }

        /* hover of file upload */
        .file-upload-input:hover {
            border-color: #45a049;
            background-color: #f1f8f5;
        }

        .file-upload-input input[type="file"] {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            cursor: pointer;
        }

        /* filed upload icons  */
        .file-upload-icon {
            font-size: 50px;
            color: #4CAF50;
            margin-bottom: 10px;
        }

        /* test  */
        .file-upload-text {
            font-size: 16px;
            font-weight: 500;
            color: #333;
        }

        .file-upload-subtext {
            font-size: 14px;
            color: #888;
        }

        /* Optional: Style for uploaded file name */
        .file-name {
            margin-top: 20px;
            font-size: 16px;
            font-weight: 500;
            color: #4CAF50;
        }

        /* step of button next  */
        .btn-nxt {
            background-color: #ff69b4;
            /* Pink color */
            color: white;
            /* White text */
            width: 140px;
            /* Larger width */
            border: none;
            /* Remove border */
            border-radius: 5px;
            /* Rounded corners */
            font-size: 18px;
            /* Increase font size */
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            /* Subtle shadow */
            transition: background-color 0.3s ease;
            /* Smooth color transition */
        }

        /* button previous */
        .btn-pre {
            background-color: #3987cc;
            color: white;
            margin-right: 70px;
            width: 100px;
            border: none;
            border-radius: 5px;
            font-size: 18px;
            /* Increase font size */
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            /* Subtle shadow */
            transition: background-color 0.3s ease;
        }

        /* Hover effect */
        .btn-nxt:hover {
            background-color: #3987cc;
            color: white;
            /* Darker pink on hover */
        }

        /* progress bar */
        .progress {
            height: 20px;
            margin-bottom: 20px;
            color: red;


            /* Space between the progress bar and buttons */
        }

        /* progressbar color */
        .prg {
            background: linear-gradient(135deg, #3987cc, #E63A7A);
            /* Custom red gradient */
        }

        /* resonsive for mobile screen */

        @media screen and (max-width: 768px) {

            /* forn conainer */
            .form-container {
                width: 100%;
            }

            /* button responsive */
            .step-buttons {
                padding: 10px;
                margin-right: 30px;
                /* padding: 0px; */

            }

            /* first step  button */
            .stp-main {
                margin-right: 80px;
            }

            /* next button */
            .btn-nxt {
                margin-right: 10px;
                width: 100px;
            }

            /* previous button */
            .btn-pre {
                margin-right: 40px;
            }
        }
    </style>
</head>

<body>
    <!-- logo -->
    <div class="m-2">
        <img src="assets/images/logo.png">
    </div>
    <!-- main contianer start -->
    <div class="container mt-5">
        <?php displaySessionMessage(); ?>
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="complete-profile" enctype="multipart/form-data">
            <!-- Step 1:  Add Photo -->
            <div class="step active position-relative" id="step1">
                <h1 class="mt-5 text-center step-num st-1">1</h1>
                <h5 class="text-center  step-head">Add Your Best Photo</h5>
                <div class="file-upload-wrapper">
                    <!-- File Upload Input -->
                    <div class="file-upload-input mt-5">
                        <div class="file-upload-icon">📁</div>
                        <div class="file-upload-text">Drag and drop or click to upload</div>
                        <div class="file-upload-subtext">Supports JPEG, PNG, JPG</div>
                        <input type="file" id="fileInput" name="uploadedFile" accept=".jpeg, .png, jpg" required>
                    </div>

                    <!-- Placeholder for showing uploaded file name -->
                    <div id="fileName" class="file-name"></div>
                </div>
                <p class="text-center mt-5">How to choose the right photo from the gallery</p>
                <div class="row justify-content-center">
                    <!-- Left-aligned list items -->
                    <div class="col-md-4">
                        <ul>
                            <li>Recent photo of just you</li>
                            <li>Clearly shows your face</li>
                            <li>Good quality, Bright and clear</li>
                        </ul>
                    </div>
                    <!-- Right-aligned list items -->
                    <div class="col-md-4">
                        <ul>
                            <li>No celebrity/ fake uploads</li>
                            <li>No nudity, children, pets, hidden faces</li>
                            <li>No texts/ memes/ ads</li>
                        </ul>
                    </div>
                </div>

                <!-- Button positioned in the bottom-right corner -->
                <div class="step-buttons stp-main position-absolute end-0 ">
                    <button type="button" class="btn btn-lg btn-nxt" id="nextToStep2">Next <i class="bi bi-arrow-right"></i> </button>
                </div>
            </div>

            <!-- Step 2: Age Group -->
            <div class="step mt-5" id="step2">
                <h1 class="mt-5 text-center step-num st-1">2</h1>
                <h3 class="text-center  ">What age group best fits your dating preferences?</h3>
                <p class="text-center ">Refine your search to find individuals who are within the age range that
                </p>
                <p class="text-center ">you find most compatible.</p>
                <div class="container mt-5">
                    <div class="row justify-content-center">
                        <div class="col-sm-8 mb-3 mb-sm-0">
                            <div class="card text-center border-0"> <!-- Added border-0 class -->
                                <div class="card-body card-body-st2 mb-5">
                                    <!-- age 1 -->
                                    <div class="row ">
                                        <div class="col-6">
                                            <div class="form-group mb-3">

                                                <label for="ageFrom"></label>
                                                <select class="form-select" name="ageFrom" id="option1" required>
                                                    <option selected value="18">18</option>
                                                    <option value="19">19</option>
                                                    <option value="20">20</option>
                                                    <option value="21">21</option>
                                                    <option value="22">22</option>
                                                    <option value="23">23</option>
                                                    <option value="24">24</option>
                                                    <option value="25">25</option>
                                                    <option value="26">26</option>
                                                    <option value="27">27</option>
                                                    <option value="28">28</option>
                                                    <option value="29">29</option>
                                                    <option value="30">30</option>
                                                    <option value="31">31</option>
                                                    <option value="32">32</option>
                                                    <option value="33">33</option>
                                                    <option value="34">34</option>
                                                    <option value="35">35</option>
                                                    <option value="36">36</option>
                                                    <option value="37">37</option>
                                                    <option value="38">38</option>
                                                    <option value="39">39</option>
                                                    <option value="40">40</option>
                                                    <option value="41">41</option>
                                                    <option value="42">42</option>
                                                    <option value="43">43</option>
                                                    <option value="44">44</option>
                                                    <option value="45">45</option>
                                                    <option value="46">46</option>
                                                    <option value="47">47</option>
                                                    <option value="48">48</option>
                                                    <option value="49">49</option>
                                                    <option value="50">50</option>
                                                    <option value="51">51</option>
                                                    <option value="52">52</option>
                                                    <option value="53">53</option>
                                                    <option value="54">54</option>
                                                    <option value="55">55</option>
                                                    <option value="56">56</option>
                                                    <option value="57">57</option>
                                                    <option value="58">58</option>
                                                    <option value="59">59</option>
                                                    <option value="60">60</option>
                                                    <option value="61">61</option>
                                                    <option value="62">62</option>
                                                    <option value="63">63</option>
                                                    <option value="64">64</option>
                                                    <option value="65">65</option>
                                                    <option value="66">66</option>
                                                    <option value="67">67</option>
                                                    <option value="68">68</option>
                                                    <option value="69">69</option>
                                                    <option value="70">70 </option>
                                                </select>
                                            </div>
                                        </div>
                                        <!-- age2 -->
                                        <div class="col-6">
                                            <div class="form-group mb-3">
                                                <label for="ageTo"></label>
                                                <select class="form-select" name="ageTo" id="option1" required>
                                                    <option value="18" selected>18</option>
                                                    <option value="19">19</option>
                                                    <option value="20">20</option>
                                                    <option value="21">21</option>
                                                    <option value="22">22</option>
                                                    <option value="23">23</option>
                                                    <option value="24">24</option>
                                                    <option value="25">25</option>
                                                    <option value="26">26</option>
                                                    <option value="27">27</option>
                                                    <option value="28">28</option>
                                                    <option value="29">29</option>
                                                    <option value="30">30</option>
                                                    <option value="31">31</option>
                                                    <option value="32">32</option>
                                                    <option value="33">33</option>
                                                    <option value="34">34</option>
                                                    <option value="35">35</option>
                                                    <option value="36">36</option>
                                                    <option value="37">37</option>
                                                    <option value="38">38</option>
                                                    <option value="39">39</option>
                                                    <option value="40">40</option>
                                                    <option value="41">41</option>
                                                    <option value="42">42</option>
                                                    <option value="43">43</option>
                                                    <option value="44">44</option>
                                                    <option value="45">45</option>
                                                    <option value="46">46</option>
                                                    <option value="47">47</option>
                                                    <option value="48">48</option>
                                                    <option value="49">49</option>
                                                    <option value="50">50</option>
                                                    <option value="51">51</option>
                                                    <option value="52">52</option>
                                                    <option value="53">53</option>
                                                    <option value="54">54</option>
                                                    <option value="55">55</option>
                                                    <option value="56">56</option>
                                                    <option value="57">57</option>
                                                    <option value="58">58</option>
                                                    <option value="59">59</option>
                                                    <option value="60">60</option>
                                                    <option value="61">61</option>
                                                    <option value="62">62</option>
                                                    <option value="63">63</option>
                                                    <option value="64">64</option>
                                                    <option value="65">65</option>
                                                    <option value="66">66</option>
                                                    <option value="67">67</option>
                                                    <option value="68">68</option>
                                                    <option value="69">69</option>
                                                    <option value="70">70 </option>
                                                </select>
                                            </div>
                                        </div>

                                    </div>
                                    <!--  -->
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="step-buttons  position-absolute end-0 ">
                        <button type="button" class="btn btn-secondary btn-lg btn-pre " id="prevToStep1">Back</button>
                        <button type="button" class="btn btn-lg btn-nxt" id="nextToStep3">Next <i class="bi bi-arrow-right"></i></button>
                    </div>
                </div>
            </div>

            <!-- Step 3: Location  -->
            <div class="step" id="step3">
                <h1 class="mt-5 text-center step-num st-1">3</h1>
                <h3 class="text-center  ">What is the preferred location for finding your partner?</h3>
                <p class="text-center ">Are you seeking someone nearby for convenience or open to exploring
                </p>
                <p class="text-center ">connections across borders?</p>

                <div class="container mt-5">
                    <div class="row justify-content-center">
                        <div class="col-sm-8 mb-3 col-md-12 mb-sm-0">
                            <div class="card text-center border-0"> <!-- Added border-0 class -->
                                <div class="card-body card-body-st2 mb-5">
                                    <!--  -->
                                    <div class="row ">
                                        <div class="col-4">
                                            <div class="form-group mb-3">

                                                <label for="country"></label>
                                                <select class="form-select" name="country" id="option1" required>
                                                    <option selected>Select Country</option>
                                                    <?php foreach ($countries as $country): ?>
                                                        <option value="<?php echo $country['id']; ?>"><?php echo $country['country_name']; ?></option>
                                                        <?php endforeach; ?>>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-4">
                                            <div class="form-group mb-3">

                                                <label for="country"></label>
                                                <select class="form-select" name="state" id="option1" required>
                                                    <option selected>Select State</option>
                                                    <?php foreach ($states as $state): ?>
                                                        <option value="<?php echo $state['id']; ?>"><?php echo $state['state_name']; ?></option>
                                                        <?php endforeach; ?>>
                                                </select>
                                            </div>
                                        </div>


                                        <div class="col-4">
                                            <div class="form-group mb-3">

                                                <label for="city"> </label>
                                                <select class="form-select" name="city" id="city" required>
                                                    <option selected>Select city</option>
                                                    <?php foreach ($cities as $city): ?>
                                                        <option value="<?php echo $city['id']; ?>"><?php echo $city['city_name']; ?></option>
                                                        <?php endforeach; ?>>
                                                </select>
                                            </div>
                                        </div>


                                    </div>
                                    <!--  -->
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="step-buttons  position-absolute end-0 ">
                        <button type="button" class="btn btn-secondary btn-lg btn-pre " id="prevToStep2">Back</button>
                        <button type="button" class="btn btn-lg btn-nxt " id="nextToStep4">Next <i class="bi bi-arrow-right"></i></button>
                    </div>
                </div>
            </div>
            <!-- Step 4: RelationShip -->
            <div class="step" id="step4">
                <h1 class="mt-5 text-center step-num st-1">4</h1>
                <h3 class="text-center  ">What type of relationship are you looking for?</h3>
                <p class="text-center ">Honesty helps everyone and you find what they are looking for. You can
                </p>
                <p class="text-center ">change your preferences at any time.</p>
                <div class="container mt-5">
                    <div class="row justify-content-center">
                        <div class="col-sm-8 mb-3 mb-sm-0">
                            <div class="card  border-0"> <!-- Added border-0 class -->
                                <div class="card-body card-body-st2 mb-5">
                                    <!--  -->
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" name="relationshipLooking" type="checkbox" id="marriage" value="marriage">
                                        <label class="form-check-label" for="marriage">Marriage</label>
                                    </div>
                                    <hr>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox" name="relationshipLooking" id="friendship" value="friendship">
                                        <label class="form-check-label" for="friendship">Friendship</label>
                                    </div>
                                    <hr>
                                    <!--  -->
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="step-buttons  position-absolute end-0 ">
                        <button type="button" class="btn btn-secondary btn-lg btn-pre " id="prevToStep3">Back</button>
                        <button type="button" class="btn btn-lg btn-nxt " id="nextToStep5">Next <i class="bi bi-arrow-right"></i></button>
                    </div>
                </div>
            </div>
    </div> <!--main container end-->
    <!-- Step 5: Ethnicity -->
    <div class="step" id="step5">
        <h1 class="mt-5 text-center step-num st-1">5</h1>
        <h3 class="text-center  ">Your ethnicity is mostly ...</h3>
        <div class="container mt-5">
            <div class="row justify-content-center">
                <div class="col-sm-8 mb-3 mb-sm-0">
                    <div class="card  border-0"> <!-- Added border-0 class -->
                        <div class="card-body card-body-st2 mb-5">
                            <!--  -->
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="ethnicity" id="arab(middle eastern)" value="arab(middle eastern)">
                                <label class="form-check-label" for="arab(middle eastern)">
                                    Arab (Middle Eastern) </label>
                            </div>
                            <hr>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="ethnicity" id="asian" value="asian">
                                <label class="form-check-label" for="asian">
                                    Asian </label>
                            </div>
                            <hr>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="ethnicity" id="black" value="black">
                                <label class="form-check-label" for="black">
                                    Black</label>
                            </div>
                            <hr>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="ethnicity" value="caucasian(white)" id="caucasian(white)">
                                <label class="form-check-label" for="caucasian(white)">
                                    Caucasian (White) </label>
                            </div>
                            <hr>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="ethnicity" value="hispanic/latino" id="hispanic/latino">
                                <label class="form-check-label" for="hispanic/latino">
                                    Hispanic/Latino </label>
                            </div>
                            <hr>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="ethnicity" value="indain" id="indain">
                                <label class="form-check-label" for="indain">
                                    Indain </label>
                            </div>
                            <hr>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="ethnicity" value="pacific islander" id="pacific islander">
                                <label class="form-check-label" for="pacific islander">
                                    Pacific Islander </label>
                            </div>
                            <hr>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="ethnicity" value="other" id="other">
                                <label class="form-check-label" for="other">
                                    Other </label>
                            </div>
                            <hr>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="ethnicity" value="mixed" id="mixed">
                                <label class="form-check-label" for="mixed">
                                    Mixed </label>
                            </div>
                            <hr>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="ethnicity" value="prefer not to say" id="prefer not to say">
                                <label class="form-check-label" for="prefer not to say">
                                    Prefre not to say </label>
                            </div>
                            <hr>

                            <!--  -->
                        </div>
                    </div>
                </div>
            </div>



            <div class="step-buttons  position-absolute end-0 ">
                <button type="button" class="btn btn-secondary btn-lg btn-pre " id="prevToStep4">Back</button>
                <button type="button" class="btn btn-lg btn-nxt " id="nextToStep6">Next <i class="bi bi-arrow-right"></i></button>
            </div>
        </div>
    </div>
    </div>
    <!-- Step 6:  describes your beliefs-->
    <div class="step" id="step6">
        <h1 class="mt-5 text-center step-num st-1">6</h1>
        <h3 class="text-center  ">Which of the following best describes your beliefs?</h3>
        <div class="container mt-5">
            <div class="row justify-content-center">
                <div class="col-sm-8 mb-3 mb-sm-0">
                    <div class="card  border-0"> <!-- Added border-0 class -->
                        <div class="card-body card-body-st2 mb-5">
                            <!--  -->
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="beliefs" value="islam-sunni" id="islam-sunni">
                                <label class="form-check-label" for="islam-sunni">
                                    Islam - Sunni
                                </label>
                            </div>
                            <hr>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="beliefs" value="islam-shiite" id="islam-shiite">
                                <label class="form-check-label" for="islam-shiite">
                                    Islam - Shiite
                                </label>
                            </div>
                            <hr>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="beliefs" value="islam-sufism" id="islam-sufism">
                                <label class="form-check-label" for="islam-sufism">
                                    Islam - Sufism
                                </label>
                            </div>
                            <hr>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="beliefs" value="islam-ahmadiyya" id="islam-ahmadiyya">
                                <label class="form-check-label" for="islam-ahmadiyya">
                                    Islam - Ahmadiyya
                                </label>
                            </div>
                            <hr>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="beliefs" value="islam-other" id="islam-other">
                                <label class="form-check-label" for="islam-other">
                                    Islam - Other
                                </label>
                            </div>
                            <hr>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="beliefs" value="willing to revert" id="willing-to-revert">
                                <label class="form-check-label" for="willing-to-revert">
                                    Willing to revert
                                </label>
                            </div>
                            <hr>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="beliefs" value="other" id="other">
                                <label class="form-check-label" for="other">
                                    Other
                                </label>
                            </div>
                            <hr>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="beliefs" value="prefer not to say" id="prefer-not-to-say">
                                <label class="form-check-label" for="prefer-not-to-say">
                                    Prefer not to say
                                </label>
                            </div>

                            <hr>

                            <!--  -->
                        </div>
                    </div>
                </div>
            </div>



            <div class="step-buttons  position-absolute end-0 ">
                <button type="button" class="btn btn-secondary btn-lg btn-pre " id="prevToStep5">Back</button>
                <button type="button" class="btn btn-lg btn-nxt " id="nextToStep7">Next <i class="bi bi-arrow-right"></i></button>
            </div>
        </div>
    </div>
    </div>
    <!-- Step 7: Alcohol -->
    <div class="step" id="step7">
        <h1 class="mt-5 text-center step-num st-1">7</h1>
        <h3 class="text-center  ">How often do you drink alcohol?</h3>
        <div class="container mt-5">
            <div class="row justify-content-center">
                <div class="col-sm-8 mb-3 mb-sm-0">
                    <div class="card  border-0"> <!-- Added border-0 class -->
                        <div class="card-body card-body-st2 mb-5">
                            <!--  -->
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="drinkAlcohol" value="do drink" id="do drink">
                                <label class="form-check-label" for="do drink">
                                    Do drink </label>
                            </div>
                            <hr>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="drinkAlcohol" value="occasionally drink" id="occasionally drink">
                                <label class="form-check-label" for="occasionally drink">
                                    Occasionally drink </label>
                            </div>
                            <hr>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="drinkAlcohol" value="do not drink" id="do not drink">
                                <label class="form-check-label" for="do not drink">
                                    Don't drink</label>
                            </div>
                            <hr>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="drinkAlcohol" value="prefer not to say" id="prefer not to say">
                                <label class="form-check-label" for="prefer not to say">
                                    Prefre not to say </label>
                            </div>
                            <hr>
                            <!--  -->
                        </div>
                    </div>
                </div>
            </div>
            <div class="step-buttons  position-absolute end-0 ">
                <button type="button" class="btn btn-secondary btn-lg btn-pre " id="prevToStep6">Back</button>
                <button type="button" class="btn btn-lg btn-nxt " id="nextToStep8">Next <i class="bi bi-arrow-right"></i></button>
            </div>
        </div>
    </div>
    </div>
    <!-- Step 8: Smoking -->
    <div class="step" id="step8">
        <h1 class="mt-5 text-center step-num st-1">8</h1>
        <h3 class="text-center  ">Do you smoke?</h3>
        <div class="container mt-5">
            <div class="row justify-content-center">
                <div class="col-sm-8 mb-3 mb-sm-0">
                    <div class="card  border-0"> <!-- Added border-0 class -->
                        <div class="card-body card-body-st2 mb-5">
                            <!--  -->
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="smoking" value="do smoke" id="do smoke">
                                <label class="form-check-label" for="do smoke">
                                    Do smoke </label>
                            </div>
                            <hr>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="smoking" value="occasionally smoke id=" occasionally smoke">
                                <label class="form-check-label" for="occasionally smoke">
                                    Occasionally smoke</label>
                            </div>
                            <hr>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="smoking" value="do not smoke" id="do not smoke">
                                <label class="form-check-label" for="do not smoke">
                                    Don't smoke</label>
                            </div>
                            <hr>

                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="smoking" value="prefer not to say" id="prefer not to say">
                                <label class="form-check-label" for="prefer not to say">
                                    Prefre not to say </label>
                            </div>
                            <hr>
                            <!--  -->
                        </div>
                    </div>
                </div>
            </div>
            <div class="step-buttons  position-absolute end-0 ">
                <button type="button" class="btn btn-secondary btn-lg btn-pre " id="prevToStep7">Back</button>
                <button type="button" class="btn btn-lg btn-nxt " id="nextToStep9">Next <i class="bi bi-arrow-right"></i></button>
            </div>
        </div>
    </div>
    </div>
    <!-- Step 9: childeren -->
    <div class="step" id="step9">
        <h1 class="mt-5 text-center step-num st-1">9</h1>
        <h3 class="text-center  ">Do you want (more) children?</h3>
        <div class="container mt-5">
            <div class="row justify-content-center">
                <div class="col-sm-8 mb-3 mb-sm-0">
                    <div class="card  border-0"> <!-- Added border-0 class -->
                        <div class="card-body card-body-st2 mb-5">
                            <!--  -->
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="children" value="yes" id="yes">
                                <label class="form-check-label" for="yes">
                                    Yes </label>
                            </div>
                            <hr>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="children" value="not sure" id="not sure">
                                <label class="form-check-label" for="not sure">
                                    Not Sure</label>
                            </div>
                            <hr>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="children" value="no" id="no">
                                <label class="form-check-label" for="no">
                                    No</label>
                            </div>
                            <hr>
                            <!--  -->
                        </div>
                    </div>
                </div>
            </div>
            <div class="step-buttons  position-absolute end-0 ">
                <button type="button" class="btn btn-secondary btn-lg btn-pre " id="prevToStep8">Back</button>
                <button type="button" class="btn btn-lg btn-nxt " id="nextToStep10">Next <i class="bi bi-arrow-right"></i></button>
            </div>
        </div>
    </div>
    </div>
    <!-- Step 10: marital status -->
    <div class="step" id="step10">
        <h1 class="mt-5 text-center step-num st-1">10</h1>
        <h3 class="text-center">What's your current marital status?</h3>
        <div class="container mt-5">
            <div class="row justify-content-center">
                <div class="col-sm-8 mb-3 mb-sm-0">
                    <div class="card  border-0"> <!-- Added border-0 class -->
                        <div class="card-body card-body-st2 mb-5">
                            <!--  -->
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="maritalStatus" value="single" id="single">
                                <label class="form-check-label" for="single">
                                    Single </label>
                            </div>
                            <hr>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="maritalStatus" value="separated" id="separated">
                                <label class="form-check-label" for="separated">
                                    Separated</label>
                            </div>
                            <hr>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="maritalStatus" value="widowed" id="widowed">
                                <label class="form-check-label" for="widowed">
                                    Widowed</label>
                            </div>
                            <hr>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="maritalStatus" value="divorced" id="divorced">
                                <label class="form-check-label" for="divorced">
                                    Divorced</label>
                            </div>
                            <hr>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="maritalStatus" value="other" id="other">
                                <label class="form-check-label" for="other">
                                    Other</label>
                            </div>
                            <hr>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="maritalStatus" value="prefer not to say" id="prefer not to say">
                                <label class="form-check-label" for="prefer not to say">
                                    Prefer not to say</label>
                            </div>
                            <hr>
                            <!--  -->
                        </div>
                    </div>
                </div>
            </div>
            <div class="step-buttons  position-absolute end-0 ">
                <button type="button" class="btn btn-secondary btn-lg btn-pre " id="prevToStep9">Back</button>
                <button type="button" class="btn btn-lg btn-nxt " id="nextToStep11">Next <i class="bi bi-arrow-right"></i></button>
            </div>
        </div>
    </div>
    </div>
    <!-- Step 11: appearance  -->
    <div class="step" id="step11">
        <h1 class="mt-5 text-center step-num st-1">11</h1>
        <h3 class="text-center">Continue the statement. I consider my appearance as:</h3>
        <div class="container mt-5">
            <div class="row justify-content-center">
                <div class="col-sm-8 mb-3 mb-sm-0">
                    <div class="card  border-0"> <!-- Added border-0 class -->
                        <div class="card-body card-body-st2 mb-5">
                            <!--  -->
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="appearance" value="below average" id="below average">
                                <label class="form-check-label" for="below average">
                                    Below average </label>
                            </div>
                            <hr>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="appearance" value="average" id="average">
                                <label class="form-check-label" for="average">
                                    Average</label>
                            </div>
                            <hr>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="appearance" value="attractive" id="attractive">
                                <label class="form-check-label" for="attractive">
                                    Attractive</label>
                            </div>
                            <hr>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="appearance" value="very attractive" id="very attractive">
                                <label class="form-check-label" for="very attractive">
                                    Very attractive</label>
                            </div>
                            <hr>
                            <!--  -->
                        </div>
                    </div>
                </div>
            </div>
            <div class="step-buttons  position-absolute end-0 ">
                <button type="button" class="btn btn-secondary btn-lg btn-pre " id="prevToStep10">Back</button>
                <button type="button" class="btn btn-lg btn-nxt " id="nextToStep12">Next <i class="bi bi-arrow-right"></i></button>
            </div>
        </div>
    </div>
    </div>
    <!-- Step 12: body type -->
    <div class="step" id="step12">
        <h1 class="mt-5 text-center step-num st-1">12</h1>
        <h3 class="text-center">How would you describe your body type?</h3>
        <div class="container mt-5">
            <div class="row justify-content-center">
                <div class="col-sm-8 mb-3 mb-sm-0">
                    <div class="card  border-0"> <!-- Added border-0 class -->
                        <div class="card-body card-body-st2 mb-5">
                            <!--  -->
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="bodyType" value="petite" id="petite">
                                <label class="form-check-label" for="petite">
                                    Petite </label>
                            </div>
                            <hr>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="bodyType" id="slim" value="slim">
                                <label class="form-check-label" for="slim">
                                    Slim</label>
                            </div>
                            <hr>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="bodyType" id="average" value="average">
                                <label class="form-check-label" for="average">
                                    Average</label>
                            </div>
                            <hr>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="bodyType" id="few extra pounds" value="few extra pounds">
                                <label class="form-check-label" for="few extra pounds">
                                    Few Extra Pounds </label>
                            </div>
                            <hr>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="bodyType" id="full figured" value="full figured">
                                <label class="form-check-label" for="full figured">
                                    Full Figured </label>
                            </div>
                            <hr>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="bodyType" id="large and lovely" value="large and lovely">
                                <label class="form-check-label" for="large and lovely">
                                    Large and Lovely</label>
                            </div>
                            <hr>
                        </div>
                    </div>
                </div>
            </div>
            <div class="step-buttons  position-absolute end-0 ">
                <button type="button" class="btn btn-secondary btn-lg btn-pre " id="prevToStep11">Back</button>
                <button type="submit" class="btn btn-lg btn-nxt " name="submit" id="submit" value="submit">Submit </button>
            </div>
        </div>
    </div>
    </div>
    </form>
    </div>
    <div class="progress">
        <div id="progressBar" class="progress-bar progress-bar-striped prg" role="progressbar" style="width: 10%;" aria-valuenow="33" aria-valuemin="0" aria-valuemax="100"></div>
    </div>
    <script>
        // Multi-step form logic
        let currentStep = 1; // Track the current step
        const totalSteps = 12; // Total number of steps

        function updateProgressBar() {
            const progressBar = document.getElementById('progressBar');
            const percentage = (currentStep / totalSteps) * 100; // Calculate percentage based on total steps
            progressBar.style.width = percentage + '%';
            progressBar.setAttribute('aria-valuenow', percentage);
        }

        // Event listener for next buttons
        for (let step = 1; step < totalSteps; step++) {
            document.getElementById(`nextToStep${step + 1}`).addEventListener('click', function() {
                document.getElementById(`step${step}`).classList.remove('active');
                document.getElementById(`step${step + 1}`).classList.add('active');
                currentStep++;
                updateProgressBar();
            });
        }

        // Event listener for previous buttons
        for (let step = 2; step <= totalSteps; step++) {
            document.getElementById(`prevToStep${step - 1}`).addEventListener('click', function() {
                document.getElementById(`step${step}`).classList.remove('active');
                document.getElementById(`step${step - 1}`).classList.add('active');
                currentStep--;
                updateProgressBar();
            });
        }
    </script>
    <script>
        // Get the file input element
        const fileInput = document.getElementById('fileInput');
        const fileNameDiv = document.getElementById('fileName');

        // Listen for file selection
        fileInput.addEventListener('change', function(event) {
            const file = event.target.files[0]; // Get the first file
            if (file) {
                fileNameDiv.textContent = file.name; // Display the file name
            } else {
                fileNameDiv.textContent = "No file chosen"; // Default text if no file is selected
            }
        });
    </script>
</body>

</html>