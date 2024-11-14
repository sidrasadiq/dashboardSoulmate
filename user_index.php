<?php include 'layouts/session.php'; ?>
<?php include 'layouts/main.php'; ?>
<?php include 'layouts/functions.php'; ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <title>User Panel</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body class="bg-light">
    <?php include 'layouts/user_pannel/header.php'; ?>

    <div class="container-fluid p-5 pt-5 main">
        <div class="row">
            <div class="col-md-3 p-md-1 mb-md-3 ">
                <a href="#" class="d-block text-decoration-none ">
                    <img src="assets/images/profile.jpeg" alt="User" width="132" height="132" class="rounded-circle ">
                </a>
            </div>

            <div class="col-md-4 prof-con mt-md-3 mt-sm-4">
                <h5 class="mt-4">Hi Abc</h5>
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
        <form action="">
            <div class="card search-card border-0 shadow">
                <div class="card-body">
                    <div class="row align-items-center">
                        <!-- Seeking -->
                        <div class="col-md-2">
                            <label for="seeking" class="fw-bold">Seeking</label>
                            <select class="form-select  custom-border" id="seeking" aria-label="Default select example">
                                <option value="male" selected>Male</option>
                                <option value="female">Female</option>
                                <option value="any">Any</option>
                            </select>
                        </div>

                        <!-- Age -->
                        <div class="col-md-1">
                            <label for="age" class="fw-bold">Age</label>
                            <select class="form-select  custom-border" id="age" aria-label="Default select example">
                                <option value="1">18</option>
                                <option value="2" selected>19</option>
                                <option value="3">20</option>
                            </select>
                        </div>

                        <!-- Country -->
                        <div class="col-md-2">
                            <label for="country" class="fw-bold">Country</label>
                            <select class="form-select  custom-border" id="country" aria-label="Default select example">
                                <option value="any" selected>Any</option>
                                <option value="canada">Canada</option>
                                <option value="egypt">Egypt</option>
                            </select>
                        </div>

                        <!-- State/Province -->
                        <div class="col-md-2">
                            <label for="state" class="fw-bold">State/Province</label>
                            <select class="form-select  custom-border" id="state" aria-label="Default select example">
                                <option value="any" selected>Any</option>
                                <option value="2">Two</option>
                                <option value="3">Three</option>
                            </select>
                        </div>

                        <!-- City -->
                        <div class="col-md-2">
                            <label for="city" class="fw-bold">City</label>
                            <select class="form-select custom-border" id="city" aria-label="Default select example">
                                <option value="any" selected>Any</option>
                                <option value="2">Two</option>
                                <option value="3">Three</option>
                            </select>
                        </div>

                        <!-- Within -->
                        <div class="col-md-2 ">
                            <label for="within" class="fw-bold">Within</label>
                            <input type="text" class="form-control  custom-border " id="within" placeholder="   - km">
                        </div>

                        <!-- Search Button -->
                        <div class="col-md-1 ">
                            <button type="submit" class="btn btn-search w-100 shadow ">Search</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- searcch section end -->
    <div class="container mt-5">
        <div class="card border-0 bg-light">
            <div class="card-body">
                <div class="row">
                    <!--  -->
                    <div class="col-md-4">
                        <div class="card mb-3" style="max-width: 540px;">
                            <div class="row g-0">
                                <div class="col-md-4">
                                    <img src="..." class="img-fluid rounded-start" alt="...">
                                </div>
                                <div class="col-md-8">
                                    <div class="card-body">
                                        <h5 class="card-title">Card title</h5>
                                        <p class="card-text">This is a wider card with supporting text below as a natural lead-in to additional content. This content is a little bit longer.</p>
                                        <p class="card-text"><small class="text-body-secondary">Last updated 3 mins ago</small></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--  -->
                    <div class="col-md-4">
                        <div class="card mb-3" style="max-width: 540px;">
                            <div class="row g-0">
                                <div class="col-md-4">
                                    <img src="..." class="img-fluid rounded-start" alt="...">
                                </div>
                                <div class="col-md-8">
                                    <div class="card-body">
                                        <h5 class="card-title">Card title</h5>
                                        <p class="card-text">This is a wider card with supporting text below as a natural lead-in to additional content. This content is a little bit longer.</p>
                                        <p class="card-text"><small class="text-body-secondary">Last updated 3 mins ago</small></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--  -->
                    <div class="col-md-4">
                        <div class="card mb-3" style="max-width: 540px;">
                            <div class="row g-0">
                                <div class="col-md-4">
                                    <img src="..." class="img-fluid rounded-start" alt="...">
                                </div>
                                <div class="col-md-8">
                                    <div class="card-body">
                                        <h5 class="card-title">Card title</h5>
                                        <p class="card-text">This is a wider card with supporting text below as a natural lead-in to additional content. This content is a little bit longer.</p>
                                        <p class="card-text"><small class="text-body-secondary">Last updated 3 mins ago</small></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <!-- second row -->


                <div class="row">
                    <!--  -->
                    <div class="col-md-4">
                        <div class="card mb-3" style="max-width: 540px;">
                            <div class="row g-0">
                                <div class="col-md-4">
                                    <img src="..." class="img-fluid rounded-start" alt="...">
                                </div>
                                <div class="col-md-8">
                                    <div class="card-body">
                                        <h5 class="card-title">Card title</h5>
                                        <p class="card-text">This is a wider card with supporting text below as a natural lead-in to additional content. This content is a little bit longer.</p>
                                        <p class="card-text"><small class="text-body-secondary">Last updated 3 mins ago</small></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--  -->
                    <div class="col-md-4">
                        <div class="card mb-3" style="max-width: 540px;">
                            <div class="row g-0">
                                <div class="col-md-4">
                                    <img src="..." class="img-fluid rounded-start" alt="...">
                                </div>
                                <div class="col-md-8">
                                    <div class="card-body">
                                        <h5 class="card-title">Card title</h5>
                                        <p class="card-text">This is a wider card with supporting text below as a natural lead-in to additional content. This content is a little bit longer.</p>
                                        <p class="card-text"><small class="text-body-secondary">Last updated 3 mins ago</small></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--  -->
                    <div class="col-md-4">
                        <div class="card mb-3" style="max-width: 540px;">
                            <div class="row g-0">
                                <div class="col-md-4">
                                    <img src="..." class="img-fluid rounded-start" alt="...">
                                </div>
                                <div class="col-md-8">
                                    <div class="card-body">
                                        <h5 class="card-title">Card title</h5>
                                        <p class="card-text">This is a wider card with supporting text below as a natural lead-in to additional content. This content is a little bit longer.</p>
                                        <p class="card-text"><small class="text-body-secondary">Last updated 3 mins ago</small></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- second row end -->

                <!-- third row start -->
                <div class="row">
                    <!--  -->
                    <div class="col-md-4">
                        <div class="card mb-3" style="max-width: 540px;">
                            <div class="row g-0">
                                <div class="col-md-4">
                                    <img src="..." class="img-fluid rounded-start" alt="...">
                                </div>
                                <div class="col-md-8">
                                    <div class="card-body">
                                        <h5 class="card-title">Card title</h5>
                                        <p class="card-text">This is a wider card with supporting text below as a natural lead-in to additional content. This content is a little bit longer.</p>
                                        <p class="card-text"><small class="text-body-secondary">Last updated 3 mins ago</small></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--  -->
                    <div class="col-md-4">
                        <div class="card mb-3" style="max-width: 540px;">
                            <div class="row g-0">
                                <div class="col-md-4">
                                    <img src="..." class="img-fluid rounded-start" alt="...">
                                </div>
                                <div class="col-md-8">
                                    <div class="card-body">
                                        <h5 class="card-title">Card title</h5>
                                        <p class="card-text">This is a wider card with supporting text below as a natural lead-in to additional content. This content is a little bit longer.</p>
                                        <p class="card-text"><small class="text-body-secondary">Last updated 3 mins ago</small></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--  -->
                    <div class="col-md-4">
                        <div class="card mb-3" style="max-width: 540px;">
                            <div class="row g-0">
                                <div class="col-md-4">
                                    <img src="..." class="img-fluid rounded-start" alt="...">
                                </div>
                                <div class="col-md-8">
                                    <div class="card-body">
                                        <h5 class="card-title">Card title</h5>
                                        <p class="card-text">This is a wider card with supporting text below as a natural lead-in to additional content. This content is a little bit longer.</p>
                                        <p class="card-text"><small class="text-body-secondary">Last updated 3 mins ago</small></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <!-- third row end -->
            </div>
        </div>
    </div>
    <?php include 'layouts/user_pannel/footer.php'; ?>

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