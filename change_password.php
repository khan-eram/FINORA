<?php
session_start();
include("config/database.php");

// Check if user is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION["user_id"];

$message = "";
$message_type = "";

// When form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $current_password = $_POST["current_password"];
    $new_password = $_POST["new_password"];
    $confirm_password = $_POST["confirm_password"];

    // Check if all fields are filled
    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {

        $message = "Please fill in all fields.";
        $message_type = "error";

    }
    // Check new password and confirm password
    elseif ($new_password !== $confirm_password) {

        $message = "New password and confirm password do not match.";
        $message_type = "error";

    }
    // Check minimum password length
    elseif (strlen($new_password) < 6) {

        $message = "New password must be at least 6 characters.";
        $message_type = "error";

    }
    else {

        // Get current password from database
        $query = "SELECT password FROM users WHERE id = '$user_id'";
        $result = mysqli_query($conn, $query);

        if ($result && mysqli_num_rows($result) == 1) {

            $user = mysqli_fetch_assoc($result);

            // Verify current password
            if (password_verify($current_password, $user["password"])) {

                // Hash new password
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

                // Update password
                $update_query = "UPDATE users 
                                 SET password = '$hashed_password' 
                                 WHERE id = '$user_id'";

                if (mysqli_query($conn, $update_query)) {

                    $message = "Password changed successfully!";
                    $message_type = "success";

                } else {

                    $message = "Something went wrong. Please try again.";
                    $message_type = "error";
                }

            } else {

                $message = "Current password is incorrect.";
                $message_type = "error";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Change Password - FINORA</title>

    <!-- Dashboard CSS -->
    <link rel="stylesheet" href="css/dashboard.css">

    <style>

        .password-page {
            max-width: 700px;
            margin: 50px auto;
            padding: 0 25px;
        }

        .password-header {
            margin-bottom: 30px;
        }

        .password-header h1 {
            font-size: 30px;
            color: #1E293B;
            margin-bottom: 8px;
        }

        .password-header p {
            color: #64748B;
            font-size: 15px;
        }

        .password-card {
            background: white;
            padding: 35px;
            border-radius: 20px;
            box-shadow: 0 8px 25px rgba(15, 23, 42, 0.08);
        }

        .form-group {
            margin-bottom: 22px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #1E293B;
        }

        .form-group input {
            width: 100%;
            padding: 13px 15px;
            border: 1px solid #CBD5E1;
            border-radius: 10px;
            font-size: 15px;
            outline: none;
            box-sizing: border-box;
        }

        .form-group input:focus {
            border-color: #2563EB;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.10);
        }

        .password-button {
            width: 100%;
            padding: 14px;
            border: none;
            border-radius: 10px;
            background: #2563EB;
            color: white;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
        }

        .password-button:hover {
            background: #1D4ED8;
        }

        .message {
            padding: 12px 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .message.success {
            background: #DCFCE7;
            color: #166534;
        }

        .message.error {
            background: #FEE2E2;
            color: #991B1B;
        }

        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: #2563EB;
            text-decoration: none;
            font-weight: 600;
        }

        .back-link:hover {
            text-decoration: underline;
        }

    </style>

</head>

<body>

<?php include("includes/dashboard_navbar.php"); ?>

<div class="password-page">

    <div class="password-header">

        <h1>Change Password</h1>

        <p>Update your FINORA account password securely.</p>

    </div>

    <div class="password-card">

        <?php if (!empty($message)) { ?>

            <div class="message <?php echo $message_type; ?>">
                <?php echo $message; ?>
            </div>

        <?php } ?>

        <form method="POST">

            <div class="form-group">

                <label for="current_password">
                    Current Password
                </label>

                <input
                    type="password"
                    id="current_password"
                    name="current_password"
                    placeholder="Enter your current password"
                    required
                >

            </div>

            <div class="form-group">

                <label for="new_password">
                    New Password
                </label>

                <input
                    type="password"
                    id="new_password"
                    name="new_password"
                    placeholder="Enter your new password"
                    required
                >

            </div>

            <div class="form-group">

                <label for="confirm_password">
                    Confirm New Password
                </label>

                <input
                    type="password"
                    id="confirm_password"
                    name="confirm_password"
                    placeholder="Confirm your new password"
                    required
                >

            </div>

            <button type="submit" class="password-button">
                Change Password
            </button>

        </form>

        <a href="settings.php" class="back-link">
            ← Back to Settings
        </a>

    </div>

</div>

</body>
</html>