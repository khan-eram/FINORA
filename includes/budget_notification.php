<?php

// ======================================
// FINORA BUDGET NOTIFICATION
// ======================================

if (!isset($_SESSION["user_id"])) {
    return;
}

$user_id = $_SESSION["user_id"];

// Current month and year
$current_month = date("n");
$current_year = date("Y");

// Get current month's budget
$budget_query = mysqli_prepare(
    $conn,
    "SELECT amount
     FROM budgets
     WHERE user_id = ?
     AND month = ?
     AND year = ?"
);

mysqli_stmt_bind_param(
    $budget_query,
    "iii",
    $user_id,
    $current_month,
    $current_year
);

mysqli_stmt_execute($budget_query);

$budget_result = mysqli_stmt_get_result($budget_query);

$notification_budget = 0;

if ($budget_row = mysqli_fetch_assoc($budget_result)) {
    $notification_budget = $budget_row["amount"];
}


// Get current month's expenses
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
    $current_month,
    $current_year
);

mysqli_stmt_execute($expense_query);

$expense_result = mysqli_stmt_get_result($expense_query);

$notification_expense = 0;

if ($expense_row = mysqli_fetch_assoc($expense_result)) {
    $notification_expense = $expense_row["total_expense"];
}


// Default notification
$budget_notification = "";


// Check budget
if ($notification_budget > 0) {

    $usage_percentage =
        ($notification_expense / $notification_budget) * 100;


    // Budget exceeded
    if ($notification_expense > $notification_budget) {

        $exceeded_amount =
            $notification_expense - $notification_budget;

        $budget_notification =
            "🚨 Budget Exceeded! You have exceeded your monthly budget by ₹"
            . number_format($exceeded_amount, 2);


    // 90% reached
    } elseif ($usage_percentage >= 90) {

        $budget_notification =
            "🟠 Budget Alert! You have used "
            . number_format($usage_percentage, 1)
            . "% of your monthly budget.";


    // 80% reached
    } elseif ($usage_percentage >= 80) {

        $budget_notification =
            "🟡 Budget Alert! You have used "
            . number_format($usage_percentage, 1)
            . "% of your monthly budget.";

    }

}

?>