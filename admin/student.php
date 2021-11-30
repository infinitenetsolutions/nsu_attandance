<?php
$page=4;
$conn = '';
if (isset($_POST["importExcelButton"])) {

  // here i to creating the connection for exporting the data in to excel
  if ($_SERVER['HTTP_HOST'] == "localhost") {
    $conn = mysqli_connect("localhost", "root", "", "nsunivac_attendance");
  } else {
    $conn = mysqli_connect("localhost", "nsunivac_attendance", "4Ckt6FML9", "nsunivac_attendance");
  }
  $file = $_FILES['importExcelFile']['tmp_name'];
  $handle = fopen($file, "r");
  if ($file == NULL) {
    echo "<script>
                        alert('Please first select an Excel file!!!');
                        location.replace('student.php');
                    </script>";
  } else {
    $c = 0;
    while (($filesop = fgetcsv($handle, 1000, ",")) !== false) {
      $student_name = $filesop[0];
      $student_roll_number = $filesop[1];
      $parent_mob_no_1 = $filesop[2];
      $parent_mob_no_2 = $filesop[3];
      $student_dob = $filesop[4];
      $student_grade_name = $filesop[5];
      $student_grade_id = "";

      $sql_grade = "SELECT * FROM `tbl_grade` WHERE `grade_name`='$student_grade_name' ";
      $result_grade = mysqli_query($conn, $sql_grade);
      $row_grade = mysqli_fetch_assoc($result_grade);
      $student_grade_id = $row_grade["grade_id"];
      $sql = "INSERT INTO `tbl_student`(`student_id`, `student_name`, `student_roll_number`, `parent_mob_no_1`, `parent_mob_no_2`, `student_dob`, `student_grade_id`) 
                    VALUES ('','$student_name','$student_roll_number','$parent_mob_no_1','$parent_mob_no_2','$student_dob','$student_grade_id')";
      $stmt = mysqli_prepare($conn, $sql);
      mysqli_stmt_execute($stmt);
      $c = $c + 1;
    }
    if ($sql) {
      echo "<script>
                            alert('Excel Imported Successfully!!!');
                            location.replace('student.php');
                        </script>";
    } else {
      echo "<script>
                            alert('Something went wrong please try again!!!');
                            location.replace('student.php');
                        </script>";
    }
  }
}
?>
<?php

//student.php

include('header.php');
include '../include/connection.php';
$grade_total='';
if(isset($_GET['grade'])){
$grade=$_GET['grade'];
if($grade!="all"){
$grade_qury="SELECT * FROM `tbl_student` WHERE `student_grade_id`='$grade'";
$grade_result=mysqli_query($connection,$grade_qury);
$grade_total=mysqli_num_rows($grade_result);
}else{
  $grade_qury="SELECT * FROM `tbl_student` WHERE 1";
  $grade_result=mysqli_query($connection,$grade_qury);
  $grade_total=mysqli_num_rows($grade_result);
}
}else {
  $grade_qury="SELECT * FROM `tbl_student` WHERE 1";
  $grade_result=mysqli_query($connection,$grade_qury);
  $grade_total=mysqli_num_rows($grade_result);
}

?>

<div class="container" style="margin-top:30px">
  <div class="card">
    <div class="card-header">
      <div class="row">
        <div class="col-md-6">Student List</div>
        <div class="col-md-6" align="right">
          <button type="button" id="import_button" class="btn btn-success btn-sm" title="CSV format only!!!">Import</button>
          <button type="button" id="add_button" class="btn btn-info btn-sm">Add</button>
        </div>
      </div>
    </div>
    <div class="card-header">
      <div class="row">
        <div class="col-md-8">Total number of Student List : -  <span class="text-danger font-weight-bold"><?php echo $grade_total; ?></span> </div>
        <div class="col-md-4" align="right">
          <select style="width:100%;" class="form-control selectpicker" data-show-subtext="true" data-live-search="true" onchange="changeGrade()" id="changeGradeId">
            <option value="all" <?php
                                echo " selected";
                                ?>>- All -</option>
            <?php
            $query = "
            		SELECT * FROM tbl_grade ORDER BY grade_name ASC
            		";
            $statement = $connect->prepare($query);
            $statement->execute();
            $result = $statement->fetchAll();
            foreach ($result as $row) {

            ?>
              <option value="<?php echo $row["grade_id"]; ?>" <?php if (isset($_GET['grade']))  if ($row["grade_id"] == $_GET["grade"]) {
                                                                echo "disabled selected";
                                                              } ?>><?php echo $row["grade_name"]; ?></option>
            <?php } ?>

          </select>
        </div>
      </div>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <span id="message_operation"></span>
        <table class="table table-striped table-bordered" id="student_table">
          <thead>
            <tr>
              <th>Student Name</th>
              <th>Roll No.</th>
              <th>Parent mob no.</th>
              <th>Alternet No.</th>

              <th>Date of Birth</th>
              <th>Grade</th>
              <th>Edit</th>
              <th>Delete</th>
            </tr>
          </thead>
          <tbody>

          </tbody>
        </table>

      </div>
    </div>
  </div>
