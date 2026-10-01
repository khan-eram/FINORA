<?php
session_start();
include("config/database.php");

// Check if user is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION["user_id"];

// Fetch all categories for the logged-in user
$categoryQuery = mysqli_prepare(
    $conn,
    "SELECT DISTINCT category
     FROM transactions
     WHERE user_id = ?
     ORDER BY category ASC"
);

mysqli_stmt_bind_param($categoryQuery, "i", $user_id);
mysqli_stmt_execute($categoryQuery);

$categoryResult = mysqli_stmt_get_result($categoryQuery);

// Search value
$search = "";
$type = "";
$category = "";

if (isset($_GET['search'])) {
    $search = trim($_GET['search']);
}

if (isset($_GET['type'])) {
    $type = trim($_GET['type']);
}

if (isset($_GET['category'])) {
    $category = trim($_GET['category']);
}

// SQL Query
$searchValue = "%" . $search . "%";

$sql = "SELECT * FROM transactions WHERE user_id = ?";

$params = array($user_id);
$types = "i";

if ($search != "") {
    $sql .= " AND (category LIKE ? OR description LIKE ?)";
    $params[] = $searchValue;
    $params[] = $searchValue;
    $types .= "ss";
}

if ($type != "") {
    $sql .= " AND type = ?";
    $params[] = $type;
    $types .= "s";
}

if ($category != "") {
    $sql .= " AND category = ?";
    $params[] = $category;
    $types .= "s";
}

$sql .= " ORDER BY transaction_date DESC";

$query = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param($query, $types, ...$params);

mysqli_stmt_execute($query);

$result = mysqli_stmt_get_result($query);
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>View Transactions | FINORA</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<link rel="stylesheet" href="css/view_transactions.css">

</head>

<body>

<div class="container">

<div class="transaction-box">

<h1>My Transactions</h1>

<p>View all your income and expenses</p>

<form method="GET" class="search-box">

    <input
        type="text"
        name="search"
        placeholder="Search by Category or Description..."
        value="<?php echo htmlspecialchars($search); ?>">

    <select name="type">

        <option value="">All Types</option>

        <option value="Income" <?php if($type=="Income") echo "selected"; ?>>
            Income
        </option>

        <option value="Expense" <?php if($type=="Expense") echo "selected"; ?>>
            Expense
        </option>

    </select>

    <select name="category">

    <option value="">All Categories</option>

    <?php while($cat = mysqli_fetch_assoc($categoryResult)) { ?>

        <option
            value="<?php echo $cat['category']; ?>"
            <?php if($category == $cat['category']) echo "selected"; ?>>

            <?php echo htmlspecialchars($cat['category']); ?>

        </option>

    <?php } ?>

</select>

    <button type="submit">
        Filter
    </button>

</form>

<br>

<a href="export_pdf.php" class="action-btn">
    📄 Export to PDF
</a>

<br><br>

<div class="table-responsive">
<table>

    </div>


<tr>

<th>Type</th>
<th>Category</th>
<th>Amount</th>
<th>Description</th>
<th>Date</th>
<th>Action</th>

</tr>

<?php

if(mysqli_num_rows($result)>0){

while($row=mysqli_fetch_assoc($result)){

?>

<tr>

<td><?php echo $row['type']; ?></td>

<td><?php echo $row['category']; ?></td>

<td>₹<?php echo $row['amount']; ?></td>

<td><?php echo $row['description']; ?></td>

<td><?php echo $row['transaction_date']; ?></td>

<td>

<a class="action-btn"
href="edit_transaction.php?id=<?php echo $row['id']; ?>">
Edit
</a>

<a class="action-btn"
href="delete_transaction.php?id=<?php echo $row['id']; ?>"
onclick="return confirm('Are you sure you want to delete this transaction?');">
Delete
</a>

</td>

</tr>

<?php

}

}else{

?>

<tr>

<td colspan="6">

No Transactions Found.

</td>

</tr>

<?php

}

?>

</table>

</div>

<br>

<a href="dashboard.php" class="back-btn">

← Back to Dashboard

</a>

</div>

</div>

</body>

</html>