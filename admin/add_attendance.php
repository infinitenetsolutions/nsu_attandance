<?php
$page = 5;
//attendance.php

include('header.php');
include '../include/connection.php';
$grade_total = '';
if (isset($_GET['grade'])) {
  $grade = $_GET['grade'];
  if ($grade != "all") {
    $grade_qury = "SELECT * FROM `tbl_attendance` WHERE `grade_id`='$grade'";
    $grade_result = mysqli_query($connection, $grade_qury);
    $grade_total = mysqli_num_rows($grade_result);
  } else {
    $grade_qury = "SELECT * FROM `tbl_attendance` WHERE 1";
    $grade_result = mysqli_query($connection, $grade_qury);
    $grade_total = mysqli_num_rows($grade_result);
  }
} else {
  $grade_qury = "SELECT * FROM `tbl_attendance` WHERE 1";
  $grade_result = mysqli_query($connection, $grade_qury);
  $grade_total = mysqli_num_rows($grade_result);
}

?>

<div class="container" style="margin-top:30px">
  <div class="card">
    <div class="card-header">
      <div class="row">
        <div class="col-md-9">Attendance List</div>
        <div class="col-md-3" align="right">
          <button type="button" id="report_button" class="btn btn-danger btn-sm">Attendance Report</button>
          <button type="button" id="add_button" class="btn btn-info btn-sm">Add</button>
        </div>
      </div>
    </div>
    <div class="card-header">
      <div class="row">
        <div class="col-md-8">Total Student Attendance List : - <span class="text-danger font-weight-bold"><?php echo $grade_total; ?></span></div>
        <div class="col-md-4" align="right">
          <select class="form-control selectpicker" onchange="changeGrade()" id="changeGradeId" data-show-subtext="true" data-live-search="true">
            <option value="all" <?php if (isset($row['grade_id']) && ($row["grade_id"] == "all")) {
                                  echo "disabled selected";
                                } ?>>All</option>
            <?php
            $query = "
            		SELECT * FROM tbl_grade ORDER BY grade_name ASC
            		";
            $statement = $connect->prepare($query);
            $statement->execute();
            $result = $statement->fetchAll();
            foreach ($result as $row) {
            ?>
              <option value="<?php echo $row["grade_id"]; ?>
                      " <?php if (isset($_GET['grade'])) {
                          if ($row["grade_id"] == $_GET["grade"]) {
                            echo "disabled selected";
                          }
                        } ?>><?php echo $row["grade_name"]; ?></option>
            <?php } ?>

          </select>
        </div>
      </div>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <span id="message_operation"></span>
        <table class="table table-striped table-bordered" id="attendance_table">
          <thead>
            <tr>
              <th>Student Name</th>
              <th>Roll Number</th>
              <th>Grade</th>
              <th>Attendance Status</th>
              <th>Attendance Date</th>
            </tr>
          </thead>
          <tbody>

          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

</body>

</html>

<script type="text/javascript" src="../js/bootstrap-datepicker.js"></script>
<link rel="stylesheet" href="css/datepicker.css" />
<link rel="stylesheet" href="../css/bootstrap.select.min.css">
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/css/bootstrap-select.min.css" />
<script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/js/bootstrap-select.min.js"></script>

<style>
  .datepicker {
    z-index: 1600 !important;
    /* has to be larger than 1050 */
  }
</style>

<?php
$grade_id = 0;
if (isset($_GET['grade']))
  if ($_GET["grade"] != "all" && !empty($_GET["grade"]))
    $grade_id = $_GET["grade"];
$query = "
SELECT * FROM tbl_grade WHERE grade_id = '$grade_id'
";

$statement = $connect->prepare($query);
$statement->execute();
$result = $statement->fetchAll();

?>

<div class="modal" id="formModal">
  <div class="modal-dialog">
    <form method="post" id="attendance_form">
      <div class="modal-content">

        <!-- Modal Header -->
        <div class="modal-header">
          <h4 class="modal-title" id="modal_title"></h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>

        <!-- Modal body -->
        <div class="modal-body">
          <?php if ($grade_id == "0") { ?>
            <center><span style="color:red;"> ...Please first select Grade...</span></center>
          <?php } ?>
          <?php
          foreach ($result as $row) {
          ?>
            <div class="form-group">
              <div class="row">
                <label class="col-md-4 text-right">Grade <span class="text-danger">*</span></label>
                <div class="col-md-8">
                  <?php
                  echo '<label>' . $row["grade_name"] . '</label>';
                  ?>
                </div>
              </div>
            </div>
            <div class="form-group">
              <div class="row">
                <label class="col-md-4 text-right">Attendance Date <span class="text-danger">*</span></label>
                <div class="col-md-8">
                  <input type="text" name="attendance_date" id="attendance_date" value="<?php echo date('Y-m-d'); ?>" class="form-control" readonly />
                  <span id="error_attendance_date" class="text-danger"></span>
                </div>
              </div>
            </div>
            <div class="form-group" id="student_details">
              <div align="right">
                <a class="btn btn-success text-white" onclick="location.replace('?grade=<?php echo $_GET["grade"]; ?>&option=present')">Present All</a>
                <a class="btn btn-danger text-white" onclick="location.replace('?grade=<?php echo $_GET["grade"]; ?>&option=absent')">Absent All</a>
              </div>
              <br />
              <div class="table-responsive">
                <table class="table table-striped table-bordered">
                  <thead>
                    <tr>
                      <th>Roll No.</th>
                      <th>Student Name</th>
                      <th>Present</th>
                      <th>Absent</th>
                    </tr>
                  </thead>
                  <?php
                  $sub_query = "
                  SELECT * FROM tbl_student 
                  WHERE student_grade_id = '" . $row["grade_id"] . "'
                ";
                  $statement = $connect->prepare($sub_query);
                  $statement->execute();
                  $student_result = $statement->fetchAll();
                  foreach ($student_result as $student) {
                  ?>
                    <tr>
                      <td><?php echo $student["student_roll_number"]; ?></td>
                      <td>
                        <?php echo $student["student_name"]; ?>
                        <input type="hidden" name="student_id[]" value="<?php echo $student["student_id"]; ?>" />
                      </td>
                      <td>
                        <input type="radio" name="attendance_status<?php echo $student["student_id"]; ?>" <?php if (!isset($_GET["option"]) || $_GET["option"] == "present") {
                                                                                                            echo "checked";
                                                                                                          } ?> value="Present" />
                      </td>
                      <td>
                        <input type="radio" name="attendance_status<?php echo $student["student_id"]; ?>" <?php if (isset($_GET["option"]) && $_GET["option"] == "absent") {
                                                                                                            echo "checked";
                                                                                                          } ?> value="Absent" />
                      </td>
                    </tr>
                  <?php
                  }
                  ?>
                </table>
              </div>
            </div>
          <?php
          }
          ?>
        </div>

        <!-- Modal footer -->
        <div class="modal-footer">
          <input type="hidden" name="action" id="action" value="Add" />
          <input type="submit" name="button_action" id="button_action" class="btn btn-success btn-sm" value="Add" />
          <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Close</button>
        </div>

      </div>
    </form>
  </div>
