<?php

session_start();

include("config/database.php");

// Check if user is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION["user_id"];


// ======================================
// UPDATE PROFILE
// ======================================

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");

    if ($name !== "" && $email !== "") {

        // Check if email is already used by another user
        $check = mysqli_prepare(
            $conn,
            "SELECT id FROM users WHERE email = ? AND id != ?"
        );

        mysqli_stmt_bind_param(
            $check,
            "si",
            $email,
            $user_id
        );

        mysqli_stmt_execute($check);

        $check_result = mysqli_stmt_get_result($check);

        if (mysqli_num_rows($check_result) > 0) {

            $error = "This email is already registered.";

        } else {

            // Update profile
            $stmt = mysqli_prepare(
                $conn,
                "UPDATE users
                 SET name = ?, email = ?
                 WHERE id = ?"
            );

            mysqli_stmt_bind_param(
                $stmt,
                "ssi",
                $name,
                $email,
                $user_id
            );

            if (mysqli_stmt_execute($stmt)) {

                // Update session name
                $_SESSION["user_name"] = $name;

                $success = "Profile updated successfully!";

            } else {

                $error = "Unable to update profile.";
            }

            mysqli_stmt_close($stmt);
        }

        mysqli_stmt_close($check);
    }
}


// ======================================
// GET USER INFORMATION
// ======================================

$stmt = mysqli_prepare(
    $conn,
    "SELECT name, email, created_at
     FROM users
     WHERE id = ?"
);

mysqli_stmt_bind_param(
    $stmt,
    "i",
    $user_id
);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$user = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>FINORA Profile</title>

    <link rel="stylesheet" href="css/dashboard.css">

    <link rel="stylesheet" href="css/profile.css">

</head>


<body>


<?php include("includes/dashboard_navbar.php"); ?>


<div class="profile-page">


    <!-- PROFILE HEADER -->

    <div class="profile-header">

        <div class="profile-avatar">
            <?php echo strtoupper(substr($user["name"], 0, 1)); ?>
        </div>

        <div>

            <h1>My Profile</h1>

            <p>
                Manage your FINORA account information
            </p>

        </div>

    </div>


    <!-- PROFILE CARD -->

    <div class="profile-card">


        <div class="profile-card-title">

            <h2>Personal Information</h2>

            <p>
                Update your name and email address.
            </p>

        </div>


        <?php if (isset($success)) { ?>

            <div class="profile-success">
                <?php echo htmlspecialchars($success); ?>
            </div>

        <?php } ?>


        <?php if (isset($error)) { ?>

            <div class="profile-error">
                <?php echo htmlspecialchars($error); ?>
            </div>

        <?php } ?>


        <form method="POST">


            <!-- NAME -->

            <div class="profile-form-group">

                <label>Full Name</label>

                <input
                    type="text"
                    name="name"
                    value="<?php echo htmlspecialchars($user["name"]); ?>"
                    required
                >

            </div>


            <!-- EMAIL -->

            <div class="profile-form-group">

                <label>Email Address</label>

                <input
                    type="email"
                    name="email"
                    value="<?php echo htmlspecialchars($user["email"]); ?>"
                    required
                >

            </div>


            <!-- MEMBER SINCE -->

            <div class="profile-info">

                <span>Member since</span>

                <strong>
                    <?php
                    echo date(
                        "d F Y",
                        strtotime($user["created_at"])
                    );
                    ?>
                </strong>

            </div>


            <!-- SAVE BUTTON -->

            <button
                type="submit"
                class="profile-save-btn"
            >
                Save Changes
            </button>


        </form>


    </div>


</div>


</body>

</html>