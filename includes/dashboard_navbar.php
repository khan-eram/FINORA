<?php
if(session_status() == PHP_SESSION_NONE){
    session_start();
}
?>

<nav class="dashboard-navbar">

    <div class="dashboard-logo">
        <img src="assets/images/finora-logo.png" alt="FINORA">
        <span>FINORA</span>
    </div>


    <div class="dashboard-links">

        <a href="dashboard.php">
            Dashboard
        </a>

        <a href="add_transaction.php">
            Add Transaction
        </a>

        <a href="view_transactions.php">
            Transactions
        </a>

        <a href="reports.php">
            Reports
        </a>

        <a href="budget.php">
            Budget
        </a>

        <a href="profile.php">
            Profile
        </a>

        <a href="chat.php">
           💬 FINORA Chat
        </a>

        <a href="settings.php">
            Settings
        </a>

        </div>

    <div class="dashboard-right">

        <a href="logout.php" class="logout-btn">
            Logout
        </a>

    </div>


</nav>