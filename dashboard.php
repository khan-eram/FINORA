<?php
session_start();

include("config/database.php");
// Check if user is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION["user_id"];

// Total Income
$income_query = mysqli_query(
    $conn,
    "SELECT SUM(amount) AS total_income
     FROM transactions
     WHERE user_id = '$user_id'
     AND type = 'Income'"
);

$income_data = mysqli_fetch_assoc($income_query);
$total_income = $income_data['total_income'] ?? 0;

// Total Expense
$expense_query = mysqli_query(
    $conn,
    "SELECT SUM(amount) AS total_expense
     FROM transactions
     WHERE user_id = '$user_id'
     AND type = 'Expense'"
);

$expense_data = mysqli_fetch_assoc($expense_query);
$total_expense = $expense_data['total_expense'] ?? 0;

// Balance
$balance = $total_income - $total_expense;
$recent_transactions = mysqli_query(
    $conn,
    "SELECT *
     FROM transactions
     WHERE user_id = '$user_id'
     ORDER BY transaction_date DESC, id DESC
     LIMIT 5"
);

// Check if user is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Dashboard | FINORA</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<link rel="stylesheet" href="css/dashboard.css">

<script src="js/theme.js"></script>

</head>

<body>

<?php include("includes/dashboard_navbar.php"); ?>

<div class="container">

    <!-- Welcome Section -->
    <div class="hero">

        <h2>
            Welcome Back,
            <?php echo $_SESSION["user_name"]; ?> 👋
        </h2>

        <p>
            Manage your income and expenses efficiently.
        </p>

    </div>

    <!-- Summary Cards -->

<div class="row mt-4 g-4">

    <div class="col-lg-4 col-md-4">
        <div class="card income-card h-100">
            <div class="card-body text-center">

                <div class="icon income-icon">💰</div>

                <h5>Total Income</h5>

                <h2>₹<?php echo number_format($total_income, 2); ?></h2>

            </div>
        </div>
    </div>

    <div class="col-lg-4 col-md-4">
        <div class="card expense-card h-100">
            <div class="card-body text-center">

                <div class="icon expense-icon">💸</div>

                <h5>Total Expense</h5>

                <h2>₹<?php echo number_format($total_expense, 2); ?></h2>

            </div>
        </div>
    </div>

    <div class="col-lg-4 col-md-4">
        <div class="card balance-card h-100">
            <div class="card-body text-center">

                <div class="icon balance-icon">💵</div>

                <h5>Balance</h5>

                <h2>₹<?php echo number_format($balance, 2); ?></h2>

            </div>
        </div>
    </div>

</div>
<!-- Recent Transactions -->

<div class="card mt-4">

    <div class="card-body">

        <h4 class="mb-4">📋 Recent Transactions</h4>

        <div class="table-responsive">

            <table class="table table-hover align-middle">

                <thead class="table-primary">

                    <tr>

                        <th>Type</th>
                        <th>Amount</th>
                        <th>Description</th>
                        <th>Date</th>
                        <th>Action</th>

                    </tr>

                </thead>

                <tbody>

                <?php if(mysqli_num_rows($recent_transactions) > 0){ ?>

                <?php while($row = mysqli_fetch_assoc($recent_transactions)){ ?>

        <tr>

            <td><?php echo $row['type']; ?></td>

            <td>₹<?php echo number_format($row['amount'],2); ?></td>

            <td><?php echo htmlspecialchars($row['description']); ?></td>

            <td><?php echo $row['transaction_date']; ?></td>

            <td>
                <a href="edit_transaction.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-warning">
                    Edit
                </a>

                <a href="delete_transaction.php?id=<?php echo $row['id']; ?>"
                   class="btn btn-sm btn-danger"
                   onclick="return confirm('Are you sure you want to delete this transaction?');">
                    Delete
                </a>
            </td>

        </tr>

    <?php } ?>

<?php } else { ?>

    <tr>

        <td colspan="5" class="text-center text-muted">

            No transactions found.

        </td>

    </tr>

<?php } ?>

</tbody>

            </table>

        </div>

        <div class="text-end">

            <a href="view_transactions.php" class="btn btn-outline-primary">

                View All Transactions →

            </a>

        </div>

    </div>

</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>