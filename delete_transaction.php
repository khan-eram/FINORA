<?php
session_start();
include("config/database.php");

// Check if user is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

// Check if transaction ID is provided
if (isset($_GET['id'])) {

    $id = $_GET['id'];
    $user_id = $_SESSION['user_id'];

    // Get transaction date
    $dateQuery = mysqli_prepare(
        $conn,
        "SELECT transaction_date
         FROM transactions
         WHERE id = ? AND user_id = ?"
    );

    mysqli_stmt_bind_param($dateQuery, "ii", $id, $user_id);
    mysqli_stmt_execute($dateQuery);

    $dateResult = mysqli_stmt_get_result($dateQuery);

    if (mysqli_num_rows($dateResult) == 0) {
        echo "Transaction not found.";
        exit();
    }

    $transaction = mysqli_fetch_assoc($dateResult);
    $transaction_date = new DateTime($transaction['transaction_date']);

    // Today's date
    $today = new DateTime();

    // Date one month ago
    $one_month_ago = new DateTime();
    $one_month_ago->modify("-1 month");

    // Check if transaction is older than 1 month
    if ($transaction_date < $one_month_ago) {

        echo "This transaction is older than 1 month and cannot be deleted.";
        exit();

    }

    // Delete transaction
    $deleteQuery = mysqli_prepare(
        $conn,
        "DELETE FROM transactions
         WHERE id = ? AND user_id = ?"
    );

    mysqli_stmt_bind_param($deleteQuery, "ii", $id, $user_id);

    if (mysqli_stmt_execute($deleteQuery)) {

        header("Location: dashboard.php?message=deleted");
        exit();

    } else {

        echo "Error deleting transaction.";

    }

} else {

    header("Location: dashboard.php");
    exit();

}
?>