<?php
include 'userlayout/header.php';

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
        /* Highlight the radio button on focus and when checked */
        .highlight-radio:focus,
        .highlight-radio:checked {
            box-shadow: 0 0 0 0.25rem rgba(0, 123, 255, 0.25);
            /* Bootstrap focus box-shadow */
            border-color: #F5367B !important;
            /* Change border color on selection */
        }

        /* Custom color for checked radio buttons */
        .highlight-radio:checked {
            background-color: #F5367B !important;
            /* Highlight color when checked */
            border-color: #F5367B !important;
        }

        .form-check {
            justify-content: space-between;
            padding: 20px 40px;
        }

        .headCustom {
            color: #4CA8F0 !important;
        }

        .container {
            margin-left: 13px;
        }
    </style>

</head>

<body class="bg-light w-100">
    <div class="container">
        <h3 class=" text-muted mt-5">Edit Profile</h3>
        <div class="max-width-3">
            Answering these profile questions will help other users find you in search results and help us to find you <br> more accurate matches.
            <em>Answer all questions below to complete this step.</em>

        </div>
        <br>
        <h4 class="headCustom ">Your Basics</h4>
        <hr>
        <!-- row start -->
        <div class="row ">
            <div class="col-12">

                <div class="card bg-light border-0">
                    <div class="card-body">
                        <p class="text-muted fs-14"> </p>
                        <div class="row">
                            <div>
                                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="needs-validation" novalidate enctype="multipart/form-data">
                                    <div class="row mb-3">


                                        <!-- First Name Input -->
                                        <div class="col-lg-4">
                                            <div class="mb-3">
                                                <label for="firstName" class="form-label text-muted">First Name:</label>
                                                <input type="text" id="firstName" name="firstName" class="form-control" required placeholder="Enter  Your First Name">
                                                <div class="valid-feedback">Looks good!</div>
                                                <div class="invalid-feedback">Please fill this field.</div>
                                            </div>
                                        </div>
                                        <!-- last Name Input -->
                                        <div class="col-lg-4">
                                            <div class="mb-3">
                                                <label for="lastName" class="form-label text-muted">Last Name:</label>
                                                <input type="text" id="lastName" name="lastName" class="form-control" required placeholder="Enter  Your Last Name">
                                                <div class="valid-feedback">Looks good!</div>
                                                <div class="invalid-feedback">Please fill this field.</div>
                                            </div>
                                        </div>
                                        <!-- Date Input -->
                                        <div class="col-lg-4">
                                            <div class="mb-3">
                                                <label for="dob" class="form-label text-muted">Date Of Birth:</label>
                                                <input type="date" id="dob" name="dob" class="form-control text-muted" required>
                                                <div class="valid-feedback">Looks good!</div>
                                                <div class="invalid-feedback">Please fill this field.</div>
                                            </div>
                                        </div>

                                        <!-- Gender Dropdown -->
                                        <div class="col-lg-4">
                                            <div class="mb-3">
                                                <label for="gender" class="form-label text-muted">I'm a:</label>
                                                <select id="gender" name="gender" class="form-select text-muted" required>
                                                    <option selected disabled value="">Select Gender</option>
                                                    <option value="male">Male</option>
                                                    <option value="female">Female</option>
                                                </select>
                                                <div class="valid-feedback">Looks good!</div>
                                                <div class="invalid-feedback">Please select a project.</div>
                                            </div>
                                        </div>

                                        <!-- contact Number -->
                                        <div class="col-lg-4">
                                            <div class="mb-3">
                                                <label for="contactNum" class="form-label text-muted">Contact Number:</label>
                                                <input type="number" id="contactNum" name="contactNum" class="form-control" required placeholder="Enter  Your  Contact Number">
                                                <div class="valid-feedback">Looks good!</div>
                                                <div class="invalid-feedback">Please fill this field.</div>
                                            </div>
                                        </div>
                                        <!-- WhatsApp Number -->
                                        <div class="col-lg-4">
                                            <div class="mb-3">
                                                <label for="WhatsNum" class="form-label text-muted">WhatsApp Number:</label>
                                                <input type="number" id="WhatsNum" name="WhatsNum" class="form-control" required placeholder="Enter  Your  WhatsApp Number">
                                                <div class="valid-feedback">Looks good!</div>
                                                <div class="invalid-feedback">Please fill this field.</div>
                                            </div>
                                        </div>
                                        <!-- CNIC Number -->
                                        <div class="col-lg-4">
                                            <div class="mb-3">
                                                <label for="cnicNum" class="form-label text-muted">CNIC Number:</label>
                                                <input type="text" id="cnicNum" name="cnicNum" class="form-control" required placeholder="Enter  Your  CNIC Number">
                                                <div class="valid-feedback">Looks good!</div>
                                                <div class="invalid-feedback">Please fill this field.</div>
                                            </div>
                                        </div>
                                        <!-- cast Dropdown -->
                                        <div class="col-lg-4">
                                            <div class="mb-3">
                                                <label for="cast" class="form-label text-muted">Cast:</label>
                                                <select id="cast" name="cast" class="form-select text-muted" required>
                                                    <option selected disabled value="">Select Cast</option>
                                                    <option value="cast1">cast1</option>
                                                    <option value="cast2">cast2</option>
                                                </select>
                                                <div class="valid-feedback">Looks good!</div>
                                                <div class="invalid-feedback">Please select a project.</div>
                                            </div>
                                        </div>
                                        <!-- Nationality Dropdown -->
                                        <div class="col-lg-4">
                                            <div class="mb-3">
                                                <label for="nationality" class="form-label text-muted">Nationality:</label>
                                                <select id="nationality" name="nationality" class="form-select text-muted" required>
                                                    <option selected disabled value="">Select Nationality</option>
                                                    <option value="nationality1">nationality1</option>
                                                    <option value="nationality2">nationalityt2</option>
                                                </select>
                                                <div class="valid-feedback">Looks good!</div>
                                                <div class="invalid-feedback">Please select a project.</div>
                                            </div>
                                        </div>
                                        <!-- Religion Dropdown -->
                                        <div class="col-lg-4">
                                            <div class="mb-3">
                                                <label for="religion" class="form-label text-muted">Religion:</label>
                                                <select id="religion" name="religion" class="form-select text-muted" required>
                                                    <option selected disabled value="">Select Religion</option>
                                                    <option value="religion1">religion1</option>
                                                    <option value="religion2">religion2</option>
                                                </select>
                                                <div class="valid-feedback">Looks good!</div>
                                                <div class="invalid-feedback">Please select a project.</div>
                                            </div>
                                        </div>
                                        <!-- qualification Dropdown -->
                                        <div class="col-lg-4">
                                            <div class="mb-3">
                                                <label for="qualification" class="form-label text-muted">Qualification:</label>
                                                <select id="qualification" name="qualification" class="form-select text-muted" required>
                                                    <option selected disabled value="">Select Qualification</option>
                                                    <option value="qualification1">qualification1</option>
                                                    <option value="qualificationn2">qualification2</option>
                                                </select>
                                                <div class="valid-feedback">Looks good!</div>
                                                <div class="invalid-feedback">Please select a project.</div>
                                            </div>
                                        </div>
                                        <!-- interests -->
                                        <div class="col-lg-4">
                                            <div class="mb-3">
                                                <label for="interests" class="form-label text-muted">Interests:</label>
                                                <input type="text" id="interests" name="interests" class="form-control" required placeholder="Enter  Your Interests">
                                                <div class="valid-feedback">Looks good!</div>
                                                <div class="invalid-feedback">Please select a project.</div>
                                            </div>
                                        </div>
                                        <!-- Country Dropdown -->
                                        <div class="col-lg-4">
                                            <div class="mb-3">
                                                <label for="country" class="form-label text-muted">Country:</label>
                                                <select id="country" name="country" class="form-select text-muted" required>
                                                    <option selected disabled value="">Select Country</option>
                                                    <option value="country1">country1</option>
                                                    <option value="country2">country2</option>
                                                </select>
                                                <div class="valid-feedback">Looks good!</div>
                                                <div class="invalid-feedback">Please select a project.</div>
                                            </div>
                                        </div>
                                        <!-- State Dropdown -->
                                        <div class="col-lg-4">
                                            <div class="mb-3">
                                                <label for="state" class="form-label text-muted">State:</label>
                                                <select id="state" name="state" class="form-select text-muted" required>
                                                    <option selected disabled value="">Select State</option>
                                                    <option value="state1">state1</option>
                                                    <option value="state2">state2</option>
                                                </select>
                                                <div class="valid-feedback">Looks good!</div>
                                                <div class="invalid-feedback">Please select a project.</div>
                                            </div>
                                        </div>
                                        <!-- City Dropdown -->
                                        <div class="col-lg-4">
                                            <div class="mb-3">
                                                <label for="city" class="form-label text-muted">City:</label>
                                                <select id="city" name="city" class="form-select text-muted" required>
                                                    <option selected disabled value="">Select City</option>
                                                    <option value="city1">city1</option>
                                                    <option value="city2">city2</option>
                                                </select>
                                                <div class="valid-feedback">Looks good!</div>
                                                <div class="invalid-feedback">Please select a project.</div>
                                            </div>
                                        </div>
                                        <!-- Profile Picture -->
                                        <div class="col-lg-4">
                                            <div class="mb-2">
                                                <label for="userProfilePic" class="form-label text-muted">Profile Picture *</label>
                                                <input type="file" id="userProfilePic" name="userProfilePic" class="form-control" accept="image/*" onchange="displayImage(this)">
                                                <img id="profilePicPreview" src="" alt="Profile Picture" class="img-thumbnail mt-2" style="max-width: 150px;">
                                                <div class="valid-feedback">Looks good!</div>
                                                <div class="invalid-feedback" id="imageError">Please fill this field.</div>
                                            </div>
                                        </div>
                                        <!-- preferences -->
                                        <div class="col-lg-4">
                                            <div class="mb-2">
                                                <label for="preferences" class="form-label text-muted">Preferences</label>
                                                <input type="text" id="preferences" name="preferences" class="form-control">
                                                <div class="valid-feedback">Looks good!</div>
                                                <div class="invalid-feedback" id="imageError">Please fill this field.</div>
                                            </div>
                                        </div>
                                        <!-- Occupation Dropdown -->
                                        <div class="col-lg-4">
                                            <div class="mb-3">
                                                <label for="occupation" class="form-label text-muted">Occupation:</label>
                                                <select id="occupation" name="occupation" class="form-select text-muted" required>
                                                    <option selected disabled value="">Select Occupation</option>
                                                    <option value="occupation1">occupation1</option>
                                                    <option value="occupation2">occupation2</option>
                                                </select>
                                                <div class="valid-feedback">Looks good!</div>
                                                <div class="invalid-feedback">Please select a project.</div>
                                            </div>
                                        </div>
                                        <!-- Bio Details Input -->
                                        <div class="col-lg-12">
                                            <div class="mb-3">
                                                <textarea id="taskDetails" name="taskDetails" class="form-control" rows="3" placeholder="A little about yourself" required></textarea>
                                                <div class="valid-feedback">Looks good!</div>
                                                <div class="invalid-feedback">Please fill this field.</div>
                                            </div>
                                        </div>
                                        <h4 class="mt-5 headCustom">Your Appearance</h4>
                                        <hr>
                                        <!-- Body  type -->
                                        <div class="col-lg-12 mt-3">
                                            <div class="mb-3">
                                                <p class="text-muted">Body type:</p>
                                                <hr>
                                                <div class="form-check form-check-inline me-5">
                                                    <input class="form-check-input fs-5 highlight-radio" type="radio" name="bodyType" value="petite" id="petite">
                                                    <label class="form-check-label text-muted  fs-6" for="petite">Petite</label>
                                                </div>

                                                <div class="form-check form-check-inline me-5">
                                                    <input class="form-check-input fs-5 highlight-radio" type="radio" name="bodyType" id="slim" value="slim">
                                                    <label class="form-check-label text-muted  fs-6" for="slim">Slim</label>
                                                </div>

                                                <div class="form-check form-check-inline me-5">
                                                    <input class="form-check-input fs-5 highlight-radio" type="radio" name="bodyType" id="athletic" value="athletic">
                                                    <label class="form-check-label text-muted  fs-6" for="athletic">Athletic</label>
                                                </div>
                                                <div class="form-check form-check-inline me-5">
                                                    <input class="form-check-input fs-5 highlight-radio" type="radio" name="bodyType" id="average" value="average">
                                                    <label class="form-check-label text-muted  fs-6" for="average">Average</label>
                                                </div>

                                                <div class="form-check form-check-inline me-5">
                                                    <input class="form-check-input fs-5 highlight-radio" type="radio" name="bodyType" id="few extra pounds" value="few extra pounds">
                                                    <label class="form-check-label text-muted  fs-6" for="few extra pounds">Few Extra Pounds</label>
                                                </div>

                                                <div class="form-check form-check-inline me-5">
                                                    <input class="form-check-input fs-5 highlight-radio" type="radio" name="bodyType" id="full figured" value="full figured">
                                                    <label class="form-check-label text-muted  fs-6" for="full figured">Full Figured</label>
                                                </div>

                                                <div class="form-check form-check-inline me-5">
                                                    <input class="form-check-input fs-5 highlight-radio" type="radio" name="bodyType" id="large and lovely" value="large and lovely">
                                                    <label class="form-check-label text-muted  fs-6" for="large and lovely">Large and Lovely</label>
                                                </div>

                                            </div>

                                        </div>
                                        <!-- ethnicity  type -->
                                        <div class="col-lg-12 mt-2">
                                            <div class="mb-3">
                                                <p class="text-muted">Your ethnicity is mostly:</p>
                                                <hr>
                                                <div class="form-check form-check-inline me-5">
                                                    <input class="form-check-input fs-5 highlight-radio" type="radio" name="ethnicity" id="arab(middle eastern)" value="arab(middle eastern)">
                                                    <label class="form-check-label text-muted  fs-6" for="arab(middle eastern)">
                                                        Arab (Middle Eastern) </label>
                                                </div>
                                                <div class="form-check form-check-inline me-5">
                                                    <input class="form-check-input fs-5 highlight-radio" type="radio" name="ethnicity" id="asian" value="asian">
                                                    <label class="form-check-label text-muted  fs-6" for="asian">
                                                        Asian </label>
                                                </div>
                                                <div class="form-check form-check-inline me-5">
                                                    <input class="form-check-input fs-5 highlight-radio" type="radio" name="ethnicity" id="black" value="black">
                                                    <label class="form-check-label text-muted  fs-6" for="black">
                                                        Black</label>
                                                </div>
                                                <div class="form-check form-check-inline me-5">
                                                    <input class="form-check-input fs-5 highlight-radio" type="radio" name="ethnicity" value="caucasian(white)" id="caucasian(white)">
                                                    <label class="form-check-label text-muted  fs-6" for="caucasian(white)">
                                                        Caucasian (White) </label>
                                                </div>
                                                <div class="form-check form-check-inline me-5">
                                                    <input class="form-check-input fs-5 highlight-radio" type="radio" name="ethnicity" value="hispanic/latino" id="hispanic/latino">
                                                    <label class="form-check-label text-muted  fs-6" for="hispanic/latino">
                                                        Hispanic/Latino </label>
                                                </div>
                                                <div class="form-check form-check-inline me-5">
                                                    <input class="form-check-input fs-5 highlight-radio" type="radio" name="ethnicity" value="indain" id="indain">
                                                    <label class="form-check-label text-muted  fs-6" for="indain">
                                                        Indain </label>
                                                </div>
                                                <div class="form-check form-check-inline me-5">
                                                    <input class="form-check-input fs-5 highlight-radio" type="radio" name="ethnicity" value="pacific islander" id="pacific islander">
                                                    <label class="form-check-label text-muted  fs-6" for="pacific islander">
                                                        Pacific Islander </label>
                                                </div>
                                                <div class="form-check form-check-inline me-5">
                                                    <input class="form-check-input fs-5 highlight-radio" type="radio" name="ethnicity" value="other" id="other">
                                                    <label class="form-check-label text-muted  fs-6" for="other">
                                                        Other </label>
                                                </div>
                                                <div class="form-check form-check-inline me-5">
                                                    <input class="form-check-input fs-5" type="radio" name="ethnicity" value="mixed" id="mixed">
                                                    <label class="form-check-label text-muted  fs-6" for="mixed">
                                                        Mixed </label>
                                                </div>
                                                <div class="form-check form-check-inline me-5">
                                                    <input class="form-check-input fs-5 highlight-radio" type="radio" name="ethnicity" value="prefer not to say" id="prefer not to say">
                                                    <label class="form-check-label text-muted  fs-6" for="prefer not to say">
                                                        Prefre not to say </label>
                                                </div>
                                            </div>

                                        </div>

                                        <!-- my appearance   -->
                                        <div class="col-lg-12 mt-2">
                                            <div class="mb-3">
                                                <p class="text-muted">I consider my appearance as:</p>
                                                <hr>
                                                <!--  -->
                                                <div class="form-check form-check-inline me-5">
                                                    <input class="form-check-input fs-5 highlight-radio" type="radio" name="appearance" value="below average" id="below average">
                                                    <label class="form-check-label text-muted  fs-6" for="below average">
                                                        Below average </label>
                                                </div>

                                                <div class="form-check form-check-inline me-5">
                                                    <input class="form-check-input fs-5 highlight-radio" type="radio" name="appearance" value="average" id="average">
                                                    <label class="form-check-label text-muted  fs-6" for="average">
                                                        Average</label>
                                                </div>

                                                <div class="form-check form-check-inline me-5">
                                                    <input class="form-check-input fs-5 highlight-radio" type="radio" name="appearance" value="attractive" id="attractive">
                                                    <label class="form-check-label text-muted  fs-6" for="attractive">
                                                        Attractive</label>
                                                </div>

                                                <div class="form-check form-check-inline me-5">
                                                    <input class="form-check-input fs-5 highlight-radio" type="radio" name="appearance" value="very attractive" id="very attractive">
                                                    <label class="form-check-label text-muted  fs-6" for="very attractive">
                                                        Very attractive</label>
                                                </div>
                                            </div>
                                            <!-- height -->
                                            <div class="col-lg-3 mt-2">
                                                <div class="mb-3">
                                                    <p class="text-muted">Height:</p>
                                                    <hr>
                                                    <select id="height" name="occupation" class="form-select text-muted" required>
                                                        <option selected disabled value="">Select Height</option>
                                                        <option value="height1">height1</option>
                                                        <option value="height2">height2</option>
                                                    </select>
                                                    <div class="valid-feedback">Looks good!</div>
                                                    <div class="invalid-feedback">Please select a project.</div>
                                                </div>
                                            </div>
                                            <!-- Weight -->
                                            <div class="col-lg-3 mt-2">
                                                <div class="mb-3">
                                                    <p class="text-muted">Weight:</p>
                                                    <hr>
                                                    <select id="weight" name="occupation" class="form-select text-muted" required>
                                                        <option selected disabled value="">Select Weight</option>
                                                        <option value="weight1">weight1</option>
                                                        <option value="weight2">weight2</option>
                                                    </select>
                                                    <div class="valid-feedback">Looks good!</div>
                                                    <div class="invalid-feedback">Please select a project.</div>
                                                </div>
                                            </div>
                                        </div>
                                        <!--  -->
                                        <h4 class="mt-5 headCustom">Your Lifestyle</h4>
                                        <hr>
                                        <!-- drink alchohal   -->
                                        <div class="col-lg-12 mt-2">
                                            <div class="mb-3">
                                                <p class="text-muted">Do you drink?</p>
                                                <hr>
                                                <div class="form-check form-check-inline me-5">
                                                    <input class="form-check-input fs-5 highlight-radio" type="radio" name="drinkAlcohol" value="do drink" id="do drink">
                                                    <label class="form-check-label text-muted  fs-6" for="do drink">
                                                        Do drink </label>
                                                </div>

                                                <div class="form-check form-check-inline me-5">
                                                    <input class="form-check-input fs-5 highlight-radio" type="radio" name="drinkAlcohol" value="occasionally drink" id="occasionally drink">
                                                    <label class="form-check-label text-muted  fs-6" for="occasionally drink">
                                                        Occasionally drink </label>
                                                </div>
                                                <div class="form-check form-check-inline me-5">
                                                    <input class="form-check-input fs-5 highlight-radio" type="radio" name="drinkAlcohol" value="do not drink" id="do not drink">
                                                    <label class="form-check-label text-muted  fs-6" for="do not drink">
                                                        Don't drink</label>
                                                </div>
                                                <div class="form-check form-check-inline me-5">
                                                    <input class="form-check-input fs-5 highlight-radio" type="radio" name="drinkAlcohol" value="prefer not to say" id="prefer not to say">
                                                    <label class="form-check-label text-muted  fs-6" for="prefer not to say">
                                                        Prefre not to say </label>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- my smoke   -->
                                        <div class="col-lg-12 mt-2">
                                            <div class="mb-3">
                                                <p class="text-muted">Do you smoke?</p>
                                                <hr>
                                                <div class="form-check form-check-inline me-5">
                                                    <input class="form-check-input fs-5 highlight-radio" type="radio" name="smoking" value="do smoke" id="do smoke">
                                                    <label class="form-check-label  text-muted  fs-6" for="do smoke">
                                                        Do smoke </label>
                                                </div>
                                                <div class="form-check form-check-inline me-5">
                                                    <input class="form-check-input fs-5 highlight-radio" type="radio" name="smoking" value="occasionally smoke id=" occasionally smoke">
                                                    <label class="form-check-label  text-muted  fs-6" for="occasionally smoke">
                                                        Occasionally smoke</label>
                                                </div>
                                                <div class="form-check form-check-inline me-5">
                                                    <input class="form-check-input fs-5 highlight-radio" type="radio" name="smoking" value="do not smoke" id="do not smoke">
                                                    <label class="form-check-label  text-muted  fs-6" for="do not smoke">
                                                        Don't smoke</label>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Marital Status:   -->
                                        <div class="col-lg-12 mt-2">
                                            <div class="mb-3">
                                                <p class="text-muted">Marital Status:
                                                </p>
                                                <hr>
                                                <!--  -->
                                                <div class="form-check form-check-inline me-5">
                                                    <input class="form-check-input fs-5 highlight-radio" type="radio" name="maritalStatus" value="single" id="single">
                                                    <label class="form-check-label text-muted  fs-6" for="single">
                                                        Single </label>
                                                </div>
                                                <div class="form-check form-check-inline me-5">
                                                    <input class="form-check-input fs-5 highlight-radio" type="radio" name="maritalStatus" value="separated" id="separated">
                                                    <label class="form-check-label text-muted  fs-6" for="separated">
                                                        Separated</label>
                                                </div>
                                                <div class="form-check form-check-inline me-5">
                                                    <input class="form-check-input fs-5 highlight-radio" type="radio" name="maritalStatus" value="widowed" id="widowed">
                                                    <label class="form-check-label text-muted  fs-6" for="widowed">
                                                        Widowed</label>
                                                </div>
                                                <div class="form-check form-check-inline me-5">
                                                    <input class="form-check-input fs-5 highlight-radio" type="radio" name="maritalStatus" value="divorced" id="divorced">
                                                    <label class="form-check-label text-muted  fs-6" for="divorced">
                                                        Divorced</label>
                                                </div>
                                                <div class="form-check form-check-inline me-5">
                                                    <input class="form-check-input fs-5 highlight-radio" type="radio" name="maritalStatus" value="other" id="other">
                                                    <label class="form-check-label text-muted  fs-6" for="other">
                                                        Other</label>
                                                </div>
                                                <div class="form-check form-check-inline me-5">
                                                    <input class="form-check-input fs-5 highlight-radio" type="radio" name="maritalStatus" value="prefer not to say" id="prefer not to say">
                                                    <label class="form-check-label text-muted  fs-6" for="prefer not to say">
                                                        Prefer not to say</label>
                                                </div>
                                            </div>

                                        </div>
                                        <!-- Do you want (more) children?  -->
                                        <div class="col-lg-12 mt-2">
                                            <div class="mb-3">
                                                <p class="text-muted">Do you want (more) children?
                                                </p>
                                                <hr>
                                                <div class="form-check form-check-inline me-5">
                                                    <input class="form-check-input fs-5 highlight-radio" type="radio" name="children" value="yes" id="yes">
                                                    <label class="form-check-label  text-muted  fs-6" for="yes">
                                                        Yes </label>
                                                </div>

                                                <div class="form-check form-check-inline me-5">
                                                    <input class="form-check-input fs-5 highlight-radio" type="radio" name="children" value="not sure" id="not sure">
                                                    <label class="form-check-label  text-muted  fs-6" for="not sure">
                                                        Not Sure</label>
                                                </div>

                                                <div class="form-check form-check-inline me-5">
                                                    <input class="form-check-input fs-5 highlight-radio" type="radio" name="children" value="no" id="no">
                                                    <label class="form-check-label  text-muted  fs-6" for="no">
                                                        No</label>
                                                </div>


                                            </div>
                                            <!--Relationship you're looking for:    -->
                                            <div class="col-lg-12 mt-2">
                                                <div class="mb-3">
                                                    <p class="text-muted">Relationship you're looking for:</p>
                                                    <hr>
                                                    <div class="form-check form-check-inline me-5">
                                                        <input class="form-check-input fs-5 highlight-radio" name="relationshipLooking" type="checkbox" id="marriage" value="marriage">
                                                        <label class="form-check-label text-muted  fs-6" for="marriage">Marriage</label>
                                                    </div>
                                                    <div class="form-check form-check-inline me-5">
                                                        <input class="form-check-input fs-5 highlight-radio" type="checkbox" name="relationshipLooking" id="friendship" value="friendship">
                                                        <label class="form-check-label text-muted  fs-6" for="friendship">Friendship</label>
                                                    </div>
                                                </div>
                                            </div>


                                            <!-- Save Profile Button -->
                                            <div class="row mb-3">
                                                <div class="col-lg-12 text-center">
                                                    <button type="submit" id="btnSaveProfile" name="btnSaveProfile" class="btn btn-primary">Save Profile</button>
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

    </div><!-- row end -->
    </div>
    <?php include 'userlayout/footer.php'; ?>

    <!-- Add Bootstrap JavaScript bundle with Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>
</body>

</html>
</body>