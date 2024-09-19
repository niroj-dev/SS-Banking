<?php
session_start();
ini_set('display_errors', 1);
Class Action {
	private $db;

	public function __construct() {
		ob_start();
   	include 'db_connect.php';
    
    $this->db = $conn;
	}
	function __destruct() {
	    $this->db->close();
	    ob_end_flush();
	}

	function login(){
		extract($_POST);
		$qry = $this->db->query("SELECT * FROM users where username = '".$username."' and password = '".$password."' ");
		if($qry->num_rows > 0){
			foreach ($qry->fetch_array() as $key => $value) {
				if($key != 'passwors' && !is_numeric($key))
					$_SESSION['login_'.$key] = $value;
			}
				return 1;
		}else{
			return 3;
		}
	}
	function login2(){
		extract($_POST);
		$qry = $this->db->query("SELECT * FROM users where username = '".$email."' and password = '".md5($password)."' ");
		if($qry->num_rows > 0){
			foreach ($qry->fetch_array() as $key => $value) {
				if($key != 'passwors' && !is_numeric($key))
					$_SESSION['login_'.$key] = $value;
			}
				return 1;
		}else{
			return 3;
		}
	}
	function logout(){
		session_destroy();
		foreach ($_SESSION as $key => $value) {
			unset($_SESSION[$key]);
		}
		header("location:login.php");
	}
	function logout2(){
		session_destroy();
		foreach ($_SESSION as $key => $value) {
			unset($_SESSION[$key]);
		}
		header("location:../index.php");
	}

	function save_user(){
		extract($_POST);
		$data = " name = '$name' ";
		$data .= ", username = '$username' ";
		$data .= ", password = '$password' ";
		$data .= ", type = '$type' ";
		if(empty($id)){
			$save = $this->db->query("INSERT INTO users set ".$data);
		}else{
			$save = $this->db->query("UPDATE users set ".$data." where id = ".$id);
		}
		if($save){
			return 1;
		}
	}
	function signup(){
		extract($_POST);
		$data = " name = '$name' ";
		$data .= ", contact = '$contact' ";
		$data .= ", address = '$address' ";
		$data .= ", username = '$email' ";
		$data .= ", password = '".md5($password)."' ";
		$data .= ", type = 3";
		$chk = $this->db->query("SELECT * FROM users where username = '$email' ")->num_rows;
		if($chk > 0){
			return 2;
			exit;
		}
			$save = $this->db->query("INSERT INTO users set ".$data);
		if($save){
			$qry = $this->db->query("SELECT * FROM users where username = '".$email."' and password = '".md5($password)."' ");
			if($qry->num_rows > 0){
				foreach ($qry->fetch_array() as $key => $value) {
					if($key != 'passwors' && !is_numeric($key))
						$_SESSION['login_'.$key] = $value;
				}
			}
			return 1;
		}
	}

	function save_settings(){
		extract($_POST);
		$data = " name = '".str_replace("'","&#x2019;",$name)."' ";
		$data .= ", email = '$email' ";
		$data .= ", contact = '$contact' ";
		$data .= ", about_content = '".htmlentities(str_replace("'","&#x2019;",$about))."' ";
		if($_FILES['img']['tmp_name'] != ''){
						$fname = strtotime(date('y-m-d H:i')).'_'.$_FILES['img']['name'];
						$move = move_uploaded_file($_FILES['img']['tmp_name'],'../assets/img/'. $fname);
					$data .= ", cover_img = '$fname' ";

		}
		
		// echo "INSERT INTO system_settings set ".$data;
		$chk = $this->db->query("SELECT * FROM system_settings");
		if($chk->num_rows > 0){
			$save = $this->db->query("UPDATE system_settings set ".$data);
		}else{
			$save = $this->db->query("INSERT INTO system_settings set ".$data);
		}
		if($save){
		$query = $this->db->query("SELECT * FROM system_settings limit 1")->fetch_array();
		foreach ($query as $key => $value) {
			if(!is_numeric($key))
				$_SESSION['setting_'.$key] = $value;
		}

			return 1;
				}
	}

	
	function save_loan_type(){
		extract($_POST);
		$data = " type_name = '$type_name' ";
		$data .= " , description = '$description' ";
		if(empty($id)){
			$save = $this->db->query("INSERT INTO loan_types set ".$data);
		}else{
			$save = $this->db->query("UPDATE loan_types set ".$data." where id=".$id);
		}
		if($save)
			return 1;
	}
	function delete_loan_type(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM loan_types where id = ".$id);
		if($delete)
			return 1;
	}
	function save_plan(){
		extract($_POST);
		$data = " months = '$months' ";
		$data .= ", interest_percentage = '$interest_percentage' ";
		$data .= ", penalty_rate = '$penalty_rate' ";
		
		if(empty($id)){
			$save = $this->db->query("INSERT INTO onlinebanking set ".$data);
		}else{
			$save = $this->db->query("UPDATE onlinebanking set ".$data." where id=".$id);
		}
		if($save)
			return 1;
	}
	function delete_plan(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM onlinebanking where id = ".$id);
		if($delete)
			return 1;
	}
	function save_borrower(){
		extract($_POST);
	
		// Prepare data for insertion/update
		$data = "C_First_Name = '". $this->db->real_escape_string($C_First_Name) ."' ";
		$data .= ", C_Last_Name = '". $this->db->real_escape_string($C_Last_Name) ."' ";
		$data .= ", Gender = '". $this->db->real_escape_string($Gender) ."' ";
		$data .= ", C_Father_Name = '". $this->db->real_escape_string($C_Father_Name) ."' ";
		$data .= ", C_Mother_Name = '". $this->db->real_escape_string($C_Mother_Name) ."' ";
		$data .= ", C_Birth_Date = '". $this->db->real_escape_string($C_Birth_Date) ."' ";
		$data .= ", C_Citizenship_No = '". $this->db->real_escape_string($C_Citizenship_No) ."' ";
		$data .= ", C_Pan_No = '". $this->db->real_escape_string($C_Pan_No) ."' ";
		$data .= ", C_Mobile_No = '". $this->db->real_escape_string($C_Mobile_No) ."' ";
		$data .= ", C_Email = '". $this->db->real_escape_string($C_Email) ."' ";
		$data .= ", C_Pincode = '". $this->db->real_escape_string($C_Pincode) ."' ";
		$data .= ", ProfileColor = '". $this->db->real_escape_string($ProfileColor) ."' ";
		$data .= ", Bio = '". $this->db->real_escape_string($Bio) ."' ";
	
		// Handle file uploads
		if(isset($_FILES['C_Citizenship_Doc']) && $_FILES['C_Citizenship_Doc']['error'] == UPLOAD_ERR_OK) {
			$citizenship_doc = time() . '_' . $_FILES['C_Citizenship_Doc']['name'];
			move_uploaded_file($_FILES['C_Citizenship_Doc']['tmp_name'], 'uploads/' . $citizenship_doc);
			$data .= ", C_Citizenship_Doc = '". $this->db->real_escape_string($citizenship_doc) ."' ";
		}
		if(isset($_FILES['C_Pan_Doc']) && $_FILES['C_Pan_Doc']['error'] == UPLOAD_ERR_OK) {
			$pan_doc = time() . '_' . $_FILES['C_Pan_Doc']['name'];
			move_uploaded_file($_FILES['C_Pan_Doc']['tmp_name'], 'uploads/' . $pan_doc);
			$data .= ", C_Pan_Doc = '". $this->db->real_escape_string($pan_doc) ."' ";
		}
		if(isset($_FILES['ProfileImage']) && $_FILES['ProfileImage']['error'] == UPLOAD_ERR_OK) {
			$profile_image = time() . '_' . $_FILES['ProfileImage']['name'];
			move_uploaded_file($_FILES['ProfileImage']['tmp_name'], 'uploads/' . $profile_image);
			$data .= ", ProfileImage = '". $this->db->real_escape_string($profile_image) ."' ";
		}
	
		if(empty($id)){
			// Insert new record
			$save = $this->db->query("INSERT INTO customer_detail SET ".$data);
		}else{
			// Update existing record
			$save = $this->db->query("UPDATE customer_detail SET ".$data." WHERE C_No=".$id);
		}
	
		// Check for errors
		if(!$save) {
			return $this->db->error;
		}
		return 1;
	}
	
	
	function delete_borrower(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM customer_detail where C_No = ".$id);
		if($delete)
			return 1;
	}
	function save_loan() {
		// Extract POST data
		extract($_POST);
	
		// Prepare data for insertion/update
		$data = "borrower_id = $borrower_id, 
				 loan_type_id = '$loan_type_id', 
				 plan_id = '$plan_id', 
				 amount = '$amount', 
				 purpose = '$purpose'";
	
		if (isset($status)) {
			$data .= ", status = '$status'";
	
			if ($status == 2) {
				// Fetch plan details
				$plan = $this->db->query("SELECT * FROM loan_plan WHERE id = $plan_id")->fetch_array();
				$months = $plan['months'];
	
				// Collect schedule data
				$schedule_data = [];
				$existing_schedules = [];
				$sid = [];
	
				// Get existing schedules
				$chk_existing = $this->db->query("SELECT id, date_due FROM loan_schedules WHERE loan_id = $id");
				while ($existing = $chk_existing->fetch_array()) {
					$existing_schedules[$existing['date_due']] = $existing['id'];
				}
	
				// Generate new schedules
				for ($i = 1; $i <= $months; $i++) {
					$date = date("Y-m-d", strtotime("+$i months"));
					if (isset($existing_schedules[$date])) {
						// Update existing schedule
						$sid[] = $existing_schedules[$date];
						$schedule_data[] = "UPDATE loan_schedules SET date_due = '$date' WHERE id = " . $existing_schedules[$date];
					} else {
						// Insert new schedule
						$schedule_data[] = "INSERT INTO loan_schedules (loan_id, date_due) VALUES ($id, '$date')";
					}
				}
	
				// Execute batch updates/inserts
				foreach ($schedule_data as $query) {
					$this->db->query($query);
				}
	
				// Delete obsolete schedules
				$existing_ids = implode(",", $sid);
				if ($existing_ids) {
					$this->db->query("DELETE FROM loan_schedules WHERE loan_id = $id AND id NOT IN ($existing_ids)");
				} else {
					$this->db->query("DELETE FROM loan_schedules WHERE loan_id = $id");
				}
	
				$data .= ", date_released = '" . date("Y-m-d H:i") . "'";
			} else {
				// Delete schedules if status is not 'Released'
				$this->db->query("DELETE FROM loan_schedules WHERE loan_id = $id");
			}
		}
	
		// Handle new loan reference number
		if (empty($id)) {
			do {
				$ref_no = mt_rand(1, 99999999);
				$check = $this->db->query("SELECT id FROM loan_list WHERE ref_no = '$ref_no'")->num_rows;
			} while ($check > 0);
			
			$data .= ", ref_no = '$ref_no'";
			$save = $this->db->query("INSERT INTO loan_list SET $data");
		} else {
			$save = $this->db->query("UPDATE loan_list SET $data WHERE id = $id");
		}
	
		return $save ? 1 : 0;
	}
	
	function delete_loan(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM loan_list where id = ".$id);
		if($delete)
			return 1;
	}
	function save_payment(){
		extract($_POST);
			$data = " loan_id = $loan_id ";
			$data .= " , payee = '$payee' ";
			$data .= " , amount = '$amount' ";
			$data .= " , penalty_amount = '$penalty_amount' ";
			$data .= " , overdue = '$overdue' ";
		if(empty($id)){
			$save = $this->db->query("INSERT INTO payments set ".$data);
		}else{
			$save = $this->db->query("UPDATE payments set ".$data." where id = ".$id);

		}
		if($save)
			return 1;

	}
	function delete_payment(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM payments where id = ".$id);
		if($delete)
			return 1;
	}

}
