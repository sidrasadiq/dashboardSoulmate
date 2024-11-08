<?php
include 'layouts/session.php';
include 'layouts/main.php';
include 'layouts/config.php';
include 'layouts/functions.php';
?>

<head>
    <title>Nationality | Soulmate.com.pk</title>
    <?php include 'layouts/title-meta.php'; ?>

    <?php include 'layouts/head-css.php'; ?>
    <style></style>
    <script>
        function confirmDelete() {
            return confirm("Are you sure you want to delete this record?");
        }
    </script>

</head>
<?php

try {
    // Step 1: Start a transaction
    $conn->begin_transaction();

    // Step 2: Get the cast ID from the URL and validate it
    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
        $castId = intval($_GET['id']); // Ensure it's an integer
    } else {
        throw new Exception("Invalid or missing ID.");
    }

    // Step 3: Prepare SQL to fetch cast data
    $sql = "SELECT id, cast_name, description, is_active, created_by, created_at, updated_by, updated_at FROM user_cast WHERE id = ?";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        throw new Exception("Failed to prepare statement: " . $conn->error);
    }

    // Step 4: Bind the ID parameter to prevent SQL injection
    $stmt->bind_param("i", $castId);

    // Step 5: Execute the query
    $stmt->execute();
    $result = $stmt->get_result();

    // Step 6: Check if data was found and fetch it
    if ($result->num_rows > 0) {
        $castData = $result->fetch_assoc();
    } else {
        throw new Exception("Cast data not found for ID: " . $castId);
    }

    // Step 7: Commit the transaction as data retrieval was successful
    $conn->commit();

    // Close statement and result set
    $stmt->close();
    $result->free();
} catch (Exception $e) {
    // Step 8: Roll back the transaction in case of any error
    $conn->rollback();

    // Store error message in session or handle it accordingly
    $_SESSION['message'][] = array("type" => "danger", "content" => "Error: " . $e->getMessage());

    // Redirect or handle the error as required (e.g., redirect to an error page or show a message)
    header("location: manage-cast.php");
    exit();
} finally {
    // Optionally, close the database connection if done
    // $conn->close();

}

?>


<body>
    <div class="wrapper">
        <?php include 'layouts/menu.php'; ?>
        <div class="content-page">
            <div class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="page-title-box">
                                <div class="page-title-right">
                                    <ol class="breadcrumb m-0">
                                        <li class="breadcrumb-item"><a href="javascript: void(0);">soulmate.com.pk</a></li>
                                        <li class="breadcrumb-item"><a href="javascript: void(0);">Cast</a></li>
                                        <li class="breadcrumb-item active">Edit Cast</li>
                                    </ol>
                                </div>
                                <h4 class="page-title">Edit Cast</h4>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <?php displaySessionMessage(); ?>
                        <h2 class="text-center">Edit Cast</h2>
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <p class="text-muted fs-14"> </p>
                                    <div class="row">
                                        <div>

                                            <form action="<?php echo htmlspecialchars("manage-cast.php"); ?>" method="post" class="needs-validation" novalidate enctype="multipart/form-data">
                                                <div class="row mb-3">
                                                    <h3>Edit Cast Information</h3>

                                                    <!-- Hidden input to hold the cast ID -->
                                                    <input type="hidden" name="castId" value="<?php echo htmlspecialchars($castId); ?>">

                                                    <div class="col-lg-6">
                                                        <div class="mb-3">
                                                            <label for="castName" class="form-label">Cast Name</label>
                                                            <input type="text" id="castName" name="castName" class="form-control" required placeholder="Enter the name of the Cast" value="<?php echo htmlspecialchars($castData['cast_name']); ?>">
                                                            <div class="valid-feedback">Looks good!</div>
                                                            <div class="invalid-feedback">Please fill this field.</div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-6">
                                                        <div class="mb-3">
                                                            <label for="castDescription" class="form-label">Cast Description</label>
                                                            <input type="text" id="castDescription" name="castDescription" class="form-control" placeholder="Enter the Cast Description" value="<?php echo htmlspecialchars($castData['description']); ?>">
                                                            <div class="valid-feedback">Looks good!</div>
                                                            <div class="invalid-feedback">Please fill this field.</div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-6">
                                                        <div class="mb-2">
                                                            <label for="castStatus" class="form-label">Cast Status *</label>
                                                            <select id="castStatus" name="castStatus" class="form-select select2" data-toggle="select2" required>
                                                                <option value="1" <?php echo $castData['is_active'] == 1 ? 'selected' : ''; ?>>Active</option>
                                                                <option value="0" <?php echo $castData['is_active'] == 0 ? 'selected' : ''; ?>>Inactive</option>
                                                            </select>
                                                            <div class="valid-feedback">Looks good!</div>
                                                            <div class="invalid-feedback">Please select a status.</div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row mb-3">
                                                    <div class="col-lg-12 text-center">
                                                        <button type="submit" id="btnUpdateCast" name="btnUpdateCast" class="btn btn-primary">Update Cast</button>
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
            <?php include 'layouts/footer.php'; ?>
        </div>
    </div>
    <?php include 'layouts/right-sidebar.php'; ?>
    <?php include 'layouts/footer-scripts.php'; ?>
    <script src="assets/js/app.min.js"></script>

    <script>
        <?php
        if (isset($_SESSION['message'])) {
            foreach ($_SESSION['message'] as $message) {
                echo "toastr." . $message['type'] . "('" . $message['content'] . "');";
            }
            unset($_SESSION['message']); // Clear messages after displaying
        }
        ?>
    </script>

</body>

</html>