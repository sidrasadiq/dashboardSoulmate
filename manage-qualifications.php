<?php
include 'layouts/session.php';
include 'layouts/main.php';
include 'layouts/config.php';
include 'layouts/functions.php';
?>

<head>
    <title>Qualifications | Soulmate.com.pk</title>
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
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["btnSaveQualification"])) {
    // Step 1: Retrieve and sanitize form data
    $qualificationName = trim($_POST['qualificationName']);
    $description = trim($_POST['description']);
    $isActive = intval($_POST['isActive']);

    // Retrieve the user ID from session (assuming user ID is stored in session)
    $userId = $_SESSION['user_id'] ?? null;

    // Check if the user ID is available
    if (!$userId) {
        $_SESSION['message'][] = array("type" => "danger", "content" => "Error: User not logged in.");
        header("location: manage-qualifications.php");
        exit();
    }

    try {
        // Step 2: Begin a database transaction
        $conn->begin_transaction();

        // Step 3: Prepare SQL statement to insert data into the qualifications table
        $sql = "INSERT INTO qualifications 
                (qualification_name, description, created_by, updated_by, is_active)
                VALUES (?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Failed to prepare statement: " . $conn->error);
        }

        // Step 4: Bind parameters for security
        $stmt->bind_param(
            "ssiii",
            $qualificationName,
            $description,
            $userId,       // Created by user ID
            $userId,       // Updated by user ID (same on initial insert)
            $isActive
        );

        // Step 5: Execute the statement
        if ($stmt->execute()) {
            // Commit transaction if the statement is executed successfully
            $conn->commit();
            $_SESSION['message'][] = array("type" => "success", "content" => "Qualification data saved successfully!");
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
        // Step 7: Redirect to manage qualifications page
        header("location: manage-qualifications.php");
        exit(); // Ensure script execution stops after redirection
    }
}


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["btnUpdateQualification"])) {
    // Step 1: Retrieve and validate form data
    $qualificationId = intval($_POST['qualificationId']); // Ensure ID is an integer
    $qualificationName = trim($_POST['qualificationName']);
    $description = trim($_POST['description']);
    $qualificationStatus = intval($_POST['qualificationStatus']); // Assuming status is an integer
    $updatedBy = $_SESSION['user_id'] ?? null; // Ensure user_id is set
    $updatedAt = date("Y-m-d H:i:s");

    // Check if user ID is available in session
    if (!$updatedBy) {
        $_SESSION['message'][] = array("type" => "danger", "content" => "Error: User not logged in.");
        header("location: manage-qualifications.php");
        exit();
    }

    try {
        // Step 2: Start a database transaction
        $conn->begin_transaction();

        // Step 3: Prepare SQL for updating the qualification data
        $sql = "UPDATE qualifications
                SET qualification_name = ?, description = ?, is_active = ?, updated_by = ?, updated_at = ?
                WHERE id = ?";

        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Failed to prepare statement: " . $conn->error);
        }

        // Step 4: Bind parameters for security (adjusted parameter types to match six fields)
        $stmt->bind_param(
            "ssisii",
            $qualificationName,        // qualification_name (string)
            $description,              // description (string)
            $qualificationStatus,      // is_active (integer)
            $updatedBy,                // updated_by (integer)
            $updatedAt,                // updated_at (string, datetime format)
            $qualificationId           // id (integer)
        );

        // Step 5: Execute the update statement
        if ($stmt->execute()) {
            $_SESSION['message'][] = array("type" => "success", "content" => "Qualification data updated successfully!");
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
        // Step 7: Redirect back to manage qualifications page
        header("location: manage-qualifications.php");
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
                                        <li class="breadcrumb-item"><a href="javascript: void(0);">Qualifications</a></li>
                                        <li class="breadcrumb-item active">Add Qualifications</li>
                                    </ol>
                                </div>
                                <h4 class="page-title">Add Qualifications</h4>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <?php displaySessionMessage(); ?>
                        <h2 class="text-center">Add Qualifications</h2>
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <p class="text-muted fs-14"> </p>
                                    <div class="row">
                                        <div>
                                            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="needs-validation" novalidate enctype="multipart/form-data">
                                                <div class="row mb-3">
                                                    <h3>Qualification Information</h3>

                                                    <!-- Qualification Name Input -->
                                                    <div class="col-lg-6">
                                                        <div class="mb-3">
                                                            <label for="qualificationName" class="form-label">Qualification Name</label>
                                                            <input type="text" id="qualificationName" name="qualificationName" class="form-control" required placeholder="Enter the qualification name">
                                                            <div class="valid-feedback">Looks good!</div>
                                                            <div class="invalid-feedback">Please enter the qualification name.</div>
                                                        </div>
                                                    </div>

                                                    <!-- Description Input -->
                                                    <div class="col-lg-6">
                                                        <div class="mb-3">
                                                            <label for="description" class="form-label">Description</label>
                                                            <textarea id="description" name="description" class="form-control" rows="2" placeholder="Enter the description (optional)"></textarea>
                                                            <div class="valid-feedback">Looks good!</div>
                                                        </div>
                                                    </div>

                                                    <!-- Status Selection -->
                                                    <div class="col-lg-6">
                                                        <div class="mb-2">
                                                            <label for="isActive" class="form-label">Status</label>
                                                            <select id="isActive" name="isActive" class="form-select select2" data-toggle="select2" required>
                                                                <option value="1">Active</option>
                                                                <option value="0">Inactive</option>
                                                            </select>
                                                            <div class="valid-feedback">Looks good!</div>
                                                            <div class="invalid-feedback">Please select a status.</div>
                                                        </div>
                                                    </div>

                                                    <!-- Hidden Fields for created_by and updated_by -->
                                                    <input type="hidden" name="created_by" value="<?php echo htmlspecialchars($_SESSION['user_id'] ?? ''); ?>">
                                                    <input type="hidden" name="updated_by" value="<?php echo htmlspecialchars($_SESSION['user_id'] ?? ''); ?>">

                                                </div>

                                                <!-- Submit Button -->
                                                <div class="row mb-3">
                                                    <div class="col-lg-12 text-center">
                                                        <button type="submit" id="btnSaveQualification" name="btnSaveQualification" class="btn btn-primary">Save Qualification</button>
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
                                    <h4 class="header-title">All Saved Cities
                                    </h4>
                                    <p class="text-muted fs-14"></p>
                                    <div class="table-responsive-sm">
                                        <?php
                                        // Start a try-catch block for error handling
                                        try {
                                            // Begin a transaction
                                            $conn->begin_transaction();

                                            // Prepare the SQL statement for fetching qualifications data
                                            $sql = "SELECT q.id, q.qualification_name, q.description, q.created_at, q.updated_at,
            u1.username AS created_by, u2.username AS updated_by,
            IF(q.is_active = 1, 'Active', 'Inactive') AS status
            FROM qualifications q
            LEFT JOIN users u1 ON q.created_by = u1.id
            LEFT JOIN users u2 ON q.updated_by = u2.id";

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
                                        ?>
                                            <table id="scroll-horizontal-datatable" class="table table-striped w-100 nowrap">
                                                <thead>
                                                    <tr>
                                                        <th>ID</th>
                                                        <th>Qualification Name</th>
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
                                                            echo "<td>" . htmlspecialchars($row['qualification_name']) . "</td>";
                                                            echo "<td>" . htmlspecialchars($row['description']) . "</td>";
                                                            echo "<td>" . htmlspecialchars($row['created_by']) . "</td>";
                                                            echo "<td>" . htmlspecialchars($row['created_at']) . "</td>";
                                                            echo "<td>" . htmlspecialchars($row['status']) . "</td>";
                                                            echo "<td>
                            <a href='edit-qualifications.php?id=" . urlencode($row['id']) . "' class='btn btn-sm btn-primary'>Edit</a>
                            <a href='delete-qualifications.php?id=" . urlencode($row['id']) . "' class='btn btn-sm btn-danger' onclick='return confirm(\"Are you sure you want to delete this qualification?\")'>Delete</a>
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