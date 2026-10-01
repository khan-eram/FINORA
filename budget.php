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

// Current month and year
$current_month = date("n");
$current_year = date("Y");

// Save Budget
if (isset($_POST["save_budget"])) {

    $budget_amount = $_POST["budget_amount"];
    $month = $_POST["month"];
    $year = $_POST["year"];

    if ($budget_amount > 0) {

        // Check if budget already exists
        $check_query = mysqli_prepare(
            $conn,
            "SELECT id FROM budgets
             WHERE user_id = ?
             AND month = ?
             AND year = ?"
        );

        mysqli_stmt_bind_param(
            $check_query,
            "iii",
            $user_id,
            $month,
            $year
        );

        mysqli_stmt_execute($check_query);
        $check_result = mysqli_stmt_get_result($check_query);

        if (mysqli_num_rows($check_result) > 0) {

            // Update existing budget
            $update_query = mysqli_prepare(
                $conn,
                "UPDATE budgets
                 SET amount = ?
                 WHERE user_id = ?
                 AND month = ?
                 AND year = ?"
            );

            mysqli_stmt_bind_param(
                $update_query,
                "diii",
                $budget_amount,
                $user_id,
                $month,
                $year
            );

            mysqli_stmt_execute($update_query);

            $message = "Budget updated successfully!";

        } else {

            // Insert new budget
            $insert_query = mysqli_prepare(
                $conn,
                "INSERT INTO budgets
                (user_id, amount, month, year)
                VALUES (?, ?, ?, ?)"
            );

            mysqli_stmt_bind_param(
                $insert_query,
                "idii",
                $user_id,
                $budget_amount,
                $month,
                $year
            );

            mysqli_stmt_execute($insert_query);

            $message = "Budget saved successfully!";
        }
    }
}

// Selected month/year
if (isset($_POST["month"]) && isset($_POST["year"])) {

    $selected_month = intval($_POST["month"]);
    $selected_year = intval($_POST["year"]);

} elseif (isset($_GET["month"]) && isset($_GET["year"])) {

    $selected_month = intval($_GET["month"]);
    $selected_year = intval($_GET["year"]);

} else {

    $selected_month = $current_month;
    $selected_year = $current_year;
}


// Get Budget
$budget_query = mysqli_prepare(
    $conn,
    "SELECT amount FROM budgets
     WHERE user_id = ?
     AND month = ?
     AND year = ?"
);

mysqli_stmt_bind_param(
    $budget_query,
    "iii",
    $user_id,
    $selected_month,
    $selected_year
);

mysqli_stmt_execute($budget_query);

$budget_result = mysqli_stmt_get_result($budget_query);

$budget_amount = 0;

if ($budget_row = mysqli_fetch_assoc($budget_result)) {
    $budget_amount = $budget_row["amount"];
}


// Get Total Expenses
$expense_query = mysqli_prepare(
    $conn,
    "SELECT COALESCE(SUM(amount), 0) AS total_expense
     FROM transactions
     WHERE user_id = ?
     AND LOWER(type) = 'expense'
     AND MONTH(transaction_date) = ?
     AND YEAR(transaction_date) = ?"
);

mysqli_stmt_bind_param(
    $expense_query,
    "iii",
    $user_id,
    $selected_month,
    $selected_year
);

mysqli_stmt_execute($expense_query);

$expense_result = mysqli_stmt_get_result($expense_query);

$expense_row = mysqli_fetch_assoc($expense_result);

$total_expense = $expense_row["total_expense"];


// Calculate remaining amount
$remaining = $budget_amount - $total_expense;


// Calculate percentage
if ($budget_amount > 0) {
    $percentage = ($total_expense / $budget_amount) * 100;
} else {
    $percentage = 0;
}

if ($percentage > 100) {
    $percentage = 100;
}


// Budget status
if ($budget_amount == 0) {

    $status = "No budget set";

} elseif ($total_expense > $budget_amount) {

    $status = "Budget exceeded";

} elseif ($percentage >= 80) {

    $status = "Almost reached";

} else {

    $status = "Within budget";
}


$month_name = date("F", mktime(0, 0, 0, $selected_month, 1));

// Get Expense Breakdown by Category
$category_query = mysqli_prepare(
    $conn,
    "SELECT category, SUM(amount) AS category_total
     FROM transactions
     WHERE user_id = ?
     AND LOWER(type) = 'expense'
     AND MONTH(transaction_date) = ?
     AND YEAR(transaction_date) = ?
     GROUP BY category
     ORDER BY category_total DESC"
);

