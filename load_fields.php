<?php 
include 'db_connect.php'; 

// Ensure $_POST data is validated and sanitized
$loan_id = isset($_POST['loan_id']) ? intval($_POST['loan_id']) : 0;
$id = isset($_POST['id']) ? intval($_POST['id']) : 0;

// Fetch payment details if $id is set
if ($id > 0) {
    $stmt = $conn->prepare("SELECT * FROM payments WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $payment = $result->fetch_assoc();
        foreach($payment as $k => $val) {
            $$k = $val;
        }
    }
    $stmt->close();
}

// Fetch loan details if $loan_id is set
if ($loan_id > 0) {
    $stmt = $conn->prepare("SELECT l.*, CONCAT(b.C_Last_Name, ', ', b.C_First_Name, ' ', b.C_First_Name) AS name, b.C_Mobile_No, b.C_Pincode 
                            FROM loan_list l 
                            INNER JOIN customer_detail b ON b.C_No = l.borrower_id 
                            WHERE l.id = ?");
    $stmt->bind_param("i", $loan_id);
    $stmt->execute();
    $loan = $stmt->get_result();
    if ($loan->num_rows > 0) {
        $meta = $loan->fetch_assoc();
        
        // Fetch loan type and plan details
        $stmt = $conn->prepare("SELECT * FROM loan_types WHERE id = ?");
        $stmt->bind_param("i", $meta['loan_type_id']);
        $stmt->execute();
        $type_arr = $stmt->get_result()->fetch_assoc();
        
        $stmt = $conn->prepare("SELECT *, CONCAT(months, ' month/s [ ', interest_percentage, '%, ', penalty_rate, ' ]') AS plan 
                                FROM loan_plan 
                                WHERE id = ?");
        $stmt->bind_param("i", $meta['plan_id']);
        $stmt->execute();
        $plan_arr = $stmt->get_result()->fetch_assoc();
        
        $monthly = ($meta['amount'] + ($meta['amount'] * ($plan_arr['interest_percentage'] / 100))) / $plan_arr['months'];
        $penalty = $monthly * ($plan_arr['penalty_rate'] / 100);
        
	// Fetch payments
	$stmt = $conn->prepare("SELECT * FROM payments WHERE loan_id = ?");
	$stmt->bind_param("i", $loan_id);
	$stmt->execute();
	$payments = $stmt->get_result();

	$paid = $payments->num_rows;
	$offset = $paid > 0 ? $paid : 0; // Use 0 if no payments to avoid issues

	$stmt = $conn->prepare("SELECT * FROM loan_schedules 
							WHERE loan_id = ? 
							ORDER BY date(date_due) ASC LIMIT 1 OFFSET ?");
	$stmt->bind_param("ii", $loan_id, $offset);
	$stmt->execute();
	$next_due_date_result = $stmt->get_result();
	$next_due_date_row = $next_due_date_result->fetch_assoc();
	$next_due_date = isset($next_due_date_row['date_due']) ? $next_due_date_row['date_due'] : null;

	$sum_paid = 0;
	if ($payments->num_rows > 0) {
		while ($p = $payments->fetch_assoc()) {
			$sum_paid += ($p['amount'] - $p['penalty_amount']);
		}
	}

}
}
?>

<!-- HTML and Form Fields -->
<div class="col-lg-12">
    <hr>
    <div class="row">
        <div class="col-md-5">
            <div class="form-group">
                <label for="">Payee</label>
                <input name="payee" class="form-control" required value="<?php echo isset($payee) ? htmlspecialchars($payee) : (isset($meta['name']) ? htmlspecialchars($meta['name']) : '') ?>">
            </div>
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="col-md-5">
            <p><small>Monthly amount:<b><?php echo number_format($monthly, 2) ?></b></small></p>
            <p><small>Penalty :<b><?php echo $add = (date('Ymd', strtotime($next_due_date)) < date("Ymd")) ? $penalty : 0; ?></b></small></p>
            <p><small>Payable Amount :<b><?php echo number_format($monthly + $add, 2) ?></b></small></p>
        </div>
        <div class="col-md-5">
            <div class="form-group">
                <label for="">Amount</label>
                <input type="number" name="amount" step="any" min="" class="form-control text-right" required value="<?php echo isset($amount) ? htmlspecialchars($amount) : '' ?>">
                <input type="hidden" name="penalty_amount" value="<?php echo $add ?>">
                <input type="hidden" name="loan_id" value="<?php echo $loan_id ?>">
                <input type="hidden" name="overdue" value="<?php echo $add > 0 ? 1 : 0 ?>">
            </div>
        </div>
    </div>
</div>