</div>
<?php include '../include/footer.php'; ?>
</body>

</html>

<script type="text/javascript" src="../js/bootstrap-datepicker.js"></script>
<link rel="stylesheet" href="../css/datepicker.css" />
<link rel="stylesheet" href="../css/bootstrap.select.min.css">
<link rel="stylesheet" href="../css/bootstrap-select.css">
<script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/js/bootstrap-select.min.js"></script>
<style>
  .datepicker {
    z-index: 1600 !important;
    /* has to be larger than 1050 */
  }
</style>

<div class="modal" id="formModal">
  <div class="modal-dialog">
    <form method="post" id="student_form">
      <div class="modal-content">

        <!-- Modal Header -->
        <div class="modal-header">
          <h4 class="modal-title" id="modal_title"></h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>

        <!-- Modal body -->
        <div class="modal-body">
          <div class="form-group">
            <div class="row">
              <label class="col-md-4 text-right">Student Name <span class="text-danger">*</span></label>
              <div class="col-md-8">
                <input type="text" name="student_name" id="student_name" class="form-control" />
                <span id="error_student_name" class="text-danger"></span>
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="row">
              <label class="col-md-4 text-right">Roll No. <span class="text-danger">*</span></label>
              <div class="col-md-8">
                <input type="text" name="student_roll_number" id="student_roll_number" class="form-control" />
                <span id="error_student_roll_number" class="text-danger"></span>
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="row">
              <label class="col-md-4 text-right">Parent Mob. No <span class="text-danger">*</span></label>
              <div class="col-md-8">
                <input type="text" name="parent_mob_no_1" id="parent_mob_no_1" class="form-control" />
                <span id="error_parent_mob_no_1" class="text-danger"></span>
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="row">
              <label class="col-md-4 text-right">Student no. <span class="text-danger">*</span></label>
              <div class="col-md-8">
                <input type="text" name="parent_mob_no_2" placeholder="Enter Student Mobile Number" id="parent_mob_no_2" class="form-control" />
                <span id="error_parent_mob_no_2" class="text-danger"></span>
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="row">
              <label class="col-md-4 text-right">Date of Birth <span class="text-danger">*</span></label>
              <div class="col-md-8">
                <input type="text" name="student_dob" id="student_dob" class="form-control" />
                <span id="error_student_dob" class="text-danger"></span>
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="row">
              <label class="col-md-4 text-right">Grade <span class="text-danger">*</span></label>
              <div class="col-md-8">
                <select name="student_grade_id" id="student_grade_id" class="form-control">
                  <option value="">Select Grade</option>
                  <?php
                  echo load_grade_list($connect);
                  ?>
                </select>
                <span id="error_student_grade_id" class="text-danger"></span>
              </div>
            </div>
          </div>


        </div>

        <!-- Modal footer -->
        <div class="modal-footer">
          <input type="hidden" name="student_id" id="student_id" />
          <input type="hidden" name="action" id="action" value="Add" />
          <input type="submit" name="button_action" id="button_action" class="btn btn-success btn-sm" value="Add" />
          <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Close</button>
        </div>

      </div>
    </form>
  </div>
</div>
<div class="modal" id="importModal">
  <div class="modal-dialog">
    <form method="post" enctype="multipart/form-data">
      <div class="modal-content">

        <!-- Modal Header -->
        <div class="modal-header">
          <h4 class="modal-title" id="import_title"></h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>

        <!-- Modal body -->
        <div class="modal-body">
          <div class="form-group">
            <div class="row">
              <div class="col-md-12">
                <input type="file" name="importExcelFile" class="form-control" />
                <span id="" class="text-danger"> <a href="format.xls">CSV format only...</a> </span>
              </div>
            </div>
          </div>
        </div>

        <!-- Modal footer -->
        <div class="modal-footer">
          <input type="submit" name="importExcelButton" class="btn btn-success btn-sm" value="Import" />
          <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Close</button>
        </div>

      </div>
    </form>
  </div>
</div>

<div class="modal" id="deleteModal">
  <div class="modal-dialog">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Delete Confirmation</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">
        <h3 align="center">Are you sure you want to remove this?</h3>
      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
        <button type="button" name="ok_button" id="ok_button" class="btn btn-primary btn-sm">OK</button>
        <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Close</button>
      </div>

    </div>
  </div>