mysqli_stmt_bind_param(
    $category_query,
    "iii",
    $user_id,
    $selected_month,
    $selected_year
);

mysqli_stmt_execute($category_query);

$category_result = mysqli_stmt_get_result($category_query);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Budget | FINORA</title>

    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/budget.css">
    
</head>

<body>

<?php include("includes/dashboard_navbar.php"); ?>


<div class="budget-container">

    <div class="budget-header">

        <h1>💰 Budget</h1>

        <p>
            Manage your monthly spending limit and track your expenses.
        </p>

    </div>


    <?php if ($message != "") { ?>

        <div class="budget-message">
            <?php echo $message; ?>
        </div>

    <?php } ?>


    <!-- Set Budget -->

    <div class="budget-form">

        <h2>Set Monthly Budget</h2>

        <form method="POST">

            <div class="form-row">

                <div class="form-group">

                    <label>Budget Amount (₹)</label>

                    <input
                        type="number"
                        name="budget_amount"
                        step="0.01"
                        min="1"
                        placeholder="Enter budget amount"
                        value="<?php echo $budget_amount > 0 ? $budget_amount : ''; ?>"
                        required
>
                 </div>


                <div class="form-group">

                    <label>Month</label>

                    <select name="month" onchange="this.form.submit()">

                        <?php

                        for ($i = 1; $i <= 12; $i++) {

                            $selected = ($i == $selected_month)
                                ? "selected"
                                : "";

                            echo "<option value='$i' $selected>"
                                . date("F", mktime(0, 0, 0, $i, 1))
                                . "</option>";
                        }

                        ?>

                    </select>

                </div>


                <div class="form-group">

                    <label>Year</label>

                    <select name="year" onchange="this.form.submit()">

                        <?php

                        for ($y = date("Y") - 2;
                             $y <= date("Y") + 2;
                             $y++) {

                            $selected = ($y == $selected_year)
                                ? "selected"
                                : "";

                            echo "<option value='$y' $selected>$y</option>";
                        }

                        ?>

                    </select>

                </div>

            </div>


            <button
                type="submit"
                name="save_budget"
                class="save-budget-btn"
            >
                Save Budget
            </button>

        </form>

    </div>


    <!-- Summary -->

    <div class="budget-summary">

        <div class="budget-card">

            <h3>Monthly Budget</h3>

            <div class="amount">
                ₹<?php echo number_format($budget_amount, 2); ?>
            </div>

        </div>


        <div class="budget-card">

            <h3>Total Spent</h3>

            <div class="amount">
                ₹<?php echo number_format($total_expense, 2); ?>
            </div>

        </div>


        <div class="budget-card">

            <h3>Remaining</h3>

            <div class="amount">

                ₹<?php echo number_format(abs($remaining), 2); ?>

            </div>

        </div>

    </div>


        <!-- Progress -->

    <div class="progress-card">

        <h2>
            <?php echo $month_name; ?> <?php echo $selected_year; ?>
        </h2>

        <br>

        <div class="progress-info">

            <span>
                Budget Usage
            </span>

            <span>
                <?php echo number_format($percentage, 1); ?>%
            </span>

        </div>

        <div class="progress-bar">

            <div
                class="progress-fill"
                style="width: <?php echo $percentage; ?>%;"
            ></div>

        </div>

        <div class="budget-status">

            <?php echo $status; ?>

        </div>

    </div>


    <!-- Expense Breakdown -->

    <div class="progress-card" style="margin-top: 25px;">

        <h2>Expense Breakdown</h2>

        <p style="color:#64748b; margin-bottom:20px;">
            Your expenses by category for
            <?php echo $month_name . " " . $selected_year; ?>
        </p>

        <?php if (mysqli_num_rows($category_result) > 0) { ?>

            <?php while ($category = mysqli_fetch_assoc($category_result)) { ?>

                <div style="
                    display:flex;
                    justify-content:space-between;
                    align-items:center;
                    padding:15px 0;
                    border-bottom:1px solid #e2e8f0;
                ">

                    <span style="font-weight:600;">
                        <?php echo htmlspecialchars($category["category"]); ?>
                    </span>

                    <span style="font-weight:700;">
                        ₹<?php echo number_format($category["category_total"], 2); ?>
                    </span>

                </div>

            <?php } ?>

        <?php } else { ?>

            <p style="color:#64748b;">
                No expenses recorded for this month.
            </p>

        <?php } ?>

    </div>

</div>


</body>

</html>