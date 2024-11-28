<?php
session_start();
include 'layouts/config.php';
include 'layouts/session.php';
include 'layouts/main.php';
include 'layouts/functions.php';

// Check if user is logged in, if not redirect to login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get the user ID from the session
$user_id = $_SESSION['user_id'];

// Fetch the user's existing profile if available
$get_profile_query = "SELECT * FROM personality_profile WHERE created_by = ?";
$stmt = $conn->prepare($get_profile_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Check if a profile exists for the user
if ($result->num_rows > 0) {
    $profile = $result->fetch_assoc();
} else {
    $profile = [];
}

// Pre-fill the form fields with the existing data (if available)
$favorite_movie = $profile['favorite_movie'] ?? '';
$favorite_book = $profile['favorite_book'] ?? '';
$sort_of_music = $profile['sort_of_music'] ?? '';
$hobbies = $profile['hobbies'] ?? '';
$dress_sense = $profile['dress_sense'] ?? '';
$sense_of_humor = $profile['sense_of_humor'] ?? '';
$describe_personality = $profile['describe_personality'] ?? '';
$like_to_travel = $profile['like_to_travel'] ?? '';
$partner_diff_culture = $profile['partner_diff_culture'] ?? '';
$spend_weekend = $profile['spend_weekend'] ?? '';
$perfect_match = $profile['perfect_match'] ?? '';

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submit"])) {
    // Get the data from the form
    $favorite_movie = $_POST['favorite_movie'];
    $favorite_book = $_POST['favorite_book'];
    $sort_of_music = $_POST['sort_of_music'];
    $hobbies = $_POST['hobbies'];
    $dress_sense = $_POST['dress_sense'];
    $sense_of_humor = $_POST['sense_of_humor'];
    $describe_personality = $_POST['describe_personality'];
    $like_to_travel = $_POST['like_to_travel'];
    $partner_diff_culture = $_POST['partner_diff_culture'];
    $spend_weekend = $_POST['spend_weekend'];
    $perfect_match = $_POST['perfect_match'];

    // Check if all fields are filled
    $is_complete = (
        !empty($favorite_movie) &&
        !empty($favorite_book) &&
        !empty($sort_of_music) &&
        !empty($hobbies) &&
        !empty($dress_sense) &&
        !empty($sense_of_humor) &&
        !empty($describe_personality) &&
        !empty($like_to_travel) &&
        !empty($partner_diff_culture) &&
        !empty($spend_weekend) &&
        !empty($perfect_match)
    ) ? 1 : 0;

    // Check if the user already has a personality profile in the database
    $check_query = "SELECT id FROM personality_profile WHERE created_by = ?";
    $stmt = $conn->prepare($check_query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // If a profile exists, update it
        $existing_profile = $result->fetch_assoc();

        $update_query = "UPDATE personality_profile 
                         SET favorite_movie = ?, favorite_book = ?, sort_of_music = ?, hobbies = ?, 
                             dress_sense = ?, sense_of_humor = ?, describe_personality = ?, like_to_travel = ?, 
                             partner_diff_culture = ?, spend_weekend = ?, perfect_match = ?, is_complete = ?, 
                             updated_by = ?, updated_at = NOW() 
                         WHERE id = ? AND user_id = ?";

        $stmt = $conn->prepare($update_query);
        $stmt->bind_param(
            "sssssssssssiiii",
            $favorite_movie,
            $favorite_book,
            $sort_of_music,
            $hobbies,
            $dress_sense,
            $sense_of_humor,
            $describe_personality,
            $like_to_travel,
            $partner_diff_culture,
            $spend_weekend,
            $perfect_match,
            $is_complete,
            $user_id,                  // updated_by
            $existing_profile['id'],  // id of the profile
            $user_id                   // user_id
        );
        $stmt->execute();

        $_SESSION['message'] = "Personality Profile updated successfully!";
    } else {
        // If no profile exists, insert a new record
        $insert_query = "INSERT INTO personality_profile (favorite_movie, favorite_book, sort_of_music, hobbies, 
                         dress_sense, sense_of_humor, describe_personality, like_to_travel, partner_diff_culture, 
                         spend_weekend, perfect_match, is_complete, created_by, user_id, created_at) 
                         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";

        $stmt = $conn->prepare($insert_query);
        $stmt->bind_param(
            "sssssssssssiii",
            $favorite_movie,
            $favorite_book,
            $sort_of_music,
            $hobbies,
            $dress_sense,
            $sense_of_humor,
            $describe_personality,
            $like_to_travel,
            $partner_diff_culture,
            $spend_weekend,
            $perfect_match,
            $is_complete,
            $user_id,  // created_by
            $user_id   // user_id
        );
        $stmt->execute();

        $_SESSION['message'] = "Personality Profile created successfully!";
    }

    // Redirect based on completeness
    if ($is_complete) {
        header("Location: user_index.php"); // Redirect if all fields are complete
    } else {
        header("Location: editPersonalityInfo.php"); // Redirect back to edit page if incomplete
    }
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
        .container {
            margin-left: 13px;
        }
    </style>