</div>
<script>
  function changeGrade() {
    var changeValue = document.getElementById('changeGradeId').value;
    window.location.href = "student.php?grade=" + changeValue;
  }
</script>
<script>
  $(document).ready(function() {

    var dataTable = $('#student_table').DataTable({
      "processing": true,
      "serverSide": true,
      "order": [],
      "ajax": {
        url: "student_action.php<?php if (empty($_GET["grade"])) {
                                  echo "";
                                } else if ($_GET["grade"] == "all") {
                                  echo "?grade=all";
                                } else {
                                  echo "?grade=" . $_GET["grade"];
                                } ?>",
        method: "POST",
        data: {
          action: 'fetch'
        },
      }
    });

    $('#student_dob').datepicker({
      format: "yyyy-mm-dd",
      autoclose: true,
      container: '#formModal modal-body'
    });

    function clear_field() {
      $('#student_form')[0].reset();
      $('#error_student_name').text('');
      $('#error_student_roll_number').text('');
      $('#error_parent_mob_no_1').text('');
      $('#error_parent_mob_no_2').text('');
      $('#error_student_dob').text('');
      $('#error_student_grade_id').text('');
    }

    $('#add_button').click(function() {
      $('#modal_title').text('Add Student');
      $('#button_action').val('Add');
      $('#action').val('Add');
      $('#formModal').modal('show');
      clear_field();
    });

    $('#import_button').click(function() {
      $('#import_title').text('Import an Excel(CSV format only)');
      $('#importModal').modal('show');
      clear_field();
    });

    $('#student_form').on('submit', function(event) {
      event.preventDefault();
      $.ajax({
        url: "student_action.php",
        method: "POST",
        data: $(this).serialize(),
        dataType: "json",
        beforeSend: function() {
          $('#button_action').val('Validate...');
          $('#button_action').attr('disabled', 'disabled');
        },
        success: function(data) {
          $('#button_action').attr('disabled', false);
          $('#button_action').val($('#action').val());
          if (data.success) {
            $('#message_operation').html('<div class="alert alert-success">' + data.success + '</div>');
            clear_field();
            $('#formModal').modal('hide');
            dataTable.ajax.reload();
          }
          if (data.error) {
            if (data.error_student_name != '') {
              $('#error_student_name').text(data.error_student_name);
            } else {
              $('#error_student_name').text('');
            }
            if (data.error_student_roll_number != '') {
              $('#error_student_roll_number').text(data.error_student_roll_number);
            } else {
              $('#error_student_roll_number').text('');
            }

            if (data.error_parent_mob_no_1 != '') {
              $('#error_parent_mob_no_1').text(data.error_parent_mob_no_1);
            } else {
              $('#error_parent_mob_no_1').text('');
            }

            if (data.error_parent_mob_no_2 != '') {
              $('#error_parent_mob_no_2').text(data.error_parent_mob_no_2);
            } else {
              $('#error_parent_mob_no_2').text('');
            }


            if (data.error_student_dob != '') {
              $('#error_student_dob').text(data.error_student_dob);
            } else {
              $('#error_student_dob').text('');
            }
            if (data.error_student_grade_id != '') {
              $('#error_student_grade_id').text(data.error_student_grade_id);
            } else {
              $('#error_student_grade_id').text('');
            }
          }
        }
      })
    });

    var student_id = '';

    $(document).on('click', '.edit_student', function() {
      student_id = $(this).attr('id');
      clear_field();
      $.ajax({
        url: "student_action.php",
        method: "POST",
        data: {
          action: 'edit_fetch',
          student_id: student_id
        },
        dataType: "json",
        success: function(data) {
          $('#student_name').val(data.student_name);
          $('#student_roll_number').val(data.student_roll_number);
          $('#parent_mob_no_1').val(data.parent_mob_no_1);
          $('#parent_mob_no_2').val(data.parent_mob_no_2);
          $('#student_dob').val(data.student_dob);
          $('#student_grade_id').val(data.student_grade_id);
          $('#student_id').val(data.student_id);
          $('#modal_title').text('Edit Student');
          $('#button_action').val('Edit');
          $('#action').val('Edit');
          $('#formModal').modal('show');
        }
      })
    });


    $(document).on('click', '.delete_student', function() {
      student_id = $(this).attr('id');
      $('#deleteModal').modal('show');
    });

    $('#ok_button').click(function() {
      $.ajax({
        url: "student_action.php",
        method: "POST",
        data: {
          student_id: student_id,
          action: "delete"
        },
        success: function(data) {
          $('#message_operation').html('<div class="alert alert-success">' + data + '</div>');
          $('#deleteModal').modal('hide');
          dataTable.ajax.reload();
        }
      })
    });

  });
</script>