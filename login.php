<?php
session_start();
include("config/database.php");

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    // Check if email exists
    $check = mysqli_prepare($conn, "SELECT id, name, password FROM users WHERE email = ?");
    mysqli_stmt_bind_param($check, "s", $email);
    mysqli_stmt_execute($check);

    $result = mysqli_stmt_get_result($check);

    if ($row = mysqli_fetch_assoc($result)) {

        // Verify Password
        if (password_verify($password, $row["password"])) {

            $_SESSION["user_id"] = $row["id"];
            $_SESSION["user_name"] = $row["name"];

            header("Location: dashboard.php");
            exit();

        } else {
            $message = "Incorrect Password!";
        }

    } else {
        $message = "Email not found!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Login | FINORA</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<link rel="stylesheet" href="css/register.css">

</head>

<body>

<div class="register-container">

<div class="register-box">

<h1>Login</h1>

<p>Welcome Back!</p>

<?php if($message!=""){ ?>

<p style="color:red;"><?php echo $message; ?></p>

<?php } ?>

<form method="POST">

<input type="email" name="email" placeholder="Email Address" required>

<input type="password" name="password" placeholder="Password" required>

<button type="submit">Login</button>

</form>

<p class="login-link">

Don't have an account?

<a href="register.php">Create Account</a>

</p>

</div>

</div>

</body>

</html>