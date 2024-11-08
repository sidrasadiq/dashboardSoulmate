<?php
include 'layouts/session.php';
include 'layouts/main.php';
include 'layouts/config.php';
include 'layouts/functions.php';
?>

<head>
    <title>Cast | Soulmate.com.pk</title>
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

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["btnSaveCast"])) {
    // Step 1: Retrieve and sanitize form data
    $castName = trim($_POST['castName']);
    $castDescription = trim($_POST['castDescription']);
    $castStatus = intval($_POST['castStatus']);
    $createdAt = date("Y-m-d H:i:s");
    $updatedAt = date("Y-m-d H:i:s");

    // Retrieve the user ID from session (assuming user ID is stored in session)
    $userId = $_SESSION['user_id'] ?? null;

    // Check if the user ID is available
    if (!$userId) {
        $_SESSION['message'][] = array("type" => "danger", "content" => "Error: User not logged in.");
        header("location: manage-cast.php");
        exit();
    }

    try {
        // Step 2: Begin a database transaction
        $conn->begin_transaction();

        // Step 3: Prepare SQL statement to insert data into user_casts table
        $sql = "INSERT INTO user_cast 
                (cast_name, description, created_at, updated_at, created_by, updated_by, is_active)
                VALUES (?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Failed to prepare statement: " . $conn->error);
        }

        // Step 4: Bind parameters for security
        $stmt->bind_param(
            "sssiiii",
            $castName,
            $castDescription,
            $createdAt,
            $updatedAt,
            $userId,  // Created by user ID
            $userId,  // Updated by user ID (same on initial insert)
            $castStatus
        );

        // Step 5: Execute the statement
        if ($stmt->execute()) {
            // Commit transaction if the statement is executed successfully
            $conn->commit();
            $_SESSION['message'][] = array("type" => "success", "content" => "Cast data saved successfully!");
        } else {
            throw new Exception("Failed to save the data. " . $stmt->error);
        }

        // Close the statement
        $stmt->close();
    } catch (Exception $e) {
        // Step 6: Rollback transaction on error
        $conn->rollback();

        // Store error message in session
        $_SESSION['message'][] = array("type" => "danger", "content" => "Error: " . $e->getMessage());
    } finally {
        // Step 7: Redirect to manage cast page
        header("location: manage-cast.php");
        exit(); // Ensure script execution stops after redirection
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["btnUpdateCast"])) {
    // Step 1: Retrieve and validate form data
    $castId = intval($_POST['castId']); // Ensure ID is an integer
    $castName = trim($_POST['castName']);
    $castDescription = trim($_POST['castDescription']);
    $castStatus = intval($_POST['castStatus']);
    $updatedBy = $_SESSION['user_id'] ?? null; // Ensure user_id is set
    $updatedAt = date("Y-m-d H:i:s");

    // Check if user ID is available in session
    if (!$updatedBy) {
        $_SESSION['message'][] = array("type" => "danger", "content" => "Error: User not logged in.");
        header("location: manage-cast.php");
        exit();
    }

    try {
        // Step 2: Start a database transaction
        $conn->begin_transaction();

        // Step 3: Prepare SQL for updating the cast data
        $sql = "UPDATE user_cast
                SET cast_name = ?, description = ?, is_active = ?, updated_by = ?, updated_at = ?
                WHERE id = ?";

        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Failed to prepare statement: " . $conn->error);
        }

        // Step 4: Bind parameters for security
        $stmt->bind_param(
            "ssiisi",
            $castName,               // cast_name (string)
            $castDescription,        // description (string)
            $castStatus,             // is_active (integer)
            $updatedBy,              // updated_by (integer)
            $updatedAt,              // updated_at (string, datetime format)
            $castId                  // id (integer)
        );

        // Step 5: Execute the update statement
        if ($stmt->execute()) {
            $_SESSION['message'][] = array("type" => "success", "content" => "Cast data updated successfully!");
            // Commit the transaction
            $conn->commit();
        } else {
            throw new Exception("Failed to update data: " . $stmt->error);
        }

        // Close the statement
        $stmt->close();
    } catch (Exception $e) {
        // Step 6: Rollback transaction in case of error
        $conn->rollback();

        // Store error message in session
        $_SESSION['message'][] = array("type" => "danger", "content" => "Error: " . $e->getMessage());
    } finally {
        // Step 7: Redirect back to manage cast page
        header("location: manage-cast.php");
        exit(); // Ensure script execution stops after redirection
    }
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
                                        <li class="breadcrumb-item active">Add Cast</li>
                                    </ol>
                                </div>
                                <h4 class="page-title">Add Cast</h4>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <?php displaySessionMessage(); ?>
                        <h2 class="text-center">Add Cast</h2>
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <p class="text-muted fs-14"> </p>
                                    <div class="row">
                                        <div>
                                            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="needs-validation" novalidate enctype="multipart/form-data">
                                                <div class="row mb-3">
                                                    <h3>Cast Information</h3>
                                                    <div class="col-lg-6">
                                                        <div class="mb-3">
                                                            <label for="castName" class="form-label">Cast Name</label>
                                                            <input type="text" id="castName" name="castName" class="form-control" required placeholder="Enter the name of the Cast">
                                                            <div class="valid-feedback">Looks good!</div>
                                                            <div class="invalid-feedback">Please enter the cast name.</div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <div class="mb-3">
                                                            <label for="castDescription" class="form-label">Cast Description</label>
                                                            <input type="text" id="castDescription" name="castDescription" class="form-control" placeholder="Enter the Cast Description">
                                                            <div class="valid-feedback">Looks good!</div>
                                                            <div class="invalid-feedback">Please enter the cast description.</div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <div class="mb-2">
                                                            <label for="castStatus" class="form-label">Cast Status *</label>
                                                            <select id="castStatus" name="castStatus" class="form-select select2" data-toggle="select2" required>
                                                                <option value="1">Active</option>
                                                                <option value="0">Inactive</option>
                                                            </select>
                                                            <div class="valid-feedback">Looks good!</div>
                                                            <div class="invalid-feedback">Please select a status.</div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row mb-3">
                                                    <div class="col-lg-12 text-center">
                                                        <button type="submit" id="btnSaveCast" name="btnSaveCast" class="btn btn-primary">Save Cast</button>
                                                    </div>
                                                </div>
                                            </form>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xl-12">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="header-title">All Saved Casts
                                    </h4>
                                    <p class="text-muted fs-14"></p>
                                    <div class="table-responsive-sm">
                                        <?php
                                        // Start a try-catch block for error handling
                                        try {
                                            // Begin a transaction
                                            $conn->begin_transaction();

                                            // Prepare the SQL statement for fetching cast data
                                            $sql = "SELECT c.id, c.cast_name, c.description, c.created_at, c.updated_at, 
                                                        u1.username AS created_by, u2.username AS updated_by, 
                                                        IF(c.is_active = 1, 'Active', 'Inactive') AS status
                                                    FROM user_cast c
                                                    LEFT JOIN users u1 ON c.created_by = u1.id
                                                    LEFT JOIN users u2 ON c.updated_by = u2.id";

                                            // Prepare and execute the query
                                            $stmt = $conn->prepare($sql);
                                            if (!$stmt) {
                                                throw new Exception("Failed to prepare statement: " . $conn->error);
                                            }
                                            $stmt->execute();

                                            // Get the result
                                            $result = $stmt->get_result();
                                            if (!$result) {
                                                throw new Exception("Failed to execute query: " . $stmt->error);
                                            }

                                            // Commit the transaction since everything is successful up to this point
                                            $conn->commit();

                                            // Begin the table display with data fetched
                                        ?>
                                            <table id="scroll-horizontal-datatable" class="table table-striped w-100 nowrap">
                                                <thead>
                                                    <tr>
                                                        <th>ID</th>
                                                        <th>Cast Name</th>
                                                        <th>Description</th>
                                                        <th>Created By</th>
                                                        <th>Created At</th>
                                                        <th>Status</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    // Loop through results and display each row in the table
                                                    if ($result->num_rows > 0) {
                                                        while ($row = $result->fetch_assoc()) {
                                                            echo "<tr>";
                                                            echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                                                            echo "<td>" . htmlspecialchars($row['cast_name']) . "</td>";
                                                            echo "<td>" . htmlspecialchars($row['description']) . "</td>";
                                                            echo "<td>" . htmlspecialchars($row['created_by']) . "</td>";
                                                            echo "<td>" . htmlspecialchars($row['created_at']) . "</td>";
                                                            echo "<td>" . htmlspecialchars($row['status']) . "</td>";
                                                            echo "<td>
                                                                        <a href='edit-cast.php?id=" . urlencode($row['id']) . "' class='btn btn-sm btn-primary'>Edit</a>
                                                                        <a href='delete-cast.php?id=" . urlencode($row['id']) . "' class='btn btn-sm btn-danger' onclick='return confirm(\"Are you sure you want to delete this cast?\")'>Delete</a>
                                                                    </td>";
                                                            echo "</tr>";
                                                        }
                                                    } else {
                                                        echo "<tr><td colspan='7' class='text-center'>No records found</td></tr>";
                                                    }
                                                    ?>
                                                </tbody>
                                            </table>
                                        <?php

                                            // Close the statement
                                            $stmt->close();
                                        } catch (Exception $e) {
                                            // Rollback the transaction if any error occurs
                                            $conn->rollback();

                                            // Display error message
                                            $_SESSION['message'][] = array("type" => "danger", "content" => "Error: " . $e->getMessage());
                                            echo "<p class='text-danger'>An error occurred while fetching the data. Please try again later.</p>";
                                        }
                                        ?>
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
        $(document).ready(function() {
            "use strict";
            $("#scroll-horizontal-datatable").DataTable({
                scrollX: true,
                language: {
                    paginate: {
                        previous: "<i class='ri-arrow-left-s-line'>",
                        next: "<i class='ri-arrow-right-s-line'>",
                    },
                },
                drawCallback: function() {
                    $(".dataTables_paginate > .pagination").addClass("pagination-rounded");
                },
            });
        });
    </script>
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