<?php
session_start();

include("config/database.php");
require("fpdf/fpdf.php");

// Check if user is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION["user_id"];

// Get transactions of logged in user
$sql = "SELECT * FROM transactions
        WHERE user_id = ?
        ORDER BY transaction_date DESC";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

// Create PDF
class PDF extends FPDF
{
    // Footer
    function Footer()
    {
        // Position 15 mm from bottom
        $this->SetY(-15);

        // Font
        $this->SetFont('Arial','I',9);

        // Gray text
        $this->SetTextColor(100,100,100);

        // Footer Text
        $this->Cell(
            0,
            10,
            'Developed by Eram | BudgetBuddy | Page '.$this->PageNo(),
            0,
            0,
            'C'
        );
    }
}

// Create PDF Object
$pdf = new PDF();

$pdf->AddPage();

$pdf->SetFont('Arial','B',18);
$pdf->Cell(190,10,'BudgetBuddy',0,1,'C');

$pdf->SetFont('Arial','',12);
$pdf->Cell(190,8,'Transaction Report',0,1,'C');

$pdf->Cell(190,8,'Generated on: '.date("d-m-Y"),0,1,'C');

$pdf->Ln(10);

// Table Header
$pdf->SetFillColor(13,110,253);

$pdf->SetTextColor(255,255,255);

$pdf->SetFont('Arial','B',11);

$pdf->Cell(30,10,'Date',1,0,'C',true);
$pdf->Cell(30,10,'Type',1,0,'C',true);
$pdf->Cell(35,10,'Category',1,0,'C',true);
$pdf->Cell(30,10,'Amount',1,0,'C',true);
$pdf->Cell(65,10,'Description',1,1,'C',true);

$pdf->Ln();

// Table Data
$pdf->SetFont('Arial','',10);
$pdf->SetTextColor(0,0,0);

$totalIncome = 0;
$totalExpense = 0;

while($row = mysqli_fetch_assoc($result))
{
    $pdf->Cell(30,10,$row['transaction_date'],1);
    $pdf->Cell(30,10,$row['type'],1);
    $pdf->Cell(35,10,$row['category'],1);
    $pdf->Cell(30,10,"Rs. ".$row['amount'],1,0,'C');
    $pdf->Cell(65,10,$row['description'],1);

    $pdf->Ln();

    if($row['type'] == "Income")
{
    $totalIncome += $row['amount'];
}
else
{
    $totalExpense += $row['amount'];
}

    
}

$balance = $totalIncome - $totalExpense;

$pdf->Ln(10);

$pdf->SetFont('Arial','B',12);

$pdf->Cell(60,10,'Total Income :');
$pdf->Cell(50,10,'Rs. '.number_format($totalIncome,2),0,1);

$pdf->Cell(60,10,'Total Expense :');
$pdf->Cell(50,10,'Rs. '.number_format($totalExpense,2),0,1);

$pdf->Cell(60,10,'Net Balance :');
$pdf->Cell(50,10,'Rs. '.number_format($balance,2),0,1);

$pdf->Output();

?>