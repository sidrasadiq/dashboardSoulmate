<?php
session_start();

// Include required files
include 'layouts/config.php';
include 'layouts/session.php';  // Ensure session_start() is called here
include 'layouts/main.php';
include 'layouts/functions.php';

// Initialize $profile as an empty array
$profile = [];

// Check if user is logged in by verifying session user_id
if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id']; // Get the user ID from the session

    // Ensure the database connection is established
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // Initialize arrays for countries, cities, and states
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

        // Fetch cities
        $queryCities = "SELECT id, city_name FROM cities ORDER BY id ASC;";
        $stmtCities = $conn->prepare($queryCities);
        $stmtCities->execute();
        $resultCities = $stmtCities->get_result();

        while ($row = $resultCities->fetch_assoc()) {
            $cities[] = $row;
        }

        // Fetch states
        $queryStates = "SELECT id, state_name FROM states ORDER BY id ASC;";
        $stmtStates = $conn->prepare($queryStates);
        $stmtStates->execute();
        $resultStates = $stmtStates->get_result();

        while ($row = $resultStates->fetch_assoc()) {
            $states[] = $row;
        }

        // Commit transaction
        $conn->commit();
    } catch (Exception $e) {
        // Rollback the transaction on error
        $conn->rollback();
        $_SESSION['message'][] = array("type" => "error", "content" => "Error: " . $e->getMessage());
        header("location: complete-profile.php");
        exit();
    }
} else {
    echo "User is not logged in.";
}
?>







<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <title>User Panel</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .cardby {
            height: 350px !important;
            /* Ensures uniform card height */
            padding: 20px;
            /* Light background for better contrast */
        }



        .card-body {
            flex-grow: 1;
            /* Allows the body to fill the remaining space */
            padding: 15px;
            text-align: start;
            /* Aligns text to the left */
        }

        .card-title {
            font-size: 1.35rem;
            font-weight: bold;
            color: #343a40;
        }

        .card-text {
            color: #6c757d;
        }
    </style>
</head>

