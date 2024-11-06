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
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["btnSaveNationality"])) {
    // Input data from form
    $nationalityName = trim($_POST['nationalityName']);
    $nationalityDescription = trim($_POST['nationalityDescription']);
    $nationalityStatus = intval($_POST['nationalityStatus']);
    $createdAt = date("Y-m-d H:i:s");
    $updatedAt = date("Y-m-d H:i:s");

    // Retrieve the user ID from session
    $userId = $_SESSION['user_id'] ?? null;

    // Check if user ID is available in session
    if (!$userId) {
        $_SESSION['message'][] = array("type" => "error", "content" => "Error: User not logged in.");
        header("location: manage-nationality.php");
        exit();
    }

    try {
        // Start the transaction
        $conn->begin_transaction();

        // Prepare the SQL statement to insert the data
        $sql = "INSERT INTO nationality 
                (nationality_name, description, created_at, updated_at, created_by, updated_by, is_active)
                VALUES (?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Failed to prepare statement: " . $conn->error);
        }

        // Bind parameters to prevent SQL injection
        $stmt->bind_param(
            "sssiiiii",
            $nationalityName,
            $nationalityDescription,
            $createdAt,
            $updatedAt,
            $userId,  // Set created_by from session user ID
            $userId,  // Set updated_by from session user ID (same on initial insert)
            $nationalityStatus
        );

        // Execute the statement
        if ($stmt->execute()) {
            // Commit transaction if the statement is executed successfully
            $conn->commit();
            $_SESSION['message'][] = array("type" => "success", "content" => "Nationality data saved successfully!");
        } else {
            throw new Exception("Failed to save the data. " . $stmt->error);
        }

        // Close the statement
        $stmt->close();
    } catch (Exception $e) {
        // Roll back transaction on error
        $conn->rollback();

        // Store error message in session
        $_SESSION['message'][] = array("type" => "error", "content" => "Error: " . $e->getMessage());
    } finally {
        // Redirect to manage nationality page
        header("location: manage-nationality.php");
        exit(); // Ensure script execution stops after header redirection
    }
}


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["btnUpdateNationality"])) {
    // Step 1: Retrieve and validate form data
    $nationalityId = intval($_POST['id']); // Make sure ID is an integer
    $nationalityName = trim($_POST['nationalityName']);
    $nationalityDescription = trim($_POST['nationalityDescription']);
    $nationalityStatus = intval($_POST['nationalityStatus']);
    $updatedBy = $_SESSION['user_id']; // Assuming user_id is stored in session
    $updatedAt = date("Y-m-d H:i:s");

    try {
        // Step 2: Start a database transaction
        $conn->begin_transaction();

        // Step 3: Prepare SQL for updating the nationality data
        $sql = "UPDATE nationality 
                SET nationality_name = ?, description = ?, is_active = ?, updated_by = ?, updated_at = ?
                WHERE id = ?";

        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Failed to prepare statement: " . $conn->error);
        }

        // Step 4: Bind parameters for security
        $stmt->bind_param(
            "ssiisi",
            $nationalityName,
            $nationalityDescription,
            $nationalityStatus,
            $updatedBy,
            $updatedAt,
            $nationalityId
        );

        // Step 5: Execute the update statement
        if ($stmt->execute()) {
            $_SESSION['message'][] = array("type" => "success", "content" => "Nationality data updated successfully!");
            // Commit the transaction
            $conn->commit();
        } else {
            throw new Exception("Failed to update data. " . $stmt->error);
        }

        // Close the statement
        $stmt->close();
    } catch (Exception $e) {
        // Step 6: Rollback transaction in case of error
        $conn->rollback();

        // Store error message in session
        $_SESSION['message'][] = array("type" => "error", "content" => "Error: " . $e->getMessage());
    } finally {
        // Step 7: Redirect back to manage nationality page
        header("location: manage-nationality.php");
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
                                        <li class="breadcrumb-item"><a href="javascript: void(0);">Nationality</a></li>
                                        <li class="breadcrumb-item active">Add Nationality</li>
                                    </ol>
                                </div>
                                <h4 class="page-title">Add Nationality</h4>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <?php displaySessionMessage(); ?>
                        <h2 class="text-center">Add Nationality</h2>
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <p class="text-muted fs-14"> </p>
                                    <div class="row">
                                        <div>
                                            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="needs-validation" novalidate enctype="multipart/form-data">
                                                <div class="row mb-3">
                                                    <h3>Nationality Information</h3>
                                                    <div class="col-lg-6">
                                                        <div class="mb-3">
                                                            <label for="nationalityName" class="form-label">Nationality Name</label>
                                                            <input type="text" id="nationalityName" name="nationalityName" class="form-control" required placeholder="Enter the name of Nationality">
                                                            <div class="valid-feedback">Looks good!</div>
                                                            <div class="invalid-feedback">Please fill this field.</div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <div class="mb-3">
                                                            <label for="nationalityDescription" class="form-label">Nationality Description</label>
                                                            <input type="text" id="nationalityDescription" name="nationalityDescription" class="form-control" required placeholder="Add Material Description">
                                                            <div class="valid-feedback">Looks good!</div>
                                                            <div class="invalid-feedback">Please fill this field.</div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <div class="mb-2">
                                                            <label for="nationalityStatus" class="form-label">Nationality Status *</label>
                                                            <select id="nationalityStatus" name="nationalityStatus" class="form-select select2" data-toggle="select2" required>
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
                                                        <button type="submit" id="btnSaveNationality" name="btnSaveNationality" class="btn btn-primary ">Save Order</button>
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
                                    <h4 class="header-title">All Saved Materials</h4>
                                    <p class="text-muted fs-14"></p>
                                    <div class="table-responsive-sm">
                                        <?php
                                        // Start a try-catch block for error handling
                                        try {
                                            // Begin a transaction
                                            $conn->begin_transaction();

                                            // Prepare the SQL statement
                                            $sql = "SELECT n.id, n.nationality_name, n.description, n.created_at, n.updated_at, 
                                                        u1.username AS created_by, u2.username AS updated_by, 
                                                        IF(n.is_active = 1, 'Active', 'Inactive') AS status
                                                    FROM nationality n
                                                    LEFT JOIN users u1 ON n.created_by = u1.id
                                                    LEFT JOIN users u2 ON n.updated_by = u2.id";

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
                                                        <th>Nationality Name</th>
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
                                                            echo "<td>" . htmlspecialchars($row['nationality_name']) . "</td>";
                                                            echo "<td>" . htmlspecialchars($row['description']) . "</td>";
                                                            echo "<td>" . htmlspecialchars($row['created_by']) . "</td>";
                                                            echo "<td>" . htmlspecialchars($row['created_at']) . "</td>";
                                                            echo "<td>" . htmlspecialchars($row['status']) . "</td>";
                                                            echo "<td>
                                                                    <a href='edit-nationality.php?id=" . urlencode($row['id']) . "' class='btn btn-sm btn-primary'>Edit</a>
                                                                    <a href='delete-nationality.php?id=" . urlencode($row['id']) . "' class='btn btn-sm btn-danger' onclick='return confirm(\"Are you sure you want to delete this nationality?\")'>Delete</a>
                                                                </td>";
                                                            echo "</tr>";
                                                        }
                                                    } else {
                                                        echo "<tr><td colspan='9' class='text-center'>No records found</td></tr>";
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
                                            $_SESSION['message'][] = array("type" => "error", "content" => "Error: " . $e->getMessage());
                                            echo "<p class='text-danger'>An error occurred while fetching the data. Please try again later.</p>";
                                        }
                                        // finally {
                                        //     // Close the database connection
                                        //     $conn->close();
                                        // }
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