<?php
include 'layouts/session.php';
include 'layouts/main.php';
include 'layouts/config.php';
include 'layouts/functions.php';
?>

<head>
    <title>Religion | Soulmate.com.pk</title>
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
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["btnSaveReligion"])) {
    // Step 1: Retrieve and sanitize form data
    $religionName = trim($_POST['religionName']);
    $religionDescription = trim($_POST['religionDescription']);
    $religionStatus = intval($_POST['religionStatus']);
    $createdAt = date("Y-m-d H:i:s");
    $updatedAt = date("Y-m-d H:i:s");

    // Retrieve the user ID from session (assuming user ID is stored in session)
    $userId = $_SESSION['user_id'] ?? null;

    // Check if the user ID is available
    if (!$userId) {
        $_SESSION['message'][] = array("type" => "danger", "content" => "Error: User not logged in.");
        header("location: manage-religion.php");
        exit();
    }

    try {
        // Step 2: Begin a database transaction
        $conn->begin_transaction();

        // Step 3: Prepare SQL statement to insert data into religion table
        $sql = "INSERT INTO religion 
                (religion_name, description, created_at, updated_at, created_by, updated_by, is_active)
                VALUES (?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Failed to prepare statement: " . $conn->error);
        }

        // Step 4: Bind parameters for security
        $stmt->bind_param(
            "sssiiii",
            $religionName,
            $religionDescription,
            $createdAt,
            $updatedAt,
            $userId,  // Created by user ID
            $userId,  // Updated by user ID (same on initial insert)
            $religionStatus
        );

        // Step 5: Execute the statement
        if ($stmt->execute()) {
            // Commit transaction if the statement is executed successfully
            $conn->commit();
            $_SESSION['message'][] = array("type" => "success", "content" => "Religion data saved successfully!");
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
        // Step 7: Redirect to manage religion page
        header("location: manage-religion.php");
        exit(); // Ensure script execution stops after redirection
    }
}
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["btnUpdateReligion"])) {
    // Step 1: Retrieve and validate form data
    $religionId = intval($_POST['religionId']); // Ensure ID is an integer
    $religionName = trim($_POST['religionName']);
    $religionDescription = trim($_POST['religionDescription']);
    $religionStatus = intval($_POST['religionStatus']);
    $updatedBy = $_SESSION['user_id'] ?? null; // Ensure user_id is set
    $updatedAt = date("Y-m-d H:i:s");

    // Check if user ID is available in session
    if (!$updatedBy) {
        $_SESSION['message'][] = array("type" => "danger", "content" => "Error: User not logged in.");
        header("location: manage-religion.php");
        exit();
    }

    try {
        // Step 2: Start a database transaction
        $conn->begin_transaction();

        // Step 3: Prepare SQL for updating the religion data
        $sql = "UPDATE religion
                SET religion_name = ?, description = ?, is_active = ?, updated_by = ?, updated_at = ?
                WHERE id = ?";

        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Failed to prepare statement: " . $conn->error);
        }

        // Step 4: Bind parameters for security
        $stmt->bind_param(
            "ssiisi",
            $religionName,           // religion_name (string)
            $religionDescription,    // description (string)
            $religionStatus,         // is_active (integer)
            $updatedBy,              // updated_by (integer)
            $updatedAt,              // updated_at (string, datetime format)
            $religionId              // id (integer)
        );

        // Step 5: Execute the update statement
        if ($stmt->execute()) {
            $_SESSION['message'][] = array("type" => "success", "content" => "Religion data updated successfully!");
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
        // Step 7: Redirect back to manage religion page
        header("location: manage-religion.php");
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
                                        <li class="breadcrumb-item"><a href="javascript: void(0);">Religion</a></li>
                                        <li class="breadcrumb-item active">Add Religion</li>
                                    </ol>
                                </div>
                                <h4 class="page-title">Add Religion</h4>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <?php displaySessionMessage(); ?>
                        <h2 class="text-center">Add Religion</h2>
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <p class="text-muted fs-14"> </p>
                                    <div class="row">
                                        <div>
                                            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="needs-validation" novalidate enctype="multipart/form-data">
                                                <div class="row mb-3">
                                                    <h3>Religion Information</h3>

                                                    <!-- Religion Name Input -->
                                                    <div class="col-lg-6">
                                                        <div class="mb-3">
                                                            <label for="religionName" class="form-label">Religion Name</label>
                                                            <input type="text" id="religionName" name="religionName" class="form-control" required placeholder="Enter the name of the Religion">
                                                            <div class="valid-feedback">Looks good!</div>
                                                            <div class="invalid-feedback">Please enter the religion name.</div>
                                                        </div>
                                                    </div>

                                                    <!-- Description Input -->
                                                    <div class="col-lg-6">
                                                        <div class="mb-3">
                                                            <label for="religionDescription" class="form-label">Religion Description</label>
                                                            <textarea id="religionDescription" name="religionDescription" class="form-control" placeholder="Enter the Religion Description"></textarea>
                                                            <div class="valid-feedback">Looks good!</div>
                                                            <div class="invalid-feedback">Please enter the religion description.</div>
                                                        </div>
                                                    </div>

                                                    <!-- Status Selection -->
                                                    <div class="col-lg-6">
                                                        <div class="mb-2">
                                                            <label for="religionStatus" class="form-label">Religion Status *</label>
                                                            <select id="religionStatus" name="religionStatus" class="form-select select2" data-toggle="select2" required>
                                                                <option value="1">Active</option>
                                                                <option value="0">Inactive</option>
                                                            </select>
                                                            <div class="valid-feedback">Looks good!</div>
                                                            <div class="invalid-feedback">Please select a status.</div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Submit Button -->
                                                <div class="row mb-3">
                                                    <div class="col-lg-12 text-center">
                                                        <button type="submit" id="btnSaveReligion" name="btnSaveReligion" class="btn btn-primary">Save Religion</button>
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

                                            // Prepare the SQL statement for fetching religion data
                                            $sql = "SELECT r.id, r.religion_name, r.description, r.created_at, r.updated_at, 
                u1.username AS created_by, u2.username AS updated_by, 
                IF(r.is_active = 1, 'Active', 'Inactive') AS status
            FROM religion r
            LEFT JOIN users u1 ON r.created_by = u1.id
            LEFT JOIN users u2 ON r.updated_by = u2.id";

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
                                                        <th>Religion Name</th>
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
                                                            echo "<td>" . htmlspecialchars($row['religion_name']) . "</td>";
                                                            echo "<td>" . htmlspecialchars($row['description']) . "</td>";
                                                            echo "<td>" . htmlspecialchars($row['created_by']) . "</td>";
                                                            echo "<td>" . htmlspecialchars($row['created_at']) . "</td>";
                                                            echo "<td>" . htmlspecialchars($row['status']) . "</td>";
                                                            echo "<td>
                                <a href='edit-religion.php?id=" . urlencode($row['id']) . "' class='btn btn-sm btn-primary'>Edit</a>
                                <a href='delete-religion.php?id=" . urlencode($row['id']) . "' class='btn btn-sm btn-danger' onclick='return confirm(\"Are you sure you want to delete this religion?\")'>Delete</a>
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