<body class="bg-light ">
    <?php include 'userlayout/header.php'; ?>

    <div class="container-fluid p-5 pt-5 main">
        <div class="row">
            <div class="col-md-3 p-md-1 mb-md-3 ">
                <a href="#" class="d-block text-decoration-none ">
                    <img src="<?php echo $_SESSION['profile_picture']; ?>" alt="User" width="132" height="132" class="rounded-circle ">
                </a>
            </div>

            <div class="col-md-4 prof-con mt-md-3 mt-sm-4">

                <h5 class="mt-4">
                    <?php echo $_SESSION['username']; ?>
                </h5>
                <button type="submit " class="btn btn-comp-prof"> Next Step: Complete your personality profile</button>
                <p class="mt-2">Learn about membership features</p>
                <div class=" emoji">
                    <i class="bi bi-hand-thumbs-up"></i>
                    <i class="bi bi-emoji-smile"></i>
                    <i class="bi bi-chat"></i>
                    <i class="bi bi-eye-slash"></i>
                    <i class="bi bi-star"></i>
                    <i class="bi bi-heart"></i>
                    <i class="bi bi-unlock"></i>

                </div>

            </div>

            <div class="col-md-5 d-flex justify-content-between align-items-center prog-con">
                <!-- Circular Progress Bars -->
                <div class="progress-circle" data-percentage="75">
                    <svg viewBox="0 0 36 36" class="circular-chart">
                        <path class="circle-bg" d="M18 2.0845a 15.9155 15.9155 0 1 0 0 31.831 a 15.9155 15.9155 0 1 0 0 -31.831" />
                        <path class="circle" d="M18 2.0845a 15.9155 15.9155 0 1 0 0 31.831 a 15.9155 15.9155 0 1 0 0 -31.831" />
                    </svg>
                    <span class="percentage">75%</span>
                </div>
                <div class="progress-circle" data-percentage="60">
                    <svg viewBox="0 0 36 36" class="circular-chart">
                        <path class="circle-bg" d="M18 2.0845a 15.9155 15.9155 0 1 0 0 31.831 a 15.9155 15.9155 0 1 0 0 -31.831" />
                        <path class="circle" d="M18 2.0845a 15.9155 15.9155 0 1 0 0 31.831 a 15.9155 15.9155 0 1 0 0 -31.831" />
                    </svg>
                    <span class="percentage">60%</span>
                </div>
                <div class="progress-circle" data-percentage="85">
                    <svg viewBox="0 0 36 36" class="circular-chart">
                        <path class="circle-bg" d="M18 2.0845a 15.9155 15.9155 0 1 0 0 31.831 a 15.9155 15.9155 0 1 0 0 -31.831" />
                        <path class="circle" d="M18 2.0845a 15.9155 15.9155 0 1 0 0 31.831 a 15.9155 15.9155 0 1 0 0 -31.831" />
                    </svg>
                    <span class="percentage">85%</span>
                </div>
                <div class="progress-circle" data-percentage="40">
                    <svg viewBox="0 0 36 36" class="circular-chart">
                        <path class="circle-bg" d="M18 2.0845a 15.9155 15.9155 0 1 0 0 31.831 a 15.9155 15.9155 0 1 0 0 -31.831" />
                        <path class="circle" d="M18 2.0845a 15.9155 15.9155 0 1 0 0 31.831 a 15.9155 15.9155 0 1 0 0 -31.831" />
                    </svg>
                    <span class="percentage">40%</span>
                </div>
            </div>

        </div>

    </div>
    <!-- search section start -->
    <div class="container-fluid">
        <form action="search-result.php" method="POST">

            <div class="card search-card border-0 shadow">
                <div class="card-body">
                    <div class="row align-items-center">
                        <!-- Seeking -->
                        <div class="col-md-2">
                            <label for="seeking" class="fw-bold">Seeking</label>
                            <select class="form-select  custom-border" id="seeking" aria-label="Default select example" name="seeking">
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                                <option value="any" selected>Any</option>
                            </select>
                        </div>

                        <!-- Age -->
                        <div class="col-md-1">
                            <label for="age" class="fw-bold">Age</label>
                            <select class="form-select  custom-border" id="age" aria-label="Default select example" name="age">
                                <option value="" selected></option>
                                <option value="18">18</option>
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

                        <!-- Country -->
                        <div class="col-md-2">
                            <label for="country" class="fw-bold">Country</label>
                            <select class="form-select custom-border" id="country" name="country">
                                <option value="any" selected>Any</option>
                                <?php foreach ($countries as $country): ?>
                                    <option value="<?php echo $country['id']; ?>"><?php echo $country['country_name']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- State/Province -->
                        <div class="col-md-2">
                            <label for="state" class="fw-bold">State/Province</label>
                            <select class="form-select  custom-border" id="state" aria-label="Default select example" name="state">
                                <option value="any" selected>Any</option>
                                <?php foreach ($states as $state): ?>
                                    <option value="<?php echo $state['id']; ?>"><?php echo $state['state_name']; ?></option>
                                    <?php endforeach; ?>>
                            </select>
                        </div>

                        <!-- City -->
                        <div class="col-md-2">
                            <label for="city" class="fw-bold">City</label>
                            <select class="form-select custom-border" id="city" aria-label="Default select example" name="city">
                                <option value="any" selected>Any</option>
                                <?php foreach ($cities as $city): ?>
                                    <option value="<?php echo $city['id']; ?>"><?php echo $city['city_name']; ?></option>
                                    <?php endforeach; ?>>
                            </select>
                        </div>

                        <!-- Within -->
                        <div class="col-md-2 ">
                            <label for="within" class="fw-bold">Within</label>
                            <input type="text" class="form-control  custom-border " id="within" placeholder="   - km" name="within">
                        </div>

                        <!-- Search Button -->
                        <div class="col-md-1 ">
                            <button type="submit" name="submit" value="submit" class="btn btn-search w-100 shadow ">Search</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Search Section Start -->

    <div class="container mt-4">
        <div class="row">
            <?php
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
            WHERE users.id != $userId";

            $result = mysqli_query($conn, $query);

            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    // Wrap the entire card in an anchor tag to make it clickable
                    $profileLink = 'showprofile.php?id=' . $row['id']; // Adjust this URL as needed
                    echo '<div class="col-md-3 mb-4">';
                    echo '<a href="' . $profileLink . '" target="_blank" class="text-decoration-none">'; // Make the card clickable and open in a new tab

                    echo '<div class="card h-100 shadow-sm border-0">';

                    // Set profile picture with a fallback
                    $profilePicture = $row['profile_picture'] ?: 'placeholder.jpg';
                    echo "<img src='uploads/{$profilePicture}' class='card-img-top' alt='Profile Picture' style='height: 200px; object-fit: cover;'>";

                    echo '<div class="card-body">';
                    echo '<div>';

                    // Display username
                    echo "<h4 class='card-title'>" . htmlspecialchars($row['username']) . "</h4>";

                    // Display date of birth, city, and country
                    echo '<p class="card-text text-muted">';
                    echo isset($row['date_of_birth']) && !empty($row['date_of_birth'])
                        ? htmlspecialchars((new DateTime())->diff(new DateTime($row['date_of_birth']))->y . " . " . $row['city_name'] . ", " . $row['country_name'])
                        : 'No data available';
                    echo '</p>';

                    // Display religion
                    echo "<h5 class='card-text'>" . htmlspecialchars($row['religion_name']) . "</h5>";

                    // Display what the user is seeking and age preferences
                    echo "<p class='card-text'><small class='text-muted'>Seeking: ";
                    echo htmlspecialchars($row['looking_for'] . " " . $row['prefer_age_from'] . "-" . $row['prefer_age_to']);
                    echo "</small></p>";

                    echo '</div>'; // End of the div wrapping username, religion, etc.

                    // Display bio
                    echo "<p class='card-text'><strong><small class='text-muted'>" . htmlspecialchars($row['bio']) . "</small></strong></p>";

                    echo '</div>'; // End of card body

                    echo '<div class="card-footer border-0 bg-transparent text-start">';
                    echo '<div>';
                    echo '<i class="bi bi-heart-fill p-2 text-muted fs-3"></i>'; // Adjust the size using fs-3
                    echo '<i class="bi bi-chat-fill p-2 text-muted fs-3"></i>';
                    echo '<i class="bi bi-gift-fill p-2 text-muted fs-3"></i>';
                    echo '<i class="bi bi-camera-fill p-2 text-muted fs-3"></i>';
                    echo '</div>';
                    echo '</div>'; // End of card footer

                    echo '</div>'; // End of card
                    echo '</a>'; // Close the anchor tag
                    echo '</div>'; // End of col-md-3
                }
            } else {
                echo '<p>No profiles found.</p>';
            }
            ?>
        </div> <!-- End of row -->
    </div> <!-- End of container -->


    <!-- Search Section End -->


    <?php include 'userlayout/footer.php'; ?>

    <script>
        document.querySelectorAll('.progress-circle').forEach(el => {
            const percentage = el.getAttribute('data-percentage');
            const circle = el.querySelector('.circle');
            const radius = 15.9155;
            const circumference = 2 * Math.PI * radius;
            const offset = circumference - (percentage / 100) * circumference;

            circle.style.strokeDasharray = `${circumference} ${circumference}`;
            circle.style.strokeDashoffset = offset;

            el.querySelector('.percentage').textContent = `${percentage}%`;
        });
    </script>

    <!-- Add Bootstrap JavaScript bundle with Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>
</body>

</html>