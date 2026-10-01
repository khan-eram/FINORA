<?php
session_start();
include("config/database.php");

// Check if user is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $user_id = $_SESSION["user_id"];
    $type = $_POST["type"];
    $category = trim($_POST["category"]);
    $amount = $_POST["amount"];
    $description = trim($_POST["description"]);
    $transaction_date = $_POST["transaction_date"];

    // 1-Month Transaction Limit
    $today = new DateTime();
    $selected_date = new DateTime($transaction_date);
    $one_month_ago = new DateTime();
    $one_month_ago->modify("-1 month");

    // Check if transaction date is within the last 1 month
    if ($selected_date < $one_month_ago || $selected_date > $today) {

        $message = "Transaction date must be within the last 1 month.";

    } else {

        $insert = mysqli_prepare(
            $conn,
            "INSERT INTO transactions
            (user_id, type, category, amount, description, transaction_date)
            VALUES (?, ?, ?, ?, ?, ?)"
        );

        mysqli_stmt_bind_param(
            $insert,
            "issdss",
            $user_id,
            $type,
            $category,
            $amount,
            $description,
            $transaction_date
        );

        if (mysqli_stmt_execute($insert)) {
            if (mysqli_stmt_execute($insert)) {
                header("Location: dashboard.php");
            exit();
            }
            $message = "Failed to add transaction!";
        }

        mysqli_stmt_close($insert);
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Add Transaction | FINORA</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<link rel="stylesheet" href="css/register.css">

</head>

<body>

<div class="register-container">

    <div class="register-box">

        <h1>Add Transaction</h1>

        <p>Track your income and expenses</p>

        <?php if ($message != "") { ?>

            <p style="color:green;">
                <?php echo $message; ?>
            </p>

        <?php } ?>

        <form method="POST">

            <select name="type" required>

                <option value="">Select Type</option>

                <option value="Income">Income</option>

                <option value="Expense">Expense</option>

            </select>

            <input
                type="text"
                name="category"
                placeholder="Category"
                required
            >

            <input
                type="number"
                name="amount"
                step="0.01"
                placeholder="Amount"
                required
            >

            <textarea
                name="description"
                placeholder="Description"
                rows="4"
            ></textarea>

            <input
                type="date"
                name="transaction_date"
                required
            >

            <button type="submit">
                Save Transaction
            </button>

        </form>

        <p class="login-link">

            <a href="dashboard.php">
                ← Back to Dashboard
            </a>

        </p>

    </div>

</div>

</body>

</html>