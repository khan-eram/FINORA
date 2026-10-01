<?php
include("config/database.php");

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $fullname = trim($_POST["fullname"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];

    // Check password match
    if ($password != $confirm_password) {
        $message = "Passwords do not match!";
    } else {

        // Check if email already exists
        $check = mysqli_prepare($conn, "SELECT id FROM users WHERE email = ?");
        mysqli_stmt_bind_param($check, "s", $email);
        mysqli_stmt_execute($check);
        mysqli_stmt_store_result($check);

        if (mysqli_stmt_num_rows($check) > 0) {
            $message = "Email already exists!";
        } else {

            // Hash password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Insert user
            $insert = mysqli_prepare($conn, "INSERT INTO users(name, email, password) VALUES (?, ?, ?)");
            mysqli_stmt_bind_param($insert, "sss", $fullname, $email, $hashedPassword);

            if (mysqli_stmt_execute($insert)) {
                echo "<script>
                    alert('Registration successful!');
                    window.location.href='login.php';
                </script>";
                exit();
            } else {
                $message = "Registration failed!";
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
<title>Register | FINORA</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="css/register.css">
</head>

<body>

<div class="register-container">
    <div class="register-box">

        <h1>Create Account</h1>
        <p>Join FINORA today</p>

        <?php if ($message != "") { ?>
            <p style="color:red;"><?php echo $message; ?></p>
        <?php } ?>

        <form action="" method="POST">

            <input type="text" name="fullname" placeholder="Full Name" required>

            <input type="email" name="email" placeholder="Email Address" required>

            <input type="password" name="password" placeholder="Password" required>

            <input type="password" name="confirm_password" placeholder="Confirm Password" required>

            <button type="submit">Create Account</button>

        </form>

        <p class="login-link">
            Already have an account?
            <a href="login.php">Login</a>
        </p>

    </div>
</div>

</body>
</html>