</div>

<div class="modal" id="reportModal">
  <div class="modal-dialog">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Make Report</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <form method="POST" action="export-excel.php">
        <!-- Modal body -->
        <div class="modal-body">
          <div class="form-group">
            <div class="input-daterange">
              <input type="text" name="from_date" id="from_date" class="form-control" placeholder="From Date" required autocomplete="off" />
              <span id="error_from_date" class="text-danger"></span>
              <br />
              <input type="text" name="to_date" id="to_date" class="form-control" placeholder="To Date" required autocomplete="off" />
              <span id="error_to_date" class="text-danger"></span>
            </div>
          </div>
        </div>
        <!-- Modal footer -->
        <div class="modal-footer">
          <input type="hidden" name="withGrade" value="<?php if (!isset($_GET["grade"])) echo "all";
                                                        else echo $_GET["grade"]; ?>" />
          <button type="submit" name="create_report" id="create_excel_report" class="btn btn-success btn-sm">Create Report</button>
          <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Close</button>
        </div>
      </form>
    </div>
  </div>
</div>
<?php include '../include/footer.php'; ?>
<script>
  function changeGrade() {
    var changeValue = document.getElementById('changeGradeId').value;
    window.location.href = "add_attendance.php?grade=" + changeValue;
  }
</script>
<script>
  $(document).ready(function() {

    var dataTable = $('#attendance_table').DataTable({
      "processing": true,
      "serverSide": true,
      "order": [],
      "ajax": {
        url: "add_attendance_action.php<?php if (empty($_GET["grade"])) {
                                          echo "";
                                        } else if ($_GET["grade"] == "all") {
                                          echo "";
                                        } else {
                                          echo "?grade=" . $_GET["grade"];
                                        } ?>",
        method: "POST",
        data: {
          action: "fetch"
        }
      }
    });

    $('#attendance_date').datepicker({
      format: 'yyyy-mm-dd',
      autoclose: true,
      container: '#formModal modal-body'
    });

    function clear_field() {
      $('#attendance_form')[0].reset();
      $('#error_attendance_date').text('');
    }

    $('#add_button').click(function() {
      $('#modal_title').text("Add Attendance");
      $('#formModal').modal('show');
      clear_field();
    });

    $('#attendance_form').on('submit', function(event) {
      event.preventDefault();
      $.ajax({
        url: "add_attendance_action.php<?php if (empty($_GET["grade"])) {
                                          echo "";
                                        } else if ($_GET["grade"] == "all") {
                                          echo "";
                                        } else {
                                          echo "?grade=" . $_GET["grade"];
                                        } ?>",
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
            if (data.error_attendance_date != '') {
              $('#error_attendance_date').text(data.error_attendance_date);
            } else {
              $('#error_attendance_date').text('');
            }
          }
        }
      })
    });

    $('.input-daterange').datepicker({
      todayBtn: "linked",
      format: "yyyy-mm-dd",
      autoclose: true,
      container: '#formModal modal-body'
    });

    $(document).on('click', '#report_button', function() {
      $('#reportModal').modal('show');
    });

    $('#create_report').click(function() {
      var from_date = $('#from_date').val();
      var to_date = $('#to_date').val();
      var error = 0;
      if (from_date == '') {
        $('#error_from_date').text('From Date is Required');
        error++;
      } else {
        $('#error_from_date').text('');
      }

      if (to_date == '') {
        $('#error_to_date').text("To Date is Required");
        error++;
      } else {
        $('#error_to_date').text('');
      }

      if (error == 0) {
        $('#from_date').val('');
        $('#to_date').val('');
        $('#formModal').modal('hide');
        window.open("report.php?action=attendance_report_teacher&from_date=" + from_date + "&to_date=" + to_date);
      }

    });

  });
</script>