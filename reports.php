<?php
session_start();
include("config/database.php");

// Check if user is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION["user_id"];

// Get Total Income
$incomeQuery = mysqli_prepare(
    $conn,
    "SELECT SUM(amount) AS total FROM transactions
     WHERE user_id = ? AND type = 'Income'"
);

mysqli_stmt_bind_param($incomeQuery, "i", $user_id);
mysqli_stmt_execute($incomeQuery);
$incomeResult = mysqli_stmt_get_result($incomeQuery);
$income = mysqli_fetch_assoc($incomeResult)['total'];

if ($income == NULL) {
    $income = 0;
}

// Get Total Expense
$expenseQuery = mysqli_prepare(
    $conn,
    "SELECT SUM(amount) AS total FROM transactions
     WHERE user_id = ? AND type = 'Expense'"
);

mysqli_stmt_bind_param($expenseQuery, "i", $user_id);
mysqli_stmt_execute($expenseQuery);
$expenseResult = mysqli_stmt_get_result($expenseQuery);
$expense = mysqli_fetch_assoc($expenseResult)['total'];

if ($expense == NULL) {
    $expense = 0;
}

// Calculate Balance
$balance = $income - $expense;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports | FINORA</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="css/dashboard.css">

    <link rel="stylesheet" href="css/reports.css">

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>

<?php include("includes/dashboard_navbar.php"); ?>

<div class="report-container">

    <h2>📊 Reports</h2>
    <p>Income vs Expense Analysis</p>

    <div class="chart-card">
    <canvas id="expenseChart"></canvas>
</div>

<div class="summary-card">

    <div class="item income-card">
        <h4>Total Income</h4>
        <p>₹<?php echo number_format($income, 2); ?></p>
    </div>

    <div class="item expense-card">
        <h4>Total Expense</h4>
        <p>₹<?php echo number_format($expense, 2); ?></p>
    </div>

    <div class="item balance-card">
        <h4>Balance</h4>
        <p>₹<?php echo number_format($balance, 2); ?></p>
    </div>

</div>

</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
<script>

const ctx = document.getElementById('expenseChart');

new Chart(ctx, {
    type: 'pie',
    data: {
        labels: ['Income', 'Expense'],
        datasets: [{
            data: [
                <?php echo $income; ?>,
                <?php echo $expense; ?>
            ],
            backgroundColor: [
                '#198754',
                '#dc3545'
            ],
            borderWidth: 2
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom'
            },
            title: {
                display: true,
                text: 'Income vs Expense'
            }
        }
    }
});

</script>
</body>
</html>