<?php

session_start();

include("config/database.php");

// Check if user is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION["user_id"];

// Load budget notification function
include("includes/budget_notification.php");

?>
<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>FINORA Settings</title>

    <link rel="stylesheet" href="css/dashboard.css">

    <link rel="stylesheet" href="css/settings.css">

</head>

<body>

<?php include("includes/dashboard_navbar.php"); ?>


<div class="settings-page">


    <!-- SETTINGS HEADER -->

    <div class="settings-header">

        <div class="settings-icon">
            ⚙️
        </div>

        <div>

            <h1>Settings</h1>

            <p>
                Manage your FINORA preferences
            </p>

        </div>

    </div>


    <!-- SETTINGS CARD -->

    <div class="settings-card">


        <!-- NOTIFICATIONS -->

        <div class="settings-section">

            <div class="settings-section-icon">
                🔔
            </div>

            <div class="settings-section-content">

                <h2>Notifications</h2>

                <p>
                    Manage your FINORA notification preferences.
                </p>

                <label class="toggle-row">

                    <span>
                       Enable notifications
                    </span>

                <input
                  type="checkbox"
                  id="notificationToggle"
                 >

                <span class="toggle-slider"></span>

                </label>

<?php if ($budget_notification != "") { ?>

    <div id="budgetNotification" class="budget-notification">
        <?php echo htmlspecialchars($budget_notification); ?>
    </div>

<?php } ?>
            </div>

        </div>


        <div class="settings-divider"></div>


        <!-- SECURITY -->

        <div class="settings-section">

            <div class="settings-section-icon">
                🔐
            </div>

            <div class="settings-section-content">

                <h2>Security</h2>

                <p>
                    Keep your FINORA account secure.
                </p>

                <a
                    href="change_password.php"
                    class="settings-action-btn"
                >
                    Change Password
                </a>

            </div>

        </div>


        <div class="settings-divider"></div>


        <!-- ACCOUNT -->

        <div class="settings-section">

            <div class="settings-section-icon">
                👤
            </div>

            <div class="settings-section-content">

                <h2>Account</h2>

                <p>
                    View and manage your account information.
                </p>

                <a
                    href="profile.php"
                    class="settings-action-btn secondary"
                >
                    View Profile
                </a>

            </div>

        </div>


    </div>


</div>


<script>

// ======================================
// NOTIFICATIONS
// ======================================

const notificationToggle =
    document.getElementById(
        "notificationToggle"
    );


const savedNotifications =
    localStorage.getItem(
        "finoraNotifications"
    );


if (savedNotifications === "on") {

    notificationToggle.checked = true;

}


notificationToggle.addEventListener(
    "change",
    function() {

        if (this.checked) {

            localStorage.setItem(
                "finoraNotifications",
                "on"
            );

        } else {

            localStorage.setItem(
                "finoraNotifications",
                "off"
            );

        }

    }
);

</script>


</body>

</html>
```
