<?php include 'db_connect.php'; ?>
<?php 
if(isset($_GET['id'])){
    $qry = $conn->query("SELECT * FROM customer_detail WHERE C_No=".$_GET['id']);
    foreach($qry->fetch_array() as $k => $val){
        $$k = $val;
    }
}
?>
<div class="container-fluid">
    <div class="col-lg-12">
        <form id="manage-borrower">
            <input type="hidden" name="id" value="<?php echo isset($_GET['id']) ? $_GET['id'] : '' ?>">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="C_First_Name" class="control-label">First Name</label>
                        <input name="C_First_Name" class="form-control" required="" value="<?php echo isset($C_First_Name) ? $C_First_Name : '' ?>">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="C_Last_Name">Last Name</label>
                        <input name="C_Last_Name" class="form-control" required="" value="<?php echo isset($C_Last_Name) ? $C_Last_Name : '' ?>">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="Gender">Gender</label>
                        <select name="Gender" class="form-control" required="">
                            <option value="Male" <?php echo (isset($Gender) && $Gender == 'Male') ? 'selected' : ''; ?>>Male</option>
                            <option value="Female" <?php echo (isset($Gender) && $Gender == 'Female') ? 'selected' : ''; ?>>Female</option>
                            <option value="Other" <?php echo (isset($Gender) && $Gender == 'Other') ? 'selected' : ''; ?>>Other</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="C_Father_Name">Father's Name</label>
                        <input name="C_Father_Name" class="form-control" value="<?php echo isset($C_Father_Name) ? $C_Father_Name : '' ?>">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="C_Mother_Name">Mother's Name</label>
                        <input name="C_Mother_Name" class="form-control" value="<?php echo isset($C_Mother_Name) ? $C_Mother_Name : '' ?>">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="C_Birth_Date">Date of Birth</label>
                        <input type="date" name="C_Birth_Date" class="form-control" value="<?php echo isset($C_Birth_Date) ? $C_Birth_Date : '' ?>">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="C_Citizenship_No">Citizenship Number</label>
                        <input name="C_Citizenship_No" class="form-control" value="<?php echo isset($C_Citizenship_No) ? $C_Citizenship_No : '' ?>">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="C_Pan_No">PAN Number</label>
                        <input name="C_Pan_No" class="form-control" value="<?php echo isset($C_Pan_No) ? $C_Pan_No : '' ?>">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="C_Mobile_No">Mobile Number</label>
                        <input type="text" name="C_Mobile_No" class="form-control" value="<?php echo isset($C_Mobile_No) ? $C_Mobile_No : '' ?>">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="C_Email">Email</label>
                        <input type="email" name="C_Email" class="form-control" value="<?php echo isset($C_Email) ? $C_Email : '' ?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="C_Pincode">Pincode</label>
                        <input name="C_Pincode" class="form-control" value="<?php echo isset($C_Pincode) ? $C_Pincode : '' ?>">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="C_Citizenship_Doc">Citizenship Document</label>
                        <input type="file" name="C_Citizenship_Doc" class="form-control" value="<?php echo isset($C_Citizenship_Doc) ? $C_Citizenship_Doc : '' ?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="C_Pan_Doc">PAN Document</label>
                        <input type="file" name="C_Pan_Doc" class="form-control" value="<?php echo isset($C_Pan_Doc) ? $C_Pan_Doc : '' ?>">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="ProfileColor">Profile Color</label>
                        <input type="color" name="ProfileColor" class="form-control" value="<?php echo isset($ProfileColor) ? $ProfileColor : '' ?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="ProfileImage">Profile Image</label>
                        <input type="file" name="ProfileImage" class="form-control" value="<?php echo isset($ProfileImage) ? $ProfileImage : '' ?>">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="Bio">Bio</label>
                        <textarea name="Bio" id="" cols="30" rows="3" class="form-control"><?php echo isset($Bio) ? $Bio : '' ?></textarea>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    $('#manage-borrower').submit(function(e){
        e.preventDefault();
        start_load();
        $.ajax({
            url: 'ajax.php?action=save_borrower',
            method: 'POST',
            data: new FormData(this),
            contentType: false,
            processData: false,
            success: function(resp){
                if(resp == 1){
                    alert_toast("Borrower data successfully saved.","success");
                    setTimeout(function(){
                        location.reload();
                    }, 1500);
                }
            }
        });
    });
</script>