</head>
<?php include 'userlayout/header.php'; ?>

<body class="bg-light w-100">
    <!-- container start -->
    <div class="container">
        <h3 class=" text-muted mt-5">Edit Personality Profile</h3>
        <div class="max-width-3">
            Let your personality shine. Express yourself in your own words to give other users a better understanding of <br> who you are.
        </div>
        <div class="col-lg-10">
            <?php
            // Display success message
            if (isset($_SESSION['message'])) {
                echo '<div class="alert alert-success mt-3" role="alert">' . $_SESSION['message'] . '</div>';
                unset($_SESSION['message']);  // Clear message after displaying it
            }
            ?>
        </div>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" class="needs-validation" novalidate enctype="multipart/form-data">
            <!-- favorite movie -->
            <div class="col-lg-10">
                <div class="mb-3 mt-5 text-muted">
                    <label for="favorite_movie" class="form-label">What is your favorite movie?</label>
                    <textarea id="favorite_movie" name="favorite_movie" class="form-control border-secondary-subtle" rows="2"
                        class="form-control border-secondary-subtle "
                        rows="2"
                        style="resize: none; overflow-y: auto;"><?php echo htmlspecialchars($favorite_movie); ?></textarea>
                    <div class="valid-feedback">Looks good!</div>
                    <div class="invalid-feedback">Please fill this field.</div>
                </div>
            </div>

            <!-- favorite book -->
            <div class="col-lg-10">
                <div class="mb-2 mt-5 text-muted">
                    <label for="favorite_book" class="form-label">What is your favorite book?</label>
                    <textarea id="favorite_book" name="favorite_book" class="form-control border-secondary-subtle" rows="2"
                        class="form-control border-secondary-subtle "
                        rows="2"
                        style="resize: none; overflow-y: auto;"><?php echo htmlspecialchars($favorite_book); ?></textarea>
                    <div class="valid-feedback">Looks good!</div>
                    <div class="invalid-feedback">Please fill this field.</div>
                </div>
            </div>

            <!-- Music preferences -->
            <div class="col-lg-10">
                <div class="mb-3 mt-5 text-muted">
                    <label for="sort_of_music" class="form-label">What sort of music do you like?</label>
                    <textarea id="sort_of_music" name="sort_of_music" class="form-control border-secondary-subtle" rows="2"
                        class="form-control border-secondary-subtle "
                        rows="2"
                        style="resize: none; overflow-y: auto;"><?php echo htmlspecialchars($sort_of_music); ?></textarea>
                    <div class="valid-feedback">Looks good!</div>
                    <div class="invalid-feedback">Please fill this field.</div>
                </div>
            </div>

            <!-- Hobbies -->
            <div class="col-lg-10">
                <div class="mb-3 mt-5 text-muted">
                    <label for="hobbies" class="form-label">What are your hobbies and interests?</label>
                    <textarea id="hobbies" name="hobbies" class="form-control border-secondary-subtle" rows="2" class="form-control border-secondary-subtle "
                        rows="2"
                        style="resize: none; overflow-y: auto;"><?php echo htmlspecialchars($hobbies); ?></textarea>
                    <div class="valid-feedback">Looks good!</div>
                    <div class="invalid-feedback">Please fill this field.</div>
                </div>
            </div>

            <!-- Dress Sense -->
            <div class="col-lg-10">
                <div class="mb-3 mt-5 text-muted">
                    <label for="dress_sense" class="form-label">How would you describe your dress sense?</label>
                    <textarea id="dress_sense" name="dress_sense" class="form-control border-secondary-subtle" rows="2" class="form-control border-secondary-subtle "
                        rows="2"
                        style="resize: none; overflow-y: auto;"><?php echo htmlspecialchars($dress_sense); ?></textarea>
                    <div class="valid-feedback">Looks good!</div>
                    <div class="invalid-feedback">Please fill this field.</div>
                </div>
            </div>

            <!-- Sense of Humor -->
            <div class="col-lg-10">
                <div class="mb-3 mt-5 text-muted">
                    <label for="sense_of_humor" class="form-label">How would you describe your sense of humor?</label>
                    <textarea id="sense_of_humor" name="sense_of_humor" class="form-control border-secondary-subtle" rows="2" class="form-control border-secondary-subtle "
                        rows="2"
                        style="resize: none; overflow-y: auto;"><?php echo htmlspecialchars($sense_of_humor); ?></textarea>
                    <div class="valid-feedback">Looks good!</div>
                    <div class="invalid-feedback">Please fill this field.</div>
                </div>
            </div>

            <!-- Personality Description -->
            <div class="col-lg-10">
                <div class="mb-3 mt-5 text-muted">
                    <label for="describe_personality" class="form-label">How would you describe your personality?</label>
                    <textarea id="describe_personality" name="describe_personality" class="form-control border-secondary-subtle" rows="2" class="form-control border-secondary-subtle "
                        rows="2"
                        style="resize: none; overflow-y: auto;"><?php echo htmlspecialchars($describe_personality); ?></textarea>
                    <div class="valid-feedback">Looks good!</div>
                    <div class="invalid-feedback">Please fill this field.</div>
                </div>
            </div>

            <!-- Preferences -->
            <div class="col-lg-10">
                <div class="mb-3 mt-5 text-muted">
                    <label for="like_to_travel" class="form-label">Do you like to travel?</label>
                    <textarea id="like_to_travel" name="like_to_travel" class="form-control border-secondary-subtle" rows="2" class="form-control border-secondary-subtle "
                        rows="2"
                        style="resize: none; overflow-y: auto;"><?php echo htmlspecialchars($like_to_travel); ?></textarea>
                    <div class="valid-feedback">Looks good!</div>
                    <div class="invalid-feedback">Please fill this field.</div>
                </div>
            </div>

            <!-- Cultural Compatibility -->
            <div class="col-lg-10">
                <div class="mb-3 mt-5 text-muted">
                    <label for="partner_diff_culture" class="form-label">Would you be open to a partner from a different culture?</label>
                    <textarea id="partner_diff_culture" name="partner_diff_culture" class="form-control border-secondary-subtle" rows="2" class="form-control border-secondary-subtle "
                        rows="2"
                        style="resize: none; overflow-y: auto;"><?php echo htmlspecialchars($partner_diff_culture); ?></textarea>
                    <div class="valid-feedback">Looks good!</div>
                    <div class="invalid-feedback">Please fill this field.</div>
                </div>
            </div>

            <!-- Weekend Preferences -->
            <div class="col-lg-10">
                <div class="mb-3 mt-5 text-muted">
                    <label for="spend_weekend" class="form-label">How do you like to spend your weekends?</label>
                    <textarea id="spend_weekend" name="spend_weekend" class="form-control border-secondary-subtle" rows="2" class="form-control border-secondary-subtle "
                        rows="2"
                        style="resize: none; overflow-y: auto;"><?php echo htmlspecialchars($spend_weekend); ?></textarea>
                    <div class="valid-feedback">Looks good!</div>
                    <div class="invalid-feedback">Please fill this field.</div>
                </div>
            </div>

            <!-- Ideal Match -->
            <div class="col-lg-10">
                <div class="mb-3 mt-5 text-muted">
                    <label for="perfect_match" class="form-label">What is your idea of a perfect match?</label>
                    <textarea id="perfect_match" name="perfect_match" class="form-control border-secondary-subtle" rows="2"
                        class="form-control border-secondary-subtle "
                        rows="2"
                        style="resize: none; overflow-y: auto;"><?php echo htmlspecialchars($perfect_match); ?></textarea>
                    <div class="valid-feedback">Looks good!</div>
                    <div class="invalid-feedback">Please fill this field.</div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="col-lg-10 text-center">
                <div class="mb-3 mt-5 text-muted">
                    <button type="submit" name="submit" class="btn btn-primary mt-4 mb-5">Submit</button>
                </div>
            </div>
        </form>
    </div>
    <!-- container end -->

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
<?php include 'userlayout/footer.php';

?>