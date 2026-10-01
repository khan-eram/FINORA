<?php
session_start();
include("config/database.php");

// Check if user is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION["user_id"];

// Check if ID exists
if (!isset($_GET['id'])) {
    header("Location: dashboard.php");
    exit();
}

$id = $_GET['id'];

// Get transaction details
$query = "SELECT * FROM transactions WHERE id='$id' AND user_id='$user_id'";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) == 0) {
    die("Transaction not found!");
}

$row = mysqli_fetch_assoc($result);

// Update transaction
if (isset($_POST['update'])) {

    $type = $_POST['type'];
    $category = $_POST['category'];
    $amount = $_POST['amount'];
    $description = $_POST['description'];
    $transaction_date = $_POST['transaction_date'];

    $update = "UPDATE transactions SET
                type='$type',
                category='$category',
                amount='$amount',
                description='$description',
                transaction_date='$transaction_date'
                WHERE id='$id' AND user_id='$user_id'";

    if (mysqli_query($conn, $update)) {
        header("Location: dashboard.php");
        exit();
    } else {
        echo "Update Failed!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Edit Transaction</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body class="bg-light">

<div class="container mt-5">

<div class="card shadow">

<div class="card-header bg-warning text-dark">
<h3>Edit Transaction</h3>
</div>

<div class="card-body">

<form method="POST">

<div class="mb-3">
<label class="form-label">Type</label>

<select name="type" class="form-select" required>

<option value="Income"
<?php if($row['type']=="Income") echo "selected"; ?>>
Income
</option>

<option value="Expense"
<?php if($row['type']=="Expense") echo "selected"; ?>>
Expense
</option>

</select>

</div>

<div class="mb-3">
<label class="form-label">Category</label>

<input
type="text"
name="category"
class="form-control"
value="<?php echo $row['category']; ?>"
required>

</div>

<div class="mb-3">
<label class="form-label">Amount</label>

<input
type="number"
step="0.01"
name="amount"
class="form-control"
value="<?php echo $row['amount']; ?>"
required>

</div>

<div class="mb-3">
<label class="form-label">Description</label>

<textarea
name="description"
class="form-control"
rows="3"><?php echo $row['description']; ?></textarea>

</div>

<div class="mb-3">
<label class="form-label">Transaction Date</label>

<input
type="date"
name="transaction_date"
class="form-control"
value="<?php echo $row['transaction_date']; ?>"
required>

</div>

<button
type="submit"
name="update"
class="btn btn-success">

Update Transaction

</button>

<a href="dashboard.php" class="btn btn-secondary">

Cancel

</a>

</form>

</div>

</div>

</div>

</body>
</html>