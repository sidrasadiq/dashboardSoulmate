 <?php
    include 'userlayout/header.php';
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

            // Check if form is submitted
            if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
                // Collect form data
                $seeking = mysqli_real_escape_string($conn, $_POST['seeking']);
                $age = mysqli_real_escape_string($conn, $_POST['age']);
                $country = mysqli_real_escape_string($conn, $_POST['country']);
                $state = mysqli_real_escape_string($conn, $_POST['state']);
                $city = mysqli_real_escape_string($conn, $_POST['city']);
                $within = mysqli_real_escape_string($conn, $_POST['within']);

                // Start building the query
                $query = "SELECT 
        profiles.*, 
        countries.country_name, 
        cities.city_name,
        states.state_name,
        users.username,
        users.email,
        nationality.nationality_name,
        religion.religion_name,
        qualifications.qualification_name
      FROM 
        profiles
      JOIN users ON profiles.user_id = users.id
      LEFT JOIN countries ON profiles.country_id = countries.id
      LEFT JOIN cities ON profiles.city_id = cities.id
      LEFT JOIN states ON profiles.state_id = states.id
      LEFT JOIN nationality ON profiles.nationality_id = nationality.id
      LEFT JOIN religion ON profiles.religion_id = religion.id
      LEFT JOIN qualifications ON profiles.qualification_id = qualifications.id
      WHERE users.id != ?";

                // Filters
                $filters = [];
                $params = [$userId]; // Always exclude the logged-in user

                // Gender filter
                if (!empty($seeking) && $seeking !== 'any') {
                    $filters[] = "profiles.gender = ?";
                    $params[] = $seeking;
                }

                // Age filter
                // Age filter
                if (!empty($age)) {
                    // Add the age condition to the filters array
                    $filters[] = "(YEAR(CURDATE()) - YEAR(profiles.date_of_birth)) - 
                  (DATE_FORMAT(profiles.date_of_birth, '%m-%d') > DATE_FORMAT(CURDATE(), '%m-%d')) = ?";
                    $params[] = $age;  // Ensure $age is a single value, not an array
                }


                // Country filter
                if (
                    !empty($country) && $country !== 'any'
                ) {
                    $filters[] = "countries.id = ?";
                    $params[] = $country;
                }

                // State filter
                if (
                    !empty($state) && $state !== 'any'
                ) {
                    $filters[] = "states.id = ?";
                    $params[] = $state;
                }

                // City filter
                if (
                    !empty($city) && $city !== 'any'
                ) {
                    $filters[] = "cities.id = ?";
                    $params[] = $city;
                }

                // Combine filters
                if (!empty($filters)) {
                    $query .= " AND " . implode(" AND ", $filters);
                }

                // Add ordering
                $query .= " ORDER BY profiles.user_id";

                // Prepare and execute the query
                $stmt = $conn->prepare($query);

                if ($stmt) {
                    $types = str_repeat('s', count($params)); // Adjust for parameter types
                    $stmt->bind_param($types, ...$params);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    // Display results
                    if ($result && $result->num_rows > 0) {
                        echo '<div class="container mt-4">';
                        echo '<div class="row">';
                        while ($row = $result->fetch_assoc()) {

                            // Wrap the entire card in an anchor tag to make it clickable
                            $profileLink = 'showprofile.php?id=' . $row['id']; // Adjust this URL as needed
                            echo '<div class="col-md-3 mb-4">';
                            echo '<a href="' . $profileLink . '" target="_blank" class="text-decoration-none">'; // Make the card clickable and open in a new tab

                            echo '<div class="card h-100">';

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
                                ? htmlspecialchars((new DateTime())->diff(new DateTime($row['date_of_birth']))->y . " . " . $row['city_name'] . "," . $row['state_name'] . ", " . $row['country_name'])
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

                            // Display icons (e.g., heart, chat, gift, camera)

                            echo '</div>'; // End of card body
                            echo '<div class="card-footer border-0 bg-transparent text-start">';

                            // Add the 'View Profile' button (if needed)
                            echo '<div>';
                            echo '<i class="bi bi-heart-fill p-2 text-muted fs-3"></i>'; // Adjust the size using fs-3
                            echo '<i class="bi bi-chat-fill p-2 text-muted fs-3"></i>';
                            echo '<i class="bi bi-gift-fill p-2 text-muted fs-3"></i>';
                            echo '<i class="bi bi-camera-fill p-2 text-muted fs-3"></i>';
                            echo '</div>';
                            echo '</div>'; // End of card footer
                            echo '</div>'; // End of card
                            echo '</a>'; // Close the anchor tag
                            echo '</div>'; // End of col-md-4
                        }
                        echo '</div>'; // End of row
                        echo '</div>'; // End of container
                    } else {
                        echo '<div class="alert alert-warning">No results found based on your filters.</div>';
                    }
                    echo "</a>";
                } else {
                    echo "Query preparation failed: " . $conn->error;
                }
            } else {
                echo "Query preparation failed: " . $conn->error;
            }
        } catch (Exception $e) {
            // Rollback the transaction on error
            $conn->rollback();
            $_SESSION['message'][] = array("type" => "error", "content" => "Error: " . $e->getMessage());
            header("location: complete-profile.php");
            exit();
        } finally {
            // Close the connection
            $conn->close();
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
         .card {
             height: 500px !important;
             /* Fixed height for uniformity */
             display: flex;
             flex-direction: column;
             justify-content: space-between;
             /* Ensures proper spacing between elements */
             box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
             /* Adds a subtle shadow for better visuals */
         }

         .card-img-top {
             height: 200px;
             /* Fixed height for the image */
             object-fit: cover;
             /* Ensures the image fits the designated space */
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



         @media (max-width: 768px) {
             .card {
                 height: auto;
                 /* Allows flexibility on smaller screens */
             }
         }
     </style>
 </head>

 <body>

     <!-- Add Bootstrap JavaScript bundle with Popper.js -->
     <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
     <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>
 </body>

 </html>
 </body>
 <?php include 'userlayout/footer.php'; ?>